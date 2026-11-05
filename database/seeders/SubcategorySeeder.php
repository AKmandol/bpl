<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

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
                    ['slug' => str($name)->slug()->toString()],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                    ],
                );
            }
        }
    }
}
