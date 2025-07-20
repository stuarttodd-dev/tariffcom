<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'users' => $users,
            'filters' => request()->only(['search']),
        ]);
    }
}
