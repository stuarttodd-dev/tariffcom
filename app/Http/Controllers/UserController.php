<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'users' => $users,
            'filters' => request()->only(['search']),
        ]);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
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
            'users' => $users,
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
