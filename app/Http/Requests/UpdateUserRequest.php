<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'prefixname' => ['nullable', Rule::in(['Mr', 'Mrs', 'Ms'])],
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'suffixname' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'photo' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'max:255'],
        ];

        $user = $this->user();
        if ($user && $this->input('email') !== $user->email) {
            $rules['email'][] = Rule::unique('users', 'email');
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'prefixname.in' => 'The selected prefix is invalid.',
            'firstname.required' => 'First name is required.',
            'lastname.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'This email is already taken.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least :min characters.',
        ];
    }
}
