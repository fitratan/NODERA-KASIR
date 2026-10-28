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

    private static function initSchema(PDO $db): void {
        $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
        $db->exec($schema);
    }

    private static function seedData(PDO $db): void {
        // Settings awal
        $settings = [
            'store_name' => 'KASIR UMKM NODERA',
            'store_subtitle' => 'Jasa Cuci Kendaraan & Kedai Kopi',
            'store_address' => 'Jl. Merdeka No. 45, Indonesia',
            'store_phone' => '0812-3456-7890',
            'business_type' => 'service_wash', // service_wash, retail, fnb
            'currency_symbol' => 'Rp',
            'tax_percent' => '0',
            'footer_receipt' => 'Terima kasih atas kunjungan Anda! Bersih, Cepat & Terpercaya.',
            'default_language' => 'id',
            'qris_enabled' => '1',
            'qris_string' => '00020101021126570011ID.NODERAPAY.WWW01189360091100213049020208123456785204541153033605802ID5914KASIR%20NODERA6007JAKARTA61051234062070703A016304ABCD'
        ];

        $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (:k, :v)");
        foreach ($settings as $k => $v) {
            $stmt->execute([':k' => $k, ':v' => $v]);
        }

        // Karyawan awal
        $employees = [
            ['name' => 'Budi Santoso', 'phone' => '081211112222', 'role' => 'admin', 'pin' => '1111', 'commission_rate' => 0],
            ['name' => 'Siti Rahma', 'phone' => '081233334444', 'role' => 'cashier', 'pin' => '1234', 'commission_rate' => 0],
            ['name' => 'Agus Saputra (Tukang Cuci 1)', 'phone' => '081255556666', 'role' => 'worker', 'pin' => '0001', 'commission_rate' => 5000],
            ['name' => 'Rian Hidayat (Tukang Cuci 2)', 'phone' => '081277778888', 'role' => 'worker', 'pin' => '0002', 'commission_rate' => 5000],
            ['name' => 'Dani Pratama (Tukang Cuci 3)', 'phone' => '081299990000', 'role' => 'worker', 'pin' => '0003', 'commission_rate' => 5000],
        ];

        $empStmt = $db->prepare("INSERT INTO employees (name, phone, role, pin, commission_rate) VALUES (?, ?, ?, ?, ?)");
        foreach ($employees as $e) {
            $empStmt->execute([$e['name'], $e['phone'], $e['role'], $e['pin'], $e['commission_rate']]);
        }

        // Kategori awal
        $categories = [
            ['name' => 'Cuci & Layanan Motor', 'icon' => 'bike', 'sort' => 1],
            ['name' => 'Perawatan & Salon', 'icon' => 'sparkles', 'sort' => 2],
            ['name' => 'Minuman & Kopi', 'icon' => 'coffee', 'sort' => 3],
            ['name' => 'Aksesoris Motor', 'icon' => 'package', 'sort' => 4],
        ];

        $catStmt = $db->prepare("INSERT INTO categories (name, icon, sort_order) VALUES (?, ?, ?)");
        foreach ($categories as $c) {
            $catStmt->execute([$c['name'], $c['icon'], $c['sort']]);
        }

        // Produk awal
        $products = [
            // Jasa Cuci Motor
            [1, 'Cuci Salju Motor Matic Kecil (Beat/Mio/Vario 125)', 'SRV-01', 'service', 15000, 0, -1, 5000],
            [1, 'Cuci Salju Motor Bebek (Supra/Jupiter)', 'SRV-02', 'service', 15000, 0, -1, 5000],
            [1, 'Cuci Salju Motor Maxi (NMax/PCX/Aerox)', 'SRV-03', 'service', 20000, 0, -1, 7000],
            [1, 'Cuci Motor Sport / Moge (Ninja/CBR/XMAX)', 'SRV-04', 'service', 25000, 0, -1, 10000],
            [1, 'Cuci Salju Mobil Kecil (Agya/Brio)', 'SRV-05', 'service', 35000, 0, -1, 15000],
            [1, 'Cuci Salju Mobil MPV/SUV (Avanza/Innova)', 'SRV-06', 'service', 45000, 0, -1, 20000],

            // Salon & Perawatan
            [2, 'Cuci Helm Full Face / Half Face', 'SLN-01', 'service', 15000, 0, -1, 5000],
            [2, 'Poles Bodi Kilap & Semir Ban Ekstra', 'SLN-02', 'service', 10000, 0, -1, 3000],
            [2, 'Wax Detailing Proteksi Cat Motor', 'SLN-03', 'service', 30000, 5000, -1, 10000],
            [2, 'Ganti Oli Mesin Matic (Paket Jasa)', 'SLN-04', 'service', 10000, 0, -1, 4000],

            // Minuman & Kopi
            [3, 'Es Kopi Susu Gula Aren', 'DRK-01', 'goods', 10000, 5000, 50, 0],
            [3, 'Kopi Tubruk / Kopi Hitam Panas', 'DRK-02', 'goods', 5000, 2000, 50, 0],
            [3, 'Teh Pucuk Harum Dingin', 'DRK-03', 'goods', 5000, 3000, 80, 0],
            [3, 'Air Mineral Botol Dingin 600ml', 'DRK-04', 'goods', 4000, 2500, 100, 0],
            [3, 'Es Teh Manis Jumbo', 'DRK-05', 'goods', 5000, 1500, 100, 0],

            // Aksesoris
            [4, 'Lap Chamois / Kanebo Kuning Microfiber', 'ACC-01', 'goods', 15000, 9000, 25, 0],
            [4, 'Semir Ban Premium Botol Spray 250ml', 'ACC-02', 'goods', 20000, 12000, 15, 0],
            [4, 'Parfum Helm Anti Bakteri Spray', 'ACC-03', 'goods', 12000, 7000, 30, 0],
        ];

        $prodStmt = $db->prepare("INSERT INTO products (category_id, name, code, type, price, cost_price, stock, default_worker_commission) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($products as $p) {
            $prodStmt->execute($p);
        }
    }
}
