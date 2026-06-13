<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class VnpayPaymentService
{
    public function createPaymentUrl(Order $order, string $ipAddress, ?string $returnUrl = null, ?string $ipnUrl = null): string
    {
        $tmnCode = config('services.vnpay.tmn_code');
        $hashSecret = config('services.vnpay.hash_secret');
        $endpoint = config('services.vnpay.url');

        if (! $tmnCode || ! $hashSecret || ! $endpoint) {
            throw new RuntimeException('Chua cau hinh thong tin VNPay.');
        }

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
            'vnp_OrderInfo' => $this->sanitizeOrderInfo("Thanh toan don hang {$txnRef}"),
            'vnp_OrderType' => config('services.vnpay.order_type', 'other'),
            'vnp_ReturnUrl' => $returnUrl ?: route('shop.checkout.return'),
            'vnp_TxnRef' => $txnRef,
            'vnp_ExpireDate' => Carbon::createFromFormat('YmdHis', $createdAt, 'Asia/Ho_Chi_Minh')
                ->addMinutes($expireMinutes)
                ->format('YmdHis'),
            'vnp_BankCode' => config('services.vnpay.bank_code', 'VNPAYQR'),
        ];

        if ($ipnUrl) {
            $data['vnp_IpnUrl'] = $ipnUrl;
        }

        ksort($data);

        $query = http_build_query($data, '', '&', PHP_QUERY_RFC3986);
        $hashData = $this->buildHashData($data);
        $secureHash = hash_hmac('sha512', $hashData, $hashSecret);

        return $endpoint.'?'.$query.'&vnp_SecureHash='.$secureHash;
    }

    public function verifySignature(array $input): bool
    {
        $hashSecret = config('services.vnpay.hash_secret');

        if (! $hashSecret) {
            throw new RuntimeException('Chua cau hinh hash secret cua VNPay.');
        }

        $secureHash = $input['vnp_SecureHash'] ?? '';
        unset($input['vnp_SecureHash'], $input['vnp_SecureHashType']);
        ksort($input);

        return hash_hmac('sha512', $this->buildHashData($input), $hashSecret) === $secureHash;
    }

    public function paymentAmountToVnd(array $input): ?int
    {
        if (! isset($input['vnp_Amount'])) {
            return null;
        }

        return (int) $input['vnp_Amount'] / 100;
    }

    public function sanitizeOrderInfo(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->replaceMatches('/[^A-Za-z0-9 _\-:.]/', '')
            ->squish()
            ->toString();
    }

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
