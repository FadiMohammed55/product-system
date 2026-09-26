<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        User::factory()->count(3)->create(['role' => 'customer']);

        $response = $this->actingAs($admin)
            ->get('/admin/users');

        $response->assertOk();
        $response->assertSee('Users');
    }

    public function test_customer_cannot_access_users_management(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)
            ->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_users_management(): void
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_search_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        User::factory()->create([
            'name' => 'John Customer',
            'email' => 'john@example.com',
            'role' => 'customer',
        ]);

        User::factory()->create([
            'name' => 'Jane Customer',
            'email' => 'jane@example.com',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/users?search=john');

        $response->assertOk();
        $response->assertSee('John Customer');
        $response->assertDontSee('Jane Customer');
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        User::factory()->count(2)->create(['role' => 'customer']);

        $response = $this->actingAs($admin)
            ->get('/admin/users?role=customer');

        $response->assertOk();
        $response->assertSee('Customer');
    }

    public function test_admin_can_update_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($admin)
            ->put("/admin/users/{$user->id}", [
                'name' => 'New Name',
                'email' => 'new@example.com',
                'role' => 'customer',
            ]);

        $response->assertRedirect("/admin/users/{$user->id}");

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_admin_cannot_remove_own_admin_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->put("/admin/users/{$admin->id}", [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'customer',
            ]);

        $response->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
        ]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->delete("/admin/users/{$admin->id}");

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('error', 'You cannot delete your own account.');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_admin_cannot_delete_last_administrator(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $secondAdmin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->delete("/admin/users/{$secondAdmin->id}");

        $response->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'id' => $secondAdmin->id,
        ]);
    }

    public function test_admin_can_delete_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin)
            ->delete("/admin/users/{$customer->id}");

        $response->assertRedirect('/admin/users');

        $this->assertDatabaseMissing('users', [
            'id' => $customer->id,
        ]);
    }
}
