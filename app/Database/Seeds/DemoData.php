<?php

namespace App\Database\Seeds;

/** Local demonstration data, shared by the seeder and phpMyAdmin SQL exporter. */
final class DemoData
{
    public static function rows(): array
    {
        $created = date('Y-m-d 08:00:00', strtotime('-30 days'));
        $products = [];
        foreach ([
            ['Tamaraw Classic Tee', '450.00', 52, 'shirt'],
            ['Green & Gold Hoodie', '1250.00', 23, 'hoodie'],
            ['Everyday Canvas Tote', '295.00', 35, 'tote'],
            ['Campus Notes Journal', '185.00', 13, 'notebook'],
            ['Golden Hour Tumbler', '595.00', 30, 'tumbler'],
            ['Tamaraw ID Lanyard', '95.00', 9, 'lanyard'],
            ['Campus Club Cap', '350.00', 7, 'cap'],
            ['Green & Gold Pen', '45.00', 4, 'pen'],
        ] as $i => [$name, $price, $stock, $image]) {
            $products[] = ['id' => $i + 1, 'name' => $name, 'price' => $price, 'stock_quantity' => $stock,
                'image' => 'sample-' . $image . '.svg', 'created_at' => $created, 'deleted_at' => null];
        }
        $users = [];
        foreach ([['admin', 'Alex Reyes'], ['mika', 'Mika Santos'], ['josh', 'Josh Dela Cruz']] as $i => [$username, $name]) {
            $users[] = ['id' => $i + 1, 'username' => $username, 'full_name' => $name,
                'password' => password_hash($i === 0 ? 'TamarawDemo!2026' : bin2hex(random_bytes(24)), PASSWORD_DEFAULT),
                'avatar' => null, 'created_at' => $created, 'deleted_at' => null];
        }
        $customers = [];
        foreach (['Andrea Garcia', 'Luis Mendoza', 'Sofia Ramos', 'Ethan Torres', 'Isabella Lim'] as $i => $name) {
            $customers[] = ['id' => $i + 1, 'full_name' => $name, 'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                'phone' => null, 'created_at' => $created, 'deleted_at' => null];
        }
        $sales = [];
        // [days ago, product, customer (null = walk-in), staff, quantity]
        foreach ([
            [6,1,1,1,2], [6,4,null,2,1], [5,2,2,2,1], [5,3,3,1,2],
            [4,5,4,3,2], [4,8,null,1,1], [3,1,5,2,1], [3,4,1,3,2],
            [2,2,3,1,2], [2,6,null,2,1], [1,5,2,3,3], [1,1,5,1,2],
            [0,1,1,1,1], [0,5,null,2,1], [0,3,3,1,2], [0,4,4,3,1], [0,7,2,1,1],
        ] as $i => [$ago, $product, $customer, $staff, $quantity]) {
            $price = \App\Libraries\Money::cents($products[$product - 1]['price']);
            $products[$product - 1]['stock_quantity'] -= $quantity;
            $sales[] = ['id' => $i + 1, 'product_id' => $product, 'customer_id' => $customer, 'sold_by' => $staff,
                'quantity' => $quantity, 'total_price' => \App\Libraries\Money::decimal($price * $quantity),
                'request_key' => hash('sha256', 'campus-demo-' . $i),
                'created_at' => date('Y-m-d', strtotime('-' . $ago . ' days')) . ' ' . sprintf('%02d:%02d:00', 9 + ($i % 7), ($i * 7) % 60)];
        }
        return ['products' => $products, 'customers' => $customers, 'users' => $users, 'sales' => $sales];
    }
}

