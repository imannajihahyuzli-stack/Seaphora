<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class CheckoutForm extends Form
{
    public string $shipping_address = '';

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'shipping_address' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }
}