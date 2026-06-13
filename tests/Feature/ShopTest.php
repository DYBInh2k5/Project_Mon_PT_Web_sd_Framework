<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_home_page_is_displayed(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_product_can_be_added_to_cart(): void
    {
        $product = Product::factory()->create([
            'is_active' => true,
            'stock' => 5,
            'price' => 1000,
        ]);

        $this
            ->post(route('shop.cart.store', $product), [
                'quantity' => 2,
            ])
            ->assertRedirect();

        $this->assertSame(2, session('shop.cart.'.$product->id.'.quantity'));
    }

    public function test_product_detail_page_is_displayed(): void
    {
        $product = Product::factory()->create([
            'is_active' => true,
        ]);

        $this->get(route('shop.products.show', $product))->assertOk();
    }

    public function test_cart_page_requires_items_before_checkout(): void
    {
        $product = Product::factory()->create([
            'is_active' => true,
            'stock' => 5,
            'price' => 1000,
        ]);

        $this->post(route('shop.cart.store', $product), [
            'quantity' => 1,
        ]);

        $this->get(route('shop.cart.index'))->assertOk();
        $this->get(route('shop.checkout.create'))->assertOk();
    }

    public function test_checkout_creates_order_and_redirects_to_vnpay(): void
    {
        config()->set('services.vnpay.tmn_code', 'DEMO1234');
        config()->set('services.vnpay.hash_secret', 'secret-demo');
        config()->set('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        config()->set('services.vnpay.version', '2.1.0');
        config()->set('services.vnpay.locale', 'vn');
        config()->set('services.vnpay.order_type', 'other');
        config()->set('services.vnpay.bank_code', 'VNPAYQR');
        config()->set('services.vnpay.expire_minutes', 15);

        $product = Product::factory()->create([
            'is_active' => true,
            'stock' => 5,
            'price' => 1000,
        ]);

        $response = $this->withSession([
            'shop.cart.'.$product->id => [
                'quantity' => 2,
            ],
        ])
            ->post(route('shop.checkout.store'), [
                'customer_name' => 'Nguyen Van A',
                'customer_email' => 'customer@example.com',
                'customer_phone' => '0900000000',
                'customer_address' => 'Ha Noi',
                'notes' => null,
            ])
            ->assertRedirect();

        $order = Order::query()->latest('id')->first();

        $this->assertNotNull($order);
        $this->assertSame('vnpay_qr', $order->payment_method);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertStringStartsWith(
            'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?',
            $response->headers->get('Location')
        );
    }
}
