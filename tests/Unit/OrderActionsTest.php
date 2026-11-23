<?php

use App\Actions\AddToCartAction;
use App\Actions\CreateOrderAction;
use App\Actions\UpdateOrderStatusAction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('checkout creates a pending order with the right total', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['price' => 10, 'stock' => 5, 'is_active' => true]);
    (new AddToCartAction)->handle($user, $product, 2);

    $order = (new CreateOrderAction)->handle($user, '12 Jalan Bunga, Rawang');

    expect($order->status)->toBe('pending')
        ->and((float) $order->fresh()->total)->toBe(20.0);
    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

test('checkout reduces the stock and empties the cart', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['price' => 10, 'stock' => 5, 'is_active' => true]);
    (new AddToCartAction)->handle($user, $product, 2);

    (new CreateOrderAction)->handle($user, '12 Jalan Bunga, Rawang');

    expect((int) $product->fresh()->stock)->toBe(3);
    $this->assertDatabaseCount('cart_items', 0);
});

test('checkout fails when the cart is empty', function () {
    $user = User::factory()->create(['role' => 'customer']);

    expect(fn () => (new CreateOrderAction)->handle($user, '12 Jalan Bunga, Rawang'))
        ->toThrow(DomainException::class, 'Your cart is empty.');
});

test('checkout fails and saves nothing when stock ran out', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['price' => 10, 'stock' => 5, 'is_active' => true]);
    (new AddToCartAction)->handle($user, $product, 3);

    $product->update(['stock' => 1]);

    expect(fn () => (new CreateOrderAction)->handle($user, '12 Jalan Bunga, Rawang'))
        ->toThrow(DomainException::class);
    $this->assertDatabaseCount('orders', 0);
});

test('an order moves from pending to paid', function () {
    $order = Order::factory()->create(['status' => 'pending']);

    $updated = (new UpdateOrderStatusAction)->handle($order, 'paid');

    expect($updated->status)->toBe('paid');
});

test('an order cannot skip a step', function () {
    $order = Order::factory()->create(['status' => 'pending']);

    expect(fn () => (new UpdateOrderStatusAction)->handle($order, 'shipped'))
        ->toThrow(DomainException::class, 'An order cannot go from pending to shipped.');
});

test('a completed order cannot be changed', function () {
    $order = Order::factory()->create(['status' => 'completed']);

    expect(fn () => (new UpdateOrderStatusAction)->handle($order, 'cancelled'))
        ->toThrow(DomainException::class);
});

test('cancelling an order puts the stock back', function () {
    $product = Product::factory()->create(['stock' => 5]);
    $order = Order::factory()->create(['status' => 'pending']);
    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    (new UpdateOrderStatusAction)->handle($order, 'cancelled');

    expect((int) $product->fresh()->stock)->toBe(7)
        ->and($order->fresh()->status)->toBe('cancelled');
});
