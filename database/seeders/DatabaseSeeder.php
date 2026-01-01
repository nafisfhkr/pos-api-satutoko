<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\InventoryMovement;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Owner
        User::updateOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Owner',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Buat Outlet
        $outlet = Outlet::firstOrCreate(
            ['name' => 'Cabang Pusat - Jakarta'],
            [
                'address' => 'Jl. Sudirman No. 1',
                'phone' => '081234567890',
            ]
        );

        // 3. Buat Kategori
        $catCoffee = Category::firstOrCreate(['name' => 'Coffee']);
        $catSnack = Category::firstOrCreate(['name' => 'Snacks']);
        $catFood = Category::firstOrCreate(['name' => 'Main Course']);

        // 4. Buat Produk Dummy
        $products = [
            [
                'name' => 'Kopi Susu Gula Aren',
                'sku' => 'KOP-001',
                'price' => 18000,
                'category_id' => $catCoffee->id,
            ],
            [
                'name' => 'Americano Hot',
                'sku' => 'KOP-002',
                'price' => 15000,
                'category_id' => $catCoffee->id,
            ],
            [
                'name' => 'Kentang Goreng',
                'sku' => 'SNK-001',
                'price' => 12000,
                'category_id' => $catSnack->id,
            ],
            [
                'name' => 'Nasi Goreng Spesial',
                'sku' => 'FOD-001',
                'price' => 25000,
                'category_id' => $catFood->id,
            ],
        ];

        foreach ($products as $prodData) {
            $product = Product::firstOrCreate(
                ['sku' => $prodData['sku']],
                $prodData
            );

            // 5. Isi Stok Awal
            $stock = Stock::firstOrCreate(
                [
                    'outlet_id' => $outlet->id,
                    'product_id' => $product->id,
                ],
                ['qty' => 100]
            );

            InventoryMovement::firstOrCreate(
                [
                    'outlet_id' => $outlet->id,
                    'product_id' => $product->id,
                    'reference_type' => 'seed',
                    'reference_id' => null,
                ],
                [
                    'qty_delta' => $stock->qty,
                    'reason' => 'initial stock',
                    'user_id' => null,
                ]
            );
        }
    }
}
