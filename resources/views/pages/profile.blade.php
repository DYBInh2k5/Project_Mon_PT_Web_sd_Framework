@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="User Profile" />

    <x-common.component-card title="Test profile" desc="Thong tin profile dang duoc lay tu bang profiles bang Query Builder.">
        <div class="flex flex-col gap-6 md:flex-row md:items-start">
            <img
                class="h-28 w-28 rounded-3xl object-cover shadow-theme-sm"
                src="{{ $profile->avatar }}"
                alt="{{ $profile->full_name }}"
            >

            <div class="grid flex-1 gap-4 md:grid-cols-2">
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Full name</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $profile->full_name }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Email</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Address</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $profile->address }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Birthday</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $profile->birthday ? \Carbon\Carbon::parse($profile->birthday)->format('F j, Y') : 'Chua cap nhat' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Gender</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $profile->gender ?: 'Chua cap nhat' }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Phone</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $profile->phone ?: 'Chua cap nhat' }}</p>
                </div>
            </div>
        </div>
    </x-common.component-card>
@endsection
