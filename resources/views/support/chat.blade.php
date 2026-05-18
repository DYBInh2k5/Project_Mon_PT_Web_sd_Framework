@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Customer Support Chatbot" />

    <div class="space-y-6">
        @session('success')
            <x-ui.alert variant="success">
                {{ $value }}
            </x-ui.alert>
        @endsession

        @if ($errors->any())
            <x-package-alert
                type="danger"
                message="Khong the gui cau hoi cho chatbot."
                :messages="$errors->all()"
            />
        @endif

        <section class="page-toolbar">
            <div class="space-y-4">
                <span class="toolbar-chip">Customer assistance</span>
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h2 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">A built-in support chatbot for quick customer answers.</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-7 text-gray-600 dark:text-gray-300">
                            Bot này hỗ trợ tra cứu trạng thái đơn hàng, giải thích quy trình giao hàng, hủy đơn và mail thông báo ngay trong giao diện quản trị.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('orders.index') }}" class="action-button">Open Orders</a>
                        <form method="POST" action="{{ route('support-chat.clear') }}">
                            @csrf
                            <button type="submit" class="action-button">Clear chat</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <section class="surface-panel p-6 xl:col-span-2" x-data x-init="$nextTick(() => { $refs.chatWindow.scrollTop = $refs.chatWindow.scrollHeight })">
                <div class="flex items-center justify-between gap-4 border-b border-gray-200 pb-4 dark:border-gray-800">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Conversation</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nhập câu hỏi ngắn gọn hoặc dùng prompt mẫu để bot trả lời nhanh.</p>
                    </div>
                    <span class="metric-pill">{{ count($messages) }} messages</span>
                </div>

                <div x-ref="chatWindow" class="mt-6 max-h-[620px] space-y-4 overflow-y-auto pr-2">
                    @foreach ($messages as $message)
                        <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="{{ $message['role'] === 'user' ? 'max-w-xl rounded-2xl rounded-br-sm bg-brand-600 px-5 py-4 text-white' : 'max-w-2xl rounded-2xl rounded-bl-sm border border-gray-200 bg-gray-50 px-5 py-4 text-gray-800 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-100' }}">
                                <p class="text-sm leading-7">{{ $message['content'] }}</p>

                                @if (! empty($message['suggestions']))
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach ($message['suggestions'] as $suggestion)
                                            <form method="POST" action="{{ route('support-chat.store') }}">
                                                @csrf
                                                <input type="hidden" name="message" value="{{ $suggestion }}">
                                                <button
                                                    type="submit"
                                                    class="{{ $message['role'] === 'user' ? 'rounded-full border border-white/25 px-3 py-1 text-xs font-medium text-white/90 transition hover:bg-white/10' : 'rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-600 transition hover:border-brand-200 hover:text-brand-600 dark:border-gray-700 dark:text-gray-300 dark:hover:text-brand-400' }}"
                                                >
                                                    {{ $suggestion }}
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('support-chat.store') }}" class="mt-6 space-y-4" novalidate>
                    @csrf
                    <div>
                        <label for="message" class="mb-2.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Your question</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="4"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            placeholder="Ví dụ: Kiểm tra đơn ORD-00023 hoặc Khách muốn hủy đơn thì xử lý sao?"
                        >{{ old('message') }}</textarea>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="action-button-primary">Send question</button>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Bot đọc dữ liệu đơn hàng thật khi bạn nhập đúng mã đơn.</span>
                    </div>
                </form>
            </section>

            <aside class="space-y-6">
                <section class="surface-panel p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick prompts</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Dùng các câu hỏi mẫu để demo nhanh trên lớp.</p>

                    <div class="mt-5 space-y-3">
                        @foreach ($quickPrompts as $prompt)
                            <form method="POST" action="{{ route('support-chat.store') }}">
                                @csrf
                                <input type="hidden" name="message" value="{{ $prompt }}">
                                <button type="submit" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-left text-sm font-medium text-gray-700 transition hover:border-brand-200 hover:text-brand-600 dark:border-gray-800 dark:text-gray-300 dark:hover:text-brand-400">
                                    {{ $prompt }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </section>

                <section class="surface-panel p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">What the chatbot can do</h3>
                    <ul class="mt-4 space-y-3 text-sm leading-7 text-gray-600 dark:text-gray-300">
                        <li>Tra cứu trạng thái đơn hàng theo mã thật trong database.</li>
                        <li>Giải thích quy trình giao hàng, hủy đơn và cập nhật trạng thái.</li>
                        <li>Hướng dẫn nhân viên mở đúng màn Orders, Products hoặc Categories.</li>
                        <li>Giải thích việc gửi mail thông báo khi đổi trạng thái đơn hàng.</li>
                    </ul>
                </section>
            </aside>
        </div>
    </div>
@endsection
