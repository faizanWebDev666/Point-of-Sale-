<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'MacBook Pro M2',
            'sku' => 'LAP-001',
            'category' => 'electronics',
            'price' => 1299.00,
            'stock' => 45,
            'min_stock' => 5,
            'description' => 'Powerful laptop with M2 chip.'
        ]);

        Product::create([
            'name' => 'Premium Cotton T-Shirt',
            'sku' => 'CLO-052',
            'category' => 'clothing',
            'price' => 25.50,
            'stock' => 8,
            'min_stock' => 10,
            'description' => 'Comfortable cotton t-shirt.'
        ]);

        Product::create([
            'name' => 'Wireless Headphones',
            'sku' => 'ACC-109',
            'category' => 'electronics',
            'price' => 89.99,
            'stock' => 120,
            'min_stock' => 15,
            'description' => 'High quality wireless sound.'
        ]);
    }
}
