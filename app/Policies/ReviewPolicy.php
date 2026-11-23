<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** A customer may review a product they bought (completed order), once only */
    public function create(User $user, Product $product): bool
    {
        if ($user->role !== 'customer') {
            return false;
        }

        $hasBought = $user->orders()
            ->where('status', 'completed')
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->exists();

        $alreadyReviewed = $user->reviews()
            ->where('product_id', $product->id)
            ->exists();

        return $hasBought && ! $alreadyReviewed;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->isAdmin() || $review->user_id === $user->id;
    }
}