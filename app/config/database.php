<?php
/**
 * NODERA POS PRO - Database Connection & Seed Data
 * Mirrors the POS Offline Pro layout & data structure
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

    $db->exec('PRAGMA journal_mode = WAL;');
    $db->exec('PRAGMA foreign_keys = ON;');

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
            INSERT INTO settings (
                store_name, store_type, store_phone, store_address, receipt_footer,
                feature_barcode, feature_variants, feature_customer, feature_open_bill,
                auto_print, tax_enabled, tax_percent, currency_symbol, paper_size
            ) VALUES (
                'POS Offline Pro', 'Toko Serba Ada & Kuliner', '08111111111', 'Jl. Alamat No 99 Makassar', 'Terima Kasih! Silahkan Datang Kembali',
                1, 1, 1, 1, 0, 1, 11, 'Rp', '58mm'
            )
        ");
    }

    // 2. Akun Pengguna
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM users");
    if ($stmt->fetch()['cnt'] == 0) {
        $uStmt = $db->prepare("INSERT INTO users (name, username, role, pin, phone, is_active) VALUES (?, ?, ?, ?, ?, 1)");
        $uStmt->execute(['Owner / Admin', 'admin', 'admin', '8888', '08111111111']);
        $uStmt->execute(['Kasir Utama', 'kasir', 'cashier', '1234', '08122222222']);
    }

    // 3. Kategori Sesuai Video
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM categories");
    if ($stmt->fetch()['cnt'] == 0) {
        $catStmt = $db->prepare("INSERT INTO categories (name, icon, sort_order) VALUES (?, ?, ?)");
        $catStmt->execute(['Buah', 'apple', 1]);
        $catStmt->execute(['Makanan', 'utensils', 2]);
        $catStmt->execute(['Minuman', 'coffee', 3]);
        $catStmt->execute(['Cemilan', 'cookie', 4]);
        $catStmt->execute(['Jasa & Layanan', 'wrench', 5]);
    }

    // 4. Produk Persis Seperti di Video TikTok
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM products");
    if ($stmt->fetch()['cnt'] == 0) {
        $pStmt = $db->prepare("
            INSERT INTO products (
                category_id, code, name, type, unit, cost_price, sell_price, stock, min_stock_alert, is_scale, has_variants, image_url, is_active
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");

        // 1. Air Botol (Minuman) - Stok tanpa batas
        $pStmt->execute([3, 'MN01', 'Air Botol', 'goods', 'pcs', 3000, 5000, 999, 10, 0, 0, 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=150&auto=format&fit=crop&q=80']);

        // 2. Anggur (Buah) - Timbangan (Volume vs Nominal)
        $pStmt->execute([1, 'BH01', 'Anggur', 'goods', 'kg', 40000, 50000, 50, 5, 1, 0, 'https://images.unsplash.com/photo-1537640538966-79f369143f8f?w=150&auto=format&fit=crop&q=80']);

        // 3. Apel (Buah) - Timbangan
        $pStmt->execute([1, 'BH02', 'Apel', 'goods', 'kg', 28000, 35000, 45, 5, 1, 0, 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=150&auto=format&fit=crop&q=80']);

        // 4. Bakso kuah (Makanan)
        $pStmt->execute([2, 'MK01', 'Bakso kuah', 'goods', 'porsi', 10000, 15000, 40, 5, 0, 0, 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=150&auto=format&fit=crop&q=80']);

        // 5. Kripik Pisang (Cemilan)
        $pStmt->execute([4, 'CM01', 'Kripik Pisang', 'goods', 'pcs', 7000, 10000, 50, 10, 0, 0, 'https://images.unsplash.com/photo-1599490659213-e2b9527bd087?w=150&auto=format&fit=crop&q=80']);

        // 6. Mie Pangsit (Makanan)
        $pStmt->execute([2, 'MK02', 'Mie Pangsit', 'goods', 'porsi', 11000, 15000, 35, 5, 0, 0, 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=150&auto=format&fit=crop&q=80']);

        // 7. Nasi Kuning (Makanan) - Punya Varian (Ayam, Rendang, Telur)
        $pStmt->execute([2, 'MK03', 'Nasi Kuning', 'goods', 'porsi', 8000, 10000, 150, 10, 0, 1, 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=150&auto=format&fit=crop&q=80']);
        $nasiKuningId = $db->lastInsertId();

        // Masukkan Varian Nasi Kuning
        $vStmt = $db->prepare("INSERT INTO product_variants (product_id, name, cost_price, sell_price, stock) VALUES (?, ?, ?, ?, ?)");
        $vStmt->execute([$nasiKuningId, 'Ayam Suwir', 11000, 15000, 80]);
        $vStmt->execute([$nasiKuningId, 'Rendang Sapi', 14000, 18000, 50]);
        $vStmt->execute([$nasiKuningId, 'Telur Balado', 9000, 12000, 120]);

        // 8. Jasa / Layanan UMKM
        $pStmt->execute([5, 'JS01', 'Jasa Layanan Cuci Komplit', 'service', 'paket', 0, 25000, 0, 0, 0, 0, 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?w=150&auto=format&fit=crop&q=80']);
    }

    // 5. Data Pelanggan Sample
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM customers");
    if ($stmt->fetch()['cnt'] == 0) {
        $cStmt = $db->prepare("INSERT INTO customers (name, phone, address, total_debt) VALUES (?, ?, ?, ?)");
        $cStmt->execute(['Pak Haji Syamsul', '08123456789', 'Jl. Sam Ratulangi No. 12', 0]);
        $cStmt->execute(['Ibu Nurhayati', '08529876543', 'Kompleks Griya Indah Blok C', 0]);
    }
}
