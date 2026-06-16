@extends('layouts.app')

@section('content')
    <div
        x-data="chatApp({
            initialMessages: @js($messages),
            sendUrl: '{{ route('chat.send') }}'
        })"
        class="flex h-[calc(100vh-120px)] flex-col overflow-hidden"
    >
        <div class="rounded-t-xl border border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Sales Coach AI
                    </h2>

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        AI tư vấn bán hàng và hỗ trợ khách hàng
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('support-chat.clear') }}">
                        @csrf
                        <button
                            type="submit"
                            class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-medium text-gray-600 transition hover:border-brand-300 hover:text-brand-600 dark:border-gray-700 dark:text-gray-300"
                        >
                            Clear
                        </button>
                    </form>

                    <div class="rounded-full bg-green-100 px-3 py-1 text-xs text-green-700">
                        Online
                    </div>
                </div>
            </div>
        </div>

        <div
            id="messages"
            x-ref="messages"
            class="flex-1 space-y-6 overflow-y-auto border-x border-gray-200 bg-gray-50 p-6 dark:border-gray-800 dark:bg-gray-950"
        >
            <template x-for="message in allMessages" :key="message.id">
                <div
                    class="flex"
                    :class="message.role === 'user' ? 'justify-end' : 'justify-start'"
                >
                    <div
                        class="max-w-4xl whitespace-pre-wrap rounded-2xl px-5 py-4 text-sm leading-7 shadow-sm"
                        :class="message.role === 'user'
                            ? 'bg-brand-500 text-white'
                            : 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white'"
                        x-text="message.content"
                    ></div>
                </div>
            </template>

            <div x-show="loading" class="flex justify-start">
                <div class="rounded-2xl bg-white px-5 py-4 shadow-sm dark:bg-gray-800">
                    <div class="flex gap-2">
                        <span class="h-2 w-2 animate-bounce rounded-full bg-gray-400"></span>
                        <span class="h-2 w-2 animate-bounce rounded-full bg-gray-400 [animation-delay:150ms]"></span>
                        <span class="h-2 w-2 animate-bounce rounded-full bg-gray-400 [animation-delay:300ms]"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-b-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <form @submit.prevent="sendMessage" class="flex gap-3">
                <textarea
                    x-model="prompt"
                    rows="2"
                    placeholder="Nhập câu hỏi..."
                    class="flex-1 resize-none rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                ></textarea>

                <button
                    type="submit"
                    :disabled="loading || !prompt.trim()"
                    class="rounded-xl bg-brand-500 px-6 py-3 font-medium text-white transition hover:bg-brand-600 disabled:opacity-50"
                >
                    Gửi
                </button>
            </form>
        </div>
    </div>

    <script>
        function chatApp({ initialMessages, sendUrl }) {
            const fallbackMessages = [
                {
                    id: 1,
                    role: 'assistant',
                    content: 'Xin chào, tôi là Sales Coach AI. Tôi có thể giúp gì cho bạn?'
                }
            ];

            return {
                prompt: '',
                loading: false,
                allMessages: (initialMessages && initialMessages.length ? initialMessages : fallbackMessages)
                    .map((message, index) => ({
                        id: message.id || index + 1,
                        role: message.role === 'assistant' ? 'assistant' : message.role,
                        content: message.content || ''
                    })),

                async sendMessage() {
                    if (! this.prompt.trim()) {
                        return;
                    }

                    const question = this.prompt;

                    this.allMessages.push({
                        id: Date.now(),
                        role: 'user',
                        content: question
                    });

                    this.prompt = '';
                    this.loading = true;
                    this.scrollBottom();

                    try {
                        const response = await fetch(sendUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify({
                                message: question
                            })
                        });

                        const data = await response.json();

                        this.allMessages.push({
                            id: Date.now() + 1,
                            role: 'assistant',
                            content: data.message || data.error || 'Chatbot chưa trả về nội dung.'
                        });
                    } catch (e) {
                        this.allMessages.push({
                            id: Date.now() + 2,
                            role: 'assistant',
                            content: e.message || 'Đã có lỗi xảy ra. Vui lòng thử lại.'
                        });
                    } finally {
                        this.loading = false;

                        this.$nextTick(() => {
                            this.scrollBottom();
                        });
                    }
                },

                scrollBottom() {
                    this.$nextTick(() => {
                        this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
                    });
                }
            };
        }
    </script>
@endsection
