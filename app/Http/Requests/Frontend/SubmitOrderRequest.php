<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class SubmitOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'uuid'       => 'required|string',
            'user_name'  => 'required|string|max:255',
            'table'      => 'required|integer|min:1',
            'user_phone' => 'nullable|string',
            'note'       => 'nullable|string',
        ];
    }
}
