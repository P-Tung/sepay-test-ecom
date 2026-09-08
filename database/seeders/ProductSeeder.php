<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::firstOrCreate(['name' => 'Điện thoại']);
        $accessories = Category::firstOrCreate(['name' => 'Phụ kiện']);
        $vehicles = Category::firstOrCreate(['name' => 'xe']);
        $clothing = Category::firstOrCreate(['name' => 'quần áo cũ']);

        $products = [
            [$electronics->id, 'iPhone 15', 'Điện thoại thông minh chính hãng.', 18990000, 12, 'products/iphone-15.jpg'],
            [$electronics->id, 'Samsung Galaxy S24', 'Màn hình đẹp, hiệu năng mạnh.', 15990000, 8, 'products/samsung-galaxy-s24.jpg'],
            [$accessories->id, 'Tai nghe Bluetooth', 'Tai nghe không dây tiện dụng.', 890000, 25, 'products/bluetooth-headphones.jpg'],
            [$accessories->id, 'Sạc nhanh USB-C', 'Bộ sạc nhanh an toàn và nhỏ gọn.', 450000, 30, 'products/usb-c-charger.jpg'],
            [$vehicles->id, 'Xe máy cũ', 'Xe máy cũ đã kiểm tra, vận hành tốt.', 18500000, 2, 'products/xe-may-cu.jpg'],
            [$clothing->id, 'Áo thun cũ', 'Áo thun đã qua sử dụng còn mới.', 80000, 15, 'products/ao-thun-cu.jpg'],
        ];

        foreach ($products as [$categoryId, $name, $description, $price, $quantity, $image]) {
            Product::updateOrCreate(
                ['name' => $name],
                [
                    'category_id' => $categoryId,
                    'description' => $description,
                    'price' => $price,
                    'quantity' => $quantity,
                    'image' => $image,
                ]
            );
        }
    }
}
