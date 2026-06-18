<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class VnpayPaymentService
{
    /**
     * Tạo URL thanh toán VNPay để chuyển hướng khách hàng.
     *
     * @param  Order  $order  Đối tượng đơn hàng cần thanh toán
     * @param  string  $ipAddress  Địa chỉ IP của người dùng thực hiện giao dịch
     * @param  string|null  $returnUrl  URL nhận phản hồi từ VNPay hiển thị cho khách hàng
     * @param  string|null  $ipnUrl  URL nhận phản hồi từ VNPay để xử lý cập nhật đơn hàng phía backend
     * @return string URL hoàn chỉnh để redirect sang cổng VNPay
     */
    public function createPaymentUrl(Order $order, string $ipAddress, ?string $returnUrl = null, ?string $ipnUrl = null): string
    {
        $tmnCode = config('services.vnpay.tmn_code');
        $hashSecret = config('services.vnpay.hash_secret');
        $endpoint = config('services.vnpay.url');

        if (! $tmnCode || ! $hashSecret || ! $endpoint) {
            throw new RuntimeException('Chưa cấu hình thông tin VNPay trong file .env hoặc config/services.php.');
        }

        // VNPay quy định số tiền thanh toán nhân thêm 100 để loại bỏ số thập phân.
        $amount = (int) round(((float) $order->total_amount) * 100);
        $txnRef = (string) $order->order_number;
        $createdAt = now('Asia/Ho_Chi_Minh')->format('YmdHis');
        $expireMinutes = (int) config('services.vnpay.expire_minutes', 15);

        $data = [
            'vnp_Version' => config('services.vnpay.version', '2.1.0'),
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => $tmnCode,
            'vnp_Amount' => $amount,
            'vnp_CreateDate' => $createdAt,
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $ipAddress,
            'vnp_Locale' => config('services.vnpay.locale', 'vn'),
            // Loại bỏ ký tự đặc biệt, dấu tiếng Việt để tránh lỗi truyền nhận dữ liệu.
            'vnp_OrderInfo' => $this->sanitizeOrderInfo("Thanh toan don hang {$txnRef}"),
            'vnp_OrderType' => config('services.vnpay.order_type', 'other'),
            'vnp_ReturnUrl' => $returnUrl ?: route('shop.checkout.return'),
            'vnp_TxnRef' => $txnRef,
            // Thiết lập thời gian hết hạn thanh toán (mặc định là 15 phút).
            'vnp_ExpireDate' => Carbon::createFromFormat('YmdHis', $createdAt, 'Asia/Ho_Chi_Minh')
                ->addMinutes($expireMinutes)
                ->format('YmdHis'),
        ];

        $bankCode = config('services.vnpay.bank_code');
        if (is_string($bankCode) && $bankCode !== '') {
            $data['vnp_BankCode'] = $bankCode;
        }



        // Bước 1: Sắp xếp mảng tham số theo thứ tự alphabet của key (bắt buộc theo quy định VNPay).
        ksort($data);

        // Bước 2: Tạo chuỗi truy vấn (query string).
        $query = http_build_query($data, '', '&', PHP_QUERY_RFC1738);
        $hashData = $this->buildHashData($data);
        
        // Bước 3: Tạo chữ ký bảo mật secureHash bằng thuật toán HMAC SHA512.
        $secureHash = hash_hmac('sha512', $hashData, $hashSecret);

        // Trả về URL thanh toán hoàn chỉnh.
        return $endpoint.'?'.$query.'&vnp_SecureHash='.$secureHash;
    }

    /**
     * Xác minh chữ ký phản hồi từ VNPay để đảm bảo an toàn bảo mật.
     */
    public function verifySignature(array $input): bool
    {
        $hashSecret = config('services.vnpay.hash_secret');

        if (! $hashSecret) {
            throw new RuntimeException('Chưa cấu hình mã bảo mật (hash secret) của VNPay.');
        }

        $secureHash = $input['vnp_SecureHash'] ?? '';
        
        // Loại bỏ secureHash ra khỏi mảng dữ liệu trước khi sắp xếp và tính toán mã băm.
        unset($input['vnp_SecureHash'], $input['vnp_SecureHashType']);
        ksort($input);

        // So sánh mã secureHash của VNPay gửi về với mã băm hệ thống tự tính toán dựa trên dữ liệu nhận được.
        return hash_hmac('sha512', $this->buildHashData($input), $hashSecret) === $secureHash;
    }

    /**
     * Chuyển đổi số tiền phản hồi từ VNPay về giá trị thực tế bằng VND (VNPay chia 100).
     */
    public function paymentAmountToVnd(array $input): ?int
    {
        if (! isset($input['vnp_Amount'])) {
            return null;
        }

        return (int) $input['vnp_Amount'] / 100;
    }

    /**
     * Chuẩn hóa và làm sạch chuỗi thông tin đơn hàng để tránh lỗi dữ liệu VNPay.
     */
    public function sanitizeOrderInfo(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->replaceMatches('/[^A-Za-z0-9 _\-:.]/', '')
            ->squish()
            ->toString();
    }

    /**
     * Xây dựng chuỗi hash data thô từ các cặp key=value được sắp xếp.
     */
    private function buildHashData(array $data): string
    {
        $pairs = [];

        foreach ($data as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (! str_starts_with((string) $key, 'vnp_')) {
                continue;
            }

            $pairs[] = urlencode((string) $key).'='.urlencode((string) $value);
        }

        return implode('&', $pairs);
    }
}
