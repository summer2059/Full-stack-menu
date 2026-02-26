<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'unit'          => 'required|string',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'status'        => 'nullable|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Item name is required.',
            'unit.required'          => 'Unit is required.',
            'current_stock.required' => 'Opening stock is required.',
            'current_stock.numeric'  => 'Stock must be a number.',
            'current_stock.min'      => 'Stock cannot be negative.',
        ];
    }
}