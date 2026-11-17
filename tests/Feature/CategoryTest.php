<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_categories(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Category::factory()->count(3)->create();

        $response = $this->actingAs($admin)
            ->get('/admin/categories');

        $response->assertOk();
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post('/admin/categories', [
                'name' => 'Electronics',
            ]);

        $response->assertRedirect('/admin/categories');

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
        ]);
    }

    public function test_category_creation_fails_with_invalid_data(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Category::factory()->create([
            'name' => 'Electronics',
        ]);

        $response = $this->actingAs($admin)
            ->post('/admin/categories', [
                'name' => '',
            ]);

        $response->assertSessionHasErrors([
            'name',
        ]);

        $response = $this->actingAs($admin)
            ->post('/admin/categories', [
                'name' => 'Electronics',
            ]);

        $response->assertSessionHasErrors([
            'name',
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::factory()->create([
            'name' => 'Old Category',
        ]);

        $response = $this->actingAs($admin)
            ->put("/admin/categories/{$category->id}", [
                'name' => 'Updated Category',
            ]);

        $response->assertRedirect('/admin/categories');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
        ]);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::factory()->create([
            'name' => 'Category To Delete',
        ]);

        $response = $this->actingAs($admin)
            ->delete("/admin/categories/{$category->id}");

        $response->assertRedirect('/admin/categories');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_admin_cannot_delete_category_with_products(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $category = Category::factory()->create([
            'name' => 'Electronics'
        ]);

        Product::factory()->create([
            'category_id' => $category->id
        ]);

        $response = $this->actingAs($admin)
            ->delete("/admin/categories/{$category->id}");

        $response->assertRedirect('/admin/categories');

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Electronics',
        ]);
    }

    public function test_customer_cannot_create_category(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)
            ->post('/admin/categories', [
                'name' => 'Unauthorized Category',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('categories', [
            'name' => 'Unauthorized Category',
        ]);
    }

    public function test_customer_cannot_update_category(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $category = Category::factory()->create([
            'name' => 'Original Category',
        ]);

        $response = $this->actingAs($customer)
            ->put("/admin/categories/{$category->id}", [
                'name' => 'Hacked Category',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Original Category',
        ]);
    }

    public function test_customer_cannot_delete_category(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $category = Category::factory()->create();

        $response = $this->actingAs($customer)
            ->delete("/admin/categories/{$category->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_admin_can_search_categories_by_name(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Category::factory()->create([
            'name' => 'Electronics'
        ]);
        Category::factory()->create([
            'name' => 'Books'
        ]);
        Category::factory()->create([
            'name' => 'Clothing'
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/categories?search=Electronics');

        $response->assertOk();
        $response->assertSee('Electronics');
        $response->assertDontSee('Books');
        $response->assertDontSee('Clothing');
    }

    public function test_categories_display_their_product_count(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::factory()->create([
            'name' => 'Electronics',
        ]);

        Product::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/categories');

        $response->assertOk();

        $response->assertViewHas('categories', function ($categories) use ($category) {
            $foundCategory = $categories->firstWhere('id', $category->id);

            return $foundCategory !== null && $foundCategory->products_count === 3;
        });
    }

    public function test_categories_are_paginated(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        for ($i = 1; $i <= 12; $i++) {
            Category::factory()->create([
                'name' => "Category {$i}",
            ]);
        }

        $response = $this->actingAs($admin)
            ->get('/admin/categories');

        $response->assertOk();

        $response->assertViewHas('categories', function ($categories) {
            return $categories->total() === 12
                && $categories->count() === 10
                && $categories->perPage() === 10;
        });
    }

    public function test_category_search_is_preserved_during_pagination(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        for ($i = 1; $i <= 12; $i++) {
            Category::factory()->create([
                'name' => "Electronics {$i}",
            ]);
        }

        Category::factory()->create([
            'name' => 'Clothing',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/categories?search=Electronics&page=2');

        $response->assertOk();

        $response->assertViewHas('categories', function ($categories) {
            return $categories->total() === 12
                && $categories->count() === 2
                && $categories->currentPage() === 2;
        });

        $response->assertSee('search=Electronics', false);
    }
}