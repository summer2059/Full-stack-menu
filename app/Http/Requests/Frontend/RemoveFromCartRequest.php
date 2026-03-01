<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class RemoveFromCartRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cart_id' => 'required|integer|exists:carts,id',
        ];
    }
}
