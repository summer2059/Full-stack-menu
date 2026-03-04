<?php

namespace App\Http\Requests\Dashboard\User;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email,' . $userId,
            'password'    => ($userId ? 'nullable' : 'required') . '|string|min:6|confirmed',
            'role'        => 'required|exists:roles,name',
            'permissions' => 'array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Full name is required.',
            'email.required'    => 'Email address is required.',
            'email.unique'      => 'This email is already taken.',
            'password.required' => 'Password is required.',
            'password.min'      => 'Password must be at least 6 characters.',
            'password.confirmed'=> 'Password confirmation does not match.',
            'role.required'     => 'Please select a role.',
            'role.exists'       => 'The selected role is invalid.',
        ];
    }
}