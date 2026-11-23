<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): bool
    {
        return $user->isStaff() || $order->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'customer';
    }

    public function update(User $user, Order $order): bool
    {
        return $user->isStaff();
    }

    public function cancel(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && $order->status === 'pending';
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}