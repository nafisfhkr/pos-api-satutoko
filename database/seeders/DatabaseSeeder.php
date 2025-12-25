<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;      
use App\Models\Outlet;     
use App\Models\Category;  
use App\Models\Product;   
use App\Models\Stock;     

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Owner
        User::create([
            'name' => 'Owner Admin',
            'email' => 'admin@pos.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Buat Outlet
        $outlet = Outlet::create([
            'name' => 'Cabang Pusat - Jakarta',
            'address' => 'Jl. Sudirman No. 1',
            'phone' => '081234567890'
        ]);

        // 3. Buat Kategori
        $catCoffee = Category::create(['name' => 'Coffee']);
        $catSnack = Category::create(['name' => 'Snacks']);
        $catFood = Category::create(['name' => 'Main Course']);

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
            $product = Product::create($prodData);

            // 5. Isi Stok Awal
            Stock::create([
                'outlet_id' => $outlet->id,
                'product_id' => $product->id,
                'qty' => 100
            ]);
        }
    }
}