<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Đây là khu vực bảo mật của ứng dụng. Vui lòng xác nhận mật khẩu trước khi tiếp tục.') }}
    </div>

    @if ($errors->any())
        <x-package-alert
            class="mb-4"
            type="danger"
            message="Xác nhận mật khẩu không thành công. Vui lòng thử lại."
            :messages="$errors->all()"
        />
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Mật khẩu')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Xác nhận') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
