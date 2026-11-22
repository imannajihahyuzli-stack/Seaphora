<?php

use App\Actions\CreateProductAction;
use App\Actions\DeleteProductAction;
use App\Actions\UpdateProductAction;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('create action stores a product', function () {
    $data = Product::factory()->make()->toArray();

    $product = (new CreateProductAction)->handle($data);

    expect($product->exists)->toBeTrue();
    $this->assertDatabaseHas('products', ['name' => $data['name']]);
});

test('update action changes the price', function () {
    $product = Product::factory()->create(['price' => 50]);

    (new UpdateProductAction)->handle($product, ['price' => 75]);

    expect($product->fresh()->price)->toEqual(75);
});

test('delete action removes an unordered product', function () {
    $product = Product::factory()->create();

    $result = (new DeleteProductAction)->handle($product);

    expect($result)->toBe('deleted');
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});
