@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Products">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Dashboard</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">Products</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    @php($modernProducts = true)

    @if ($modernProducts)
        <div
            x-data="{ showDeleteModal: false, deleteAction: '', deleteName: '' }"
            class="space-y-6"
        >
            @session('success')
                <x-ui.alert variant="success">
                    {{ $value }}
                </x-ui.alert>
            @endsession

            <section class="page-toolbar">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="space-y-3">
                        <span class="toolbar-chip">Inventory board</span>
                        <div>
                            <h2 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">Products with cleaner filters and faster actions.</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-7 text-gray-600 dark:text-gray-300">
                                Quản lý danh sách sản phẩm, hình ảnh, tồn kho và trạng thái trong một màn hình gọn hơn.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <span class="metric-pill">{{ $summary['total'] }} total items</span>
                            <span class="metric-pill">{{ $summary['active'] }} active</span>
                            <span class="metric-pill">{{ $summary['lowStock'] }} low stock</span>
                            <span class="metric-pill">${{ number_format($summary['inventoryValue'], 2) }} inventory value</span>
                        </div>
                    </div>

                    <a href="{{ route('products.create') }}" class="action-button-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 5v14m-7-7h14" />
                        </svg>
                        Add Product
                    </a>
                </div>
            </section>

            <section
                x-data="{ expanded: {{ (! empty($filters['search']) || ! empty($filters['category']) || ! empty($filters['status'])) ? 'true' : 'false' }} }"
                class="surface-panel p-5"
            >
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filter products</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tìm nhanh theo tên, SKU, danh mục và trạng thái.</p>
                    </div>

                    <button @click="expanded = !expanded" type="button" class="action-button">
                        <span x-text="expanded ? 'Hide filters' : 'Show filters'"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition" :class="{ 'rotate-180': expanded }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <form x-show="expanded" x-cloak method="GET" action="{{ route('products.index') }}" class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-4">
                    <div>
                        <x-forms.input
                            name="search"
                            label="Search"
                            type="text"
                            :value="$filters['search'] ?? ''"
                        />
                    </div>

                    <div class="w-full px-2.5">
                        <label for="category" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Category</label>
                        <select id="category" name="category" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full px-2.5">
                        <label for="status" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                        <select id="status" name="status" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="">All statuses</option>
                            <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                            <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Hidden</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-3 px-2.5">
                        <button type="submit" class="action-button-primary w-full sm:w-auto">
                            Apply
                        </button>
                        <a href="{{ route('products.index') }}" class="action-button w-full sm:w-auto">
                            Reset
                        </a>
                    </div>
                </form>
            </section>

            <section class="table-shell">
                <div class="border-b border-gray-200/80 px-5 py-4 dark:border-gray-800 sm:px-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product list</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $products->total() }} results in current view.</p>
                        </div>
                        <div class="toolbar-chip">Blade + Tailwind + Alpine</div>
                    </div>
                </div>

                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1180px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Product</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Category</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">SKU</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Price</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Stock</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Status</th>
                                <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr class="table-row-hover border-b border-gray-100 last:border-b-0 dark:border-gray-800">
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-4">
                                            @if ($product->imageUrl())
                                                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-[20px] object-cover shadow-theme-xs">
                                            @else
                                                <div class="flex h-16 w-16 items-center justify-center rounded-[20px] bg-gray-100 text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                                    No image
                                                </div>
                                            @endif

                                            <div class="max-w-[260px]">
                                                <a href="{{ route('products.show', $product) }}" class="text-sm font-semibold text-gray-900 hover:text-brand-600 dark:text-white dark:hover:text-brand-400">
                                                    {{ $product->name }}
                                                </a>
                                                <p class="mt-1 line-clamp-2 text-sm text-gray-500 dark:text-gray-400">{{ $product->description ?: 'No description' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-sm text-gray-500 dark:text-gray-400">
                                        @if ($product->category)
                                            <a href="{{ route('product-categories.show', $product->category) }}" class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-brand-50 hover:text-brand-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-brand-500/15 dark:hover:text-brand-400">
                                                {{ $product->category->name }}
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">No category</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-sm font-medium text-gray-600 dark:text-gray-300">{{ $product->sku }}</td>
                                    <td class="px-5 py-4 sm:px-6 text-sm font-semibold text-gray-900 dark:text-white">${{ number_format((float) $product->price, 2) }}</td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $product->stock <= 10 ? 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400' : 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' }}">
                                            <span class="h-2 w-2 rounded-full {{ $product->stock <= 10 ? 'bg-warning-500' : 'bg-success-500' }}"></span>
                                            {{ $product->stock }} in stock
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="data-badge {{ $product->is_active ? 'data-badge-success' : 'data-badge-neutral' }}">
                                            {{ $product->is_active ? 'Active' : 'Hidden' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-right">
                                        <div class="relative inline-block text-left" x-data="{ open: false }">
                                            <button @click="open = !open" type="button" class="action-button">
                                                Actions
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m19 9-7 7-7-7" />
                                                </svg>
                                            </button>

                                            <div
                                                x-show="open"
                                                x-cloak
                                                @click.away="open = false"
                                                class="absolute right-0 z-20 mt-2 w-44 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900"
                                            >
                                                <a href="{{ route('products.show', $product) }}" class="block rounded-xl px-3 py-2 text-left text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.03]">View detail</a>
                                                <a href="{{ route('products.edit', $product) }}" class="block rounded-xl px-3 py-2 text-left text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.03]">Edit</a>
                                                <button
                                                    @click="open = false; showDeleteModal = true; deleteAction = '{{ route('products.destroy', $product) }}'; deleteName = @js($product->name)"
                                                    type="button"
                                                    class="block w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-error-700 transition hover:bg-error-50 dark:text-error-400 dark:hover:bg-error-500/10"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 sm:px-6">
                                        <div class="empty-state">
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">No products found.</p>
                                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Thử đổi bộ lọc hoặc thêm sản phẩm mới để bắt đầu.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            @if ($products->hasPages())
                <div>{{ $products->links() }}</div>
            @endif

            <div
                x-show="showDeleteModal"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4"
                @keydown.escape.window="showDeleteModal = false"
            >
                <div @click.away="showDeleteModal = false" class="w-full max-w-md rounded-[28px] bg-white p-6 shadow-theme-xl dark:bg-gray-900">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.29 3.86l-7.17 12.42A2 2 0 0 0 4.83 19h14.34a2 2 0 0 0 1.71-2.72L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                        </svg>
                    </div>
                    <div class="mt-4 text-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete product</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Bạn sắp xóa <span class="font-semibold text-gray-900 dark:text-white" x-text="deleteName"></span>. Hành động này không thể hoàn tác.
                        </p>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="showDeleteModal = false" class="action-button">Cancel</button>
                        <form :action="deleteAction" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-button-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
    <div class="space-y-6">
        @session('success')
            <x-ui.alert variant="success">
                {{ $value }}
            </x-ui.alert>
        @endsession

        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div>
                    <x-forms.input
                        name="search"
                        label="Search"
                        type="text"
                        :value="$filters['search'] ?? ''"
                    />
                </div>

                <div class="w-full px-2.5">
                    <label for="category" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Category</label>
                    <select id="category" name="category" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full px-2.5">
                    <label for="status" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                    <select id="status" name="status" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                        <option value="">All statuses</option>
                        <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                        <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Hidden</option>
                    </select>
                </div>

                <div class="flex items-end gap-3 px-2.5">
                    <button type="submit" class="bg-brand-500 hover:bg-brand-600 rounded-lg px-4 py-3 text-sm font-medium text-white">
                        Filter
                    </button>
                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="flex items-center justify-end">
            <a href="{{ route('products.create') }}" class="bg-brand-500 hover:bg-brand-600 rounded-lg px-4 py-3 text-sm font-medium text-white">
                Add Product
            </a>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[1150px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Image</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Product</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Category</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">SKU</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Price</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Stock</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p></th>
                            <th class="px-5 py-3 text-left sm:px-6"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Actions</p></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 sm:px-6">
                                    @if ($product->imageUrl())
                                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-14 w-14 rounded-xl object-cover">
                                    @else
                                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gray-100 text-xs text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                            No image
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <a href="{{ route('products.show', $product) }}" class="font-medium text-gray-900 hover:text-brand-600 dark:text-white dark:hover:text-brand-400">
                                        {{ $product->name }}
                                    </a>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $product->description ?: 'No description' }}</div>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-500 dark:text-gray-400">
                                    @if ($product->category)
                                        <a href="{{ route('product-categories.show', $product->category) }}" class="hover:text-brand-600 dark:hover:text-brand-400">
                                            {{ $product->category->name }}
                                        </a>
                                    @endif
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-500 dark:text-gray-400">{{ $product->sku }}</td>
                                <td class="px-5 py-4 sm:px-6 text-sm font-medium text-gray-700 dark:text-gray-300">${{ number_format((float) $product->price, 2) }}</td>
                                <td class="px-5 py-4 sm:px-6 text-sm text-gray-500 dark:text-gray-400">{{ $product->stock }}</td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $product->is_active ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                        {{ $product->is_active ? 'Active' : 'Hidden' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('products.edit', $product) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                            Edit
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center rounded-lg border border-error-200 bg-error-50 px-3 py-2 text-sm font-medium text-error-700 transition hover:bg-error-100 dark:border-error-500/20 dark:bg-error-500/10 dark:text-error-400">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:px-6">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($products->hasPages())
            <div>{{ $products->links() }}</div>
        @endif
    </div>
    @endif
@endsection
