<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var string $search */
        $search = $request->input('search');

        $users = User::query()
            ->search($search)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Users/Index', [
            'users' => UserResource::collection($users),
            'filters' => request()->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create');
    }

    public function show(User $user): Response
    {
        $user->load('details');

        return Inertia::render('Users/Show', [
            'user' => new UserResource($user),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['type'] ??= 'user';
        $user = User::create($validated);
        return redirect()->route('users.show', $user)->with('success', 'User created successfully.');
    }

    public function edit(User $user): Response
    {
        $user->load('details');

        return Inertia::render('Users/Edit', [
            'user' => new UserResource($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if (array_key_exists('password', $validated) && empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);
        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function trashed(Request $request): Response
    {
        /** @var string $search */
        $search = $request->input('search');

        $users = User::onlyTrashed()
            ->search($search)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Users/Trashed', [
            'users' => UserResource::collection($users),
            'filters' => request()->only(['search']),
        ]);
    }

    public function restore(int $userId): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        $user->restore();

        return redirect()->route('users.trashed')
            ->with('success', 'User restored successfully.');
    }

    public function delete(int $userId): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        $user->forceDelete();

        return redirect()->route('users.trashed')
            ->with('success', 'User permanently deleted.');
    }
}
