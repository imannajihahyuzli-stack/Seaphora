<?php

use App\Models\Order;
use App\Models\User;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('only customers can place orders', function () {
    $policy = new OrderPolicy;

    expect($policy->create(User::factory()->make(['role' => 'customer'])))->toBeTrue()
        ->and($policy->create(User::factory()->make(['role' => 'staff'])))->toBeFalse()
        ->and($policy->create(User::factory()->make(['role' => 'admin'])))->toBeFalse();
});

test('a customer can only view their own order', function () {
    $owner = User::factory()->create(['role' => 'customer']);
    $other = User::factory()->create(['role' => 'customer']);
    $order = Order::factory()->make(['user_id' => $owner->id]);
    $policy = new OrderPolicy;

    expect($policy->view($owner, $order))->toBeTrue()
        ->and($policy->view($other, $order))->toBeFalse();
});

test('staff and admin can view and update any order', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    $order = Order::factory()->make(['user_id' => $customer->id]);
    $policy = new OrderPolicy;

    foreach (['staff', 'admin'] as $role) {
        $user = User::factory()->create(['role' => $role]);

        expect($policy->view($user, $order))->toBeTrue()
            ->and($policy->update($user, $order))->toBeTrue();
    }
});

test('a customer cannot update an order status', function () {
    $owner = User::factory()->create(['role' => 'customer']);
    $order = Order::factory()->make(['user_id' => $owner->id]);

    expect((new OrderPolicy)->update($owner, $order))->toBeFalse();
});

test('a customer can cancel their own pending order only', function () {
    $owner = User::factory()->create(['role' => 'customer']);
    $other = User::factory()->create(['role' => 'customer']);
    $policy = new OrderPolicy;

    $pending = Order::factory()->make(['user_id' => $owner->id, 'status' => 'pending']);
    $paid = Order::factory()->make(['user_id' => $owner->id, 'status' => 'paid']);

    expect($policy->cancel($owner, $pending))->toBeTrue()
        ->and($policy->cancel($other, $pending))->toBeFalse()
        ->and($policy->cancel($owner, $paid))->toBeFalse();
});

test('only an admin can delete an order', function () {
    $order = Order::factory()->make();
    $policy = new OrderPolicy;

    expect($policy->delete(User::factory()->make(['role' => 'admin']), $order))->toBeTrue()
        ->and($policy->delete(User::factory()->make(['role' => 'staff']), $order))->toBeFalse();
});