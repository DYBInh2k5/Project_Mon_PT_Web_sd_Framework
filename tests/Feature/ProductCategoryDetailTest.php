<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_filter_categories_by_status_and_search(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        ProductCategory::factory()->create(['name' => 'Gaming Gear', 'slug' => 'gaming-gear', 'is_active' => true]);
        ProductCategory::factory()->create(['name' => 'Hidden Office', 'slug' => 'hidden-office', 'is_active' => false]);

        $this->actingAs($editor)
            ->get('/product-categories?status=active&search=Gaming')
            ->assertOk()
            ->assertSee('Gaming Gear')
            ->assertDontSee('Hidden Office');
    }

    public function test_editor_can_view_category_detail_page(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $category = ProductCategory::factory()->create(['name' => 'Audio']);
        Product::factory()->create([
            'product_category_id' => $category->id,
            'name' => 'Studio Headphones',
        ]);

        $this->actingAs($editor)
            ->get("/product-categories/{$category->id}")
            ->assertOk()
            ->assertSee('Audio')
            ->assertSee('Studio Headphones');
    }
}
