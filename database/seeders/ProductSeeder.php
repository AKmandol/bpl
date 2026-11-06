<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['category' => 'electronics', 'subcategory' => 'Phones', 'name' => 'Everyday Smartphone', 'old_price' => 499.99, 'new_price' => 399.99],
            ['category' => 'electronics', 'subcategory' => 'Laptops', 'name' => 'Student Laptop', 'old_price' => 799.99, 'new_price' => 699.99],
            ['category' => 'clothing', 'subcategory' => 'Men', 'name' => 'Classic Cotton Shirt', 'old_price' => 39.99, 'new_price' => 29.99],
            ['category' => 'clothing', 'subcategory' => 'Women', 'name' => 'Everyday Tote Bag', 'old_price' => null, 'new_price' => 24.99],
        ];

        foreach ($products as $product) {
            $category = Category::where('slug', $product['category'])->firstOrFail();
            $subcategory = Subcategory::query()
                ->where('category_id', $category->id)
                ->where('name', $product['subcategory'])
                ->firstOrFail();
            $slug = str($product['name'])->slug()->toString();

            Product::firstOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory->id,
                    'name' => $product['name'],
                    'description' => 'A sample product for demonstrating the storefront.',
                    'old_price' => $product['old_price'],
                    'new_price' => $product['new_price'],
                ],
            );
        }
    }
}
