@extends('layouts.shop')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="aspect-[4/3] bg-stone-100 dark:bg-gray-950">
                    @if ($product->imageUrl())
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center bg-gradient-to-br from-brand-100 via-stone-100 to-amber-100 text-6xl font-semibold text-brand-700 dark:from-gray-900 dark:via-gray-950 dark:to-gray-900 dark:text-brand-300">
                            {{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <a href="{{ route('shop.index') }}" class="text-sm font-medium text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">Quay lai shop</a>
                    <h1 class="mt-3 text-4xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ $product->name }}</h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $product->category?->name ?? 'Khong co danh muc' }}</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-stone-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Gia</div>
                        <div class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ number_format((float) $product->price, 0) }} VND</div>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Ton kho</div>
                        <div class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $product->stock }}</div>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">SKU</div>
                        <div class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $product->sku }}</div>
                    </div>
                </div>

                <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Mo ta san pham</h2>
                    <p class="mt-3 text-sm leading-7 text-gray-600 dark:text-gray-300">
                        {{ $product->description ?: 'San pham dang cap nhat mo ta.' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('shop.cart.store', $product) }}">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="rounded-2xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white hover:bg-brand-700">
                            Them vao gio
                        </button>
                    </form>
                    <a href="{{ route('shop.cart.index') }}" class="rounded-2xl border border-stone-200 px-5 py-3 text-sm font-medium text-gray-700 hover:border-brand-300 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200">
                        Di den gio hang
                    </a>
                    <a href="{{ route('shop.checkout.create') }}" class="rounded-2xl border border-stone-200 px-5 py-3 text-sm font-medium text-gray-700 hover:border-brand-300 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200">
                        Mua ngay
                    </a>
                </div>

                @if ($quantityInCart > 0)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-amber-200">
                        San pham nay dang co trong gio hang voi so luong {{ $quantityInCart }}.
                    </div>
                @endif
            </div>
        </div>

        @if ($relatedProducts->isNotEmpty())
            <div class="mt-12 border-t border-stone-200 pt-10 dark:border-gray-800">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">San pham lien quan</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cac san pham trong cung danh muc.</p>
                    </div>
                    <a href="{{ route('shop.index') }}" class="text-sm font-medium text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">
                        Xem tat ca
                    </a>
                </div>

                <div class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($relatedProducts as $related)
                        <article class="overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="aspect-[4/3] bg-stone-100 dark:bg-gray-950">
                                @if ($related->imageUrl())
                                    <img src="{{ $related->imageUrl() }}" alt="{{ $related->name }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="space-y-3 p-5">
                                <h3 class="text-base font-semibold text-gray-950 dark:text-white">{{ $related->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $related->category?->name }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="font-semibold text-gray-950 dark:text-white">{{ number_format((float) $related->price, 0) }} VND</div>
                                    <a href="{{ route('shop.products.show', $related) }}" class="text-sm font-medium text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">
                                        Xem
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
