<?php

use App\Livewire\Brands\Index as BrandsIndex;
use App\Livewire\Cart\Index as CartIndex;
use App\Livewire\Categories\Index as CategoriesIndex;
use App\Livewire\Orders\Checkout as CheckoutPage;
use App\Livewire\Orders\Index as OrdersIndex;
use App\Livewire\Products\Form as ProductFormPage;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\Reviews\Index as ReviewsIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('products', ProductsIndex::class)->name('products.index');
    Route::get('products/{product}/reviews', ReviewsIndex::class)->name('reviews.index');
    Route::get('orders', OrdersIndex::class)->name('orders.index');

    Route::middleware('role:customer')->group(function () {
        Route::get('cart', CartIndex::class)->name('cart.index');
        Route::get('checkout', CheckoutPage::class)->name('checkout');
    });

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