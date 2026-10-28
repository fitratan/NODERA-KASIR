<?php
/**
 * NODERA POS - Database Connection & Initialization
 * Universal UMKM SQLite 3 Manager
 */

function getDatabase(): PDO {
    static $db = null;
    if ($db !== null) {
        return $db;
    }

    $dbDir = __DIR__ . '/../database';
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }

    $dbFile = $dbDir . '/pos.sqlite';
    $needSeed = !file_exists($dbFile) || filesize($dbFile) === 0;

    $db = new PDO('sqlite:' . $dbFile, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 15
    ]);

    // Enable WAL mode & foreign keys for high-performance concurrent writes
    $db->exec('PRAGMA journal_mode = WAL;');
    $db->exec('PRAGMA foreign_keys = ON;');

    // Jalankan skema
    $schemaFile = $dbDir . '/schema.sql';
    if (file_exists($schemaFile)) {
        $schema = file_get_contents($schemaFile);
        $db->exec($schema);
    }

    if ($needSeed) {
        seedInitialData($db);
    }

    return $db;
}

function seedInitialData(PDO $db): void {
    // 1. Settings Toko
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM settings");
    if ($stmt->fetch()['cnt'] == 0) {
        $db->exec("
            INSERT INTO settings (store_name, store_subtitle, store_phone, store_address, receipt_footer, tax_percent, currency_symbol, paper_size, theme_mode)
            VALUES ('NODERA MART & CAFE', 'Universal Point of Sale System', '0812-9876-5432', 'Jl. Boulevard Raya Blok A No. 12', 'Terima kasih atas kunjungan Anda! Silakan datang kembali.', 0, 'Rp', '58mm', 'dark')
        ");
    }

    // 2. Akun Pengguna (Admin & Kasir)
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM users");
    if ($stmt->fetch()['cnt'] == 0) {
        $userStmt = $db->prepare("INSERT INTO users (name, username, role, pin, phone, is_active) VALUES (?, ?, ?, ?, ?, 1)");
        $userStmt->execute(['Owner / Admin', 'admin', 'admin', '8888', '081200000001']);
        $userStmt->execute(['Siti Rahma', 'siti', 'cashier', '1111', '081200000002']);
        $userStmt->execute(['Fajar Pratama', 'fajar', 'cashier', '2222', '081200000003']);
    }

    // 3. Kategori Universal
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM categories");
    if ($stmt->fetch()['cnt'] == 0) {
        $catStmt = $db->prepare("INSERT INTO categories (name, color, icon, sort_order) VALUES (?, ?, ?, ?)");
        $catStmt->execute(['Makanan & Minuman', 'emerald', 'coffee', 1]);
        $catStmt->execute(['Sembako & Kebutuhan', 'sky', 'package', 2]);
        $catStmt->execute(['Snack & Cemilan', 'amber', 'cookie', 3]);
        $catStmt->execute(['Jasa & Layanan', 'purple', 'wrench', 4]);
    }

    // 4. Produk & Jasa Sample
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM products");
    if ($stmt->fetch()['cnt'] == 0) {
        $prodStmt = $db->prepare("
            INSERT INTO products (category_id, code, name, type, cost_price, sell_price, stock, min_stock_alert, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");

        // F&B
        $prodStmt->execute([1, 'FB01', 'Kopi Susu Gula Aren', 'goods', 7000, 18000, 45, 10]);
        $prodStmt->execute([1, 'FB02', 'Americano Iced', 'goods', 5000, 15000, 30, 10]);
        $prodStmt->execute([1, 'FB03', 'Matcha Latte', 'goods', 8000, 20000, 25, 5]);
        $prodStmt->execute([1, 'FB04', 'Roti Bakar Coklat Keju', 'goods', 8000, 16000, 20, 5]);

        // Sembako / Retail
        $prodStmt->execute([2, '899123456001', 'Beras Premium 5kg', 'goods', 36000, 42000, 18, 5]);
        $prodStmt->execute([2, '899123456002', 'Minyak Goreng 2 Liter', 'goods', 28000, 34000, 24, 8]);
        $prodStmt->execute([2, '899123456003', 'Gula Pasir 1kg', 'goods', 13500, 17000, 35, 10]);
        $prodStmt->execute([2, '899123456004', 'Telur Ayam 1kg (16 butir)', 'goods', 24000, 28500, 15, 5]);

        // Snack
        $prodStmt->execute([3, '8999999111', 'Keripik Singkong Balado', 'goods', 6000, 10000, 40, 10]);
        $prodStmt->execute([3, '8999999222', 'Kacang Almond Panggang 100g', 'goods', 18000, 25000, 12, 5]);

        // Jasa / Layanan (Stock 0 / unlimited)
        $prodStmt->execute([4, 'SRV01', 'Jasa Layanan Cuci Komplit', 'service', 0, 25000, 0, 0]);
        $prodStmt->execute([4, 'SRV02', 'Jasa Servis Ringan / Tune Up', 'service', 0, 50000, 0, 0]);
        $prodStmt->execute([4, 'SRV03', 'Jasa Cuci Karpet / Laundry Express', 'service', 0, 35000, 0, 0]);
    }
}
