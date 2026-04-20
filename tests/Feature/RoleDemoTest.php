<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDemoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_demo_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/role-demo/admin')
            ->assertOk()
            ->assertSee('Admin Area');
    }

    public function test_editor_is_forbidden_from_admin_demo_page(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);

        $this->actingAs($editor)
            ->get('/role-demo/admin')
            ->assertForbidden();
    }

    public function test_user_can_access_user_demo_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get('/role-demo/user')
            ->assertOk()
            ->assertSee('User Area');
    }
}
