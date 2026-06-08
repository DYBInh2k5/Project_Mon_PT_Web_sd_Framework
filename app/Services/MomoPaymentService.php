<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MomoPaymentService
{
    public function createPayment(Order $order, array $items, array $customer): array
    {
        $baseUrl = rtrim(config('services.momo.base_url', 'https://test-payment.momo.vn'), '/');
        $partnerCode = config('services.momo.partner_code');
        $accessKey = config('services.momo.access_key');
        $secretKey = config('services.momo.secret_key');

        if (! $partnerCode || ! $accessKey || ! $secretKey) {
            throw new RuntimeException('MoMo sandbox chua duoc cau hinh.');
        }

        $requestId = (string) Str::uuid();
        $orderId = $order->order_number;
        $amount = (int) round((float) $order->total_amount);
        $redirectUrl = route('shop.checkout.return');
        $ipnUrl = route('shop.checkout.ipn');
        $orderInfo = "Thanh toan don hang {$orderId} tai MonPT Shop";
        $extraData = base64_encode(json_encode([
            'customer_email' => $customer['email'] ?? null,
            'customer_phone' => $customer['phone'] ?? null,
        ], JSON_UNESCAPED_UNICODE));

        $payload = [
            'partnerCode' => $partnerCode,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'requestType' => 'captureWallet',
            'extraData' => $extraData,
            'items' => array_map(function (array $item): array {
                return [
                    'id' => (string) $item['product']->id,
                    'name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                    'amount' => (int) round((float) $item['product']->price),
                    'image' => $item['product']->imageUrl() ? url($item['product']->imageUrl()) : null,
                ];
            }, $items),
            'userInfo' => array_filter([
                'name' => $customer['name'] ?? null,
                'phoneNumber' => $customer['phone'] ?? null,
                'email' => $customer['email'] ?? null,
            ]),
            'lang' => 'vi',
        ];

        $rawSignature = $this->buildRequestSignature($payload, $accessKey);
        $payload['signature'] = hash_hmac('sha256', $rawSignature, $secretKey);

        $response = Http::timeout(30)
            ->acceptJson()
            ->post("{$baseUrl}/v2/gateway/api/create", $payload);

        if (! $response->successful()) {
            throw new RuntimeException('MoMo tra ve HTTP '.$response->status().'.');
        }

        $body = $response->json();

        if ((int) Arr::get($body, 'resultCode', -1) !== 0) {
            throw new RuntimeException(Arr::get($body, 'message', 'MoMo khong tao duoc giao dich.'));
        }

        return $body;
    }

    public function verifyNotification(array $payload): bool
    {
        $accessKey = config('services.momo.access_key');
        $secretKey = config('services.momo.secret_key');

        if (! $accessKey || ! $secretKey) {
            return false;
        }

        $expected = $this->buildResponseSignature($payload, $accessKey);
        $signature = (string) Arr::get($payload, 'signature', '');

        return hash_equals(hash_hmac('sha256', $expected, $secretKey), $signature);
    }

    private function buildRequestSignature(array $payload, string $accessKey): string
    {
        return implode('&', [
            'accessKey='.$accessKey,
            'amount='.(string) Arr::get($payload, 'amount'),
            'extraData='.(string) Arr::get($payload, 'extraData', ''),
            'ipnUrl='.(string) Arr::get($payload, 'ipnUrl'),
            'orderId='.(string) Arr::get($payload, 'orderId'),
            'orderInfo='.(string) Arr::get($payload, 'orderInfo'),
            'partnerCode='.(string) Arr::get($payload, 'partnerCode'),
            'redirectUrl='.(string) Arr::get($payload, 'redirectUrl'),
            'requestId='.(string) Arr::get($payload, 'requestId'),
            'requestType='.(string) Arr::get($payload, 'requestType'),
        ]);
    }

    private function buildResponseSignature(array $payload, string $accessKey): string
    {
        return implode('&', [
            'accessKey='.$accessKey,
            'amount='.(string) Arr::get($payload, 'amount'),
            'extraData='.(string) Arr::get($payload, 'extraData', ''),
            'message='.(string) Arr::get($payload, 'message'),
            'orderId='.(string) Arr::get($payload, 'orderId'),
            'orderInfo='.(string) Arr::get($payload, 'orderInfo'),
            'orderType='.(string) Arr::get($payload, 'orderType', 'momo_wallet'),
            'partnerCode='.(string) Arr::get($payload, 'partnerCode'),
            'payType='.(string) Arr::get($payload, 'payType'),
            'requestId='.(string) Arr::get($payload, 'requestId'),
            'responseTime='.(string) Arr::get($payload, 'responseTime'),
            'resultCode='.(string) Arr::get($payload, 'resultCode'),
            'transId='.(string) Arr::get($payload, 'transId'),
        ]);
    }
}
