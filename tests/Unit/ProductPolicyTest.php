<?php

use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('customer can view but not manage products', function () {
    $customer = User::factory()->make(['role' => 'customer']);
    $product = Product::factory()->make();
    $policy = new ProductPolicy;

    expect($policy->viewAny($customer))->toBeTrue()
        ->and($policy->create($customer))->toBeFalse()
        ->and($policy->update($customer, $product))->toBeFalse()
        ->and($policy->delete($customer, $product))->toBeFalse();
});

test('staff and admin can manage products', function () {
    $product = Product::factory()->make();
    $policy = new ProductPolicy;

    foreach (['staff', 'admin'] as $role) {
        $user = User::factory()->make(['role' => $role]);

        expect($policy->create($user))->toBeTrue()
            ->and($policy->update($user, $product))->toBeTrue()
            ->and($policy->delete($user, $product))->toBeTrue();
    }
});
