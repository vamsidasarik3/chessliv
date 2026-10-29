<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

class CategoryController extends Controller
{
    /**
     * Display category listing with article counts and filters.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('articles')->orderBy('order')->orderBy('name');

        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $categories = $query->paginate(12)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:categories,slug',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $baseSlug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        if (empty($baseSlug)) {
            $baseSlug = 'category-' . time();
        }

        $slug = $baseSlug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        DB::transaction(function () use ($validated, $slug, $request) {
            Category::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'icon' => $validated['icon'] ?: 'bi-tag',
                'description' => $validated['description'] ?? null,
                'order' => $validated['order'] ?? (Category::max('order') + 1),
                'is_active' => $request->boolean('is_active', true),
            ]);
        });

        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$validated['name']}' created successfully.");
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:categories,slug,' . $category->id,
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : $category->slug;

        DB::transaction(function () use ($category, $validated, $slug, $request) {
            $category->update([
                'name' => $validated['name'],
                'slug' => $slug,
                'icon' => $validated['icon'] ?: 'bi-tag',
                'description' => $validated['description'] ?? null,
                'order' => $validated['order'] ?? $category->order,
                'is_active' => $request->boolean('is_active', true),
            ]);
        });

        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' updated successfully.");
    }

    /**
     * Toggle active/inactive state of a category.
     */
    public function toggle($id)
    {
        $category = Category::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        $status = $category->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' has been {$status}.");
    }

    /**
     * Safely delete a category with article reassignment.
     */
    public function destroy($id)
    {
        $category = Category::withCount('articles')->findOrFail($id);

        // Prevent deleting the very last category
        if (Category::count() <= 1) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete the only existing category in the system.');
        }

        $name = $category->name;

        DB::transaction(function () use ($category) {
            // Find an alternative default category
            $defaultCat = Category::where('id', '!=', $category->id)->orderBy('id')->first();

            if ($defaultCat) {
                // Reassign primary category on articles
                Article::where('category_id', $category->id)->update(['category_id' => $defaultCat->id]);
            }

            $category->delete();
        });

        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$name}' deleted. Associated articles reassigned safely.");
    }
}
