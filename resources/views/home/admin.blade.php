@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="CheckAge Demo">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Dashboard</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">CheckAge</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    <div class="space-y-6">
        <section class="page-toolbar">
            <div class="space-y-3">
                <span class="toolbar-chip">Middleware demo</span>
                <div>
                    <h2 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">Trang test middleware `CheckAge`.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-7 text-gray-600 dark:text-gray-300">
                        Route này dùng để minh hoạ middleware cơ bản theo yêu cầu của cô. Nếu tuổi nhỏ hơn `200` thì vào được trang này.
                    </p>
                </div>
            </div>
        </section>

        <section class="surface-panel p-6">
            <div class="grid gap-4 md:grid-cols-2">
                <a href="{{ url('/check_age/18') }}" class="action-button-primary">
                    Test /check_age/18
                </a>
                <a href="{{ url('/check_age/200') }}" class="action-button-danger">
                    Test /check_age/200
                </a>
            </div>

            @if (request()->route('age') !== null)
                <div class="mt-6 rounded-2xl bg-brand-50 p-4 text-sm text-brand-700 dark:bg-brand-500/10 dark:text-brand-300">
                    Tuoi dang test: <span class="font-semibold">{{ request()->route('age') }}</span>
                </div>
            @endif
        </section>
    </div>
@endsection
