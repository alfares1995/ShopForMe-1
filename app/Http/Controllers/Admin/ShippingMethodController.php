<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ShippingMethod;

class ShippingMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $shippingMethods = ShippingMethod::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->has('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->orderBy('sort_order')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('ShippingMethods/Index', [
            'shippingMethods' => $shippingMethods,
            'filters' => $request->only(['search', 'is_active'])
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return Inertia::render('ShippingMethods/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'estimated_days_min' => 'nullable|integer|min:1',
            'estimated_days_max' => 'nullable|integer|min:1|gte:estimated_days_min',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        ShippingMethod::create($validated);

        return redirect()->route('shipping-methods.index')
            ->with('success', 'Shipping method created successfully.');
    }

    /**
     * Display the specified resource.
     */
   public function show(ShippingMethod $shippingMethod)
    {
        return Inertia::render('ShippingMethods/Show', [
            'shippingMethod' => $shippingMethod
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingMethod $shippingMethod)
    {
        return Inertia::render('ShippingMethods/Edit', [
            'shippingMethod' => $shippingMethod
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, ShippingMethod $shippingMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'estimated_days_min' => 'nullable|integer|min:1',
            'estimated_days_max' => 'nullable|integer|min:1|gte:estimated_days_min',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $shippingMethod->update($validated);

        return redirect()->route('shipping-methods.index')
            ->with('success', 'Shipping method updated successfully.');
    }

    

    /**
     * Remove the specified resource from storage.
     */
 public function destroy(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();

        return redirect()->route('shipping-methods.index')
            ->with('success', 'Shipping method deleted successfully.');
    }
 public function getAvailable(Request $request)
    {
        $request->validate([
            'cart_total' => 'required|numeric|min:0'
        ]);

        $shippingMethods = ShippingMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($method) use ($request) {
                $isFree = $method->free_shipping_threshold && 
                         $request->cart_total >= $method->free_shipping_threshold;
                
                return [
                    'id' => $method->id,
                    'name' => $method->name,
                    'description' => $method->description,
                    'price' => $isFree ? 0 : $method->price,
                    'original_price' => $method->price,
                    'is_free' => $isFree,
                    'estimated_days_min' => $method->estimated_days_min,
                    'estimated_days_max' => $method->estimated_days_max
                ];
            });

        return response()->json($shippingMethods);
    }
}
