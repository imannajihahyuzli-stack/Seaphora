<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer cannot open the create product page', function () {
    $user = User::factory()->create(['role' => 'customer']);

    $this->actingAs($user)->get('/products/create')->assertForbidden();
});

test('staff can open the create product page', function () {
    $user = User::factory()->create(['role' => 'staff']);

    $this->actingAs($user)->get('/products/create')->assertOk();
});

test('guest is redirected to login', function () {
    $this->get('/products/create')->assertRedirect('/login');
});
