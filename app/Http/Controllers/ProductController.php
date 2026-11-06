<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with(['category:id,name', 'subcategory:id,category_id,name'])
            ->orderBy('name')
            ->paginate(10);
        $categories = $this->categoryOptions();

        return view('admin.products.index', [
            'categories' => $categories,
            'hasSubcategories' => $categories->contains(
                fn (Category $category): bool => $category->subcategories->isNotEmpty(),
            ),
            'products' => $products,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');

            if (! $imagePath) {
                return back()->withInput()->withErrors(['image' => 'The image could not be saved. Please try again.']);
            }
        }

        Product::create([
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'description' => $validated['description'],
            'image_path' => $imagePath,
            'old_price' => $validated['old_price'] ?? null,
            'new_price' => $validated['new_price'],
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request);
        $previousImagePath = $product->image_path;
        $newImagePath = null;

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('products', 'public');

            if (! $newImagePath) {
                return back()->withInput()->withErrors(['image' => 'The image could not be saved. Please try again.']);
            }
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name'], $product),
            'description' => $validated['description'],
            'old_price' => $validated['old_price'] ?? null,
            'new_price' => $validated['new_price'],
            'image_path' => $newImagePath ?? $previousImagePath,
        ]);

        if ($previousImagePath && $product->image_path !== $previousImagePath) {
            Storage::disk('public')->delete($previousImagePath);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $imagePath = $product->image_path;
        $product->delete();

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'subcategory_id' => [
                'required',
                'integer',
                Rule::exists('subcategories', 'id')->where(
                    fn (QueryBuilder $query): QueryBuilder => $query->where('category_id', $request->input('category_id')),
                ),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:2048'],
            'old_price' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99', 'gte:new_price'],
            'new_price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
        ]);
    }

    private function uniqueSlug(string $name, ?Product $product = null): string
    {
        if ($product && Str::slug($name) === Str::slug($product->name)) {
            return $product->slug;
        }

        $baseSlug = Str::slug($name) ?: 'product';
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::query()
            ->where('slug', $slug)
            ->when($product, fn (Builder $query): Builder => $query->where('id', '!=', $product->id))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /** @return EloquentCollection<int, Category> */
    private function categoryOptions(): EloquentCollection
    {
        return Category::query()
            ->with('subcategories:id,category_id,name')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
