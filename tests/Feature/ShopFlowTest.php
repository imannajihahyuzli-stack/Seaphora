<?php

use App\Actions\AddToCartAction;
use App\Livewire\Orders\Checkout;
use App\Livewire\Orders\Index as OrdersIndex;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\Reviews\Index as ReviewsIndex;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ---------- Cart ----------

test('a customer can open the cart page', function () {
    $user = User::factory()->create(['role' => 'customer']);

    $this->actingAs($user)->get('/cart')->assertOk();
});

test('staff cannot open the cart page', function () {
    $user = User::factory()->create(['role' => 'staff']);

    $this->actingAs($user)->get('/cart')->assertForbidden();
});

test('a customer can add a product to the cart', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['stock' => 5, 'is_active' => true]);

    $this->actingAs($user);

    Livewire::test(ProductsIndex::class)->call('addToCart', $product->id);

    $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 1]);
});

test('staff cannot add a product to the cart', function () {
    $user = User::factory()->create(['role' => 'staff']);
    $product = Product::factory()->create(['stock' => 5, 'is_active' => true]);

    $this->actingAs($user);

    Livewire::test(ProductsIndex::class)
        ->call('addToCart', $product->id)
        ->assertForbidden();
});

// ---------- Checkout ----------

test('checkout rejects a short address', function () {
    $user = User::factory()->create(['role' => 'customer']);

    $this->actingAs($user);

    Livewire::test(Checkout::class)
        ->set('form.shipping_address', 'abc')
        ->call('placeOrder')
        ->assertHasErrors(['form.shipping_address']);

    $this->assertDatabaseCount('orders', 0);
});

test('checkout places an order and goes to the orders page', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['price' => 10, 'stock' => 5, 'is_active' => true]);
    (new AddToCartAction)->handle($user, $product, 2);

    $this->actingAs($user);

    Livewire::test(Checkout::class)
        ->set('form.shipping_address', '12 Jalan Bunga, Rawang')
        ->call('placeOrder')
        ->assertRedirect(route('orders.index'));

    $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'status' => 'pending']);
});

// ---------- Orders ----------

test('a customer only sees their own orders', function () {
    $me = User::factory()->create(['role' => 'customer']);
    $someoneElse = User::factory()->create(['role' => 'customer']);
    $mine = Order::factory()->create(['user_id' => $me->id]);
    Order::factory()->create(['user_id' => $someoneElse->id]);

    $this->actingAs($me);

    Livewire::test(OrdersIndex::class)
        ->assertViewHas('orders', fn ($orders) => $orders->count() === 1 && $orders->first()->id === $mine->id);
});

test('staff see every order', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    Order::factory()->count(3)->create();

    $this->actingAs($staff);

    Livewire::test(OrdersIndex::class)
        ->assertViewHas('orders', fn ($orders) => $orders->count() === 3);
});

test('staff can move an order to the next status', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $order = Order::factory()->create(['status' => 'pending']);

    $this->actingAs($staff);

    Livewire::test(OrdersIndex::class)->call('advance', $order->id, 'paid');

    expect($order->fresh()->status)->toBe('paid');
});

test('a customer cannot change an order status', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    $order = Order::factory()->create(['user_id' => $customer->id, 'status' => 'pending']);

    $this->actingAs($customer);

    Livewire::test(OrdersIndex::class)
        ->call('advance', $order->id, 'paid')
        ->assertForbidden();

    expect($order->fresh()->status)->toBe('pending');
});

test('a customer can cancel their own pending order', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    $order = Order::factory()->create(['user_id' => $customer->id, 'status' => 'pending']);

    $this->actingAs($customer);

    Livewire::test(OrdersIndex::class)->call('cancel', $order->id);

    expect($order->fresh()->status)->toBe('cancelled');
});

test('a customer cannot cancel an order that is already paid', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    $order = Order::factory()->create(['user_id' => $customer->id, 'status' => 'paid']);

    $this->actingAs($customer);

    Livewire::test(OrdersIndex::class)
        ->call('cancel', $order->id)
        ->assertForbidden();

    expect($order->fresh()->status)->toBe('paid');
});

test('a customer cannot cancel someone elses order', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    $order = Order::factory()->create(['status' => 'pending']);

    $this->actingAs($customer);

    Livewire::test(OrdersIndex::class)
        ->call('cancel', $order->id)
        ->assertForbidden();
});

// ---------- Reviews ----------

test('a customer with a completed order can write a review', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['is_active' => true]);
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'completed']);
    OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);

    $this->actingAs($user);

    Livewire::test(ReviewsIndex::class, ['product' => $product])
        ->set('form.rating', 4)
        ->set('form.comment', 'Lovely product')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'rating' => 4,
    ]);
});

test('a customer who did not buy the product cannot review it', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['is_active' => true]);

    $this->actingAs($user);

    Livewire::test(ReviewsIndex::class, ['product' => $product])
        ->set('form.rating', 5)
        ->call('save')
        ->assertForbidden();

    $this->assertDatabaseCount('reviews', 0);
});

test('a review rating must be between 1 and 5', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['is_active' => true]);
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'completed']);
    OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);

    $this->actingAs($user);

    Livewire::test(ReviewsIndex::class, ['product' => $product])
        ->set('form.rating', 9)
        ->call('save')
        ->assertHasErrors(['form.rating']);
});

test('script text in a review is shown as harmless text', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = Product::factory()->create(['is_active' => true]);
    Review::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'comment' => '<script>alert(1)</script>',
    ]);

    $this->actingAs($user);

    Livewire::test(ReviewsIndex::class, ['product' => $product])
        ->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false);
});

test('an admin can delete any review', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create(['is_active' => true]);
    $review = Review::factory()->create(['product_id' => $product->id]);

    $this->actingAs($admin);

    Livewire::test(ReviewsIndex::class, ['product' => $product])->call('delete', $review->id);

    $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
});
