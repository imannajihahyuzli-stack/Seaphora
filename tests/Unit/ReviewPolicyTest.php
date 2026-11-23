<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function buyProduct(User $user, Product $product, string $status): void
{
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => $status]);

    OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);
}

test('a customer who bought the product can review it', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create();
    buyProduct($user, $product, 'completed');

    expect((new ReviewPolicy)->create($user, $product))->toBeTrue();
});

test('a customer who never bought the product cannot review it', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create();

    expect((new ReviewPolicy)->create($user, $product))->toBeFalse();
});

test('an order that is not completed does not allow a review', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create();
    buyProduct($user, $product, 'pending');

    expect((new ReviewPolicy)->create($user, $product))->toBeFalse();
});

test('a customer cannot review the same product twice', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create();
    buyProduct($user, $product, 'completed');
    Review::factory()->create(['user_id' => $user->id, 'product_id' => $product->id]);

    expect((new ReviewPolicy)->create($user, $product))->toBeFalse();
});

test('staff cannot write reviews', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $product = Product::factory()->create();

    expect((new ReviewPolicy)->create($staff, $product))->toBeFalse();
});

test('only the author or an admin can delete a review', function () {
    $author = User::factory()->create(['role' => 'customer']);
    $other = User::factory()->create(['role' => 'customer']);
    $admin = User::factory()->create(['role' => 'admin']);
    $review = Review::factory()->create(['user_id' => $author->id]);
    $policy = new ReviewPolicy;

    expect($policy->delete($author, $review))->toBeTrue()
        ->and($policy->delete($admin, $review))->toBeTrue()
        ->and($policy->delete($other, $review))->toBeFalse();
});
