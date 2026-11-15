<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Users (Admin and Cashier)
        User::create([
            'name' => 'Shop Admin',
            'email' => 'admin@pos.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Shop Cashier',
            'email' => 'cashier@pos.com',
            'password' => Hash::make('cashier123'),
            'role' => 'cashier',
        ]);

        // 2. Create Default Categories
        $electronics = Category::create(['name' => 'Electronics']);
        $groceries = Category::create(['name' => 'Groceries']);
        $clothing = Category::create(['name' => 'Clothing']);
        $beverages = Category::create(['name' => 'Beverages']);
        $snacks = Category::create(['name' => 'Snacks']);

        // 3. Create Default Products (Sri Lankan Rupee Pricing)
        Product::create([
            'category_id' => $electronics->id,
            'name' => 'Laptop HP Pavillion',
            'sku' => 'PROD-001',
            'purchase_price' => 180000.00,
            'selling_price' => 245000.00,
            'stock_quantity' => 15,
            'low_stock_alert' => 5,
            'image' => null,
        ]);

        Product::create([
            'category_id' => $electronics->id,
            'name' => 'Samsung Galaxy S23',
            'sku' => 'PROD-002',
            'purchase_price' => 190000.00,
            'selling_price' => 235000.00,
            'stock_quantity' => 25,
            'low_stock_alert' => 5,
            'image' => null,
        ]);

        Product::create([
            'category_id' => $groceries->id,
            'name' => 'Fresh Milk 1L',
            'sku' => 'PROD-003',
            'purchase_price' => 380.00,
            'selling_price' => 480.00,
            'stock_quantity' => 50,
            'low_stock_alert' => 10,
            'image' => null,
        ]);

        Product::create([
            'category_id' => $groceries->id,
            'name' => 'Basmati Rice 5kg',
            'sku' => 'PROD-004',
            'purchase_price' => 2200.00,
            'selling_price' => 2650.00,
            'stock_quantity' => 30,
            'low_stock_alert' => 8,
            'image' => null,
        ]);

        Product::create([
            'category_id' => $clothing->id,
            'name' => 'Cotton T-Shirt L',
            'sku' => 'PROD-005',
            'purchase_price' => 1200.00,
            'selling_price' => 1850.00,
            'stock_quantity' => 40,
            'low_stock_alert' => 10,
            'image' => null,
        ]);

        Product::create([
            'category_id' => $beverages->id,
            'name' => 'Coca-Cola 330ml',
            'sku' => 'PROD-006',
            'purchase_price' => 220.00,
            'selling_price' => 280.00,
            'stock_quantity' => 100,
            'low_stock_alert' => 20,
            'image' => null,
        ]);

        // This product will have stock below the alert threshold (stock: 3 < threshold: 5)
        Product::create([
            'category_id' => $beverages->id,
            'name' => 'Orange Juice 1L',
            'sku' => 'PROD-007',
            'purchase_price' => 650.00,
            'selling_price' => 850.00,
            'stock_quantity' => 3,
            'low_stock_alert' => 5,
            'image' => null,
        ]);

        Product::create([
            'category_id' => $snacks->id,
            'name' => 'Potato Chips Classic',
            'sku' => 'PROD-008',
            'purchase_price' => 320.00,
            'selling_price' => 420.00,
            'stock_quantity' => 60,
            'low_stock_alert' => 15,
            'image' => null,
        ]);
    }
}
