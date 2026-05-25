@extends('layouts.app')

@section('content')
    {{-- Trang nay dung de xem chi tiet 1 user.
         Day la noi de giai thich voi co:
         - role cua user
         - status active / inactive
         - email da verify hay chua
         - thoi gian tao va cap nhat --}}
    <x-common.page-breadcrumb pageTitle="User Detail">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('users.index') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Users</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">{{ $user->name }}</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    <div class="space-y-6">
        <section class="page-toolbar">
            <div class="space-y-3">
                <span class="toolbar-chip">User profile</span>
                <div>
                    <h2 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $user->name }}</h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <span class="data-badge {{ $user->role === 'admin' ? 'data-badge-brand' : ($user->role === 'editor' ? 'data-badge-success' : 'data-badge-neutral') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                    <span class="data-badge {{ $user->is_active ? 'data-badge-success' : 'data-badge-neutral' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="metric-pill">{{ $user->email_verified_at ? 'Verified email' : 'Email pending' }}</span>
                </div>
            </div>
        </section>

        <section class="surface-panel p-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Created at</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->created_at?->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Updated at</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->updated_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="mt-8 border-t border-gray-200 pt-6 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Profile information</h3>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Full name</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->profile?->full_name ?? 'Chua cap nhat' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Phone</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->profile?->phone ?? 'Chua cap nhat' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Birthday</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->profile?->birthday ?? 'Chua cap nhat' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Gender</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->profile?->gender ?? 'Chua cap nhat' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Address</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->profile?->address ?? 'Chua cap nhat' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <a href="{{ route('users.edit', $user) }}" class="action-button-primary">Edit User</a>
                <a href="{{ route('users.index') }}" class="action-button">Back</a>
            </div>
        </section>
    </div>
@endsection
