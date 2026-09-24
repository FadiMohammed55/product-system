<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_guest_can_view_registration_page(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_customer_can_create_an_account(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'newcustomer@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('users', [
            'name' => 'Test Customer',
            'email' => 'newcustomer@test.com',
            'role' => 'customer',
        ]);
    }

    public function test_customer_cannot_register_with_existing_email(): void
    {
        User::factory()->create([
            'email' => 'existing@test.com',
        ]);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Another Customer',
            'email' => 'existing@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');

        $response->assertSessionHasErrors('email');
    }

    public function test_customer_cannot_register_with_unconfirmed_password(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Test Customer',
            'email' => 'customer1@test.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertRedirect('/register');

        $response->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', [
            'email' => 'customer1@test.com'
        ]);
    }
}
