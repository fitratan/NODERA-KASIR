<?php
/**
 * Migration v3: Loyalty Points, Customer Tiers (Wholesale, VIP, Regular), 
 * Bank Transfer Settings, and Transaction Customer Association.
 */

require_once __DIR__ . '/../config/database.php';

$db = getDatabase();

echo "Starting Database Migration v3...\n";

function addCol(PDO $db, string $table, string $column, string $type) {
    $stmt = $db->query("PRAGMA table_info($table)");
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array($column, $cols)) {
        $db->exec("ALTER TABLE $table ADD COLUMN $column $type");
        echo "  [+] Added column $column to $table\n";
    }
}

// 1. Customers Table: Tier, Points, Custom Discount %
addCol($db, 'customers', 'tier', "TEXT DEFAULT 'regular'"); // 'regular', 'vip', 'wholesale'
addCol($db, 'customers', 'points', "INTEGER DEFAULT 0");
addCol($db, 'customers', 'discount_percent', "REAL DEFAULT 0");

// 2. Settings Table: Loyalty Points Config & Bank Transfer
addCol($db, 'settings', 'loyalty_points_enabled', "INTEGER DEFAULT 1");
addCol($db, 'settings', 'point_earn_rate', "REAL DEFAULT 10000"); // Tiap Rp 10.000 dapat 1 Poin
addCol($db, 'settings', 'point_redeem_value', "REAL DEFAULT 100"); // 1 Poin = Rp 100
addCol($db, 'settings', 'bank_name', "TEXT DEFAULT 'BCA'");
addCol($db, 'settings', 'bank_account_no', "TEXT DEFAULT '1234-5678-90'");
addCol($db, 'settings', 'bank_account_holder', "TEXT DEFAULT 'Toko POS Pro'");

// 3. Transactions Table: Customer ID, Points Used & Earned
addCol($db, 'transactions', 'customer_id', "INTEGER DEFAULT NULL");
addCol($db, 'transactions', 'customer_name', "TEXT DEFAULT ''");
addCol($db, 'transactions', 'points_used', "INTEGER DEFAULT 0");
addCol($db, 'transactions', 'points_discount', "REAL DEFAULT 0");
addCol($db, 'transactions', 'points_earned', "INTEGER DEFAULT 0");

// 4. Update Sample Customers with Initial Points & Tiers
$db->exec("
    UPDATE customers SET tier = 'regular', points = 45 WHERE id = 1;
    UPDATE customers SET tier = 'wholesale', points = 120 WHERE id = 2;
    UPDATE customers SET tier = 'vip', points = 15, discount_percent = 5 WHERE id = 3;
");

// 5. Ensure wholesale prices on products exist for demo testing
$db->exec("
    UPDATE products SET wholesale_min_qty = 3, wholesale_price = 14000 WHERE id = 1 AND (wholesale_price = 0 OR wholesale_price IS NULL);
    UPDATE products SET wholesale_min_qty = 2, wholesale_price = 10000 WHERE id = 2 AND (wholesale_price = 0 OR wholesale_price IS NULL);
    UPDATE products SET wholesale_min_qty = 5, wholesale_price = 4000 WHERE id = 3 AND (wholesale_price = 0 OR wholesale_price IS NULL);
");

echo "Migration v3 Completed Successfully!\n";
