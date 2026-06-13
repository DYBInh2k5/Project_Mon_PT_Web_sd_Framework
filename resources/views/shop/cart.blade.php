@extends('layouts.shop')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 border-b border-stone-200 pb-5 sm:flex-row sm:items-end sm:justify-between dark:border-gray-800">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">Giỏ hàng</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $count }} sản phẩm trong giỏ.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('shop.index') }}" class="rounded-2xl border border-stone-200 px-4 py-2 text-sm font-medium text-gray-700 hover:border-brand-300 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200">Tiếp tục mua sắm</a>
                <form method="POST" action="{{ route('shop.cart.clear') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-2xl border border-red-200 px-4 py-2 text-sm font-medium text-red-700 hover:border-red-300 hover:bg-red-50 dark:border-red-900/40 dark:text-red-300 dark:hover:bg-red-950/20">
                        Xóa giỏ hàng
                    </button>
                </form>
            </div>
        </div>

        @if ($items === [])
            <div class="mt-8 rounded-3xl border border-dashed border-stone-300 bg-white p-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                Giỏ hàng đang trống.
            </div>
        @else
            <div class="mt-8 grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
                <div class="space-y-4">
                    @foreach ($items as $item)
                        <article class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="flex flex-col gap-5 sm:flex-row">
                                <div class="h-28 w-full overflow-hidden rounded-2xl bg-stone-100 sm:w-32 dark:bg-gray-950">
                                    @if ($item['product']->imageUrl())
                                        <img src="{{ $item['product']->imageUrl() }}" alt="{{ $item['product']->name }}" class="h-full w-full object-cover">
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                        <div>
                                            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">{{ $item['product']->name }}</h2>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item['product']->category?->name }}</p>
                                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $item['product']->description }}</p>
                                        </div>
                                        <div class="text-left md:text-right">
                                            <div class="text-lg font-semibold text-gray-950 dark:text-white">{{ number_format((float) $item['product']->price, 0) }} VND</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">SKU {{ $item['product']->sku }}</div>
                                        </div>
                                    </div>

                                    <div class="mt-5 flex flex-wrap items-center gap-3">
                                        <form method="POST" action="{{ route('shop.cart.update', $item['product']) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" min="0" max="{{ $item['product']->stock }}" name="quantity" value="{{ $item['quantity'] }}" class="w-24 rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-950">
                                            <button type="submit" class="rounded-xl border border-stone-200 px-3 py-2 text-sm font-medium text-gray-700 hover:border-brand-300 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200">
                                                Cập nhật
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('shop.cart.destroy', $item['product']) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl border border-red-200 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50 dark:border-red-900/40 dark:text-red-300 dark:hover:bg-red-950/20">
                                                Xoa
                                            </button>
                                        </form>

                                        <div class="ml-auto text-sm font-medium text-gray-900 dark:text-white">
                                            Thanh tien: {{ number_format((float) $item['line_total'], 0) }} VND
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="space-y-6">
                    <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold text-gray-950 dark:text-white">Tổng đơn hàng</h3>
                        <div class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                            <div class="flex items-center justify-between">
                                <span>Tổng sản phẩm</span>
                                <span>{{ $count }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Tạm tính</span>
                                <span>{{ number_format((float) $subtotal, 0) }} VND</span>
                            </div>
                        </div>

                        <a href="{{ route('shop.checkout.create') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-700">
                            Tiến hành thanh toán VNPay
                        </a>
                    </div>

                    <div class="rounded-3xl border border-stone-200 bg-stone-50 p-6 dark:border-gray-800 dark:bg-gray-950">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Lưu ý</h4>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                            Luồng này là giỏ hàng thực tế của shop công khai. Khi bấm thanh toán, hệ thống sẽ tạo đơn hàng và chuyển sang cổng VNPay nếu cấu hình đủ thông tin trong `.env`.
                        </p>
                    </div>
                </aside>
            </div>
        @endif
    </section>
@endsection
