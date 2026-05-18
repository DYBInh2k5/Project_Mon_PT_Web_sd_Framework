<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessOrderPaymentRequest;
use App\Models\Order;
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

    public function store(ProcessOrderPaymentRequest $request, Order $order): RedirectResponse
    {
        if ($order->payment_status === 'paid') {
            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Don hang nay da duoc thanh toan truoc do.');
        }

        $validated = $request->validated();

        $order->update([
            'payment_status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'transaction_code' => 'TXN-'.Str::upper(Str::random(10)),
            'paid_at' => now(),
            'customer_email' => $validated['customer_email'],
            'status' => $order->status === 'pending' ? 'processing' : $order->status,
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Thanh toan don hang thanh cong. He thong da cap nhat trang thai thanh toan.');
    }
}
