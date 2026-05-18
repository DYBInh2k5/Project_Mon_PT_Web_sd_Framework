<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $baseQuery = Order::query();

        $orders = Order::withCount('items')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($nested) use ($search) {
                    $nested->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->toString());
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('placed_at', '>=', $request->date('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('placed_at', '<=', $request->date('date_to'));
            })
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
        $order->load(['items.product']);

        return view('orders.show', [
            'title' => 'Order Detail',
            'order' => $order,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $previousStatus = $order->status;
        $newStatus = $request->validated('status');

        if ($previousStatus === $newStatus) {
            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order status is already up to date.');
        }

        $order->update([
            'status' => $newStatus,
        ]);

        Mail::to($order->customer_email)->send(new OrderStatusUpdatedMail($order, $previousStatus));

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order status updated and notification email sent successfully.');
    }
}
