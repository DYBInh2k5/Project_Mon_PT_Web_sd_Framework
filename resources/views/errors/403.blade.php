@extends('layouts.fullscreen-layout')

@section('content')
    @php
        $currentYear = date('Y');
    @endphp
    <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden p-6 z-1">
        <x-common.common-grid-shape />

        <div class="mx-auto w-full max-w-2xl text-center">
            <span class="toolbar-chip">403 Forbidden</span>
            <h1 class="mt-6 text-5xl font-bold text-gray-900 dark:text-white sm:text-6xl">Ban khong co quyen vao trang nay</h1>
            <p class="mt-4 text-base text-gray-600 dark:text-gray-400 sm:text-lg">
                Middleware da chan truy cap vi tai khoan hien tai khong dung role yeu cau.
            </p>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('dashboard') }}" class="action-button-primary">Ve dashboard</a>
                <a href="{{ route('role-demo.index') }}" class="action-button">Mo role demo</a>
            </div>
        </div>

        <p class="absolute bottom-6 left-1/2 -translate-x-1/2 text-center text-sm text-gray-500 dark:text-gray-400">
            &copy; {{ $currentYear }} - TailAdmin
        </p>
    </div>
@endsection
