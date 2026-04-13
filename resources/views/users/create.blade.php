@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Create User">
        <x-slot:breadcrumbs>
            <li>
                <a href="{{ route('users.index') }}" class="text-gray-700 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-500">Users</a>
            </li>
            <li>
                <span class="text-gray-700 dark:text-gray-400">Create</span>
            </li>
        </x-slot:breadcrumbs>
    </x-common.page-breadcrumb>

    <div class="space-y-6">
        <section class="surface-panel p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create a new user</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Them user moi de demo danh sach, role va phan quyen.</p>
            </div>

            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf

                <x-forms.input label="Name" name="name" :value="old('name')" required />
                <x-forms.input label="Email" name="email" type="email" :value="old('email')" required />
                <x-forms.input label="Password" name="password" type="password" required />

                <div class="w-full px-2.5">
                    <label for="role" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Role</label>
                    <select id="role" name="role" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @foreach (['admin' => 'Admin', 'editor' => 'Editor', 'viewer' => 'Viewer'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', 'viewer') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-2 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="action-button-primary">Create User</button>
                    <a href="{{ route('users.index') }}" class="action-button">Cancel</a>
                </div>
            </form>
        </section>
    </div>
@endsection
