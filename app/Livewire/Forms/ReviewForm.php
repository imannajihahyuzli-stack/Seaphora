<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class ReviewForm extends Form
{
    public int|string $rating = 5;

    public ?string $comment = '';

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}