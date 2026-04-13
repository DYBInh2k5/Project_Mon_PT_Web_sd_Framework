@php
    $category ??= null;
@endphp

<div class="space-y-5">
    <div>
        <x-forms.input
            name="name"
            label="Category name"
            type="text"
            :value="old('name', $category?->name)"
            required
            autofocus
        />
    </div>

    <div>
        <x-forms.input
            name="slug"
            label="Slug"
            type="text"
            :value="old('slug', $category?->slug)"
        />
    </div>

    <div class="w-full px-2.5">
        <label for="description" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Description
        </label>
        <textarea
            id="description"
            name="description"
            rows="4"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90"
            placeholder="Describe this product category">{{ old('description', $category?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="w-full px-2.5">
        <label class="flex items-center gap-3">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500"
                @checked(old('is_active', $category?->is_active ?? true))
            >
            <span class="text-sm text-gray-700 dark:text-gray-300">Active category</span>
        </label>
    </div>

    <div class="flex items-center gap-3 px-2.5">
        <button type="submit" class="bg-brand-500 hover:bg-brand-600 rounded-lg px-4 py-3 text-sm font-medium text-white">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('product-categories.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
            Cancel
        </a>
    </div>
</div>
