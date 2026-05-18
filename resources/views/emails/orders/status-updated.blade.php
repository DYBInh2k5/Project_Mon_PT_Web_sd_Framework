<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #1f2937;">
    <h2 style="margin-bottom: 12px;">Order status updated</h2>

    <p>Hello {{ $order->customer_name }},</p>

    <p>
        Your order <strong>{{ $order->order_number }}</strong> has been updated.
    </p>

    <p>
        Previous status:
        <strong>{{ ucfirst($previousStatus) }}</strong>
        <br>
        New status:
        <strong>{{ ucfirst($order->status) }}</strong>
    </p>

    <p>
        Total amount:
        <strong>${{ number_format((float) $order->total_amount, 2) }}</strong>
    </p>

    <p>Thank you for shopping with us.</p>
</div>
