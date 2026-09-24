<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_login_with_correct_credentials(): void
    {
        $customer = User::factory()->create([
            'email' => 'customer@test.com',
            'password' => 'password123',
            'role' => 'customer'
        ]);

        $response = $this->post('/login', [
            'email' => 'customer@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirectToRoute('products.index');

        $this->assertAuthenticatedAs($customer);
    }

    public function test_admin_can_login_with_correct_credentials(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirectToRoute('admin.dashboard');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_user_cannot_login_with_incorrect_password(): void
    {
        User::factory()->create([
            'email' => 'customer@test.com',
            'password' => 'password123',
            'role' => 'customer',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'customer@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)
            ->post('/logout');

        $response->assertRedirect('/login');

        $this->assertGuest();
    }
}
