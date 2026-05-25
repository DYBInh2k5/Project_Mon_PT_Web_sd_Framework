@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit User">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('users.index') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Users</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">Edit</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    Edit: {{ $user->name }}
                </h3>
            </div>

            @if ($errors->any())
                <div class="px-6 pt-6">
                    <x-package-alert
                        type="danger"
                        message="Khong the cap nhat user. Vui long kiem tra lai."
                        :messages="$errors->all()"
                    />
                </div>
            @endif

            <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="p-6" novalidate>
                @csrf
                @method('PUT')

                <div>
                    <x-forms.input label="Name" name="name" :value="$user->name" required />
                </div>

                <div class="mt-4">
                    <x-forms.input label="Email" name="email" :value="$user->email" required />
                </div>

                <div class="mt-4 w-full px-2.5">
                    <label for="role" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Role
                    </label>
                    <select
                        id="role"
                        name="role"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90"
                    >
                        @foreach (['admin' => 'Admin', 'editor' => 'Editor', 'user' => 'User'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-2 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4 w-full px-2.5">
                    <label class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                    <label class="inline-flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500" @checked(old('is_active', $user->is_active))>
                        <span>User is active</span>
                    </label>
                    @error('is_active')
                        <p class="mt-2 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 border-t border-gray-200 pt-6 dark:border-gray-800">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Profile information</h4>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Admin cap nhat thong tin profile cua user.</p>
                </div>

                <div class="mt-4">
                    <x-forms.input label="Full name" name="full_name" :value="old('full_name', $profile?->full_name ?? $user->name)" />
                </div>

                <div class="mt-4">
                    <x-forms.input label="Address" name="address" :value="old('address', $profile?->address)" />
                </div>

                <div class="mt-4 w-full px-2.5">
                    <label for="avatar" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Avatar image</label>
                    @if ($profile?->avatar)
                        <div class="mb-4">
                            <img
                                src="{{ str_starts_with($profile->avatar, 'http') ? $profile->avatar : asset('storage/'.$profile->avatar) }}"
                                alt="{{ $profile->full_name }}"
                                class="h-20 w-20 rounded-2xl object-cover shadow-theme-sm"
                            >
                        </div>
                    @endif
                    <input
                        id="avatar"
                        name="avatar"
                        type="file"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 shadow-theme-xs file:mr-4 file:rounded-lg file:border-0 file:bg-brand-500 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-brand-600 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
                    @error('avatar')
                        <p class="mt-2 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <x-forms.input label="Birthday" name="birthday" type="date" :value="old('birthday', $profile?->birthday)" />
                </div>

                <div class="mt-4">
                    <x-forms.input label="Gender" name="gender" :value="old('gender', $profile?->gender)" />
                </div>

                <div class="mt-4">
                    <x-forms.input label="Phone" name="phone" :value="old('phone', $profile?->phone)" />
                </div>

                <div class="mt-4 w-full px-2.5">
                    <div class="mt-1 flex items-center gap-3">
                        <button type="submit" class="bg-brand-500 hover:bg-brand-600 flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white">
                            Update User
                        </button>
    
                        <a href="{{ route('users.index') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
