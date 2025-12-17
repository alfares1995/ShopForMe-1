<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
            })
            ->when($request->brand_id, function ($query, $brandId) {
                $query->where('brand_id', $brandId);
            })
            ->when($request->stock_status, function ($query, $status) {
                $query->where('stock_status', $status);
            })
            ->when($request->has('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->with(['brand', 'categories', 'images' => function ($query) {
                $query->where('is_primary', true);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(100)
            ->withQueryString();

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        return Inertia::render('admin/products/index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $request->only(['search', 'category_id', 'brand_id', 'stock_status', 'is_active'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $attributes = ProductAttribute::orderBy('sort_order')->get();

        return Inertia::render('admin/products/create', [
            'categories' => $categories,
            'brands' => $brands,
            'attributes' => $attributes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateProductData($request);
        
        // Generate unique slug
        $validated['slug'] = $this->generateUniqueSlug($validated['name']);
        
        // Set stock status based on quantity
        $validated['stock_status'] = $validated['stock_quantity'] > 0 ? 'in_stock' : 'out_of_stock';

        DB::beginTransaction();
        
        try {
            $product = Product::create($validated);

            // Attach categories
            if (!empty($validated['category_ids'])) {
                $product->categories()->attach($validated['category_ids']);
            }

            // Handle image uploads
            $this->handleImageUploads($product, $request->images ?? []);

            // Add attribute values
            $this->handleAttributeValues($product, $validated['attributes'] ?? []);

            DB::commit();

            return redirect()->route('admin.product.index')
                ->with('success', 'Product created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to create product. Please try again.'])
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load([
            'brand',
            'categories',
            'images' => function ($query) {
                $query->orderBy('sort_order');
            },
            'attributeValues.attribute',
            'reviews' => function ($query) {
                $query->where('is_approved', true)
                      ->with('user')
                      ->orderBy('created_at', 'desc');
            }
        ]);

        return Inertia::render('admin/products/show', [
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load(['categories', 'images' => function ($query) {
            $query->orderBy('sort_order');
        }, 'attributeValues']);
        
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $attributes = ProductAttribute::orderBy('sort_order')->get();

        return Inertia::render('admin/products/edit', [
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
            'attributes' => $attributes
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProductData($request, $product->id);
        
        // Generate unique slug if name changed
        if ($validated['name'] !== $product->name) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name']);
        }
        
        $validated['stock_status'] = $validated['stock_quantity'] > 0 ? 'in_stock' : 'out_of_stock';

        DB::beginTransaction();
        
        try {
            $product->update($validated);

            // Sync categories
            if (isset($validated['category_ids'])) {
                $product->categories()->sync($validated['category_ids']);
            }

            // Handle new image uploads if provided
            if ($request->has('images') && !empty($request->images)) {
                $this->handleImageUploads($product, $request->images);
            }

            // Update attribute values if provided
            if (isset($validated['attributes'])) {
                $this->updateAttributeValues($product, $validated['attributes']);
            }

            DB::commit();

            return redirect()->route('admin.product.index')
                ->with('success', 'Product updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product update failed: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to update product. Please try again.'])
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        
        try {
            // Delete all product images from storage
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }
            
            $product->delete();
            
            DB::commit();

            return redirect()->route('admin.product.index')
                ->with('success', 'Product deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product deletion failed: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to delete product. Please try again.']);
        }
    }

    /**
     * Validate product data
     */
    private function validateProductData(Request $request, $productId = null)
    {
        $skuRule = $productId ? "required|string|unique:products,sku,{$productId}" : 'required|string|unique:products';
        
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => $skuRule,
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gte:price',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'track_stock' => 'boolean',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'brand_id' => 'nullable|exists:brands,id',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_digital' => 'boolean',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'attributes' => 'nullable|array'
        ]);
    }

    /**
     * Generate unique slug for product
     */
    private function generateUniqueSlug($name)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Handle image uploads
     */
    private function handleImageUploads(Product $product, array $images)
    {
        if (empty($images)) {
            return;
        }

        $hasPrimary = false;
        
        foreach ($images as $index => $imageData) {
            if (!isset($imageData['image_path'])) {
                continue;
            }
            
            $uploadedFile = $imageData['image_path'];
            $imagePath = $uploadedFile->store('products', 'public');

            $isPrimary = $imageData['is_primary'] ?? false;
            
            // Ensure only one primary image
            if ($isPrimary && !$hasPrimary) {
                $hasPrimary = true;
                // Set all existing images as non-primary
                $product->images()->update(['is_primary' => false]);
            } elseif ($isPrimary && $hasPrimary) {
                $isPrimary = false;
            }

            $product->images()->create([
                'image_path' => $imagePath,
                'alt_text' => $imageData['alt_text'] ?? $product->name,
                'is_primary' => $isPrimary,
                'sort_order' => $index,
            ]);
        }
        
        // If no primary image was set, make the first one primary
        if (!$hasPrimary && $product->images()->count() > 0) {
            $product->images()->first()->update(['is_primary' => true]);
        }
    }

    /**
     * Handle attribute values for new products
     */
    private function handleAttributeValues(Product $product, array $attributes)
    {
        foreach ($attributes as $attributeId => $value) {
            if (!empty($value)) {
                $product->attributeValues()->create([
                    'product_attribute_id' => $attributeId,
                    'value' => $value
                ]);
            }
        }
    }

    /**
     * Update attribute values for existing products
     */
    private function updateAttributeValues(Product $product, array $attributes)
    {
        // Delete existing attribute values
        $product->attributeValues()->delete();
        
        // Add new attribute values
        $this->handleAttributeValues($product, $attributes);
    }
    /**
     * Delete a specific product image
     */
    public function deleteImage(Product $product, $imageId)
    {
        $image = ProductImage::where('product_id', $product->id)->findOrFail($imageId);

        // Delete from storage
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Delete from database
        $image->delete();

        // If this was the primary image, make another one primary
        if ($image->is_primary) {
            $nextImage = $product->images()->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Image deleted successfully.');
    }
}