<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
    <header class="border-b border-stone-200 bg-white/90 backdrop-blur dark:border-gray-800 dark:bg-gray-950/85">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <x-app-logo class="h-9 w-9" />
                <div>
                    <div class="text-sm font-semibold tracking-tight">MonPT Shop</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Website ban hang demo</div>
                </div>
            </a>

            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('home') }}" class="rounded-full px-4 py-2 font-medium text-gray-700 hover:bg-stone-100 dark:text-gray-200 dark:hover:bg-gray-900">Shop</a>
                <a href="{{ route('articles.index') }}" class="rounded-full px-4 py-2 font-medium text-gray-700 hover:bg-stone-100 dark:text-gray-200 dark:hover:bg-gray-900">Articles</a>
                @php($cartCount = collect(session('shop.cart', []))->sum('quantity'))
                <a href="{{ route('shop.cart.index') }}" class="rounded-full px-4 py-2 font-medium text-gray-700 hover:bg-stone-100 dark:text-gray-200 dark:hover:bg-gray-900">
                    Cart ({{ $cartCount }})
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-brand-600 px-4 py-2 font-medium text-white hover:bg-brand-700">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-full bg-brand-600 px-4 py-2 font-medium text-white hover:bg-brand-700">Login</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/40 dark:bg-green-950/20 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900/40 dark:bg-red-950/20 dark:text-red-200">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <footer class="border-t border-stone-200 bg-white dark:border-gray-800 dark:bg-gray-950">
        <div class="mx-auto max-w-7xl px-4 py-6 text-sm text-gray-500 sm:px-6 lg:px-8 dark:text-gray-400">
            MonPT Shop - giao dien cong khai cho khach xem san pham.
        </div>
    </footer>
</body>
</html>
