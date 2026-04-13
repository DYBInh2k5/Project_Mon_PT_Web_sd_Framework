@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Dashboard">
        <x-slot:breadcrumbs>
            <li>
                <span class="text-gray-700 dark:text-gray-400">Dashboard</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    @php($modernDashboard = true)

    @if ($modernDashboard)
        <div class="space-y-6">
            <section class="page-toolbar overflow-hidden">
                <div class="grid gap-6 xl:grid-cols-[1.7fr_1fr]">
                    <div class="space-y-4">
                        <span class="toolbar-chip">Overview studio</span>
                        <div>
                            <h2 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                                Product management workspace with a cleaner admin flow.
                            </h2>
                            <p class="mt-3 max-w-2xl text-sm leading-7 text-gray-600 dark:text-gray-300">
                                Theo dõi nhanh user, danh mục và sản phẩm trong một màn hình. Giao diện được giữ bằng Blade
                                nhưng đẩy mạnh Tailwind để nhìn hiện đại và rõ ưu tiên hơn.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <span class="metric-pill">
                                <span class="h-2 w-2 rounded-full bg-success-500"></span>
                                {{ $stats['activeProducts'] }} products active
                            </span>
                            <span class="metric-pill">
                                <span class="h-2 w-2 rounded-full bg-brand-500"></span>
                                {{ $stats['activeCategories'] }} categories visible
                            </span>
                            <span class="metric-pill">
                                <span class="h-2 w-2 rounded-full bg-warning-500"></span>
                                {{ $stats['editorUsers'] }} editors ready
                            </span>
                        </div>
                    </div>

                    <div class="glass-panel subtle-gradient p-5">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Health snapshot</p>
                        <div class="mt-5 grid grid-cols-2 gap-4">
                            <div class="rounded-2xl bg-white/75 p-4 dark:bg-gray-900/60">
                                <p class="text-xs uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Verified</p>
                                <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['verifiedUsers'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-white/75 p-4 dark:bg-gray-900/60">
                                <p class="text-xs uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Low stock</p>
                                <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['lowStockProducts'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-white/75 p-4 dark:bg-gray-900/60">
                                <p class="text-xs uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">New week</p>
                                <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['newUsersThisWeek'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-white/75 p-4 dark:bg-gray-900/60">
                                <p class="text-xs uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Categories</p>
                                <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['totalCategories'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <div class="stat-card">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total users</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['totalUsers'] }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Quản lý quyền admin, editor và viewer.</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Verified users</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['verifiedUsers'] }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Email đã xác thực để giảm thao tác kiểm tra.</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Editors</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['editorUsers'] }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nhóm có quyền quản lý sản phẩm và danh mục.</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Visible categories</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['activeCategories'] }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Danh mục đang hiển thị trên hệ thống.</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Products live</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['activeProducts'] }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Sản phẩm đang active để bán hoặc demo.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
                <div class="table-shell xl:col-span-4">
                    <div class="border-b border-gray-200/80 px-5 py-4 dark:border-gray-800 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent users</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Latest accounts in your SQLite database</p>
                            </div>
                            <a href="{{ route('users.index') }}" class="action-button">
                                Manage users
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Name</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Email</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentUsers as $user)
                                    <tr class="table-row-hover border-b border-gray-100 last:border-b-0 dark:border-gray-800">
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-50 text-sm font-semibold text-brand-700 dark:bg-brand-500/15 dark:text-brand-400">
                                                    {{ $user->initials() }}
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                                    <div class="text-xs uppercase tracking-[0.18em] text-gray-400">{{ ucfirst($user->role) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 sm:px-6">{{ $user->email }}</td>
                                        <td class="px-5 py-4 sm:px-6">
                                            @if ($user->email_verified_at)
                                                <span class="data-badge data-badge-success">Verified</span>
                                            @else
                                                <span class="inline-flex items-center gap-2 rounded-full bg-warning-50 px-3 py-1 text-xs font-semibold text-warning-700 dark:bg-warning-500/15 dark:text-warning-400">Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 sm:px-6">{{ $user->created_at?->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:px-6">
                                            No users yet. Run the database seeder to generate sample data.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-shell xl:col-span-3">
                    <div class="border-b border-gray-200/80 px-5 py-4 dark:border-gray-800 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product categories</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Quick view for editor workflow</p>
                            </div>
                            @if (auth()->user()?->hasRole('editor', 'admin'))
                                <a href="{{ route('product-categories.index') }}" class="action-button">
                                    Open
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3 p-5 sm:p-6">
                        @forelse ($recentCategories as $category)
                            <div class="rounded-[22px] border border-gray-100 p-4 transition hover:border-brand-200 hover:bg-brand-25/50 dark:border-gray-800 dark:hover:border-brand-500/20 dark:hover:bg-white/[0.03]">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $category->name }}</h4>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $category->description ?: 'No description' }}</p>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="metric-pill">{{ $category->products_count }} products</span>
                                            <span class="metric-pill">{{ $category->slug }}</span>
                                        </div>
                                    </div>
                                    <span class="data-badge {{ $category->is_active ? 'data-badge-success' : 'data-badge-neutral' }}">
                                        {{ $category->is_active ? 'Active' : 'Hidden' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No categories yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="table-shell xl:col-span-5">
                    <div class="border-b border-gray-200/80 px-5 py-4 dark:border-gray-800 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent products</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Products linked to category management</p>
                            </div>
                            @if (auth()->user()?->hasRole('editor', 'admin'))
                                <a href="{{ route('products.index') }}" class="action-button">
                                    Open
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3 p-5 sm:p-6">
                        @forelse ($recentProducts as $product)
                            <div class="rounded-[22px] border border-gray-100 p-4 transition hover:border-brand-200 hover:bg-brand-25/50 dark:border-gray-800 dark:hover:border-brand-500/20 dark:hover:bg-white/[0.03]">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        @if ($product->imageUrl())
                                            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-14 w-14 rounded-2xl object-cover">
                                        @else
                                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                                No image
                                            </div>
                                        @endif
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</h4>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $product->category?->name ?? 'No category' }} · SKU {{ $product->sku }}
                                            </p>
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <span class="metric-pill">Stock {{ $product->stock }}</span>
                                                <span class="data-badge {{ $product->is_active ? 'data-badge-success' : 'data-badge-neutral' }}">
                                                    {{ $product->is_active ? 'Active' : 'Hidden' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm uppercase tracking-[0.18em] text-gray-400">Price</p>
                                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">${{ number_format((float) $product->price, 2) }}</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Updated {{ $product->updated_at?->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No products yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @else
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total users</p>
                <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['totalUsers'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Verified users</p>
                <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['verifiedUsers'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">New this week</p>
                <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['newUsersThisWeek'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Product categories</p>
                <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['totalCategories'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Products</p>
                <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['totalProducts'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <div class="rounded-2xl border border-gray-200 bg-white xl:col-span-3 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent users</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Latest accounts in your SQLite database</p>
                        </div>
                        <a href="{{ route('users.index') }}"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                            Manage users
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Name</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Email</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Status</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentUsers as $user)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 sm:px-6">{{ $user->email }}</td>
                                    <td class="px-5 py-4 sm:px-6">
                                        @if ($user->email_verified_at)
                                            <span class="inline-flex rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-400">
                                                Verified
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-warning-50 px-2.5 py-1 text-xs font-medium text-warning-700 dark:bg-warning-500/15 dark:text-warning-400">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 sm:px-6">{{ $user->created_at?->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:px-6">
                                        No users yet. Run the database seeder to generate sample data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white xl:col-span-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product categories</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Quick view for editor workflow</p>
                        </div>
                        @if (auth()->user()?->hasRole('editor', 'admin'))
                            <a href="{{ route('product-categories.index') }}"
                                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                                Open
                            </a>
                        @endif
                    </div>
                </div>

                <div class="space-y-3 p-5 sm:p-6">
                    @forelse ($recentCategories as $category)
                        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $category->name }}</h4>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $category->description ?: 'No description' }}</p>
                                </div>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $category->is_active ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                    {{ $category->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No categories yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white xl:col-span-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent products</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Products linked to category management</p>
                        </div>
                        @if (auth()->user()?->hasRole('editor', 'admin'))
                            <a href="{{ route('products.index') }}"
                                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                                Open
                            </a>
                        @endif
                    </div>
                </div>

                <div class="space-y-3 p-5 sm:p-6">
                    @forelse ($recentProducts as $product)
                        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</h4>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $product->category?->name ?? 'No category' }} · SKU {{ $product->sku }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format((float) $product->price, 2) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Stock: {{ $product->stock }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No products yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
