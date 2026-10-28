<?php
// app/config/database.php

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dbPath = __DIR__ . '/../database/pos.sqlite';
            $isNew = !file_exists($dbPath);

            self::$instance = new PDO("sqlite:" . $dbPath);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            if ($isNew || filesize($dbPath) === 0) {
                self::initSchema(self::$instance);
                self::seedData(self::$instance);
            }
        }
        return self::$instance;
    }

    public static function resetDatabase(): void {
        $dbPath = __DIR__ . '/../database/pos.sqlite';
        if (file_exists($dbPath)) {
            unlink($dbPath);
        }
        self::$instance = null;
        self::getConnection();
    }

    private static function initSchema(PDO $db): void {
        $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
        $db->exec($schema);
    }

    private static function seedData(PDO $db): void {
        // Universal Store Settings
        $settings = [
            'store_name' => 'NODERA POS',
            'store_subtitle' => 'Sistem Kasir Universal UMKM',
            'store_address' => 'Jl. Diponegoro No. 88, Pusat Niaga',
            'store_phone' => '0812-3456-7890',
            'business_type' => 'universal',
            'currency_symbol' => 'Rp',
            'tax_percent' => '0',
            'footer_receipt' => 'Terima kasih telah berbelanja. Barang yang sudah dibeli tidak dapat ditukar.',
            'default_language' => 'id',
            'qris_enabled' => '1',
            'qris_string' => '00020101021126570011ID.NODERAPAY.WWW01189360091100213049020208123456785204541153033605802ID5914NODERA%20POS6007JAKARTA61051234062070703A016304ABCD'
        ];

        $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (:k, :v)");
        foreach ($settings as $k => $v) {
            $stmt->execute([':k' => $k, ':v' => $v]);
        }

        // Karyawan & Kasir (Universal Roles)
        $employees = [
            ['name' => 'Budi Santoso', 'phone' => '081211112222', 'role' => 'admin', 'pin' => '1111'],
            ['name' => 'Siti Rahma', 'phone' => '081233334444', 'role' => 'cashier', 'pin' => '1234'],
            ['name' => 'Fajar Pratama', 'phone' => '081255556666', 'role' => 'cashier', 'pin' => '2222'],
            ['name' => 'Dewi Anggraini', 'phone' => '081277778888', 'role' => 'staff', 'pin' => '3333'],
        ];

        $empStmt = $db->prepare("INSERT INTO employees (name, phone, role, pin) VALUES (?, ?, ?, ?)");
        foreach ($employees as $e) {
            $empStmt->execute([$e['name'], $e['phone'], $e['role'], $e['pin']]);
        }

        // Kategori Universal
        $categories = [
            ['name' => 'Makanan & Minuman', 'icon' => 'coffee', 'sort' => 1],
            ['name' => 'Sembako & Pokok', 'icon' => 'shopping-cart', 'sort' => 2],
            ['name' => 'Snack & Cemilan', 'icon' => 'cookie', 'sort' => 3],
            ['name' => 'Kebutuhan Harian', 'icon' => 'package', 'sort' => 4],
            ['name' => 'Jasa & Layanan', 'icon' => 'wrench', 'sort' => 5],
        ];

        $catStmt = $db->prepare("INSERT INTO categories (name, icon, sort_order) VALUES (?, ?, ?)");
        foreach ($categories as $c) {
            $catStmt->execute([$c['name'], $c['icon'], $c['sort']]);
        }

        // Produk & Layanan Universal (Cocok untuk Retail, Kafe, Jasa, maupun Toko)
        $products = [
            // F&B
            [1, 'Kopi Susu Aren (Cup 16oz)', 'FNB-001', 'goods', 15000, 7000, 100],
            [1, 'Americano Dingin (Iced Black)', 'FNB-002', 'goods', 12000, 4000, 100],
            [1, 'Teh Manis Jumbo Dingin', 'FNB-003', 'goods', 5000, 1500, 150],
            [1, 'Mie Instan Kuah / Goreng Telur', 'FNB-004', 'goods', 12000, 6000, 80],
            [1, 'Roti Bakar Coklat Keju', 'FNB-005', 'goods', 16000, 8000, 40],

            // Sembako
            [2, 'Beras Premium Ramos 5 Kg', 'SBK-001', 'goods', 72000, 65000, 50],
            [2, 'Minyak Goreng Pouch 2 Liter', 'SBK-002', 'goods', 34000, 31000, 40],
            [2, 'Gula Pasir Kristal 1 Kg', 'SBK-003', 'goods', 17500, 15500, 60],
            [2, 'Telur Ayam Negeri 1 Kg', 'SBK-004', 'goods', 28000, 25000, 35],
            [2, 'Tepung Terigu Segitiga 1 Kg', 'SBK-005', 'goods', 13000, 11000, 45],

            // Snack
            [3, 'Keripik Singkong Balado 250g', 'SNK-001', 'goods', 12000, 8000, 30],
            [3, 'Biskuit Coklat Kaleng 300g', 'SNK-002', 'goods', 22000, 18000, 25],
            [3, 'Kacang Kulit Sangrai 200g', 'SNK-003', 'goods', 10000, 7000, 40],

            // Kebutuhan Harian
            [4, 'Sabun Cuci Piring Refill 750ml', 'KBT-001', 'goods', 14000, 11500, 30],
            [4, 'Deterjen Bubuk Wangi 800g', 'KBT-002', 'goods', 19000, 16000, 25],
            [4, 'Tisu Gulung Dapur / Toilet 4 Roll', 'KBT-003', 'goods', 16000, 12000, 40],

            // Jasa / Layanan Universal (Bisa untuk Servis, Cuci, Laundry, Pangkas, dll)
            [5, 'Jasa Standar / Cuci / Perawatan Ringan', 'SRV-001', 'service', 15000, 0, -1],
            [5, 'Jasa Lengkap / Servis / Cuci Khusus', 'SRV-002', 'service', 25000, 0, -1],
            [5, 'Jasa Premium / Detailing / Servis Besar', 'SRV-003', 'service', 50000, 5000, -1],
        ];

        $prodStmt = $db->prepare("INSERT INTO products (category_id, name, code, type, price, cost_price, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($products as $p) {
            $prodStmt->execute($p);
        }
    }
}
