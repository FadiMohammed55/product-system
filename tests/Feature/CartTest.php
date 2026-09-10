<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_cart(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)
            ->get('/cart');

        $response->assertOk();
    }

    public function test_customer_can_add_product_to_cart(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create();

        $response = $this->actingAs($customer)
            ->post("/cart/{$product->id}");

        $response->assertRedirect('/products');

        $response->assertSessionHas('cart', [
            $product->id => 1,
        ]);
    }

    public function test_adding_same_product_increases_quantity(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create();

        $this->actingAs($customer)
            ->post("/cart/{$product->id}");

        $this->actingAs($customer)
            ->post("/cart/{$product->id}");

        $this->assertEquals(
            2,
            session('cart')[$product->id]
        );
    }

    public function test_customer_can_update_product_quantity(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create();

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $product->id => 1,
                ],
            ])
            ->patch("/cart/{$product->id}", [
                'quantity' => 5,
            ]);

        $response->assertRedirect('/cart');

        $response->assertSessionHas('cart', [
            $product->id => 5,
        ]);
    }

    public function test_customer_cannot_update_invalid_quantity(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create();

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $product->id => 1,
                ],
            ])
            ->patch("/cart/{$product->id}", [
                'quantity' => 0,
            ]);

        $response->assertSessionHasErrors([
            'quantity',
        ]);
    }

    public function test_customer_can_remove_product_from_cart(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create();

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $product->id => 2,
                ],
            ])
            ->delete("/cart/{$product->id}");

        $response->assertRedirect('/cart');

        $response->assertSessionHas('cart', []);
    }

    public function test_guest_cannot_access_cart(): void
    {
        $response = $this->get('/cart');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_add_product_to_cart(): void
    {
        $product = Product::factory()->create();

        $response = $this->post("/cart/{$product->id}");

        $response->assertRedirect('/login');
    }
}