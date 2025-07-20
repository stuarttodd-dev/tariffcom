<?php

declare(strict_types=1);

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('UpdateUserRequest passes with valid data', function (): void {
    $user = User::factory()->create(['email' => 'existing@example.com']);
    $data = [
        'prefixname' => 'Mr',
        'firstname' => 'Jane',
        'middlename' => 'B',
        'lastname' => 'Smith',
        'suffixname' => 'Sr',
        'email' => 'existing@example.com', // same as user, should pass
        'photo' => null,
        'type' => 'user',
    ];
    $request = new UpdateUserRequest();
    $mockRoute = new class($user) {
        public function __construct(public $user) {}

        public function parameter($key) { return $key === 'user' ? $this->user->id : null; }
    };
    $request->setRouteResolver(fn (): object => $mockRoute);
    $validator = Validator::make($data, $request->rules());
    if ($validator->fails()) {
        dump($validator->errors()->all());
    }

    expect($validator->passes())->toBeTrue();
});

test('UpdateUserRequest fails with duplicate email', function (): void {
    $user = User::factory()->create(['email' => 'existing@example.com']);
    $other = User::factory()->create(['email' => 'other@example.com']);
    $data = [
        'firstname' => 'Jane',
        'lastname' => 'Smith',
        'email' => 'other@example.com', // duplicate
    ];
    $request = new UpdateUserRequest();
    $mockRoute = new class($user) {
        public function __construct(public $user) {}

        public function parameter($key) { return $key === 'user' ? $this->user->id : null; }
    };
    $request->setRouteResolver(fn (): object => $mockRoute);

    $validator = Validator::make($data, $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('UpdateUserRequest passes when user updates with their own email', function (): void {
    $user = User::factory()->create(['email' => 'existing@example.com']);
    $data = [
        'firstname' => 'Jane',
        'lastname' => 'Smith',
        'email' => 'existing@example.com', // same as current user, should pass
    ];
    $request = new UpdateUserRequest();
    $mockRoute = new class($user) {
        public function __construct(public $user) {}

        public function parameter($key) { return $key === 'user' ? $this->user->id : null; }
    };
    $request->setRouteResolver(fn (): object => $mockRoute);

    $validator = Validator::make($data, $request->rules());
    expect($validator->passes())->toBeTrue();
});

test('UpdateUserRequest fails with invalid prefixname', function (): void {
    $user = User::factory()->create();
    $data = [
        'prefixname' => 'Dr', // invalid
        'firstname' => 'Jane',
        'lastname' => 'Smith',
        'email' => 'jane@example.com',
    ];
    $request = new UpdateUserRequest();
    $mockRoute = new class($user) {
        public function __construct(public $user) {}

        public function parameter($key) { return $key === 'user' ? $this->user->id : null; }
    };
    $request->setRouteResolver(fn (): object => $mockRoute);
    $validator = Validator::make($data, $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('prefixname'))->toBeTrue();
}); 