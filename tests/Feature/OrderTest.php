<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_their_orders(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        Order::factory()->create([
            'user_id' => $customer->id,
        ]);

        $response = $this->actingAs($customer)
            ->get('/orders');

        $response->assertOk();
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $customerOne = User::factory()->create([
            'role' => 'customer',
        ]);

        $customerTwo = User::factory()->create([
            'role' => 'customer',
        ]);

        $order = Order::factory()->create([
            'user_id' => $customerTwo->id,
        ]);

        $response = $this->actingAs($customerOne)
            ->get("/orders/{$order->id}");

        $response->assertNotFound();
    }

    public function test_guest_cannot_view_orders(): void
    {
        $response = $this->get('/orders');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_view_order_details(): void
    {
        $order = Order::factory()->create();

        $response = $this->get("/orders/{$order->id}");

        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_all_orders(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Order::factory()->count(3)->create();

        $response = $this->actingAs($admin)
            ->get('/admin/orders');

        $response->assertOk();
    }

    public function test_admin_can_view_order_details(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::factory()->create();

        $order = Order::factory()->create();

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => $product->price,
        ]);

        $response = $this->actingAs($admin)
            ->get("/admin/orders/{$order->id}");

        $response->assertOk();
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $order = Order::factory()->create([
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->patch("/admin/orders/{$order->id}/status", [
                'status' => 'processing',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }

    public function test_admin_cannot_update_order_with_invalid_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $order = Order::factory()->create([
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->patch("/admin/orders/{$order->id}/status", [
                'status' => 'invalid-status',
            ]);

        $response->assertSessionHasErrors([
            'status',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    public function test_customer_cannot_update_order_status(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($customer)
            ->patch("/admin/orders/{$order->id}/status", [
                'status' => 'completed',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    public function test_customer_cannot_access_admin_orders(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)
            ->get('/admin/orders');

        $response->assertStatus(403);
    }
}