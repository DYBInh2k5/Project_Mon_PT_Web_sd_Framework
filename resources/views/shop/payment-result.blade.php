@extends('layouts.shop')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-stone-200 bg-white p-8 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            @if ($order && $isSuccess)
                <div class="inline-flex rounded-full bg-green-100 px-4 py-2 text-sm font-medium text-green-700 dark:bg-green-500/10 dark:text-green-300">
                    Thanh toan thanh cong
                </div>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">Don hang {{ $order->order_number }} da duoc thanh toan</h1>
            @else
                <div class="inline-flex rounded-full bg-red-100 px-4 py-2 text-sm font-medium text-red-700 dark:bg-red-500/10 dark:text-red-300">
                    Thanh toan chua thanh cong
                </div>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">Chua xac nhan duoc giao dich</h1>
            @endif

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl bg-stone-50 p-5 dark:bg-gray-950">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Ma don</div>
                    <div class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $order?->order_number ?? 'N/A' }}</div>
                </div>
                <div class="rounded-2xl bg-stone-50 p-5 dark:bg-gray-950">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Trang thai</div>
                    <div class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $order?->payment_status ?? 'unknown' }}</div>
                </div>
                <div class="rounded-2xl bg-stone-50 p-5 dark:bg-gray-950">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Giao dich</div>
                    <div class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $order?->transaction_code ?? 'Chua co' }}</div>
                </div>
                <div class="rounded-2xl bg-stone-50 p-5 dark:bg-gray-950">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Xac thuc</div>
                    <div class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $verified ? 'Hop le' : 'Chua hop le' }}</div>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('shop.index') }}" class="rounded-2xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white hover:bg-brand-700">
                    Quay ve shop
                </a>
                @auth
                @if ($order)
                    <a href="{{ route('orders.show', $order) }}" class="rounded-2xl border border-stone-200 px-5 py-3 text-sm font-medium text-gray-700 hover:border-brand-300 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200">
                        Xem don trong admin
                    </a>
                @endif
                @endauth
            </div>
        </div>
    </section>
@endsection
