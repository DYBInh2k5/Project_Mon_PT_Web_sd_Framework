@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Users" />

    @php($modernUsers = true)

    @if ($modernUsers)
        <div class="space-y-6">
            @session('success')
                <x-ui.alert variant="success">
                    {{ $value }}
                </x-ui.alert>
            @endsession

            <section class="page-toolbar">
                <div class="space-y-4">
                    <span class="toolbar-chip">Role management</span>
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <h2 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">Users and roles in one clean workspace.</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-7 text-gray-600 dark:text-gray-300">
                                Theo dõi tài khoản, vai trò và trạng thái xác thực với bố cục gọn hơn, dễ sửa quyền nhanh hơn.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <span class="metric-pill">{{ $summary['total'] }} total users</span>
                            <span class="metric-pill">{{ $summary['admins'] }} admins</span>
                            <span class="metric-pill">{{ $summary['editors'] }} editors</span>
                            <span class="metric-pill">{{ $summary['users'] }} users</span>
                            <span class="metric-pill">{{ $summary['active'] }} active</span>
                            <span class="metric-pill">{{ $summary['verified'] }} verified</span>
                        </div>
                    </div>
                </div>
            </section>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('users.create') }}" class="action-button-primary">Create User</a>
                <a href="{{ route('role-demo.index') }}" class="action-button">Open Role Demo</a>
            </div>

            <section
                x-data="{ expanded: {{ (! empty($filters['search']) || ! empty($filters['role'])) ? 'true' : 'false' }} }"
                class="surface-panel p-5"
            >
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filter users</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tìm theo tên, email hoặc lọc theo role.</p>
                    </div>

                    <button @click="expanded = !expanded" type="button" class="action-button">
                        <span x-text="expanded ? 'Hide filters' : 'Show filters'"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition" :class="{ 'rotate-180': expanded }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <form x-show="expanded" x-cloak method="GET" action="{{ route('users.index') }}" class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-4">
                    <div>
                        <x-forms.input
                            name="search"
                            label="Search"
                            type="text"
                            :value="$filters['search'] ?? ''"
                        />
                    </div>

                    <div class="w-full px-2.5">
                        <label for="role" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Role</label>
                        <select id="role" name="role" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="">All roles</option>
                            <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>Admin</option>
                            <option value="editor" @selected(($filters['role'] ?? '') === 'editor')>Editor</option>
                            <option value="user" @selected(($filters['role'] ?? '') === 'user')>User</option>
                        </select>
                    </div>

                    <div class="w-full px-2.5">
                        <label for="status" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                        <select id="status" name="status" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="">All statuses</option>
                            <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                            <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-3 px-2.5">
                        <button type="submit" class="action-button-primary w-full sm:w-auto">Apply</button>
                        <a href="{{ route('users.index') }}" class="action-button w-full sm:w-auto">Reset</a>
                    </div>
                </form>
            </section>

            <section class="table-shell">
                <div class="border-b border-gray-200/80 px-5 py-4 dark:border-gray-800 sm:px-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User list</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $users->total() }} results in current view.</p>
                        </div>
                        <div class="toolbar-chip">Admin access only</div>
                    </div>
                </div>

                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1160px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">User</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Role</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Status</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Verified</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Created</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Updated</th>
                                <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="table-row-hover border-b border-gray-100 last:border-b-0 dark:border-gray-800">
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-sm font-semibold text-brand-700 dark:bg-brand-500/15 dark:text-brand-400">
                                                {{ $user->initials() }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="data-badge {{ $user->role === 'admin' ? 'data-badge-brand' : ($user->role === 'editor' ? 'data-badge-success' : 'data-badge-neutral') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="data-badge {{ $user->is_active ? 'data-badge-success' : 'data-badge-neutral' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        @if ($user->email_verified_at)
                                            <span class="data-badge data-badge-success">Verified</span>
                                        @else
                                            <span class="data-badge data-badge-neutral">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-sm text-gray-500 dark:text-gray-400">{{ $user->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-5 py-4 sm:px-6 text-sm text-gray-500 dark:text-gray-400">{{ $user->updated_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-5 py-4 sm:px-6 text-right">
                                        <div class="relative inline-block text-left" x-data="{ open: false }">
                                            <button @click="open = !open" type="button" class="action-button">
                                                Actions
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m19 9-7 7-7-7" />
                                                </svg>
                                            </button>

                                            <div
                                                x-show="open"
                                                x-cloak
                                                @click.away="open = false"
                                                class="absolute right-0 z-20 mt-2 w-44 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900"
                                            >
                                                <a href="{{ route('users.show', $user) }}" class="block rounded-xl px-3 py-2 text-left text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.03]">View detail</a>
                                                <a href="{{ route('users.edit', $user) }}" class="block rounded-xl px-3 py-2 text-left text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.03]">Edit role</a>
                                                @if (! auth()->user()?->is($user))
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="block w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-error-700 transition hover:bg-error-50 dark:text-error-400 dark:hover:bg-error-500/10">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center sm:px-6">
                                        <div class="empty-state">
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">No users found.</p>
                                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Thử đổi bộ lọc để tìm đúng tài khoản bạn cần.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            @if ($users->hasPages())
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="space-y-6">
            @session('success')
                <x-ui.alert variant="success">
                    {{ $value }}
                </x-ui.alert>
            @endsession

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1102px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left sm:px-6">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Name</p>
                                </th>
                                <th class="px-5 py-3 text-left sm:px-6">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Email</p>
                                </th>
                                <th class="px-5 py-3 text-left sm:px-6">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Role</p>
                                </th>
                                <th class="px-5 py-3 text-left sm:px-6">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Created At</p>
                                </th>
                                <th class="px-5 py-3 text-left sm:px-6">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Updated At</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4 sm:px-6">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $user->name }}</p>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $user->email }}</p>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $user->role === 'admin' ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-400' : ($user->role === 'editor' ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $user->is_active ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $user->created_at }}</p>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $user->updated_at }}</p>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('users.edit', $user) }}"
                                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-8 text-center">
                                        <p class="text-gray-500 dark:text-gray-400">No users found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($users->hasPages())
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    @endif
@endsection
