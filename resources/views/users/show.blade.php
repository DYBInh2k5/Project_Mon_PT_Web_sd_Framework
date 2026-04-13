@extends('layouts.app')

@section('content')
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

            <div class="mt-6 flex items-center gap-3">
                <a href="{{ route('users.edit', $user) }}" class="action-button-primary">Edit User</a>
                <a href="{{ route('users.index') }}" class="action-button">Back</a>
            </div>
        </section>
    </div>
@endsection
