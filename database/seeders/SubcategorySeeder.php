<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subcategories = [
            'electronics' => ['Phones', 'Laptops'],
            'clothing' => ['Men', 'Women'],
        ];

        foreach ($subcategories as $categorySlug => $names) {
            $category = Category::where('slug', $categorySlug)->firstOrFail();

            foreach ($names as $name) {
                Subcategory::firstOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                    ],
                    [
                        'slug' => $this->uniqueSlug($name),
                    ],
                );
            }
        }
    }

    private function uniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'subcategory';
        $slug = $baseSlug;
        $suffix = 2;

        while (Subcategory::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
