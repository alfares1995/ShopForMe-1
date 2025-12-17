<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $brands = Brand::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->has('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->withCount('products')
            ->paginate(60)
            ->withQueryString();

        return Inertia::render('admin/brands/index', [
            'brands' => $brands, // Fixed: was 'brand', now 'brands'
            'filters' => $request->only(['search', 'is_active'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/brands/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'website' => 'nullable|url|max:255', // Fixed: added 'url' validation
            'is_active' => 'boolean',
        ]);

        // Generate slug from name if not provided
        $slug = $request->slug ?: Str::slug($request->name) . '-' . Brand::generateSlugSuffix();

        Brand::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'logo' => $request->hasFile('logo') ? $request->file('logo')->store('logos', 'public') : null,
            'website' => $request->website,
            'is_active' => $request->boolean('is_active'), // Fixed: proper boolean handling
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        // Fixed: Proper eager loading with pagination
        $brand->loadCount('products');
        $products = $brand->products()
            ->with(['images', 'categories'])
            ->paginate(12);

        return Inertia::render('admin/brands/show', [
            'brand' => $brand,
            'products' => $products,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return Inertia::render('admin/brands/edit', [
            'brand' => $brand,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('brands')->ignore($brand->id)
            ],
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
        ]);

        // Handle logo upload and deletion of old logo
        $logoPath = $brand->logo;
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        // Generate new slug only if name changed and no custom slug provided
        $slug = $brand->slug;
        if ($request->name !== $brand->name && !$request->slug) {
            $slug = Str::slug($request->name) . '-' . Brand::generateSlugSuffix();
        } elseif ($request->slug) {
            $slug = Str::slug($request->slug);
        }

        $brand->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'logo' => $logoPath,
            'website' => $request->website,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        // Delete associated logo file if exists
        if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();
        
        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
    }
}