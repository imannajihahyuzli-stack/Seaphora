<?php

use App\Actions\AddToCartAction;
use App\Actions\RemoveFromCartAction;
use App\Actions\UpdateCartItemQuantityAction;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('adding a product creates a cart item', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);

    $item = (new AddToCartAction)->handle($user, $product, 2);

    expect((int) $item->quantity)->toBe(2);
    $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 2]);
});

test('adding the same product again increases the quantity', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);

    (new AddToCartAction)->handle($user, $product, 2);
    $item = (new AddToCartAction)->handle($user, $product, 3);

    expect((int) $item->quantity)->toBe(5)
        ->and(CartItem::count())->toBe(1);
});

test('the quantity can never go above the stock', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['stock' => 3, 'is_active' => true]);

    (new AddToCartAction)->handle($user, $product, 2);
    $item = (new AddToCartAction)->handle($user, $product, 2);

    expect((int) $item->quantity)->toBe(3);
});

test('an out of stock product cannot be added', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['stock' => 0, 'is_active' => true]);

    expect(fn () => (new AddToCartAction)->handle($user, $product))
        ->toThrow(DomainException::class, 'This product is not available.');
});

test('a hidden product cannot be added', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['stock' => 10, 'is_active' => false]);

    expect(fn () => (new AddToCartAction)->handle($user, $product))
        ->toThrow(DomainException::class);
});

test('updating a quantity to zero removes the item', function () {
    $product = Product::factory()->create(['stock' => 10]);
    $item = CartItem::factory()->create(['product_id' => $product->id, 'quantity' => 1]);

    $result = (new UpdateCartItemQuantityAction)->handle($item, 0);

    expect($result)->toBeNull();
    $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
});

test('updating a quantity is capped at the stock', function () {
    $product = Product::factory()->create(['stock' => 4]);
    $item = CartItem::factory()->create(['product_id' => $product->id, 'quantity' => 1]);

    $result = (new UpdateCartItemQuantityAction)->handle($item, 10);

    expect((int) $result->quantity)->toBe(4);
});

test('removing an item deletes it from the cart', function () {
    $item = CartItem::factory()->create();

    (new RemoveFromCartAction)->handle($item);

    $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
});