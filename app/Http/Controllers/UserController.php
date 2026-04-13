<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $baseQuery = User::query();

        $users = User::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->string('role')->toString());
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.index', [
            'title' => 'Users',
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
            'summary' => [
                'total' => (clone $baseQuery)->count(),
                'admins' => (clone $baseQuery)->where('role', 'admin')->count(),
                'editors' => (clone $baseQuery)->where('role', 'editor')->count(),
                'verified' => (clone $baseQuery)->whereNotNull('email_verified_at')->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a newly created resource.
     */
    public function create(): View
    {
        return view('users.create', [
            'title' => 'Create User',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        return view('users.show', [
            'title' => 'User Detail',
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        return view('users.edit', [
            'title' => 'Edit User',
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Request $request): RedirectResponse
    {
        if ($request->user()?->is($user)) {
            return redirect()
                ->route('users.index')
                ->with('success', 'You cannot delete the account currently being used.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
