<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_checkout_cart(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create([
            'price' => 100,
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $product->id => 2,
                ],
            ])
            ->post('/checkout');

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'total' => 200,
            'status' => 'pending',
        ]);

        $order = Order::where('user_id', $customer->id)->first();

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100,
        ]);
    }

    public function test_checkout_calculates_total_correctly(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $productOne = Product::factory()->create([
            'price' => 100,
        ]);

        $productTwo = Product::factory()->create([
            'price' => 50,
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $productOne->id => 2,
                    $productTwo->id => 3,
                ],
            ])
            ->post('/checkout');

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'total' => 350,
        ]);
    }

    public function test_cart_is_cleared_after_successful_checkout(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create([
            'price' => 100,
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $product->id => 2,
                ],
            ])
            ->post('/checkout');

        $response->assertRedirect();

        $response->assertSessionMissing('cart');
    }

    public function test_checkout_fails_when_cart_is_empty(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [],
            ])
            ->post('/checkout');

        $response->assertRedirect('/cart');

        $response->assertSessionHas('error', 'Your cart is empty');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_guest_cannot_checkout(): void
    {
        $response = $this->post('/checkout');

        $response->assertRedirect('/login');

        $this->assertDatabaseCount('orders', 0);
    }

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

    public function test_customer_can_view_their_order_details(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $customer->id,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => $product->price,
        ]);

        $response = $this->actingAs($customer)
            ->get("/orders/{$order->id}");

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
}