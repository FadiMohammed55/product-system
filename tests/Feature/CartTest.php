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

    public function test_cart_removes_products_that_no_longer_exist(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create([
            'name' => 'Deleted Product',
            'price' => 100,
            'currency' => 'USD',
        ]);

        $productId = $product->id;

        // Add the product to the cart.
        $this->withSession([
            'cart' => [
                $productId => 2,
            ],
        ]);

        // Delete the product after it was added to the cart.
        $product->delete();

        $response = $this->actingAs($customer)
            ->get('/cart');

        $response->assertOk();

        $response->assertSessionHas(
            'error',
            'Some products in your cart are no longer available.'
        );

        // The deleted product should no longer exist in the cart.
        $cart = session('cart', []);

        $this->assertArrayNotHasKey($productId, $cart);
    }

    public function test_cart_handles_unsupported_product_currency(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create([
            'name' => 'Unsupported Currency Product',
            'price' => 100,
            'currency' => 'GBP',
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $product->id => 1,
                ],
            ])
            ->get('/cart');

        $response->assertRedirect('/cart');

        $response->assertSessionHas(
            'error',
            'One or more products have an unsupported currency'
        );
    }

    public function test_cart_calculates_total_for_mixed_currencies(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $usdProduct = Product::factory()->create([
            'name' => 'USD Product',
            'price' => 100,
            'currency' => 'USD',
        ]);

        $eurProduct = Product::factory()->create([
            'name' => 'EUR Product',
            'price' => 100,
            'currency' => 'EUR',
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $usdProduct->id => 1,
                    $eurProduct->id => 2,
                ],
            ])
            ->get('/cart');

        $response->assertOk();

        // 100 USD + (100 EUR × 1.15 × 2) = 330 USD
        $response->assertSee('330.00');
        $response->assertSee('USD');
    }
}