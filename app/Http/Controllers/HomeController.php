<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function index()
    {
        $featured = Product::with('images')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take(8)
            ->get();

        $latest = Product::with('images')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        return Inertia::render('Welcome', [
            'featuredProducts' => $featured,
            'latestProducts'   => $latest,
        ]);
    }
}
