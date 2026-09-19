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

    public function test_checkout_converts_mixed_currencies_to_usd(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $usdProduct = Product::factory()->create([
            'name' => 'USD Product',
            'price' => 100,
            'currency' => 'USD'
        ]);

        $eurProduct = Product::factory()->create([
            'name' => 'EUR Product',
            'price' => 100,
            'currency' => 'EUR'
        ]);

        $ilsProduct = Product::factory()->create([
            'name' => 'ILS Product',
            'price' => 100,
            'currency' => 'ILS'
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $usdProduct->id => 2,
                    $eurProduct->id => 1,
                    $ilsProduct->id => 3,
                ],
            ])
            ->post('/checkout');

        $response->assertRedirect();

        $order = Order::where('user_id', $customer->id)->first();

        $this->assertNotNull($order);

        $this->assertSame('USD', $order->currency);

        $this->assertEquals(414.00, $order->total);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $usdProduct->id,
            'product_name' => 'USD Product',
            'quantity' => 2,
            'price' => 100,
            'currency' => 'USD',
            'converted_price' => 100,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $eurProduct->id,
            'product_name' => 'EUR Product',
            'quantity' => 1,
            'price' => 100,
            'currency' => 'EUR',
            'converted_price' => 115,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $ilsProduct->id,
            'product_name' => 'ILS Product',
            'quantity' => 3,
            'price' => 100,
            'currency' => 'ILS',
            'converted_price' => 33,
        ]);
    }

    public function test_order_keeps_product_snapshot_after_product_is_updated(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer'
        ]);

        $product = Product::factory()->create([
            'name' => 'Original Product',
            'price' => 100,
            'currency' => 'EUR'
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $product->id => 2,
                ],
            ])
            ->post('/checkout');

        $response->assertRedirect();

        $order = Order::where('user_id', $customer->id)->first();

        $this->assertNotNull($order);

        $product->update([
            'name' => 'Updated Product',
            'price' => 200,
            'currency' => 'USD',
        ]);

        $orderItem = $order->items()->first();

        $this->assertNotNull($orderItem);
        $this->assertSame('Original Product', $orderItem->product_name);
        $this->assertEquals(100, $orderItem->price);
        $this->assertSame('EUR', $orderItem->currency);
        $this->assertEquals(115, $orderItem->converted_price);
        $this->assertEquals(2, $orderItem->quantity);

        $this->assertEquals(230, $order->total);
        $this->assertSame('USD', $order->currency);
    }

    public function test_order_item_keeps_snapshot_after_product_is_deleted(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer'
        ]);

        $product = Product::factory()->create([
            'name' => 'Deleted Product',
            'price' => 100,
            'currency' => 'EUR',
        ]);

        $this->assertNotNull($product->id);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $product->id => 2,
                ],
            ])
            ->post('/checkout');

        $response->assertRedirect();

        $order = Order::where('user_id', $customer->id)->first();

        $this->assertNotNull($order);

        $orderItem = $order->items()->first();

        $this->assertNotNull($orderItem);

        $productId = $product->id;

        $product->delete();

        $this->assertDatabaseMissing('products', [
            'id' => $productId,
        ]);

        $orderItem = $order->items()->first();

        $this->assertNotNull($orderItem);

        $this->assertNull($orderItem->product_id);

        $this->assertSame('Deleted Product', $orderItem->product_name);
        $this->assertEquals(100, $orderItem->price);
        $this->assertSame('EUR', $orderItem->currency);
        $this->assertEquals(115, $orderItem->converted_price);
        $this->assertEquals(2, $orderItem->quantity);

        $this->assertEquals(230, $order->total);
        $this->assertSame('USD', $order->currency);
    }

    public function test_customer_can_view_order_with_deleted_product(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create([
            'name' => 'Deleted Product',
            'price' => 100,
            'currency' => 'EUR',
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $product->id => 2,
                ],
            ])
            ->post('/checkout');

        $response->assertRedirect();

        $order = Order::where('user_id', $customer->id)->first();

        $this->assertNotNull($order);

        $product->delete();

        $response = $this->actingAs($customer)
            ->get("/orders/{$order->id}");

        $response->assertOk();

        $response->assertSee('Deleted Product');
        $response->assertSee('Product no longer available');
        $response->assertSee('100.00');
        $response->assertSee('EUR');
        $response->assertSee('115.00');
        $response->assertSee('USD');
        $response->assertSee('230');
    }

    public function test_admin_can_view_order_with_deleted_product(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create([
            'name' => 'Deleted Product',
            'price' => 100,
            'currency' => 'EUR',
        ]);

        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $product->id => 2,
                ],
            ])
            ->post('/checkout');

        $response->assertRedirect();

        $order = Order::where('user_id', $customer->id)->first();

        $this->assertNotNull($order);

        $product->delete();

        $response = $this->actingAs($admin)
            ->get("/admin/orders/{$order->id}");

        $response->assertOk();

        $response->assertSee('Deleted Product');
        $response->assertSee('Product no longer available');
        $response->assertSee('100.00');
        $response->assertSee('EUR');
        $response->assertSee('115.00');
        $response->assertSee('USD');
        $response->assertSee('230.00');
    }

    public function test_checkout_fails_when_product_in_cart_no_longer_exists(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create([
            'price' => 100,
            'currency' => 'USD',
        ]);

        $productId = $product->id;

        // Product exists when it is added to the cart
        $this->assertDatabaseHas('products', [
            'id' => $productId,
        ]);

        // Product is deleted before checkout
        $product->delete();

        $this->assertDatabaseMissing('products', [
            'id' => $productId,
        ]);

        // Customer still has the deleted product in the cart
        $response = $this->actingAs($customer)
            ->withSession([
                'cart' => [
                    $productId => 2,
                ],
            ])
            ->post('/checkout');

        // Checkout should return the customer to the cart
        $response->assertRedirect('/cart');

        // The correct error should be shown
        $response->assertSessionHas(
            'error',
            'Some products in your cart are no longer available.'
        );

        // No order should have been created
        $this->assertDatabaseCount('orders', 0);

        // The cart should still contain the deleted product
        $this->assertNotEmpty(session('cart'));

        $this->assertSame(
            2,
            session('cart')[$productId]
        );
    }
}