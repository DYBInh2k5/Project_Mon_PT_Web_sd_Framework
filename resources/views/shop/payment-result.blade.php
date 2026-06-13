@extends('layouts.shop')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-stone-200 bg-white p-8 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            @if ($order && $isSuccess)
                <div class="inline-flex rounded-full bg-green-100 px-4 py-2 text-sm font-medium text-green-700 dark:bg-green-500/10 dark:text-green-300">
                    Thanh toán VNPay thành công
                </div>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                    Đơn hàng {{ $order->order_number }} đã được VNPay xác nhận
                </h1>
            @else
                <div class="inline-flex rounded-full bg-red-100 px-4 py-2 text-sm font-medium text-red-700 dark:bg-red-500/10 dark:text-red-300">
                    Chưa xác nhận được thanh toán
                </div>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                    Giao dịch VNPay chưa hoàn tất hoặc không hợp lệ
                </h1>
            @endif

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-stone-200 bg-stone-50 p-4 text-sm dark:border-gray-800 dark:bg-gray-950">
                    <div class="text-gray-500 dark:text-gray-400">Cổng thanh toán</div>
                    <div class="mt-1 font-semibold text-gray-950 dark:text-white">{{ $gateway ?? 'VNPay' }}</div>
                </div>
                <div class="rounded-2xl border border-stone-200 bg-stone-50 p-4 text-sm dark:border-gray-800 dark:bg-gray-950">
                    <div class="text-gray-500 dark:text-gray-400">Trạng thái</div>
                    <div class="mt-1 font-semibold text-gray-950 dark:text-white">{{ $isSuccess ? 'Đã thanh toán' : 'Chưa thanh toán' }}</div>
                </div>
            </div>

            @if ($order)
                <div class="mt-6 rounded-2xl border border-stone-200 bg-stone-50 p-5 text-sm dark:border-gray-800 dark:bg-gray-950">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <div class="text-gray-500 dark:text-gray-400">Mã đơn</div>
                            <div class="font-semibold text-gray-950 dark:text-white">{{ $order->order_number }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500 dark:text-gray-400">Mã giao dịch</div>
                            <div class="font-semibold text-gray-950 dark:text-white">{{ $order->transaction_code ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500 dark:text-gray-400">Tổng tiền</div>
                            <div class="font-semibold text-gray-950 dark:text-white">{{ number_format((float) $order->total_amount, 0) }} VND</div>
                        </div>
                        <div>
                            <div class="text-gray-500 dark:text-gray-400">Trạng thái thanh toán</div>
                            <div class="font-semibold text-gray-950 dark:text-white">{{ $order->payment_status }}</div>
                        </div>
                    </div>
                </div>
            @endif

            @if (! empty($message))
                <div class="mt-6 rounded-2xl border border-stone-200 bg-white p-4 text-sm text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                    {{ $message }}
                </div>
            @endif

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('shop.index') }}" class="rounded-2xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white hover:bg-brand-700">
                    Quay lại shop
                </a>
            </div>
        </div>
    </section>
@endsection
