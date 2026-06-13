@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="User Profile" />

    @php
        $avatarUrl = null;

        if (! empty($profile->avatar)) {
            $avatarUrl = str_starts_with($profile->avatar, 'http')
                ? $profile->avatar
                : asset('storage/'.$profile->avatar);
        }
    @endphp

    <x-common.component-card title="Test profile" desc="Thông tin profile dang duoc lay bang Eloquent qua quan he User hasOne Profile.">
        <div class="mb-6 flex items-center justify-end">
            <a
                href="{{ route('settings.profile.edit') }}"
                class="inline-flex items-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-700"
            >
                Edit profile
            </a>
        </div>

        <div class="flex flex-col gap-6 md:flex-row md:items-start">
            @if ($avatarUrl)
                <img
                    class="h-28 w-28 rounded-3xl object-cover shadow-theme-sm"
                    src="{{ $avatarUrl }}"
                    alt="{{ $profile->full_name }}"
                >
            @else
                <div class="flex h-28 w-28 items-center justify-center rounded-3xl bg-brand-50 text-2xl font-semibold text-brand-700 shadow-theme-sm dark:bg-brand-500/15 dark:text-brand-400">
                    {{ strtoupper(substr($profile->full_name, 0, 1)) }}
                </div>
            @endif

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
                        {{ $profile->birthday ? \Carbon\Carbon::parse($profile->birthday)->format('F j, Y') : 'Chua cập nhật' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Gender</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $profile->gender ?: 'Chua cập nhật' }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Phone</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $profile->phone ?: 'Chua cập nhật' }}</p>
                </div>
            </div>
        </div>
    </x-common.component-card>
@endsection
