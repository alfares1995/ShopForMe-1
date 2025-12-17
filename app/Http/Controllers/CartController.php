<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\ShoppingCart;

class CartController extends Controller
{
    private function getCartItems()
    {
        $query = ShoppingCart::query();

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $query->where('session_id', session()->getId());
        }

        return $query->with('product')->get()->filter(fn($item) => $item->product);
    }

    public function index()
    {
        $cart = $this->getCartItems();
        return Inertia::render('customer/cart', ['cart' => $cart]);
    }

    public function items()
    {
        $cartItems = $this->getCartItems()->map(function ($item) {
            return [
                'id'       => $item->product->id,
                'name'     => $item->product->name,
                'price'    => $item->product->price,
                'quantity' => $item->quantity,
            ];
        })->values();

        return response()->json($cartItems);
    }

    public function add(Request $request, Product $product)
    {
        ShoppingCart::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id'    => auth()->id(),
                'session_id' => auth()->check() ? null : session()->getId(),
                'price'      => $product->price,
            ],
            [
                'quantity' => DB::raw('quantity + 1')
            ]
        );

        return redirect()->back()->with('success', 'Product added to cart.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = ShoppingCart::where('product_id', $product->id);

        if (auth()->check()) {
            $cartItem->where('user_id', auth()->id());
        } else {
            $cartItem->where('session_id', session()->getId());
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return redirect()->back()->with('success', 'Cart updated.');
    }

    public function remove(Product $product)
    {
        $cartItem = ShoppingCart::where('product_id', $product->id);

        if (auth()->check()) {
            $cartItem->where('user_id', auth()->id());
        } else {
            $cartItem->where('session_id', session()->getId());
        }

        $cartItem->delete();

        return redirect()->back()->with('success', 'Product removed from cart.');
    }

    public function clear()
    {
        $cartQuery = ShoppingCart::query();

        if (auth()->check()) {
            $cartQuery->where('user_id', auth()->id());
        } else {
            $cartQuery->where('session_id', session()->getId());
        }

        $cartQuery->delete();

        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }
}
