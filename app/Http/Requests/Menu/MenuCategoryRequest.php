<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;

class MenuCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PATCH') || $this->isMethod('PUT');

        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => $isUpdate
                                ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
                                : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'priority'    => 'required|integer',
            'status'      => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Title is required.',
            'image.required'    => 'Image is required.',
            'image.image'       => 'The file must be an image.',
            'priority.required' => 'Priority is required.',
            'priority.integer'  => 'Priority must be a number.',
            'status.required'   => 'Status is required.',
            'status.in'         => 'Status must be Active or Inactive.',
        ];
    }
}