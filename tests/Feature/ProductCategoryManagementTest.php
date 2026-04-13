<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_view_product_categories_page(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);

        $this->actingAs($editor)
            ->get('/product-categories')
            ->assertOk()
            ->assertSee('Product Categories');
    }

    public function test_viewer_is_forbidden_from_product_categories_page(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);

        $this->actingAs($viewer)
            ->get('/product-categories')
            ->assertForbidden();
    }

    public function test_editor_can_create_a_product_category(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);

        $this->actingAs($editor)
            ->post('/product-categories', [
                'name' => 'Gaming Chairs',
                'slug' => 'gaming-chairs',
                'description' => 'Seats and ergonomic accessories for gamers.',
                'is_active' => '1',
            ])
            ->assertRedirect('/product-categories');

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Gaming Chairs',
            'slug' => 'gaming-chairs',
            'created_by' => $editor->id,
        ]);
    }
}
