<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Query nay dung de tinh cac thong ke tong hop o dau trang users.
        $baseQuery = User::query();

        // Query chinh de lay danh sach user hien thi trong bang.
        // Du lieu co the duoc tim kiem, loc va phan trang.
        $users = User::query()
            // Tim theo ten hoac email.
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            // Loc theo role.
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->string('role')->toString());
            })
            // Loc theo status Active / Inactive.
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->string('status')->toString() === 'active');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Tra du lieu sang view users.index.
        return view('users.index', [
            'title' => 'Users',
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status']),
            'summary' => [
                'total' => (clone $baseQuery)->count(),
                'admins' => (clone $baseQuery)->where('role', 'admin')->count(),
                'editors' => (clone $baseQuery)->where('role', 'editor')->count(),
                'users' => (clone $baseQuery)->where('role', 'user')->count(),
                'active' => (clone $baseQuery)->where('is_active', true)->count(),
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
        // Validation da duoc xu ly trong StoreUserRequest.
        // Neu hop le thi tao moi user trong database.
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
        // Route model binding:
        // {user} tren URL se tu dong duoc map thanh model User $user.
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
        // Update thong tin user gom:
        // name, email, role, is_active
        $user->update($request->validated());

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function toggleStatus(User $user, Request $request): RedirectResponse
    {
        // Khong cho user tu tat chinh tai khoan dang dang nhap.
        // Muc dich la tranh viec admin bi mat quyen quan tri do tu tay tat account.
        if ($request->user()?->is($user)) {
            return redirect()
                ->route('users.index')
                ->with('success', 'You cannot deactivate the account currently being used.');
        }

        // Dao nguoc status hien tai:
        // true -> false
        // false -> true
        // Route nay duoc goi khi bam vao badge status hoac menu Actions.
        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Request $request): RedirectResponse
    {
        // Khong cho xoa chinh tai khoan dang dang nhap.
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
