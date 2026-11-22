<?php

use App\Livewire\Brands\Index as BrandsIndex;
use App\Livewire\Categories\Index as CategoriesIndex;
use App\Livewire\Products\Form as ProductFormPage;
use App\Livewire\Products\Index as ProductsIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('products', ProductsIndex::class)->name('products.index');

    Route::middleware('role:staff,admin')->group(function () {
        Route::get('products/create', ProductFormPage::class)->name('products.create');
        Route::get('products/{product}/edit', ProductFormPage::class)->name('products.edit');

        Route::middleware('role:admin')->group(function () {
            Route::get('brands', BrandsIndex::class)->name('brands.index');
            Route::get('categories', CategoriesIndex::class)->name('categories.index');
        });

    });
});

require __DIR__.'/settings.php';
