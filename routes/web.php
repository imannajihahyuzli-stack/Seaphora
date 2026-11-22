<?php

use App\Livewire\Products\Form as ProductFormPage;
use App\Livewire\Products\Index as ProductsIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('products', ProductsIndex::class)->name('products.index');
    Route::get('products/create', ProductFormPage::class)->name('products.create');
    Route::get('products/{product}/edit', ProductFormPage::class)->name('products.edit');
});

require __DIR__.'/settings.php';
