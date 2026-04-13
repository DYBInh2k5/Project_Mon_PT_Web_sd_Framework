@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="$category->name">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('product-categories.index') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Product Categories</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">{{ $category->name }}</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $category->name }}</h2>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $category->description ?: 'No description' }}</p>
                </div>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $category->is_active ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                    {{ $category->is_active ? 'Active' : 'Hidden' }}
                </span>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Slug</p>
                    <p class="mt-2 font-medium text-gray-900 dark:text-white">{{ $category->slug }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Products</p>
                    <p class="mt-2 font-medium text-gray-900 dark:text-white">{{ $category->products->count() }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Created by</p>
                    <p class="mt-2 font-medium text-gray-900 dark:text-white">{{ $category->creator?->name ?? 'System' }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Products in this category</h3>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($category->products as $product)
                    <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-4">
                            @if ($product->imageUrl())
                                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-14 w-14 rounded-xl object-cover">
                            @else
                                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gray-100 text-xs text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                    No image
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('products.show', $product) }}" class="font-medium text-gray-900 hover:text-brand-600 dark:text-white dark:hover:text-brand-400">
                                    {{ $product->name }}
                                </a>
                                <p class="text-sm text-gray-500 dark:text-gray-400">SKU {{ $product->sku }} · Stock {{ $product->stock }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900 dark:text-white">${{ number_format((float) $product->price, 2) }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->is_active ? 'Active' : 'Hidden' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:px-6">
                        No products in this category yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
