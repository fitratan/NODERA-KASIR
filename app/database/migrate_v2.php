<?php
/**
 * Migration v2: Expand database for Admin Portal, Category Photos, 
 * Debts/Kasbon, Expenses, Shifts, Commissions, and Integration Settings.
 */

require_once __DIR__ . '/../config/database.php';

$db = getDatabase();

echo "Starting Database Migration v2...\n";

// Helper function to add column if not exists
function addColumnIfNotExists(PDO $db, string $table, string $column, string $type) {
    $stmt = $db->query("PRAGMA table_info($table)");
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array($column, $cols)) {
        $db->exec("ALTER TABLE $table ADD COLUMN $column $type");
        echo "  [+] Added column $column to $table\n";
    }
}

// 1. Settings Table Updates
addColumnIfNotExists($db, 'settings', 'store_logo', "TEXT DEFAULT ''");
addColumnIfNotExists($db, 'settings', 'print_logo_receipt', "INTEGER DEFAULT 1");
addColumnIfNotExists($db, 'settings', 'telegram_bot_token', "TEXT DEFAULT ''");
addColumnIfNotExists($db, 'settings', 'telegram_chat_id', "TEXT DEFAULT ''");
addColumnIfNotExists($db, 'settings', 'telegram_daily_report', "INTEGER DEFAULT 1");
addColumnIfNotExists($db, 'settings', 'telegram_low_stock', "INTEGER DEFAULT 1");
addColumnIfNotExists($db, 'settings', 'wa_mode', "TEXT DEFAULT 'wame'");
addColumnIfNotExists($db, 'settings', 'wa_gateway_url', "TEXT DEFAULT ''");
addColumnIfNotExists($db, 'settings', 'wa_gateway_token', "TEXT DEFAULT ''");
addColumnIfNotExists($db, 'settings', 'qris_gateway_provider', "TEXT DEFAULT 'manual'");
addColumnIfNotExists($db, 'settings', 'qris_api_key', "TEXT DEFAULT ''");
addColumnIfNotExists($db, 'settings', 'qris_merchant_code', "TEXT DEFAULT ''");
addColumnIfNotExists($db, 'settings', 'sound_fx_enabled', "INTEGER DEFAULT 1");
addColumnIfNotExists($db, 'settings', 'admin_pin', "TEXT DEFAULT '1234'");

// 2. Categories Table Updates
addColumnIfNotExists($db, 'categories', 'image_url', "TEXT DEFAULT ''");
addColumnIfNotExists($db, 'categories', 'color', "TEXT DEFAULT 'teal'");

// 3. Products Table Updates
addColumnIfNotExists($db, 'products', 'commission_type', "TEXT DEFAULT 'none'"); // 'none', 'fixed', 'percent'
addColumnIfNotExists($db, 'products', 'commission_val', "REAL DEFAULT 0");
addColumnIfNotExists($db, 'products', 'wholesale_min_qty', "REAL DEFAULT 0");
addColumnIfNotExists($db, 'products', 'wholesale_price', "REAL DEFAULT 0");

// 4. Customers Table Updates
addColumnIfNotExists($db, 'customers', 'debt_limit', "REAL DEFAULT 500000");
addColumnIfNotExists($db, 'customers', 'notes', "TEXT DEFAULT ''");

// 5. Transactions Table Updates
addColumnIfNotExists($db, 'transactions', 'split_cash', "REAL DEFAULT 0");
addColumnIfNotExists($db, 'transactions', 'split_non_cash', "REAL DEFAULT 0");
addColumnIfNotExists($db, 'transactions', 'handled_by', "TEXT DEFAULT ''");

// 6. Create Debts & Debt Payments Tables
$db->exec("
CREATE TABLE IF NOT EXISTS debts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_id INTEGER NOT NULL REFERENCES customers(id) ON DELETE CASCADE,
    transaction_id INTEGER REFERENCES transactions(id) ON DELETE SET NULL,
    invoice_no TEXT,
    total_debt REAL NOT NULL,
    remaining_debt REAL NOT NULL,
    status TEXT DEFAULT 'unpaid', -- 'unpaid', 'partial', 'paid'
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS debt_payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    debt_id INTEGER NOT NULL REFERENCES debts(id) ON DELETE CASCADE,
    customer_id INTEGER NOT NULL REFERENCES customers(id) ON DELETE CASCADE,
    amount_paid REAL NOT NULL,
    payment_method TEXT DEFAULT 'cash',
    cashier_name TEXT NOT NULL,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS expenses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category TEXT NOT NULL, -- 'Operasional', 'Listrik/Air', 'Gaji/Bonus', 'Bahan Baku', 'Lainnya'
    amount REAL NOT NULL,
    notes TEXT NOT NULL,
    cashier_name TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS shifts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cashier_id INTEGER REFERENCES users(id),
    cashier_name TEXT NOT NULL,
    starting_cash REAL NOT NULL DEFAULT 0,
    ending_cash_actual REAL DEFAULT 0,
    ending_cash_system REAL DEFAULT 0,
    cash_difference REAL DEFAULT 0,
    status TEXT DEFAULT 'open', -- 'open', 'closed'
    notes TEXT,
    opened_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    closed_at DATETIME
);

CREATE TABLE IF NOT EXISTS stock_adjustments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    type TEXT NOT NULL, -- 'damaged', 'expired', 'personal', 'diff'
    qty REAL NOT NULL,
    loss_amount REAL DEFAULT 0,
    notes TEXT,
    user_name TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
");

// Update categories with rich icons and sample images if empty
$catImages = [
    'Buah' => 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?w=100&auto=format&fit=crop&q=80',
    'Makanan' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100&auto=format&fit=crop&q=80',
    'Minuman' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=100&auto=format&fit=crop&q=80',
    'Cemilan' => 'https://images.unsplash.com/photo-1599490659213-e2b9527bd087?w=100&auto=format&fit=crop&q=80',
    'Jasa & Layanan' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?w=100&auto=format&fit=crop&q=80'
];

foreach ($catImages as $name => $img) {
    $db->prepare("UPDATE categories SET image_url = ? WHERE name = ? AND (image_url IS NULL OR image_url = '')")
       ->execute([$img, $name]);
}

// Seed default customers if empty
$cntCust = $db->query("SELECT COUNT(*) FROM customers")->fetchColumn();
if ($cntCust == 0) {
    $db->exec("
    INSERT INTO customers (name, phone, address, total_debt, debt_limit, notes) VALUES
    ('Pak Budi (Langganan)', '081234567890', 'Gang Melati No. 12', 45000, 300000, 'Langganan kopi & makan siang'),
    ('Ibu Siti (Warung Sebelah)', '085298765432', 'Depan Toko', 0, 500000, 'Member grosir sembako'),
    ('Mas Dani (Ojol)', '087811223344', 'Kost Biru', 20000, 150000, 'Bayar kasbon tiap sore');
    ");
    
    // Seed initial debt for Pak Budi and Mas Dani
    $db->exec("
    INSERT INTO debts (customer_id, invoice_no, total_debt, remaining_debt, status, notes) VALUES
    (1, 'INV-20261028-001', 45000, 45000, 'unpaid', 'Nasi Kuning & Rokok'),
    (3, 'INV-20261028-002', 20000, 20000, 'unpaid', 'Kripik Pisang & Air Mineral');
    ");
}

echo "Migration v2 Completed Successfully!\n";
