<?php

use App\Livewire\Categories\Index;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin can create a category', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('form.name', 'Test Category')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
});

test('category name is required', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('save')
        ->assertHasErrors(['form.name' => 'required']);
});

test('staff cannot open category management', function () {
    $staff = User::factory()->create(['role' => 'staff']);

    $this->actingAs($staff)->get('/categories')->assertForbidden();
});

test('a category with products cannot be deleted', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('delete', $product->category_id);

    $this->assertDatabaseHas('categories', ['id' => $product->category_id]);
});

test('an empty category can be deleted', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('delete', $category->id);

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});
