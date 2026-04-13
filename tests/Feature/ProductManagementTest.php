<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_view_products_page(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);

        $this->actingAs($editor)
            ->get('/products')
            ->assertOk()
            ->assertSee('Products');
    }

    public function test_editor_can_create_product(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $category = ProductCategory::factory()->create();

        Storage::fake('public');

        $this->actingAs($editor)
            ->post('/products', [
                'product_category_id' => $category->id,
                'name' => 'Wireless Headset',
                'slug' => 'wireless-headset',
                'sku' => 'WH-001',
                'price' => '199.90',
                'stock' => 12,
                'description' => 'Audio gear for gaming and office calls.',
                'image' => UploadedFile::fake()->image('headset.jpg'),
                'is_active' => '1',
            ])
            ->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'name' => 'Wireless Headset',
            'sku' => 'WH-001',
            'created_by' => $editor->id,
        ]);
    }

    public function test_editor_can_filter_products_by_category_and_status(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $laptopCategory = ProductCategory::factory()->create(['name' => 'Laptop']);
        $officeCategory = ProductCategory::factory()->create(['name' => 'Office']);

        \App\Models\Product::factory()->create([
            'product_category_id' => $laptopCategory->id,
            'name' => 'Laptop Pro 15',
            'is_active' => true,
        ]);

        \App\Models\Product::factory()->create([
            'product_category_id' => $officeCategory->id,
            'name' => 'Office Mouse',
            'is_active' => false,
        ]);

        $this->actingAs($editor)
            ->get('/products?category='.$laptopCategory->id.'&status=active&search=Laptop')
            ->assertOk()
            ->assertSee('Laptop Pro 15')
            ->assertDontSee('Office Mouse');
    }

    public function test_editor_can_view_product_detail_page(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $category = ProductCategory::factory()->create();
        $product = \App\Models\Product::factory()->create([
            'product_category_id' => $category->id,
            'name' => 'Detail Product',
        ]);

        $this->actingAs($editor)
            ->get("/products/{$product->id}")
            ->assertOk()
            ->assertSee('Detail Product');
    }
}
