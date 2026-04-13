@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="$product->name">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Products</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">{{ $product->name }}</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 xl:col-span-1 dark:border-gray-800 dark:bg-white/[0.03]">
            @if ($product->imageUrl())
                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-80 w-full rounded-2xl object-cover">
            @else
                <div class="flex h-80 w-full items-center justify-center rounded-2xl bg-gray-100 text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    No product image
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 xl:col-span-2 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $product->name }}</h2>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $product->description ?: 'No description' }}</p>
                </div>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $product->is_active ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                    {{ $product->is_active ? 'Active' : 'Hidden' }}
                </span>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Category</p>
                    <a href="{{ route('product-categories.show', $product->category) }}" class="mt-2 block font-medium text-gray-900 hover:text-brand-600 dark:text-white dark:hover:text-brand-400">
                        {{ $product->category?->name }}
                    </a>
                </div>
                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">SKU</p>
                    <p class="mt-2 font-medium text-gray-900 dark:text-white">{{ $product->sku }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Price</p>
                    <p class="mt-2 font-medium text-gray-900 dark:text-white">${{ number_format((float) $product->price, 2) }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Stock</p>
                    <p class="mt-2 font-medium text-gray-900 dark:text-white">{{ $product->stock }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Created by</p>
                    <p class="mt-2 font-medium text-gray-900 dark:text-white">{{ $product->creator?->name ?? 'System' }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Updated at</p>
                    <p class="mt-2 font-medium text-gray-900 dark:text-white">{{ $product->updated_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
