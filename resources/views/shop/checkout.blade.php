@extends('layouts.shop')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <span class="toolbar-chip">VNPay checkout</span>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">Thanh toán đơn hàng</h1>
                    <p class="mt-2 text-sm leading-7 text-gray-600 dark:text-gray-300">
                        Hệ thống sẽ tạo đơn hàng và chuyển bạn sang cổng thanh toán VNPay. Khi thanh toán xong, VNPay sẽ tự quay về trang kết quả và backend sẽ cập nhật trạng thái đơn hàng.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-950/20 dark:text-red-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('shop.checkout.store') }}" class="mt-6 space-y-5" novalidate>
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <x-forms.input name="customer_name" label="Họ và tên" type="text" :value="old('customer_name')" />
                        <x-forms.input name="customer_email" label="Email" type="email" :value="old('customer_email')" />
                        <x-forms.input name="customer_phone" label="Số điện thoại" type="text" :value="old('customer_phone')" />
                        <div></div>
                    </div>

                    <div>
                        <label for="customer_address" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Địa chỉ giao hàng</label>
                        <textarea id="customer_address" name="customer_address" rows="4" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white">{{ old('customer_address') }}</textarea>
                    </div>

                    <div>
                        <label for="notes" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ghi chú</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white">{{ old('notes') }}</textarea>
                    </div>

                    <div class="rounded-2xl border border-stone-200 bg-stone-50 p-4 text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300">
                        <div class="flex items-start gap-3">
                            <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-brand-500"></span>
                            <p>
                                Thanh toán sẽ tạo đơn hàng và chuyển sang cổng VNPay. Nếu chưa cấu hình `VNPAY_TMN_CODE` hoặc `VNPAY_HASH_SECRET` trong `.env`, hệ thống sẽ báo lỗi và giữ đơn hàng ở trạng thái chưa thanh toán.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="rounded-2xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white hover:bg-brand-700">
                            Thanh toán VNPay
                        </button>
                        <a href="{{ route('shop.cart.index') }}" class="rounded-2xl border border-stone-200 px-5 py-3 text-sm font-medium text-gray-700 hover:border-brand-300 hover:text-brand-700 dark:border-gray-700 dark:text-gray-200">
                            Quay lại giỏ hàng
                        </a>
                    </div>
                </form>
            </div>

            <aside class="space-y-6">
                <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Đơn hàng tạm tính</h2>
                    <div class="mt-5 space-y-4">
                        @foreach ($items as $item)
                            <div class="flex items-center justify-between gap-4 text-sm">
                                <div>
                                    <div class="font-medium text-gray-950 dark:text-white">{{ $item['product']->name }}</div>
                                    <div class="text-gray-500 dark:text-gray-400">x {{ $item['quantity'] }}</div>
                                </div>
                                <div class="font-semibold text-gray-950 dark:text-white">{{ number_format((float) $item['line_total'], 0) }} VND</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 border-t border-stone-200 pt-4 text-sm dark:border-gray-800">
                        <div class="flex items-center justify-between font-semibold text-gray-950 dark:text-white">
                            <span>Tạm tính</span>
                            <span>{{ number_format((float) $subtotal, 0) }} VND</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
