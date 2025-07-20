<?php

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

describe('StoreUserRequest', function (): void {
    it('authorizes all users', function (): void {
        $request = new StoreUserRequest();
        
        expect($request->authorize())->toBeTrue();
    });

    it('has correct validation rules', function (): void {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        expect($rules)->toHaveKey('prefixname');
        expect($rules)->toHaveKey('firstname');
        expect($rules)->toHaveKey('lastname');
        expect($rules)->toHaveKey('email');
        expect($rules)->toHaveKey('password');
        expect($rules['firstname'])->toContain('required');
        expect($rules['lastname'])->toContain('required');
        expect($rules['email'])->toContain('required', 'email', 'unique:users');
        expect($rules['password'])->toContain('required', 'min:8', 'confirmed');
    });

    it('validates required fields', function (): void {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, (new StoreUserRequest())->rules());
        
        expect($validator->passes())->toBeTrue();
    });

    it('fails validation without required fields', function (): void {
        $data = [
            'firstname' => 'John',
            // missing lastname, email, password
        ];

        $validator = Validator::make($data, (new StoreUserRequest())->rules());
        
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('lastname'))->toBeTrue();
        expect($validator->errors()->has('email'))->toBeTrue();
        expect($validator->errors()->has('password'))->toBeTrue();
    });

    it('validates email format', function (): void {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, (new StoreUserRequest())->rules());
        
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('email'))->toBeTrue();
    });

    it('validates email uniqueness', function (): void {
        User::factory()->create(['email' => 'john@example.com']);

        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, (new StoreUserRequest())->rules());
        
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('email'))->toBeTrue();
    });

    it('validates password confirmation', function (): void {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ];

        $validator = Validator::make($data, (new StoreUserRequest())->rules());
        
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('password'))->toBeTrue();
    });

    it('validates password minimum length', function (): void {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ];

        $validator = Validator::make($data, (new StoreUserRequest())->rules());
        
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('password'))->toBeTrue();
    });

    it('validates prefixname values', function (): void {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'prefixname' => 'Invalid',
        ];

        $validator = Validator::make($data, (new StoreUserRequest())->rules());
        
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('prefixname'))->toBeTrue();
    });

    it('accepts valid prefixname values', function (): void {
        $validPrefixes = ['Mr', 'Mrs', 'Ms'];

        foreach ($validPrefixes as $prefix) {
            $data = [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'prefixname' => $prefix,
            ];

            $validator = Validator::make($data, (new StoreUserRequest())->rules());
            
            expect($validator->passes())->toBeTrue();
        }
    });

    it('has custom error messages', function (): void {
        $request = new StoreUserRequest();
        $messages = $request->messages();

        expect($messages)->toHaveKey('prefixname.in');
        expect($messages)->toHaveKey('firstname.required');
        expect($messages)->toHaveKey('lastname.required');
        expect($messages)->toHaveKey('email.unique');
        expect($messages)->toHaveKey('password.confirmed');
        expect($messages)->toHaveKey('password.min');
    });
}); 