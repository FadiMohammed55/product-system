<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_search_products_by_name(): void
    {
        $admin = $this->createAdmin();

        Product::factory()->create([
            'name' => 'Gaming Laptop',
        ]);

        Product::factory()->create([
            'name' => 'Wireless Mouse',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/products?search=Laptop');

        $response->assertOk();
        $response->assertSee('Gaming Laptop');
        $response->assertDontSee('Wireless Mouse');
    }

    public function test_admin_can_filter_products_by_category(): void
    {
        $admin = $this->createAdmin();

        $phones = Category::factory()->create([
            'name' => 'Phones',
        ]);

        $laptops = Category::factory()->create([
            'name' => 'Laptops',
        ]);

        Product::factory()->create([
            'name' => 'Test Phone',
            'category_id' => $phones->id,
        ]);

        Product::factory()->create([
            'name' => 'Test Laptop',
            'category_id' => $laptops->id,
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/products?category_id=' . $phones->id);

        $response->assertOk();
        $response->assertSee('Test Phone');
        $response->assertDontSee('Test Laptop');
    }

    public function test_admin_can_filter_products_by_currency(): void
    {
        $admin = $this->createAdmin();

        Product::factory()->create([
            'name' => 'USD Product',
            'currency' => 'USD',
        ]);

        Product::factory()->create([
            'name' => 'EUR Product',
            'currency' => 'EUR',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/products?currency=USD');

        $response->assertOk();
        $response->assertSee('USD Product');
        $response->assertDontSee('EUR Product');
    }

    public function test_admin_can_filter_products_by_stock_status(): void
    {
        $admin = $this->createAdmin();

        Product::factory()->create([
            'name' => 'Empty Stock Product',
            'stock' => 0,
        ]);

        Product::factory()->create([
            'name' => 'Low Stock Product',
            'stock' => 3,
        ]);

        Product::factory()->create([
            'name' => 'Available Stock Product',
            'stock' => 10,
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/products?stock=low');

        $response->assertOk();
        $response->assertSee('Low Stock Product');
        $response->assertDontSee('Empty Stock Product');
        $response->assertDontSee('Available Stock Product');
    }

    public function test_admin_can_combine_product_filters(): void
    {
        $admin = $this->createAdmin();

        $category = Category::factory()->create();

        Product::factory()->create([
            'name' => 'Matching Phone',
            'category_id' => $category->id,
            'currency' => 'USD',
            'stock' => 3,
        ]);

        Product::factory()->create([
            'name' => 'Matching Phone EUR',
            'category_id' => $category->id,
            'currency' => 'EUR',
            'stock' => 3,
        ]);

        $response = $this->actingAs($admin)->get(
            '/admin/products?search=Matching'
            . '&category_id=' . $category->id
            . '&currency=USD&stock=low'
        );

        $response->assertOk();
        $response->assertSee('Matching Phone');
        $response->assertDontSee('Matching Phone EUR');
    }

    public function test_products_are_paginated_and_filters_are_preserved(): void
    {
        $admin = $this->createAdmin();

        Product::factory()->count(16)->create([
            'name' => 'Phone Model',
            'currency' => 'USD',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/products?search=Phone&currency=USD');

        $response->assertOk();
        $response->assertSee('Showing 1');
        $response->assertSee('of 16 products');
        $response->assertSee('Phone Model');

        // Pagination links should preserve the active filters.
        $response->assertSee('search=Phone', false);
        $response->assertSee('currency=USD', false);

        // Only 15 products should appear on the first page.
        $this->assertSame(
            15,
            substr_count($response->getContent(), 'Phone Model')
        );

        $secondPage = $this->actingAs($admin)
            ->get('/admin/products?search=Phone&currency=USD&page=2');

        $secondPage->assertOk();
        $secondPage->assertSee('Phone Model');
        $secondPage->assertSee('of 16 products');
    }
}