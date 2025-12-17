<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index(Request $request)
    {
        $reviews = Review::query()
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('comment', 'like', "%{$search}%")
                      ->orWhere('reviewer_name', 'like', "%{$search}%");
            })
            ->when($request->rating, function ($query, $rating) {
                $query->where('rating', $rating);
            })
            ->when($request->has('is_approved'), function ($query) use ($request) {
                $query->where('is_approved', $request->boolean('is_approved'));
            })
            ->when($request->product_id, function ($query, $productId) {
                $query->where('product_id', $productId);
            })
            ->with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $products = Product::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews,
            'products' => $products,
            'filters' => $request->only(['search', 'rating', 'is_approved', 'product_id'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|max:1000',
            'order_id' => 'nullable|exists:orders,id'
        ]);

        $user = Auth::user();
        
        $validated['user_id'] = $user ? $user->id : null;
        $validated['reviewer_name'] = $user ? $user->first_name . ' ' . $user->last_name : 'Anonymous';
        $validated['reviewer_email'] = $user ? $user->email : 'anonymous@example.com';
        $validated['is_verified'] = $validated['order_id'] ? true : false;

        Review::create($validated);

        return back()->with('success', 'Review submitted successfully.');
    }

    public function show(Review $review)
    {
        $review->load(['product', 'user', 'order']);

        return Inertia::render('Reviews/Show', [
            'review' => $review
        ]);
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Review approved successfully.');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        return back()->with('success', 'Review rejected successfully.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
   
}
