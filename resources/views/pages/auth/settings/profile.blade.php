@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Profile Settings">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Dashboard</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">Profile Settings</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    <x-layouts.settings title="Profile" description="Cap nhat thong tin profile tu bang profiles bang Query Builder">
        @if ($errors->any())
            <div class="mb-6">
                <x-package-alert
                    type="danger"
                    message="Vui long kiem tra lai du lieu da nhap."
                    :messages="$errors->all()"
                />
            </div>
        @endif

        @if (session('status'))
            <div class="mb-6">
                <x-package-alert type="success" :message="session('status')" />
            </div>
        @endif

        <form method="POST" action="{{ route('settings.profile.update') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
            @csrf
            @method('PUT')

            <div>
                <x-forms.input
                    name="name"
                    label="Display name (users table)"
                    type="text"
                    :value="old('name', $user->name)"
                />
            </div>

            <div>
                <x-forms.input
                    name="email"
                    label="Email (users table)"
                    type="email"
                    :value="old('email', $user->email)"
                />
            </div>

            <div>
                <x-forms.input
                    name="full_name"
                    label="Full name"
                    type="text"
                    :value="old('full_name', $profile->full_name)"
                    required
                    autofocus
                />
            </div>

            <div>
                <x-forms.input
                    name="address"
                    label="Address"
                    type="text"
                    :value="old('address', $profile->address)"
                />
            </div>

            <div>
                <label for="avatar" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Avatar image</label>
                @if ($profile->avatar)
                    <div class="mb-4">
                        <img
                            src="{{ str_starts_with($profile->avatar, 'http') ? $profile->avatar : asset('storage/'.$profile->avatar) }}"
                            alt="{{ $profile->full_name }}"
                            class="h-24 w-24 rounded-3xl object-cover shadow-theme-sm"
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
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Upload anh JPG, PNG hoac WEBP. Kich thuoc toi da 2MB.
                </p>
                @error('avatar')
                    <p class="mt-2 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-forms.input
                    name="birthday"
                    label="Birthday"
                    type="date"
                    :value="old('birthday', $profile->birthday)"
                />
            </div>

            <div>
                <x-forms.input
                    name="gender"
                    label="Gender"
                    type="text"
                    :value="old('gender', $profile->gender)"
                />
            </div>

            <div>
                <x-forms.input
                    name="phone"
                    label="Phone"
                    type="text"
                    :value="old('phone', $profile->phone)"
                />
            </div>

            <div>
                <x-ui.button type="submit" variant="primary">
                    Save profile
                </x-ui.button>
            </div>
        </form>

        <div class="mt-8 border-t border-gray-200 pt-8 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete account</h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Delete your account and all of its resources</p>

            <form method="POST" action="{{ route('settings.profile.destroy') }}" class="mt-4 space-y-4" novalidate
                  onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                @csrf
                @method('DELETE')

                <div>
                    <x-forms.input
                        name="current_password"
                        label="Current password"
                        type="password"
                        required
                    />
                </div>

                <x-ui.button
                    type="submit"
                    variant="primary"
                    className="bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800"
                >
                    Delete account
                </x-ui.button>
            </form>
        </div>
    </x-layouts.settings>
@endsection
