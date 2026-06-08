<?php

namespace Tests\Feature;

use App\Models\Product;
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
}
