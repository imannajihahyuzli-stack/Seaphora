<?php

namespace App\Actions;

use App\Models\Review;

class DeleteReviewAction
{
    public function handle(Review $review): void
    {
        $review->delete();
    }
}
