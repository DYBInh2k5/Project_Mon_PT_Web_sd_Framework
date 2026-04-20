@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Role Demo">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Dashboard</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">Role Demo</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    <div class="space-y-6">
        <section class="page-toolbar">
            <div class="space-y-4">
                <span class="toolbar-chip">Role access demo</span>
                <div>
                    <h2 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">Demo middleware role theo tung khu vuc.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-7 text-gray-600 dark:text-gray-300">
                        Dung trang nay de mo nhanh cac route chi cho phep <code>admin</code>, <code>editor</code>, hoac <code>user</code>.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <span class="metric-pill">Current user: {{ $user->name }}</span>
                    <span class="data-badge {{ $user->role === 'admin' ? 'data-badge-brand' : ($user->role === 'editor' ? 'data-badge-success' : 'data-badge-neutral') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-3">
            <a href="{{ route('role-demo.admin') }}" class="surface-panel block p-6 transition hover:-translate-y-1 hover:shadow-theme-lg">
                <span class="data-badge data-badge-brand">Admin only</span>
                <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">Admin Area</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Chi user role `admin` vao duoc route nay.</p>
            </a>

            <a href="{{ route('role-demo.editor') }}" class="surface-panel block p-6 transition hover:-translate-y-1 hover:shadow-theme-lg">
                <span class="data-badge data-badge-success">Editor or Admin</span>
                <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">Editor Area</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Cho phep `editor` va `admin` truy cap.</p>
            </a>

            <a href="{{ route('role-demo.user') }}" class="surface-panel block p-6 transition hover:-translate-y-1 hover:shadow-theme-lg">
                <span class="data-badge data-badge-neutral">All logged-in roles</span>
                <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">User Area</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Cho phep `user`, `editor`, va `admin`.</p>
            </a>
        </div>
    </div>
@endsection
