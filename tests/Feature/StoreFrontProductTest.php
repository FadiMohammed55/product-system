<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreFrontProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create an authenticated customer.
     */
    private function actingAsCustomer(): static
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        return $this->actingAs($customer);
    }

    public function test_customer_can_view_products_page(): void
    {
        $this->actingAsCustomer();

        Product::factory()->create([
            'name' => 'iPhone 17',
        ]);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('iPhone 17');
    }

    public function test_customer_can_search_products_by_name(): void
    {
        $this->actingAsCustomer();

        Product::factory()->create([
            'name' => 'iPhone 17',
        ]);

        Product::factory()->create([
            'name' => 'Samsung Galaxy',
        ]);

        $response = $this->get(route('products.index', [
            'search' => 'iPhone'
        ]));

        $response->assertOk();
        $response->assertSee('iPhone 17');
        $response->assertDontSee('Samsung Galaxy');
    }

    public function test_customer_can_filter_products_by_category(): void
    {
        $this->actingAsCustomer();

        $phones = Category::factory()->create([
            'name' => 'Phones',
        ]);

        $laptops = Category::factory()->create([
            'name' => 'Laptops',
        ]);

        Product::factory()->create([
            'name' => 'iPhone 17',
            'category_id' => $phones->id,
        ]);

        Product::factory()->create([
            'name' => 'MacBook Pro',
            'category_id' => $laptops->id,
        ]);

        $response = $this->get(route('products.index', [
            'category_id' => $phones->id,
        ]));

        $response->assertOk();
        $response->assertSee('iPhone 17');
        $response->assertDontSee('MacBook Pro');
    }

    public function test_customer_can_filter_products_by_currency(): void
    {
        $this->actingAsCustomer();

        Product::factory()->create([
            'name' => 'USD Product',
            'currency' => 'USD',
        ]);

        Product::factory()->create([
            'name' => 'EUR Product',
            'currency' => 'EUR',
        ]);

        $response = $this->get(route('products.index', [
            'currency' => 'USD',
        ]));

        $response->assertOk();
        $response->assertSee('USD Product');
        $response->assertDontSee('EUR Product');
    }

    public function test_customer_can_sort_products_by_price_ascending(): void
    {
        $this->actingAsCustomer();

        Product::factory()->create([
            'name' => 'Expensive Product',
            'price' => 500,
        ]);

        Product::factory()->create([
            'name' => 'Cheap Product',
            'price' => 50,
        ]);

        $response = $this->get(route('products.index', [
            'sort' => 'price_asc'
        ]));

        $response->assertOk();
        $response->assertSeeInOrder([
            'Cheap Product',
            'Expensive Product',
        ]);
    }

    public function test_customer_can_sort_products_by_price_descending(): void
    {
        $this->actingAsCustomer();

        Product::factory()->create([
            'name' => 'Cheap Product',
            'price' => 50,
        ]);

        Product::factory()->create([
            'name' => 'Expensive Product',
            'price' => 500,
        ]);

        $response = $this->get(route('products.index', [
            'sort' => 'price_desc'
        ]));

        $response->assertOk();
        $response->assertSeeInOrder([
            'Expensive Product',
            'Cheap Product',
        ]);
    }

    public function test_customer_can_paginate_products(): void
    {
        $this->actingAsCustomer();

        Product::factory()->count(13)->create();

        $firstPage = $this->get(route('products.index'));

        $firstPage->assertOk();
        $firstPage->assertSee('Next');

        $secondPage = $this->get(route('products.index', [
            'page' => 2,
        ]));

        $secondPage->assertOk();
    }

    public function test_pagination_preserves_search_flters(): void
    {
        $this->actingAsCustomer();

        $category = Category::factory()->create();

        Product::factory()->count(13)->create([
            'name' => 'Test Product',
            'category_id' => $category->id,
            'currency' => 'USD',
        ]);

        $response = $this->get(route('products.index', [
            'search' => 'Test',
            'category_id' => $category->id,
            'currency' => 'USD',
            'page' => 2,
        ]));

        $response->assertOk();

        $response->assertSee('search=Test', false);
        $response->assertSee('category_id=' . $category->id, false);
        $response->assertSee('currency=USD', false);
    }

    public function test_invalid_sort_value_is_rejected(): void
    {
        $this->actingAsCustomer();

        $response = $this->get(route('products.index', [
            'sort' => 'invalid_sort',
        ]));

        $response->assertSessionHasErrors('sort');
    }

    public function test_invalid_category_is_rejected(): void
    {
        $this->actingAsCustomer();

        $response = $this->get(route('products.index', [
            'category_id' => 99999,
        ]));

        $response->assertSessionHasErrors('category_id');
    }

    public function test_invalid_currency_is_rejected(): void
    {
        $this->actingAsCustomer();

        Product::factory()->create([
            'currency' => 'USD',
        ]);

        $response = $this->get(route('products.index', [
            'currency' => 'GBP',
        ]));

        $response->assertSessionHasErrors('currency');
    }

    public function test_guest_cannot_view_products_page(): void
    {
        $response = $this->get(route('products.index'));

        $response->assertRedirect('/login');
    }

    public function test_admin_cannot_view_customer_products_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('products.index'));

        $response->assertForbidden();
    }
}