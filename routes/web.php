<?php

use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index'])->name('shop.index');
Route::get('/categories/{category:slug}', [ShopController::class, 'category'])->name('shop.categories.show');
Route::get('/subcategories/{subcategory:slug}', [ShopController::class, 'subcategory'])->name('shop.subcategories.show');
Route::get('/products/{product:slug}', [ShopController::class, 'show'])->name('shop.products.show');
Route::view('/admin', 'admin.coming-soon')->name('admin.home');
