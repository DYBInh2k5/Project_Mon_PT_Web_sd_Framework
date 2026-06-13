@extends('layouts.shop')

@section('content')
    <section class="border-b border-stone-200 bg-gradient-to-b from-white to-stone-50 dark:border-gray-800 dark:from-gray-950 dark:to-gray-950">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[1.2fr_0.8fr] lg:px-8 lg:py-16">
            <div class="space-y-6">
                <div class="inline-flex items-center rounded-full border border-stone-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200">
                    Cửa hàng công khai
                </div>
                <div class="space-y-4">
                    <h1 class="max-w-3xl text-4xl font-semibold tracking-tight text-gray-950 sm:text-5xl dark:text-white">
                        Mua sắm sản phẩm văn phòng, công nghệ và đồ dùng hằng ngày.
                    </h1>
                    <p class="max-w-2xl text-base leading-7 text-gray-600 dark:text-gray-300">
                        Đây là mặt tiền của website bán hàng. Khách có thể xem danh mục, tìm sản phẩm, lọc theo loại và xem giá trị sản phẩm như một shop thông thường.
                    </p>
                </div>

                <form method="GET" action="{{ route('shop.index') }}" class="grid gap-3 sm:grid-cols-[1.4fr_0.8fr_auto]">
                    <input
                        type="text"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Tìm sản phẩm, SKU hoặc mô tả..."
                        class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm outline-none ring-0 placeholder:text-gray-400 focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    >

                    <select
                        name="category"
                        class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    >
                        <option value="">Tất cả danh mục</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>
                                {{ $category->name }} ({{ $category->products_count }})
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="rounded-2xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">
                        Lọc sản phẩm
                    </button>
                </form>

                <div class="flex flex-wrap gap-3">
                    <div class="rounded-2xl border border-stone-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Sản phẩm</div>
                        <div class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $summary['products'] }}</div>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Danh mục</div>
                        <div class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $summary['categories'] }}</div>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Còn hàng</div>
                        <div class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $summary['inStock'] }}</div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('shop.cart.index') }}" class="rounded-2xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800 dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200">
                        Đi đến giỏ hàng
                    </a>
                    <a href="{{ route('shop.checkout.create') }}" class="rounded-2xl border border-stone-200 px-5 py-3 text-sm font-medium text-gray-700 hover:border-brand-300 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200">
                        Thanh toán
                    </a>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Danh mục nổi bật</div>
                    <div class="mt-4 space-y-3">
                        @forelse ($categories->take(6) as $category)
                            <div class="flex items-center justify-between rounded-2xl bg-stone-50 px-4 py-3 dark:bg-gray-950">
                                <div>
                                    <div class="font-medium text-gray-950 dark:text-white">{{ $category->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $category->description }}</div>
                                </div>
                                <div class="text-sm font-semibold text-brand-700 dark:text-brand-400">{{ $category->products_count }}</div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Chưa có danh mục còn hàng.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Gợi ý mua nhanh</div>
                    <div class="mt-4 space-y-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        <p>Khách có thể lọc sản phẩm theo danh mục ngay trên trang chủ.</p>
                        <p>Trang này dùng để mô tả shop bình thường, còn phần quản trị vẫn nằm trong dashboard.</p>
                        <p>Sản phẩm hết hàng sẽ không hiện trong mặt tiền công khai.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 border-b border-stone-200 pb-5 sm:flex-row sm:items-end sm:justify-between dark:border-gray-800">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">Sản phẩm hiện có</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $products->total() }} kết quả phù hợp với bộ lọc hiện tại.</p>
            </div>
            <a href="{{ route('articles.index') }}" class="text-sm font-medium text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">
                Xem bài viết giới thiệu
            </a>
        </div>

        <div class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
            @forelse ($products as $product)
                <article class="overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <div class="aspect-[4/3] bg-stone-100 dark:bg-gray-950">
                        @if ($product->imageUrl())
                            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center bg-gradient-to-br from-brand-100 via-stone-100 to-amber-100 text-3xl font-semibold text-brand-700 dark:from-gray-900 dark:via-gray-950 dark:to-gray-900 dark:text-brand-300">
                                {{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-base font-semibold text-gray-950 dark:text-white">{{ $product->name }}</h3>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $product->category?->name ?? 'Khong co danh mục' }}</p>
                            </div>
                            <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-300">
                                {{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}
                            </span>
                        </div>

                        <p class="line-clamp-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                            {{ $product->description ?: 'Sản phẩm dang cập nhật mô tả.' }}
                        </p>

                        <div class="flex items-center justify-between gap-3 pt-1">
                            <div>
                                <div class="text-lg font-semibold text-gray-950 dark:text-white">{{ number_format((float) $product->price, 0) }} VND</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">SKU {{ $product->sku }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('shop.products.show', $product) }}" class="rounded-2xl border border-stone-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-brand-300 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200 dark:hover:border-brand-500 dark:hover:text-brand-300">
                                    Xem chi tiết
                                </a>
                                <form method="POST" action="{{ route('shop.cart.store', $product) }}">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="rounded-2xl border border-stone-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-brand-300 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200 dark:hover:border-brand-500 dark:hover:text-brand-300">
                                        Thêm vào giỏ
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-stone-300 bg-white p-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                    Khong tim thay sản phẩm phù hợp.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </section>
@endsection
