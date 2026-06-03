<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessOrderPaymentRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderPaymentController extends Controller
{
    public function create(Order $order): View
    {
        return view('orders.payment', [
            'title' => 'Order Payment',
            'order' => $order,
            'paymentMethods' => [
                'credit_card' => 'Credit Card',
                'bank_transfer' => 'Bank Transfer',
                'e_wallet' => 'E-Wallet',
            ],
        ]);
    }

    public function store(ProcessOrderPaymentRequest $request, Order $order, OrderService $orders): RedirectResponse
    {
        if ($order->payment_status === 'paid') {
            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Don hang nay da duoc thanh toan truoc do.');
        }

        $validated = $request->validated();

        // Man payment chi la demo: sau khi thanh toan, luu thong tin giao dich vao order.
        $order->update([
            'payment_status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'transaction_code' => 'TXN-'.Str::upper(Str::random(10)),
            'paid_at' => now(),
            'customer_email' => $validated['customer_email'],
        ]);

        // Neu don moi thanh toan van dang pending, dua sang processing bang OrderService
        // de van co history va event gui mail giong thao tac doi status thu cong.
        if ($order->status === 'pending') {
            $orders->updateStatus(
                $order->refresh(),
                'processing',
                $request->user(),
                'Auto updated after successful demo payment.'
            );
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Thanh toan don hang thanh cong. He thong da cap nhat trang thai thanh toan.');
    }
}
