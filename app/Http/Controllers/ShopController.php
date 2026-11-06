<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        return $this->renderShop();
    }

    public function category(Category $category): View
    {
        return $this->renderShop(category: $category);
    }

    public function subcategory(Subcategory $subcategory): View
    {
        return $this->renderShop(category: $subcategory->category, subcategory: $subcategory);
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'subcategory']);

        return view('shop.show', [
            'categories' => $this->sidebarCategories(),
            'product' => $product,
        ]);
    }

    private function renderShop(?Category $category = null, ?Subcategory $subcategory = null): View
    {
        $products = Product::query()
            ->with([
                'category:id,name,slug',
                'subcategory:id,category_id,name,slug',
            ])
            ->when($category, fn (Builder $query): Builder => $query->whereBelongsTo($category))
            ->when($subcategory, fn (Builder $query): Builder => $query->whereBelongsTo($subcategory))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', [
            'categories' => $this->sidebarCategories(),
            'activeCategory' => $category,
            'activeSubcategory' => $subcategory,
            'products' => $products,
        ]);
    }

    private function sidebarCategories(): EloquentCollection
    {
        return Category::query()
            ->with(['subcategories' => function (HasMany $subcategories): void {
                $subcategories->withCount('products');
            }])
            ->withCount('products')
            ->orderBy('name')
            ->get();
    }
}
