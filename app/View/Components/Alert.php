<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public string $type;

    public string $message;

    /**
     * @var array<int, string>
     */
    public array $messages;

    /**
     * Khởi tạo đối tượng Alert component.
     * Nhận vào kiểu thông báo (type - success, error, info,...) và thông điệp đơn (message) hoặc danh sách thông điệp (messages).
     */
    public function __construct(string $type = 'info', $message = '', $messages = [])
    {
        $this->type = $type;
        $this->message = (string) ($message ?? '');

        if (is_string($messages)) {
            $messages = [$messages];
        }

        // Lọc bỏ các phần tử trống trong mảng
        $this->messages = array_values(array_filter(
            is_array($messages) ? $messages : [],
            fn ($item) => filled($item)
        ));
    }

    /**
     * Kiểm tra xem có thông báo nào cần hiển thị hay không.
     */
    public function hasMessages(): bool
    {
        return $this->message !== '' || $this->messages !== [];
    }

    /**
     * Trả về Blade view tương ứng để vẽ giao diện component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alert');
    }
}
