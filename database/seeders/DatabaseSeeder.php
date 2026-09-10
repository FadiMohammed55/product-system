<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // Categories
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

        // Products
        $iphone = Product::create([
            'category_id' => $phones->id,
            'name' => 'iPhone 15',
            'price' => 999.99,
            'currency' => 'USD',
            'rating' => 4.8,
            'image' => null,
        ]);

        $samsung = Product::create([
            'category_id' => $phones->id,
            'name' => 'Samsung Galaxy S24',
            'price' => 899.99,
            'currency' => 'USD',
            'rating' => 4.7,
            'image' => null,
        ]);

        $macbook = Product::create([
            'category_id' => $laptops->id,
            'name' => 'MacBook Pro 14',
            'price' => 1999.99,
            'currency' => 'USD',
            'rating' => 4.9,
            'image' => null,
        ]);

        $dell = Product::create([
            'category_id' => $laptops->id,
            'name' => 'Dell XPS 15',
            'price' => 1499.99,
            'currency' => 'USD',
            'rating' => 4.6,
            'image' => null,
        ]);

        $airpods = Product::create([
            'category_id' => $accessories->id,
            'name' => 'AirPods Pro',
            'price' => 249.99,
            'currency' => 'USD',
            'rating' => 4.7,
            'image' => null,
        ]);

        // Order
        $order = Order::create([
            'user_id' => $customer->id,
            'total' => 0,
            'status' => 'pending',
        ]);

        // Order Items
        $order->items()->create([
            'product_id' => $iphone->id,
            'quantity' => 1,
            'price' => $iphone->price,
        ]);

        $order->items()->create([
            'product_id' => $airpods->id,
            'quantity' => 2,
            'price' => $airpods->price,
        ]);

        // Calculate Order Total
        $total =
            ($iphone->price * 1) +
            ($airpods->price * 2);

        $order->update([
            'total' => $total,
        ]);
    }
}
