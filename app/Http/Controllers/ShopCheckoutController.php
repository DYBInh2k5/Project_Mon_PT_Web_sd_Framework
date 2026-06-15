<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ShoppingCartService;
use App\Services\VnpayPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopCheckoutController extends Controller
{
    public function create(ShoppingCartService $cart): RedirectResponse|View
    {
        // Chi cho phep vao checkout khi gio hang con du lieu hop le.
        $items = $this->resolveItems($cart);

        if ($items === []) {
            return redirect()->route('shop.index')->withErrors(['cart' => 'Giỏ hàng đang trống.']);
        }

        return view('shop.checkout', [
            'title' => 'Thanh toán VNPay',
            'items' => $items,
            'subtotal' => collect($items)->sum(fn (array $item) => $item['line_total']),
        ]);
    }

    public function store(Request $request, ShoppingCartService $cart, VnpayPaymentService $vnpay): RedirectResponse
    {
        // Loc lai gio hang ngay truoc khi tao don de tranh san pham da bi an hoac het hang.
        $items = $this->resolveItems($cart);

        if ($items === []) {
            return redirect()->route('shop.index')->withErrors(['cart' => 'Giỏ hàng đang trống.']);
        }

        // Validate thong tin khach hang truoc khi sinh don va tao URL thanh toan VNPay.
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_address' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $order = DB::transaction(function () use ($validated, $items): Order {
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => 'vnpay',
                'total_amount' => collect($items)->sum(fn (array $item) => $item['line_total']),
                'placed_at' => now(),
            ]);

            foreach ($items as $item) {
                /** @var Product $product */
                $product = $item['product'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'line_total' => $item['line_total'],
                ]);
            }

            return $order;
        });

        session()->put('shop.checkout.pending_order', $order->order_number);

        $paymentUrl = $vnpay->createPaymentUrl(
            $order->refresh(),
            (string) $request->ip(),
            route('shop.checkout.return'),
            route('shop.checkout.ipn')
        );

        return redirect()->away($paymentUrl);
    }

    /**
     * Xử lý kết quả trả về của VNPay qua trình duyệt (returnUrl).
     *
     * Nhiệm vụ chính của returnUrl là hiển thị kết quả giao dịch trực quan cho khách hàng (Frontend).
     * Không nên hoàn toàn dựa vào returnUrl để cập nhật trạng thái đơn hàng trong database,
     * vì khách hàng có thể tắt trình duyệt giữa chừng trước khi redirect về lại trang shop.
     */
    public function vnpayReturn(Request $request, VnpayPaymentService $vnpay, OrderService $orders): View
    {
        // Trích xuất các tham số dạng vnp_* từ URL query string.
        $payload = $this->extractVnpPayload($request);
        
        // Tìm đơn hàng tương ứng dựa trên mã giao dịch (vnp_TxnRef).
        $order = $this->findOrderFromPayload($payload);
        $isSuccess = false;
        $message = 'Chưa xác nhận được giao dịch VNPay.';

        // Bước 1: Kiểm tra chữ ký an toàn (checksum) để đảm bảo dữ liệu không bị sửa đổi trên đường đi.
        if ($order && $vnpay->verifySignature($payload)) {
            // Bước 2: Lấy số tiền thực tế khách đã thanh toán từ tham số VNPay.
            $amount = $vnpay->paymentAmountToVnd($payload);
            $responseCode = (string) ($payload['vnp_ResponseCode'] ?? '');
            $transactionStatus = (string) ($payload['vnp_TransactionStatus'] ?? '');

            // Bước 3: So khớp số tiền thanh toán từ VNPay với tổng số tiền của đơn hàng trong hệ thống.
            // Bước 4: Kiểm tra mã phản hồi vnp_ResponseCode (00 là thành công).
            if ($responseCode === '00' && $transactionStatus === '00' && $amount === (int) round((float) $order->total_amount)) {
                // Đánh dấu đơn hàng đã thanh toán và chuyển đổi trạng thái đơn hàng.
                $this->markOrderPaid($order, $payload, $orders);
                
                // Xóa giỏ hàng và đơn hàng tạm sau khi thanh toán thành công.
                session()->forget('shop.cart');
                session()->forget('shop.checkout.pending_order');
                $isSuccess = true;
                $message = 'Giao dịch được thực hiện thành công.';
            } else {
                $message = 'Giao dịch chưa thành công hoặc số tiền không khớp.';
            }
        } elseif (! $order) {
            $message = 'Không tìm thấy đơn hàng để đối soát.';
        } else {
            $message = 'Chữ ký VNPay không hợp lệ.';
        }

        return view('shop.payment-result', [
            'title' => $isSuccess ? 'Thanh toán thành công' : 'Thanh toán chưa thành công',
            'order' => $order,
            'isSuccess' => $isSuccess,
            'gateway' => 'VNPay',
            'message' => $message,
            'payload' => $payload,
        ]);
    }

    /**
     * Xử lý truy vấn bất đồng bộ (IPN) từ server VNPay (ipnUrl).
     *
     * Nhiệm vụ chính của IPN là đối soát server-to-server tự động, hoạt động ngầm (Backend).
     * Dù khách hàng có tắt trình duyệt thì VNPay vẫn gửi request IPN này tới server của shop.
     * Đây là nơi quan trọng nhất để cập nhật trạng thái thanh toán đơn hàng một cách an toàn và tin cậy.
     */
    public function ipn(Request $request, VnpayPaymentService $vnpay, OrderService $orders): JsonResponse
    {
        $payload = $this->extractVnpPayload($request);
        $order = $this->findOrderFromPayload($payload);

        // Quy trình kiểm tra bảo mật bắt buộc của VNPay IPN:
        // 1. Kiểm tra chữ ký checksum (signature)
        if (! $vnpay->verifySignature($payload)) {
            return response()->json([
                'RspCode' => '97',
                'Message' => 'Chữ ký không hợp lệ',
            ]);
        }

        // 2. Tìm kiếm đơn hàng trong cơ sở dữ liệu dựa trên vnp_TxnRef
        if (! $order) {
            return response()->json([
                'RspCode' => '01',
                'Message' => 'Không tìm thấy đơn hàng',
            ]);
        }

        // 3. So khớp số tiền thanh toán thực tế với số tiền của đơn hàng
        $amount = $vnpay->paymentAmountToVnd($payload);
        if ($amount !== (int) round((float) $order->total_amount)) {
            return response()->json([
                'RspCode' => '04',
                'Message' => 'Số tiền không khớp',
            ]);
        }

        $responseCode = (string) ($payload['vnp_ResponseCode'] ?? '');
        $transactionStatus = (string) ($payload['vnp_TransactionStatus'] ?? '');

        // 4. Kiểm tra mã trạng thái giao dịch
        if ($responseCode === '00' && $transactionStatus === '00') {
            // 5. Kiểm tra xem đơn hàng đã được cập nhật trạng thái thanh toán trước đó chưa (tránh trùng lặp)
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'RspCode' => '02',
                    'Message' => 'Đơn hàng đã được xác nhận',
                ]);
            }

            // Ghi nhận thanh toán và cập nhật trạng thái đơn hàng sang processing
            $this->markOrderPaid($order, $payload, $orders);

            return response()->json([
                'RspCode' => '00',
                'Message' => 'Xác nhận thành công',
            ]);
        }

        return response()->json([
            'RspCode' => '00',
            'Message' => 'Giao dịch không thành công',
        ]);
    }

    private function extractVnpPayload(Request $request): array
    {
        return collect($request->query())
            ->filter(fn ($value, string $key) => str_starts_with($key, 'vnp_'))
            ->all();
    }

    private function findOrderFromPayload(array $payload): ?Order
    {
        $txnRef = $payload['vnp_TxnRef'] ?? null;

        if (! $txnRef) {
            return null;
        }

        return Order::query()
            ->with('items')
            ->where('order_number', $txnRef)
            ->first();
    }

    private function markOrderPaid(Order $order, array $payload, OrderService $orders): void
    {
        if ($order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => 'vnpay',
                'transaction_code' => (string) ($payload['vnp_TransactionNo'] ?? $payload['vnp_TxnRef'] ?? Str::upper(Str::random(10))),
                'paid_at' => now(),
            ]);
        }

        if ($order->status === 'pending') {
            $orders->updateStatus(
                $order->refresh(),
                'processing',
                null,
                'Auto updated after successful VNPay payment.'
            );
        }
    }

    private function resolveItems(ShoppingCartService $cart): array
    {
        // Chi giu cac san pham con active va con ton tai trong database.
        $items = [];
        $products = Product::query()
            ->with('category')
            ->whereIn('id', $cart->productIds())
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        foreach ($cart->all() as $productId => $row) {
            $product = $products->get((int) $productId);

            if (! $product) {
                continue;
            }

            $quantity = min((int) ($row['quantity'] ?? 1), max(1, (int) $product->stock));

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'line_total' => round(((float) $product->price) * $quantity),
            ];
        }

        return $items;
    }

    private function generateOrderNumber(): string
    {
        return 'WEB-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
    }
}
