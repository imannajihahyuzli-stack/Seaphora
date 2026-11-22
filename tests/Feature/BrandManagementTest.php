<?php

use App\Livewire\Brands\Index;
use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin can create a brand', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('form.name', 'Test Brand')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('brands', ['name' => 'Test Brand']);
});

test('brand name is required', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('save')
        ->assertHasErrors(['form.name' => 'required']);
});

test('staff cannot open brand management', function () {
    $staff = User::factory()->create(['role' => 'staff']);

    $this->actingAs($staff)->get('/brands')->assertForbidden();
});

test('a brand with products cannot be deleted', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('delete', $product->brand_id);

    $this->assertDatabaseHas('brands', ['id' => $product->brand_id]);
});

test('an empty brand can be deleted', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $brand = Brand::factory()->create();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('delete', $brand->id);

    $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
});
