<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubcategoryController extends Controller
{
    public function index(): View
    {
        $subcategories = Subcategory::query()
            ->with('category:id,name')
            ->withCount('products')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.subcategories.index', [
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'subcategories' => $subcategories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subcategories', 'name')->where(
                    fn (QueryBuilder $query): QueryBuilder => $query->where('category_id', $request->input('category_id')),
                ),
            ],
        ]);

        Subcategory::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
        ]);

        return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory created successfully.');
    }

    public function update(Request $request, Subcategory $subcategory): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subcategories', 'name')
                    ->where(fn (QueryBuilder $query): QueryBuilder => $query->where('category_id', $request->input('category_id')))
                    ->ignore($subcategory),
            ],
        ]);

        if ($subcategory->products()->exists() && (int) $validated['category_id'] !== $subcategory->category_id) {
            return back()->withInput()->withErrors([
                'category_id' => 'You cannot move a subcategory while it has products assigned to it.',
            ]);
        }

        $subcategory->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name'], $subcategory),
        ]);

        return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory updated successfully.');
    }

    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        if ($subcategory->products()->exists()) {
            return redirect()->route('admin.subcategories.index')->with(
                'error',
                'This subcategory still has products. Remove or reassign them before deleting it.',
            );
        }

        $subcategory->delete();

        return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory deleted successfully.');
    }

    private function uniqueSlug(string $name, ?Subcategory $subcategory = null): string
    {
        if ($subcategory && Str::slug($name) === Str::slug($subcategory->name)) {
            return $subcategory->slug;
        }

        $baseSlug = Str::slug($name) ?: 'subcategory';
        $slug = $baseSlug;
        $suffix = 2;

        while (Subcategory::query()
            ->where('slug', $slug)
            ->when($subcategory, fn (Builder $query): Builder => $query->where('id', '!=', $subcategory->id))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
