<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ========================================
        // Users
        // ========================================

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'name' => 'Customer',
            'email' => 'customer@test.com',
            'password' => 'password',
            'role' => 'customer',
        ]);

        // ========================================
        // Categories
        // ========================================

        $electronics = Category::create([
            'name' => 'Electronics',
        ]);

        $phones = Category::create([
            'name' => 'Phones',
        ]);

        $laptops = Category::create([
            'name' => 'Laptops',
        ]);

        $accessories = Category::create([
            'name' => 'Accessories',
        ]);

        // ========================================
        // Products
        // ========================================

        $iphone = Product::create([
            'category_id' => $phones->id,
            'name' => 'iPhone 15',
            'price' => 999.99,
            'currency' => 'USD',
            'rating' => 4.8,
            'image' => null,
            'stock' => 10,
        ]);

        $samsung = Product::create([
            'category_id' => $phones->id,
            'name' => 'Samsung Galaxy S24',
            'price' => 899.99,
            'currency' => 'USD',
            'rating' => 4.7,
            'image' => null,
            'stock' => 15,
        ]);

        $macbook = Product::create([
            'category_id' => $laptops->id,
            'name' => 'MacBook Pro 14',
            'price' => 1999.99,
            'currency' => 'USD',
            'rating' => 4.9,
            'image' => null,
            'stock' => 5,
        ]);

        $dell = Product::create([
            'category_id' => $laptops->id,
            'name' => 'Dell XPS 15',
            'price' => 1499.99,
            'currency' => 'USD',
            'rating' => 4.6,
            'image' => null,
            'stock' => 8,
        ]);

        $airpods = Product::create([
            'category_id' => $accessories->id,
            'name' => 'AirPods Pro',
            'price' => 249.99,
            'currency' => 'USD',
            'rating' => 4.7,
            'image' => null,
            'stock' => 20,
        ]);

        // ========================================
        // Order
        // ========================================

        $order = Order::create([
            'user_id' => $customer->id,
            'total' => 0,
            'currency' => 'USD',
            'status' => 'pending',
        ]);

        // ========================================
        // Order Items
        // ========================================

        $order->items()->create([
            'product_id' => $iphone->id,
            'product_name' => $iphone->name,
            'quantity' => 1,
            'price' => $iphone->price,
            'currency' => $iphone->currency,
            'converted_price' => $iphone->price,
        ]);

        $order->items()->create([
            'product_id' => $airpods->id,
            'product_name' => $airpods->name,
            'quantity' => 2,
            'price' => $airpods->price,
            'currency' => $airpods->currency,
            'converted_price' => $airpods->price,
        ]);

        // ========================================
        // Calculate Order Total
        // ========================================

        $total =
            ($iphone->price * 1) +
            ($airpods->price * 2);

        $order->update([
            'total' => $total,
        ]);
    }
}
