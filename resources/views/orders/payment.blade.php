@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Payment for '.$order->order_number">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Orders</a>
            </li>
            <li>
                <a href="{{ route('orders.show', $order) }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">{{ $order->order_number }}</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">Payment</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    <div class="space-y-6">
        @if ($errors->any())
            <x-package-alert
                type="danger"
                message="Không thể xu ly thanh toán."
                :messages="$errors->all()"
            />
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <section class="surface-panel p-6 xl:col-span-2">
                <div>
                    <span class="toolbar-chip">Online payment demo</span>
                    <h2 class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">Simulate online checkout for {{ $order->order_number }}</h2>
                    <p class="mt-2 text-sm leading-7 text-gray-600 dark:text-gray-300">
                        Màn này mô phỏng quy trình thanh toán trực tuyến để demo luồng xử lý đơn hàng. Sau khi submit thành công, hệ thống sẽ cập nhật trạng thái thanh toán và sinh mã giao dịch.
                    </p>
                </div>

                <form method="POST" action="{{ route('orders.payment.store', $order) }}" class="mt-6 space-y-5" novalidate>
                    @csrf

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="w-full">
                            <label for="payment_method" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Payment method</label>
                            <select id="payment_method" name="payment_method" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                @foreach ($paymentMethods as $value => $label)
                                    <option value="{{ $value }}" @selected(old('payment_method') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <x-forms.input
                            name="customer_email"
                            label="Receipt email"
                            type="email"
                            :value="old('customer_email', $order->customer_email)"
                        />

                        <x-forms.input
                            name="cardholder_name"
                            label="Cardholder name"
                            type="text"
                            :value="old('cardholder_name', $order->customer_name)"
                        />

                        <x-forms.input
                            name="card_last_four"
                            label="Card last four digits"
                            type="text"
                            :value="old('card_last_four')"
                            placeholder="1234"
                        />
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="action-button-primary">Pay now</button>
                        <a href="{{ route('orders.show', $order) }}" class="action-button">Back to order</a>
                    </div>
                </form>
            </section>

            <aside class="space-y-6">
                <section class="surface-panel p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment summary</h3>
                    <div class="mt-5 space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Order number</p>
                            <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Customer</p>
                            <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Current payment status</p>
                            <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' : 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Amount</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">${{ number_format((float) $order->total_amount, 2) }}</p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
@endsection
