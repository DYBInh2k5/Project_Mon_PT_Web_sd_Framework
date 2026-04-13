<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/users')
            ->assertOk()
            ->assertSee('Users');
    }

    public function test_editor_is_forbidden_from_users_page(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);

        $this->actingAs($editor)
            ->get('/users')
            ->assertForbidden();
    }

    public function test_admin_can_update_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create(['role' => 'viewer']);

        $this->actingAs($admin)
            ->put("/users/{$targetUser->id}", [
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'role' => 'editor',
            ])
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'role' => 'editor',
        ]);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post('/users', [
                'name' => 'Demo User',
                'email' => 'demo-user@example.com',
                'role' => 'viewer',
                'password' => 'password123',
            ])
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'email' => 'demo-user@example.com',
            'role' => 'viewer',
        ]);
    }

    public function test_admin_can_view_user_detail_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create(['role' => 'viewer']);

        $this->actingAs($admin)
            ->get("/users/{$targetUser->id}")
            ->assertOk()
            ->assertSee($targetUser->email);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create(['role' => 'viewer']);

        $this->actingAs($admin)
            ->delete("/users/{$targetUser->id}")
            ->assertRedirect('/users');

        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }
}
