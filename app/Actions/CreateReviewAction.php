<?php

namespace App\Actions;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use DomainException;

class CreateReviewAction
{
    public function handle(User $user, Product $product, int $rating, ?string $comment): Review
    {
        if ($user->reviews()->where('product_id', $product->id)->exists()) {
            throw new DomainException('You have already reviewed this product.');
        }

        return Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => $rating,
            'comment' => $comment,
        ]);
    }
}
