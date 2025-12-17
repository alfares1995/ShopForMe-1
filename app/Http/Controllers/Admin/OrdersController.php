<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Stripe\Stripe;
use Stripe\Refund;
use Stripe\Exception\ApiErrorException;

class OrdersController extends Controller
{
    /**
     * Display all orders with search and filtering.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'payments'])
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter (optional)
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(20)
            ->appends($request->only(['search', 'status']));

        return inertia('admin/orders/index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Display single order with all relationships.
     */
    public function show(Order $order)
    {
        return inertia('admin/orders/show', [
            'order' => $order->load([
                'items.product.images', 
                'payments', 
                'addresses',
                'user'
            ]),
        ]);
    }

    /**
     * Update order status with validation and logging.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
        ]);

        // Prevent invalid status transitions
        $this->validateStatusTransition($order->status, $validated['status']);

        $oldStatus = $order->status;
        
        DB::beginTransaction();
        try {
            $order->update(['status' => $validated['status']]);

            // Log the status change
            Log::info("Order status updated", [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'updated_by' => auth()->id(),
            ]);

            // Optional: Send notification to customer
            // event(new OrderStatusUpdated($order));

            DB::commit();

            return back()->with('success', 'Order status updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Order status update failed", [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['status' => 'Failed to update order status.']);
        }
    }

    /**
     * Issue a refund for Stripe or COD orders.
     */
    public function refund(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:' . $order->total_amount
            ],
            'reason' => 'nullable|string|max:255',
        ]);

        // Check if order is already refunded
        if ($order->status === 'refunded') {
            return back()->withErrors(['refund' => 'This order has already been refunded.']);
        }

        $payment = $order->payments()
            ->whereIn('status', ['completed', 'paid'])
            ->first();

        if (!$payment) {
            return back()->withErrors(['refund' => 'No eligible payment found for this order.']);
        }

        // Check if already refunded
        if ($payment->status === 'refunded') {
            return back()->withErrors(['refund' => 'Payment has already been refunded.']);
        }

        DB::beginTransaction();
        try {
            if ($payment->payment_method === 'stripe') {
                $this->processStripeRefund($payment, $validated['amount'], $validated['reason'] ?? null);
            } else {
                // COD or other manual refund methods
                $this->processManualRefund($payment, $validated['reason'] ?? null);
            }

            // Update order status
            $order->update(['status' => 'refunded']);

            // Log the refund
            Log::info("Refund processed successfully", [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_id' => $payment->id,
                'amount' => $validated['amount'],
                'method' => $payment->payment_method,
                'processed_by' => auth()->id(),
            ]);

            // Optional: Send refund confirmation email
            // Mail::to($order->customer_email)->send(new RefundProcessed($order));

            DB::commit();

            return back()->with('success', 'Refund issued successfully.');

        } catch (ApiErrorException $e) {
            DB::rollBack();
            
            Log::error("Stripe refund failed", [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'stripe_error_code' => $e->getError()->code ?? null,
            ]);

            return back()->withErrors([
                'refund' => "Stripe refund failed: " . $this->formatStripeError($e)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Refund processing failed", [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'refund' => "Refund failed: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Process Stripe refund via API.
     *
     * @throws ApiErrorException
     */
    protected function processStripeRefund(Payment $payment, float $amount, ?string $reason): void
    {
        if (!$payment->transaction_id) {
            throw new \Exception("Stripe payment intent ID is missing.");
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // Create Stripe refund
        $refund = Refund::create([
            'payment_intent' => $payment->transaction_id,
            'amount' => (int) round($amount * 100), // Convert to cents
            'reason' => 'requested_by_customer',
            'metadata' => [
                'order_id' => $payment->order_id,
                'admin_reason' => $reason ?? 'No reason provided',
            ],
        ]);

        // Update payment record
        $paymentDetails = $payment->payment_details ?? [];
        $paymentDetails['refund_id'] = $refund->id;
        $paymentDetails['refund_amount'] = $amount;
        $paymentDetails['refund_created_at'] = now()->toIso8601String();

        $payment->update([
            'status' => 'refunded',
            'failure_reason' => $reason,
            'payment_details' => $paymentDetails,
        ]);
    }

    /**
     * Process manual refund (COD, bank transfer, etc.).
     */
    protected function processManualRefund(Payment $payment, ?string $reason): void
    {
        $paymentDetails = $payment->payment_details ?? [];
        $paymentDetails['refund_processed_at'] = now()->toIso8601String();
        $paymentDetails['refund_method'] = 'manual';

        $payment->update([
            'status' => 'refunded',
            'failure_reason' => $reason ?? 'Manual refund processed by admin',
            'payment_details' => $paymentDetails,
        ]);
    }

    /**
     * Validate status transitions to prevent invalid changes.
     *
     * @throws ValidationException
     */
    protected function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $invalidTransitions = [
            'delivered' => ['pending', 'processing'],
            'refunded' => ['pending', 'processing', 'shipped'],
            'cancelled' => ['delivered'],
        ];

        if (isset($invalidTransitions[$currentStatus]) && 
            in_array($newStatus, $invalidTransitions[$currentStatus])) {
            throw ValidationException::withMessages([
                'status' => "Cannot change status from '{$currentStatus}' to '{$newStatus}'."
            ]);
        }
    }

    /**
     * Format Stripe error messages for users.
     */
    protected function formatStripeError(ApiErrorException $e): string
    {
        $errorCode = $e->getError()->code ?? 'unknown';
        
        $friendlyMessages = [
            'charge_already_refunded' => 'This payment has already been refunded.',
            'amount_too_large' => 'Refund amount exceeds the original payment amount.',
            'payment_intent_unexpected_state' => 'Payment is not in a refundable state.',
        ];

        return $friendlyMessages[$errorCode] ?? $e->getMessage();
    }
}