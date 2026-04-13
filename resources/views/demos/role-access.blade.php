@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="$title">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Dashboard</a>
            </li>
            <li>
                <a href="{{ route('role-demo.index') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Role Demo</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">{{ $title }}</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    <div class="space-y-6">
        <section class="page-toolbar">
            <div class="space-y-4">
                <span class="toolbar-chip">Authorized access</span>
                <div>
                    <h2 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $title }}</h2>
                    <p class="mt-2 text-sm leading-7 text-gray-600 dark:text-gray-300">
                        Ban dang vao dung route duoc bao ve boi middleware <code>role</code>.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <span class="metric-pill">Current user: {{ $user->name }}</span>
                    <span class="metric-pill">Current role: {{ $user->role }}</span>
                    <span class="metric-pill">Required role: {{ $requiredRole }}</span>
                </div>
            </div>
        </section>

        <section class="surface-panel p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ket qua demo</h3>
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                Neu tai khoan khong dung role, middleware se chan va chuyen sang trang loi <code>403</code>.
                Neu vao duoc den day thi co nghia la middleware da hoat dong dung.
            </p>
        </section>
    </div>
@endsection
