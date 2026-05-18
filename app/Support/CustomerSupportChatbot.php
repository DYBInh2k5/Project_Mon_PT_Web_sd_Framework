<?php

namespace App\Support;

use App\Models\Order;
use App\Models\ProductCategory;
use Illuminate\Support\Str;

class CustomerSupportChatbot
{
    public function respond(string $message): array
    {
        $normalized = Str::lower(trim($message));

        if ($normalized === '') {
            return $this->fallback();
        }

        if ($orderResponse = $this->handleOrderLookup($message)) {
            return $orderResponse;
        }

        if ($this->containsAny($normalized, ['don hang', 'order', 'trang thai'])) {
            return [
                'message' => 'Bạn có thể gửi mã đơn hàng như ORD-00023 để mình kiểm tra trạng thái. Nếu chưa có mã đơn, bạn vào mục Orders để xem danh sách và chi tiết đơn hàng.',
                'suggestions' => [
                    'Kiểm tra đơn ORD-00023',
                    'Đơn hàng đang xử lý bao lâu?',
                    'Làm sao cập nhật trạng thái đơn hàng?',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['giao hang', 'van chuyen', 'ship'])) {
            return [
                'message' => 'Với đơn hàng đang ở trạng thái shipped, hệ thống hiểu là đơn đã bàn giao cho vận chuyển. Bạn có thể theo dõi và cập nhật tiếp sang completed khi khách xác nhận đã nhận hàng.',
                'suggestions' => [
                    'Khi nào nên chuyển sang completed?',
                    'Làm sao tìm đơn theo ngày?',
                    'Kiểm tra đơn ORD-00023',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['huy', 'cancel', 'tra hang', 'doi tra'])) {
            return [
                'message' => 'Nếu khách muốn hủy đơn, bạn có thể mở chi tiết đơn hàng và đổi trạng thái sang cancelled. Trước khi đổi, nên xác nhận lại tình trạng giao hàng để tránh hủy nhầm đơn đã giao.',
                'suggestions' => [
                    'Đổi trạng thái đơn hàng như thế nào?',
                    'Kiểm tra đơn ORD-00023',
                    'Gửi mail khi hủy đơn có tự động không?',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['thanh toan', 'payment', 'chuyen khoan'])) {
            return [
                'message' => 'Hệ thống hiện đã có màn thanh toán online dạng demo cho từng đơn hàng. Bạn có thể mở Order Detail, bấm Open checkout và hoàn tất thanh toán để cập nhật payment status, payment method và transaction code.',
                'suggestions' => [
                    'Cách mở checkout cho đơn hàng',
                    'Kiểm tra đơn ORD-00023',
                    'Khi đổi trạng thái có gửi mail không?',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['san pham', 'danh muc', 'catalog'])) {
            $categories = ProductCategory::query()
                ->where('is_active', true)
                ->limit(3)
                ->pluck('name')
                ->all();

            $categoryList = $categories === []
                ? 'Hiện tại hệ thống đang có khu quản lý sản phẩm và danh mục riêng.'
                : 'Một vài danh mục đang hoạt động là: '.implode(', ', $categories).'.';

            return [
                'message' => $categoryList.' Bạn có thể vào Product Catalog để xem, thêm hoặc chỉnh sửa sản phẩm và danh mục.',
                'suggestions' => [
                    'Cách thêm sản phẩm mới',
                    'Kiểm tra tồn kho sản phẩm',
                    'Quản lý danh mục ở đâu?',
                ],
            ];
        }

        if ($this->containsAny($normalized, ['email', 'mail', 'gui thu'])) {
            return [
                'message' => 'Khi cập nhật trạng thái đơn hàng ở màn Order Detail, hệ thống sẽ gửi mail thông báo cho khách qua mailer hiện tại. Trong môi trường demo này, mail đang được ghi vào log để kiểm tra dễ hơn.',
                'suggestions' => [
                    'Khi nào mail được gửi?',
                    'Kiểm tra đơn ORD-00023',
                    'Mở quản lý đơn hàng',
                ],
            ];
        }

        return $this->fallback();
    }

    protected function handleOrderLookup(string $message): ?array
    {
        preg_match('/ORD-\d{5}/i', $message, $matches);
        $orderNumber = $matches[0] ?? null;

        if (! $orderNumber) {
            return null;
        }

        $order = Order::query()
            ->where('order_number', Str::upper($orderNumber))
            ->first();

        if (! $order) {
            return [
                'message' => 'Mình chưa tìm thấy mã đơn '.$orderNumber.'. Bạn thử kiểm tra lại mã đơn hoặc vào danh sách Orders để đối chiếu.',
                'suggestions' => [
                    'Mở danh sách Orders',
                    'Tìm đơn theo ngày',
                    'Đơn hàng đang xử lý bao lâu?',
                ],
            ];
        }

        return [
            'message' => 'Đơn '.$order->order_number.' của '.$order->customer_name.' hiện ở trạng thái '.Str::headline($order->status).', tổng tiền $'.number_format((float) $order->total_amount, 2).', đặt lúc '.$order->placed_at?->format('d/m/Y H:i').'.',
            'suggestions' => [
                'Cập nhật trạng thái đơn hàng',
                'Khi nào nên chuyển sang completed?',
                'Gửi mail cho khách khi đổi trạng thái',
            ],
        ];
    }

    protected function containsAny(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (Str::contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }

    protected function fallback(): array
    {
        return [
            'message' => 'Mình có thể hỗ trợ về trạng thái đơn hàng, vận chuyển, hủy đơn, danh mục sản phẩm và mail thông báo. Bạn thử hỏi ngắn gọn hơn hoặc gửi kèm mã đơn như ORD-00023 nhé.',
            'suggestions' => [
                'Kiểm tra đơn ORD-00023',
                'Làm sao cập nhật trạng thái đơn hàng?',
                'Mail thông báo hoạt động thế nào?',
            ],
        ];
    }
}
