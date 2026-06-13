@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="$order->order_number">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Orders</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">{{ $order->order_number }}</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    <div class="space-y-6">
        @session('success')
            <x-ui.alert variant="success">
                {{ $value }}
            </x-ui.alert>
        @endsession

        @if ($errors->any())
            <x-package-alert
                type="danger"
                message="Không thể xu ly yeu cau tren đơn hàng."
                :messages="$errors->all()"
            />
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <section class="surface-panel p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <span class="toolbar-chip">Order detail</span>
                            <h2 class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</h2>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Đặt lúc {{ $order->placed_at?->format('d/m/Y H:i') }}.</p>
                        </div>
                        <span class="data-badge {{ match($order->status) {
                            'completed' => 'data-badge-success',
                            'processing' => 'data-badge-brand',
                            'shipped' => 'data-badge-warning',
                            'cancelled' => 'data-badge-neutral',
                            default => 'data-badge-warning',
                        } }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total amount</p>
                            <p class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">${{ number_format((float) $order->total_amount, 2) }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Items count</p>
                            <p class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">{{ $order->items->count() }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Customer phone</p>
                            <p class="mt-2 text-base font-semibold text-gray-900 dark:text-white">{{ $order->customer_phone }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Payment status</p>
                            <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' : 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Payment method</p>
                            <p class="mt-2 text-base font-semibold text-gray-900 dark:text-white">{{ $order->payment_method ? str($order->payment_method)->replace('_', ' ')->title() : 'Not paid yet' }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Transaction code</p>
                            <p class="mt-2 text-base font-semibold text-gray-900 dark:text-white">{{ $order->transaction_code ?: 'Pending' }}</p>
                        </div>
                    </div>
                </section>

                <section class="table-shell">
                    <div class="border-b border-gray-200/80 px-5 py-4 dark:border-gray-800 sm:px-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order items</h3>
                    </div>

                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[820px]">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Product</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Unit price</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Quantity</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Line total</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr class="table-row-hover border-b border-gray-100 last:border-b-0 dark:border-gray-800">
                                        <td class="px-5 py-4 sm:px-6">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->product_name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->product?->sku ?? 'Archived product' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6 text-sm text-gray-500 dark:text-gray-400">${{ number_format((float) $item->unit_price, 2) }}</td>
                                        <td class="px-5 py-4 sm:px-6 text-sm text-gray-500 dark:text-gray-400">{{ $item->quantity }}</td>
                                        <td class="px-5 py-4 sm:px-6 text-sm font-semibold text-gray-900 dark:text-white">${{ number_format((float) $item->line_total, 2) }}</td>
                                        <td class="px-5 py-4 text-right sm:px-6">
                                            @if ($item->product)
                                                <a href="{{ route('products.show', $item->product) }}" class="action-button">View product</a>
                                            @else
                                                <span class="text-sm text-gray-400">Unavailable</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="surface-panel p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Customer information</h3>
                    <div class="mt-5 space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Full name</p>
                            <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                            <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $order->customer_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                            <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $order->customer_phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                            <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $order->customer_address }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
                            <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $order->notes ?: 'No notes' }}</p>
                        </div>
                    </div>
                </section>

                <section class="surface-panel p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Mô phỏng thanh toán trực tuyến để demo phần cộng thêm của project.</p>
                        </div>
                        <a href="{{ route('orders.payment.create', $order) }}" class="action-button-primary">
                            {{ $order->payment_status === 'paid' ? 'View payment' : 'Open checkout' }}
                        </a>
                    </div>
                </section>

                <section class="surface-panel p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Update order status</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Khi đổi trạng thái, service sẽ ghi lịch sử và phát event để listener gửi email cho khách hàng.</p>

                    <form method="POST" action="{{ route('orders.update-status', $order) }}" class="mt-5 space-y-4" novalidate>
                        @csrf
                        @method('PATCH')

                        <div class="w-full">
                            <label for="status" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Order status</label>
                            <select id="status" name="status" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="action-button-primary w-full">Save status and send mail</button>
                    </form>
                </section>

                <section class="surface-panel p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Status history</h3>
                    <div class="mt-5 space-y-4">
                        @forelse ($order->statusHistories->sortByDesc('created_at') as $history)
                            <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                                <div class="flex flex-wrap items-center gap-2 text-sm">
                                    <span class="data-badge data-badge-neutral">{{ ucfirst($history->previous_status) }}</span>
                                    <span class="text-gray-400">to</span>
                                    <span class="data-badge data-badge-brand">{{ ucfirst($history->new_status) }}</span>
                                </div>
                                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $history->created_at?->format('d/m/Y H:i') }}
                                    @if ($history->changer)
                                        by {{ $history->changer->name }}
                                    @endif
                                </p>
                                @if ($history->note)
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $history->note }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Chưa có lich su đổi trạng thái cho đơn hàng nay.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
