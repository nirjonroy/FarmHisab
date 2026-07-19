<?php

namespace App\Http\Requests\Admin;

use App\Support\AccessControl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.update') ?? false;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(AccessControl::ROLES), Rule::exists('roles', 'name')->where('guard_name', 'web')],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('users.name'),
            'email' => __('users.email'),
            'password' => __('users.password'),
            'role' => __('users.role'),
        ];
    }
}
