<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PATCH') || $this->isMethod('PUT');

        return [
            'title'            => 'required|string|max:255',
            'menu_category_id' => 'required|exists:menu_categories,id',
            'description'      => 'nullable|string',
            'image'            => $isUpdate
                                    ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
                                    : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price'            => 'required|numeric|min:0',
            'rating'           => 'required|numeric|min:1|max:5',
            'priority'         => 'required|integer',
            'status'           => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'            => 'Title is required.',
            'menu_category_id.required' => 'Please select a category.',
            'menu_category_id.exists'   => 'Selected category does not exist.',
            'image.required'            => 'Please upload an image.',
            'image.image'               => 'The file must be an image.',
            'price.required'            => 'Price is required.',
            'price.numeric'             => 'Price must be a number.',
            'rating.required'           => 'Rating is required.',
            'rating.min'                => 'Rating must be at least 1.',
            'rating.max'                => 'Rating cannot exceed 5.',
            'priority.required'         => 'Priority is required.',
            'priority.integer'          => 'Priority must be a number.',
            'status.in'                 => 'Status must be Active or Inactive.',
        ];
    }
}