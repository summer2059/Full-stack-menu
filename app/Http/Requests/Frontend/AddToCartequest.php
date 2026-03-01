<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuid'    => 'required|string',
            'menu_id' => 'required|integer|exists:menus,id',
        ];
    }
}
