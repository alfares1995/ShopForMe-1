<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
      $categories = Category::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })

              ->when($request->parent_id, function ($query, $parentId) {
                if ($parentId === 'root') {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $parentId);
                }
            })
          
            ->when($request->has('is_active') && $request->is_active !== '', function ($query) use ($request) {
                    $query->where('is_active', (int) $request->is_active);})

            ->with(['parent', 'children'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/category/index', [
            'categories' => $categories,
            'parentCategories' => $parentCategories,
            'filters' => $request->only(['search','parent_id', 'is_active'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/category/create', [
            'parentCategories' => $parentCategories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'required|boolean'
        ]);
        Category::create([
                'name' => $request->input('name'),
                'slug' => Str::slug($request->input('name').'-'. Category::generateSlugSuffix()),
                'description' => $request->input('description'),
                
                'meta_title' => Str::limit($request->input('name'), 255),
                'meta_description' => Str::limit($request->input('description'), 255),
                'sort_order' => $request->input('sort_order') ?? 0,
                'parent_id' => $request->input('parent_id'),
                'is_active' => $request->input('is_active'),
            ]
            
        );
        return redirect()->route('admin.category.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    $products = $category->products()->with(['brand', 'images'])->paginate(12);

    $category->load(['parent', 'children']);

    return Inertia::render('admin/category/show', [
        'category' => $category,
        'products' => $products
    ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->getKey())
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/category/edit', [
            'category' => $category,
            'parentCategories' => $parentCategories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);
        $category->update([
            'name' => $request->input('name'),
            'slug' => $request->input('slug') ?? Str::slug($request->input('name') . '-' . Str::random(5)),
            'description' => $request->input('description'),
            'meta_title' => Str::limit($request->input('name'), 255),
            'meta_description' => Str::limit($request->input('description'), 255),
            'sort_order' => $request->input('sort_order') ?? 0,
            'is_active' => $request->input('is_active'),
        ]);
        return redirect()->route('admin.category.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
        $category->delete();
        return redirect()->route('admin.category.index')->with('success', 'Category deleted successfully.');

    }
}
