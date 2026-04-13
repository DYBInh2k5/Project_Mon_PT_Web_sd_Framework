@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Create Product">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Products</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">Create</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h3 class="text-base font-medium text-gray-800 dark:text-white/90">New product</h3>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @include('products._form', ['submitLabel' => 'Create Product'])
        </form>
    </div>
@endsection
