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
    /**
     * Hiển thị danh sách các đơn hàng.
     */
    public function index(Request $request): View
    {
        // Khởi tạo query gốc để tính toán các số liệu thống kê.
        $baseQuery = Order::query();

        // Các điều kiện tìm kiếm và bộ lọc được chuyển vào local scope trong Model Order
        // giúp Controller gọn hơn và dễ tái sử dụng hơn.
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
            'title' => 'Đơn hàng',
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

    /**
     * Hiển thị chi tiết một đơn hàng.
     */
    public function show(Order $order): View
    {
        // Eager load các sản phẩm trong đơn hàng và lịch sử thay đổi trạng thái kèm người thực hiện để tránh lỗi N+1 query.
        $order->load(['items.product', 'statusHistories.changer']);

        return view('orders.show', [
            'title' => 'Chi tiết đơn hàng',
            'order' => $order,
            'statuses' => Order::STATUSES,
        ]);
    }

    /**
     * Cập nhật trạng thái của đơn hàng.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, OrderService $orders): RedirectResponse
    {
        $newStatus = $request->validated('status');

        // Controller chỉ nhận request và gọi nghiệp vụ từ OrderService.
        // Nghiệp vụ cập nhật trạng thái, ghi lịch sử và phát Event nằm trọn vẹn trong OrderService.
        $history = $orders->updateStatus(
            $order,
            $newStatus,
            $request->user(),
            'Manual status update from order detail page.'
        );

        if (! $history) {
            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Trạng thái đơn hàng hiện tại đã được cập nhật trước đó.');
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Cập nhật trạng thái đơn hàng và gửi email thông báo thành công.');
    }
}
