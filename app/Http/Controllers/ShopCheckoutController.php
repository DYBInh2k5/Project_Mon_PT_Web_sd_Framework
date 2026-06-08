<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\MomoPaymentService;
use App\Services\OrderService;
use App\Services\ShoppingCartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ShopCheckoutController extends Controller
{
    public function create(ShoppingCartService $cart): RedirectResponse|View
    {
        $items = $this->resolveItems($cart);

        if ($items === []) {
            return redirect()->route('shop.index')->withErrors(['cart' => 'Gio hang dang trong.']);
        }

        return view('shop.checkout', [
            'title' => 'Checkout',
            'items' => $items,
            'subtotal' => collect($items)->sum(fn (array $item) => $item['line_total']),
        ]);
    }

    public function store(Request $request, ShoppingCartService $cart, MomoPaymentService $momo, OrderService $orders): RedirectResponse
    {
        $items = $this->resolveItems($cart);

        if ($items === []) {
            return redirect()->route('shop.index')->withErrors(['cart' => 'Gio hang dang trong.']);
        }

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
                'payment_method' => 'momo_wallet',
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

        try {
            $response = $momo->createPayment($order, $items, [
                'name' => $validated['customer_name'],
                'email' => $validated['customer_email'],
                'phone' => $validated['customer_phone'],
            ]);
        } catch (\Throwable $exception) {
            $order->delete();

            report($exception);

            return back()
                ->withInput()
                ->withErrors(['payment' => 'Khong tao duoc giao dich MoMo. Hay kiem tra cau hinh sandbox.']);
        }

        session()->put('shop.checkout.pending_order', $order->order_number);

        return redirect()->away($response['payUrl']);
    }

    public function callback(Request $request, OrderService $orders): View
    {
        $payload = $request->all();
        $order = Order::where('order_number', $request->string('orderId')->toString())->first();

        $verified = $order && app(MomoPaymentService::class)->verifyNotification($payload);
        $isSuccess = $verified && (int) $request->integer('resultCode') === 0 && $order;

        if ($isSuccess) {
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => 'momo_wallet',
                'transaction_code' => 'MOMO-'.$request->input('transId'),
                'paid_at' => Carbon::createFromTimestampMs((int) $request->input('responseTime')),
            ]);

            if ($order->status === 'pending') {
                $orders->updateStatus(
                    $order->refresh(),
                    'processing',
                    null,
                    'Auto updated after successful MoMo payment.'
                );
            }

            $cart = app(ShoppingCartService::class);
            $cart->clear();
        }

        return view('shop.payment-result', [
            'title' => 'Payment Result',
            'order' => $order,
            'verified' => $verified,
            'isSuccess' => $isSuccess,
        ]);
    }

    public function ipn(Request $request, OrderService $orders): Response|JsonResponse
    {
        $payload = $request->all();
        $order = Order::where('order_number', $request->string('orderId')->toString())->first();

        if (! $order || ! app(MomoPaymentService::class)->verifyNotification($payload)) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        if ((int) $request->integer('resultCode') === 0) {
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => 'momo_wallet',
                'transaction_code' => 'MOMO-'.$request->input('transId'),
                'paid_at' => Carbon::createFromTimestampMs((int) $request->input('responseTime')),
            ]);

            if ($order->status === 'pending') {
                $orders->updateStatus(
                    $order->refresh(),
                    'processing',
                    null,
                    'Auto updated after successful MoMo IPN.',
                );
            }
        }

        return response()->noContent();
    }

    private function resolveItems(ShoppingCartService $cart): array
    {
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
