<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\ShoppingCart;
use App\Models\Product;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    /**
     * Display checkout page with cart items.
     */
    public function index()
    {
        $cartItems = $this->getCartItems();

        return inertia('customer/checkout/index', [
            'cartItems' => $cartItems,
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    /**
     * Store a new order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'state'          => 'nullable|string|max:255',
            'postal_code'    => 'required|string|max:20',
            'country'        => 'required|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'payment_method' => 'required|in:stripe,cod',
        ]);

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return back()->withErrors(['cart' => 'Your cart is empty.']);
        }

        DB::beginTransaction();

        try {
            // Validate all products are available before proceeding
            foreach ($cartItems as $item) {
                if (!$item->product || !$item->product->is_active) {
                    throw new \Exception("Product is no longer available.");
                }
                
                // Check stock availability
                if ($item->product->track_stock && $item->product->stock_quantity < $item->quantity) {
                    throw new \Exception("Insufficient stock for {$item->product->name}.");
                }
            }

            // Calculate totals
            $subtotal = $cartItems->sum(fn($i) => $i->product->price * $i->quantity);
            $taxAmount = $this->calculateTax($subtotal);
            $shippingAmount = $this->calculateShipping($subtotal);
            $discountAmount = 0;
            $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

            // Create order
            $order = Order::create([
                'order_number'    => $this->generateOrderNumber(),
                'user_id'         => auth()->id(),
                'guest_email'     => auth()->check() ? null : $request->email,
                'status'          => 'pending',
                'subtotal'        => $subtotal,
                'tax_amount'      => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'total_amount'    => $totalAmount,
                'currency'        => 'GBP',
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku'  => $item->product->sku,
                    'quantity'     => $item->quantity,
                    'price'        => $item->product->price,
                    'total'        => $item->product->price * $item->quantity,
                ]);
                
                // Reduce stock if tracking is enabled
                if ($item->product->track_stock) {
                    $item->product->decrement('stock_quantity', $item->quantity);
                    
                    // Update stock status if out of stock
                    if ($item->product->stock_quantity <= 0) {
                        $item->product->update(['stock_status' => 'out_of_stock']);
                    }
                }
            }

            // Create shipping address
            OrderAddress::create([
                'order_id'       => $order->id,
                'type'           => 'shipping',
                'first_name'     => $request->first_name,
                'last_name'      => $request->last_name,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city'           => $request->city,
                'state'          => $request->state,
                'postal_code'    => $request->postal_code,
                'country'        => $request->country,
                'phone'          => $request->phone,
            ]);

            // Handle payment based on method
            if ($request->payment_method === 'stripe') {
                // Create payment record
                $payment = Payment::create([
                    'order_id'       => $order->id,
                    'payment_method' => 'stripe',
                    'status'         => 'pending',
                    'amount'         => $totalAmount,
                    'currency'       => $order->currency,
                ]);

                // Create Stripe checkout session
                $paymentData = $this->handleStripePayment($order);
                
                $payment->update([
                    'payment_id' => '1234567890', // Placeholder for actual payment ID
                    'status'     => 'processing',
                ]);

                DB::commit();

                return inertia('customer/checkout/payment', [
                    'sessionId'  => $paymentData['id'],
                    'stripeKey'  => config('services.stripe.key'),
                    'orderId'    => $order->id,
                ]);
            }

            // COD payment handling
            $payment = Payment::create([
                'order_id'       => $order->id,
                'payment_method' => 'cod',
                'status'         => 'pending',
                'amount'         => $totalAmount,
                'currency'       => $order->currency,
            ]);

            // Clear cart
            $cartItems->each->delete();

            // Update order status
            $order->update(['status' => 'processing']);

            DB::commit();

            return redirect()->route('checkout.success', ['order' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Checkout error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'checkout' => 'Checkout failed: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    /**
     * Retrieve cart items.
     */
    private function getCartItems()
    {
        $query = ShoppingCart::query();

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $query->where('session_id', session()->getId());
        }

        return $query->with('product')->get();
    }

    /**
     * Generate unique order number.
     */
    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Calculate tax.
     */
    private function calculateTax(float $subtotal): float
    {
        $taxRate = config('shop.tax_rate', 0.20);
        return round($subtotal * $taxRate, 2);
    }

    /**
     * Calculate shipping.
     */
    private function calculateShipping(float $subtotal): float
    {
        $freeShippingThreshold = config('shop.free_shipping_min', 50);
        $standardShipping = config('shop.shipping_rate', 4.99);
        return $subtotal >= $freeShippingThreshold ? 0 : $standardShipping;
    }

    /**
     * Handle Stripe payment creation with Embedded Checkout.
     */
    private function handleStripePayment(Order $order): array
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = $order->items->map(function ($item) {
            return [
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => [
                        'name' => $item->product_name,
                    ],
                    'unit_amount' => (int)($item->price * 100), // Convert to pence
                ],
                'quantity' => $item->quantity,
            ];
        })->toArray();

        // Add shipping as line item if applicable
        if ($order->shipping_amount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => [
                        'name' => 'Shipping',
                    ],
                    'unit_amount' => (int)($order->shipping_amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        // Add tax as line item
        if ($order->tax_amount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => [
                        'name' => 'VAT (20%)',
                    ],
                    'unit_amount' => (int)($order->tax_amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        // Create Checkout Session for Embedded Checkout
        $session = Session::create([
            'ui_mode' => 'embedded', // Enable embedded checkout
            'line_items' => $lineItems,
            'mode' => 'payment',
            'return_url' => route('checkout.success', ['order' => $order->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'customer_email' => $order->user ? $order->user->email : $order->guest_email,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ]);

        return [
            'id' => $session->client_secret, // Return client_secret for embedded checkout
            'client_secret' => $session->client_secret,
        ];
    }

    /**
     * Handle successful payment.
     */
    public function success(Request $request, Order $order)
    {
        // Verify the order belongs to the current user or session
        if (auth()->check() && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // If coming from Stripe, verify the payment
        if ($request->has('session_id')) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $session = Session::retrieve($request->session_id);
                
                if ($session->payment_status === 'paid') {
                    // Update payment status
                    $payment = $order->payments()->first();
                    if ($payment) {
                        $payment->update([
                            'status' => 'completed',
                            'transaction_id' => $session->payment_intent,
                            'paid_at' => now(),
                        ]);
                    }
                    
                    // Update order status
                    $order->update(['status' => 'processing']);
                    
                    // Clear cart
                    $this->getCartItems()->each->delete();
                }
            } catch (\Exception $e) {
                Log::error('Stripe verification error: ' . $e->getMessage());
            }
        }

        return inertia('customer/checkout/success', [
            'orderId' => $order->id,
            'orderNumber' => $order->order_number,
            'order' => $order->load(['items', 'addresses', 'payments']),
        ]);
    }
}