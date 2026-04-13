@php
    $product ??= null;
@endphp

<div class="space-y-5">
    <div>
        <x-forms.input
            name="name"
            label="Product name"
            type="text"
            :value="old('name', $product?->name)"
            required
            autofocus
        />
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="w-full px-2.5">
            <label for="product_category_id" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Category
            </label>
            <select
                id="product_category_id"
                name="product_category_id"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90"
            >
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('product_category_id', $product?->product_category_id) === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('product_category_id')
                <p class="mt-2 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <x-forms.input
                name="sku"
                label="SKU"
                type="text"
                :value="old('sku', $product?->sku)"
                required
            />
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
            <x-forms.input
                name="slug"
                label="Slug"
                type="text"
                :value="old('slug', $product?->slug)"
            />
        </div>

        <div>
            <x-forms.input
                name="price"
                label="Price"
                type="number"
                step="0.01"
                min="0"
                :value="old('price', $product?->price)"
                required
            />
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
            <x-forms.input
                name="stock"
                label="Stock"
                type="number"
                min="0"
                :value="old('stock', $product?->stock ?? 0)"
                required
            />
        </div>
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
            placeholder="Describe this product">{{ old('description', $product?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="w-full px-2.5">
        <label for="image" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Product image
        </label>
        <input
            id="image"
            name="image"
            type="file"
            accept="image/*"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90"
        >
        @error('image')
            <p class="mt-2 text-sm text-error-500">{{ $message }}</p>
        @enderror

        @if ($product?->imageUrl())
            <div class="mt-3">
                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-24 w-24 rounded-xl object-cover">
            </div>
        @endif
    </div>

    <div class="w-full px-2.5">
        <label class="flex items-center gap-3">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500"
                @checked(old('is_active', $product?->is_active ?? true))
            >
            <span class="text-sm text-gray-700 dark:text-gray-300">Active product</span>
        </label>
    </div>

    <div class="flex items-center gap-3 px-2.5">
        <button type="submit" class="bg-brand-500 hover:bg-brand-600 rounded-lg px-4 py-3 text-sm font-medium text-white">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('products.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
            Cancel
        </a>
    </div>
</div>
