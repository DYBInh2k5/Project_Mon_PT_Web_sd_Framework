<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $baseQuery = Order::query();

        // Cac dieu kien search/filter duoc dua vao local scope trong Order model
        // de controller gon hon va co the tai su dung cho API/dashboard sau nay.
        $orders = Order::query()
            ->withCount('items')
            ->search($request->string('search')->toString())
            ->status($request->string('status')->toString())
            ->placedFrom($request->date('date_from'))
            ->placedUntil($request->date('date_to'))
            ->orderByDesc('placed_at')
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', [
            'title' => 'Orders',
            'orders' => $orders,
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to']),
            'statuses' => Order::STATUSES,
            'summary' => [
                'total' => (clone $baseQuery)->count(),
                'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                'processing' => (clone $baseQuery)->where('status', 'processing')->count(),
                'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
                'revenue' => (float) (clone $baseQuery)->selectRaw('COALESCE(SUM(total_amount), 0) as value')->value('value'),
            ],
        ]);
    }

    public function show(Order $order): View
    {
        // Load san san pham trong don va nguoi da doi status de tranh N+1 query tren view.
        $order->load(['items.product', 'statusHistories.changer']);

        return view('orders.show', [
            'title' => 'Order Detail',
            'order' => $order,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, OrderService $orders): RedirectResponse
    {
        $newStatus = $request->validated('status');

        // Controller chi nhan request va goi service.
        // Nghiep vu cap nhat status, ghi history va phat event nam trong OrderService.
        $history = $orders->updateStatus(
            $order,
            $newStatus,
            $request->user(),
            'Manual status update from order detail page.'
        );

        if (! $history) {
            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order status is already up to date.');
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order status updated and notification email sent successfully.');
    }
}
