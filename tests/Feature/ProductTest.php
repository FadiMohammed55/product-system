<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_products(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Product::factory()->count(3)->create();

        $response = $this->actingAs($admin)
            ->get('/admin/products');

        $response->assertOk();
    }

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::factory()->create();

        $respnose = $this->actingAs($admin)
            ->post('/admin/products', [
                'name' => 'iPhone 15',
                'price' => 999.99,
                'currency' => 'USD',
                'rating' => 4.5,
                'category_id' => $category->id,
            ]);

        $respnose->assertRedirect('/admin/products');

        $this->assertDatabaseHas('products', [
            'name' => 'iPhone 15',
            'price' => 999.99,
            'currency' => 'USD',
            'rating' => 4.5,
            'category_id' => $category->id,
        ]);
    }

    public function test_product_creation_fails_with_invalid_data(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post('/admin/products', [
                'name' => '',
                'price' => -100,
                'currency' => 'X',
                'rating' => 10,
                'category_id' => 99999,
            ]);

        $response->assertSessionHasErrors([
            'name',
            'price',
            'currency',
            'rating',
            'category_id',
        ]);
    }

    public function test_admin_can_update_product(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Old Product',
            'price' => 100,
        ]);

        $response = $this->actingAs($admin)
            ->put("/admin/products/{$product->id}", [
                'name' => 'Updated Product',
                'price' => 250,
                'currency' => 'USD',
                'rating' => 4.5,
                'category_id' => $category->id,
            ]);

        $response->assertRedirect('/admin/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 250,
            'currency' => 'USD',
            'rating' => 4.5,
            'category_id' => $category->id,
        ]);
    }

    public function test_admin_can_delete_product(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::factory()->create();

        $response = $this->actingAs($admin)
            ->delete("/admin/products/{$product->id}");

        $response->assertRedirect('/admin/products');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_customer_cannot_create_product(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $category = Category::factory()->create();

        $response = $this->actingAs($customer)
            ->post('/admin/products', [
                'name' => 'Unauthorized Product',
                'price' => 100,
                'currency' => 'USD',
                'rating' => 4.5,
                'category_id' => $category->id,
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('products', [
            'name' => 'Unauthorized Product',
        ]);
    }

    public function test_customer_cannot_update_product(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create([
            'name' => 'Original Product',
            'price' => 100,
        ]);

        $response = $this->actingAs($customer)
            ->put("/admin/products/{$product->id}", [
                'name' => 'Hacked Product',
                'price' => 999,
                'currency' => 'USD',
                'rating' => 5,
                'category_id' => $product->category_id,
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Original Product',
            'price' => 100,
        ]);
    }

    public function test_customer_cannot_delete_product(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create();

        $response = $this->actingAs($customer)
            ->delete("/admin/products/{$product->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }
}
