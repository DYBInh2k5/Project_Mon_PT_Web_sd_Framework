@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Orders" />

    <div class="space-y-6">
        @session('success')
            <x-ui.alert variant="success">
                {{ $value }}
            </x-ui.alert>
        @endsession

        <section class="page-toolbar">
            <div class="space-y-4">
                <span class="toolbar-chip">Order management</span>
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h2 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">Track every order in one operational view.</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-7 text-gray-600 dark:text-gray-300">
                            Theo dõi đơn hàng mới nhất, lọc theo trạng thái, tìm theo ngày và mở nhanh chi tiết khách hàng.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <span class="metric-pill">{{ $summary['total'] }} total orders</span>
                        <span class="metric-pill">{{ $summary['pending'] }} pending</span>
                        <span class="metric-pill">{{ $summary['processing'] }} processing</span>
                        <span class="metric-pill">{{ $summary['completed'] }} completed</span>
                        <span class="metric-pill">${{ number_format($summary['revenue'], 2) }} revenue</span>
                    </div>
                </div>
            </div>
        </section>

        <section
            x-data="{ expanded: {{ (! empty($filters['search']) || ! empty($filters['status']) || ! empty($filters['date_from']) || ! empty($filters['date_to'])) ? 'true' : 'false' }} }"
            class="surface-panel p-5"
        >
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filter orders</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tìm theo mã đơn, tên khách hàng, email hoặc lọc theo ngày và trạng thái.</p>
                </div>

                <button @click="expanded = !expanded" type="button" class="action-button">
                    <span x-text="expanded ? 'Hide filters' : 'Show filters'"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition" :class="{ 'rotate-180': expanded }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m19 9-7 7-7-7" />
                    </svg>
                </button>
            </div>

            <form x-show="expanded" x-cloak method="GET" action="{{ route('orders.index') }}" class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-5">
                <div class="lg:col-span-2">
                    <x-forms.input
                        name="search"
                        label="Search"
                        type="text"
                        :value="$filters['search'] ?? ''"
                        placeholder="Order number, customer name, email or phone"
                    />
                </div>

                <div class="w-full px-2.5">
                    <label for="status" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                    <select id="status" name="status" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-forms.input
                        name="date_from"
                        label="From date"
                        type="date"
                        :value="$filters['date_from'] ?? ''"
                    />
                </div>

                <div>
                    <x-forms.input
                        name="date_to"
                        label="To date"
                        type="date"
                        :value="$filters['date_to'] ?? ''"
                    />
                </div>

                <div class="flex items-end gap-3 px-2.5 lg:col-span-5">
                    <button type="submit" class="action-button-primary w-full sm:w-auto">Apply</button>
                    <a href="{{ route('orders.index') }}" class="action-button w-full sm:w-auto">Reset</a>
                </div>
            </form>
        </section>

        <section class="table-shell">
            <div class="border-b border-gray-200/80 px-5 py-4 dark:border-gray-800 sm:px-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order list</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $orders->total() }} results sorted from newest to oldest.</p>
                    </div>
                    <div class="toolbar-chip">Editor or admin</div>
                </div>
            </div>

            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[1180px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Order</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Customer</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Status</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Payment</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Items</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Total</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Placed at</th>
                            <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr class="table-row-hover border-b border-gray-100 last:border-b-0 dark:border-gray-800">
                                <td class="px-5 py-4 sm:px-6">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->notes ?: 'No note' }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $order->customer_name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->customer_email }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="data-badge {{ match($order->status) {
                                        'completed' => 'data-badge-success',
                                        'processing' => 'data-badge-brand',
                                        'shipped' => 'data-badge-warning',
                                        'cancelled' => 'data-badge-neutral',
                                        default => 'data-badge-warning',
                                    } }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="data-badge {{ $order->payment_status === 'paid' ? 'data-badge-success' : 'data-badge-warning' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-500 dark:text-gray-400">{{ $order->items_count }} items</td>
                                <td class="px-5 py-4 sm:px-6 text-sm font-semibold text-gray-900 dark:text-white">${{ number_format((float) $order->total_amount, 2) }}</td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-500 dark:text-gray-400">{{ $order->placed_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-4 text-right sm:px-6">
                                    <a href="{{ route('orders.show', $order) }}" class="action-button">View detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center sm:px-6">
                                    <div class="empty-state">
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">No orders found.</p>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Thử thay đổi ngày hoặc trạng thái để tìm đúng đơn hàng bạn cần.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @if ($orders->hasPages())
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
