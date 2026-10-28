<?php
/**
 * NODERA POS PRO - Universal UMKM REST API v2
 * Comprehensive Backend for Layar Kasir & Back-Office Admin Portal
 */

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', '0');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../app/config/database.php';

try {
    $db = getDatabase();
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Gagal terkoneksi ke database SQLite: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?? [];

function getActiveUser(PDO $db): array {
    if (!empty($_SESSION['user_id'])) {
        $stmt = $db->prepare("SELECT id, name, username, role, phone, is_active FROM users WHERE id = ? AND is_active = 1");
        $stmt->execute([$_SESSION['user_id']]);
        $u = $stmt->fetch();
        if ($u) {
            return $u;
        }
    }
    $stmt = $db->query("SELECT id, name, username, role, phone, is_active FROM users WHERE is_active = 1 ORDER BY CASE role WHEN 'admin' THEN 1 ELSE 2 END ASC LIMIT 1");
    $default = $stmt->fetch();
    return $default ?: ['id' => 1, 'name' => 'Owner / Admin', 'role' => 'admin', 'username' => 'admin'];
}

switch ($action) {

    // =========================================================================
    // 1. AUTENTIKASI & VERIFIKASI PIN ADMIN
    // =========================================================================
    case 'auth_login':
        $pin = trim($input['pin'] ?? '');
        $userId = intval($input['user_id'] ?? 0);

        if ($pin === '') {
            echo json_encode(['success' => false, 'error' => 'PIN wajib diisi.']);
            exit;
        }

        if ($userId > 0) {
            $stmt = $db->prepare("SELECT id, name, username, role, phone, is_active FROM users WHERE id = ? AND pin = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([$userId, $pin]);
        } else {
            $stmt = $db->prepare("SELECT id, name, username, role, phone, is_active FROM users WHERE pin = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([$pin]);
        }

        $user = $stmt->fetch();
        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'PIN salah. Silakan coba lagi.']);
            exit;
        }

        $_SESSION['user_id'] = $user['id'];
        $db->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?")->execute([$user['id']]);

        echo json_encode([
            'success' => true,
            'user' => $user,
            'message' => 'Login berhasil sebagai ' . $user['name']
        ]);
        break;

    case 'auth_logout':
        unset($_SESSION['user_id']);
        echo json_encode(['success' => true]);
        break;

    case 'admin_verify_pin':
        $pin = trim($input['pin'] ?? '');
        $settings = $db->query("SELECT admin_pin FROM settings ORDER BY id DESC LIMIT 1")->fetch();
        $adminPin = $settings['admin_pin'] ?? '1234';
        
        // Cek juga ke tabel users yang punya role 'admin'
        $stmt = $db->prepare("SELECT id, name FROM users WHERE pin = ? AND role = 'admin' AND is_active = 1 LIMIT 1");
        $stmt->execute([$pin]);
        $adminUser = $stmt->fetch();

        if ($pin === $adminPin || $adminUser) {
            $_SESSION['is_admin_verified'] = true;
            echo json_encode(['success' => true, 'message' => 'Otorisasi Admin Berhasil']);
        } else {
            echo json_encode(['success' => false, 'error' => 'PIN Admin salah.']);
        }
        break;

    // =========================================================================
    // 2. DATA INISIALISASI LENGKAP (KASIR & POS)
    // =========================================================================
    case 'init':
        $settings = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1")->fetch() ?: [];
        $categories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, name ASC")->fetchAll();
        
        // Produk dengan kategori
        $prodStmt = $db->query("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            ORDER BY c.sort_order ASC, p.name ASC
        ");
        $products = $prodStmt->fetchAll();

        // Ambil varian produk jika ada
        $varStmt = $db->query("SELECT * FROM product_variants WHERE is_active = 1 ORDER BY id ASC");
        $allVariants = $varStmt->fetchAll();
        $variantsByProd = [];
        foreach ($allVariants as $v) {
            $variantsByProd[$v['product_id']][] = $v;
        }

        foreach ($products as &$p) {
            $p['variants'] = $variantsByProd[$p['id']] ?? [];
        }

        // Open bills yang belum lunas
        $openBills = $db->query("SELECT * FROM open_bills WHERE status = 'open' ORDER BY id DESC")->fetchAll();
        $customers = $db->query("SELECT * FROM customers ORDER BY name ASC")->fetchAll();
        $activeUser = getActiveUser($db);
        $allUsers = $db->query("SELECT id, name, username, role, phone FROM users WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

        // Expense Categories & Shift
        $expCats = $db->query("SELECT * FROM expense_categories ORDER BY name ASC")->fetchAll();
        $curShiftStmt = $db->prepare("SELECT * FROM shifts WHERE cashier_id = ? AND status = 'open' ORDER BY id DESC LIMIT 1");
        $curShiftStmt->execute([$activeUser['id']]);
        $currentShift = $curShiftStmt->fetch() ?: null;

        echo json_encode([
            'success' => true,
            'settings' => $settings,
            'categories' => $categories,
            'products' => $products,
            'open_bills' => $openBills,
            'customers' => $customers,
            'expense_categories' => $expCats,
            'active_user' => $activeUser,
            'users' => $allUsers,
            'current_shift' => $currentShift
        ]);
        break;

    // =========================================================================
    // 3. TRANSAKSI CHECKOUT KASIR (Tunai, QRIS, Kasbon, Split)
    // =========================================================================
    case 'checkout':
        if (empty($input['items'])) {
            echo json_encode(['success' => false, 'error' => 'Keranjang pesanan masih kosong.']);
            exit;
        }

        $activeUser = getActiveUser($db);
        $cashierId = $activeUser['id'];
        $cashierName = $activeUser['name'];

        $customerId = !empty($input['customer_id']) ? intval($input['customer_id']) : null;
        $customerName = trim($input['customer_name'] ?? '');
        $orderRef = trim($input['order_ref'] ?? '');
        $paymentMethod = $input['payment_method'] ?? 'cash'; // 'cash', 'qris', 'debt', 'split'
        $amountPaid = floatval($input['amount_paid'] ?? 0);
        $splitCash = floatval($input['split_cash'] ?? 0);
        $splitNonCash = floatval($input['split_non_cash'] ?? 0);
        $discount = floatval($input['discount'] ?? 0);
        $tax = floatval($input['tax'] ?? 0);
        $notes = trim($input['notes'] ?? '');
        $handledBy = trim($input['handled_by'] ?? $cashierName);
        $openBillId = !empty($input['open_bill_id']) ? intval($input['open_bill_id']) : null;

        // Hitung total dan HPP modal
        $subtotal = 0;
        $totalCost = 0;
        foreach ($input['items'] as $item) {
            $subtotal += floatval($item['price']) * floatval($item['qty']);
            $totalCost += floatval($item['cost_price'] ?? 0) * floatval($item['qty']);
        }
        $total = max(0, $subtotal - $discount + $tax);

        // Validasi Metode Kasbon / Hutang
        if ($paymentMethod === 'debt') {
            if (!$customerId) {
                echo json_encode(['success' => false, 'error' => 'Metode Kasbon/Hutang wajib memilih Pelanggan (Member).']);
                exit;
            }
            $custStmt = $db->prepare("SELECT name, phone, total_debt, debt_limit FROM customers WHERE id = ?");
            $custStmt->execute([$customerId]);
            $cust = $custStmt->fetch();
            if (!$cust) {
                echo json_encode(['success' => false, 'error' => 'Data pelanggan tidak ditemukan.']);
                exit;
            }
            $customerName = $cust['name'];
            $newTotalDebt = floatval($cust['total_debt']) + ($total - $amountPaid);
            if (floatval($cust['debt_limit']) > 0 && $newTotalDebt > floatval($cust['debt_limit'])) {
                echo json_encode([
                    'success' => false, 
                    'error' => "Kasbon melebihi limit plafon pelanggan! Limit: Rp " . number_format($cust['debt_limit'], 0, ',', '.') . " | Total jika ditambah: Rp " . number_format($newTotalDebt, 0, ',', '.')
                ]);
                exit;
            }
        } elseif ($paymentMethod === 'split') {
            if (($splitCash + $splitNonCash) < $total) {
                echo json_encode(['success' => false, 'error' => 'Jumlah pembayaran campuran (Tunai + Non-Tunai) kurang dari total tagihan.']);
                exit;
            }
            $amountPaid = $splitCash + $splitNonCash;
        } elseif ($paymentMethod === 'cash') {
            if ($amountPaid < $total) {
                echo json_encode(['success' => false, 'error' => 'Uang tunai yang diterima kurang dari total tagihan.']);
                exit;
            }
        } else {
            if ($amountPaid === 0.0) {
                $amountPaid = $total;
            }
        }

        $changeAmount = ($paymentMethod === 'cash') ? max(0, $amountPaid - $total) : 0;

        $db->beginTransaction();
        try {
            // Generate Invoice No: INV-YYMMDD-XXX
            $datePrefix = date('ymd');
            $countStmt = $db->query("SELECT COUNT(*) as cnt FROM transactions WHERE DATE(created_at) = DATE('now')");
            $todayCount = intval($countStmt->fetch()['cnt']) + 1;
            $invoiceNo = sprintf("INV-%s-%03d", $datePrefix, $todayCount);

            $pointsUsed = intval($input['points_used'] ?? 0);
            $pointsDiscount = floatval($input['points_discount'] ?? 0);
            $pointsEarned = 0;

            $stmt = $db->prepare("
                INSERT INTO transactions (
                    invoice_no, cashier_id, cashier_name, customer_id, customer_name, order_ref, 
                    subtotal, discount, tax, total, payment_method, amount_paid, 
                    change_amount, split_cash, split_non_cash, handled_by, status, notes,
                    points_used, points_discount, points_earned
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $invoiceNo, $cashierId, $cashierName, $customerId, $customerName, $orderRef,
                $subtotal, $discount, $tax, $total, $paymentMethod, $amountPaid,
                $changeAmount, $splitCash, $splitNonCash, $handledBy, $notes,
                $pointsUsed, $pointsDiscount, $pointsEarned
            ]);
            $transactionId = $db->lastInsertId();

            // Potong saldo poin yang digunakan & tambahkan poin baru jika member dipilih
            if ($customerId) {
                if ($pointsUsed > 0) {
                    $db->prepare("UPDATE customers SET points = MAX(0, points - ?) WHERE id = ?")
                       ->execute([$pointsUsed, $customerId]);
                }
                $cfg = $db->query("SELECT loyalty_points_enabled, point_earn_rate FROM settings LIMIT 1")->fetch();
                if (!empty($cfg['loyalty_points_enabled'])) {
                    $earnRate = floatval($cfg['point_earn_rate']) ?: 10000;
                    $pointsEarned = intval(floor($total / $earnRate));
                    if ($pointsEarned > 0) {
                        $db->prepare("UPDATE customers SET points = points + ? WHERE id = ?")
                           ->execute([$pointsEarned, $customerId]);
                        $db->prepare("UPDATE transactions SET points_earned = ? WHERE id = ?")
                           ->execute([$pointsEarned, $transactionId]);
                    }
                }
            }

            $itemStmt = $db->prepare("
                INSERT INTO transaction_items (
                    transaction_id, product_id, product_name, variant_name, unit, cost_price, sell_price, qty, subtotal
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stockStmt = $db->prepare("UPDATE products SET stock = stock - ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND type = 'goods'");
            $mutationStmt = $db->prepare("
                INSERT INTO stock_mutations (product_id, type, qty, previous_stock, current_stock, notes, user_name)
                VALUES (?, 'sale', ?, ?, ?, ?, ?)
            ");

            foreach ($input['items'] as $item) {
                $pId = $item['product_id'] ?? null;
                $pName = $item['name'];
                $varName = $item['variant_name'] ?? null;
                $unit = $item['unit'] ?? 'pcs';
                $price = floatval($item['price']);
                $costPrice = floatval($item['cost_price'] ?? 0);
                $qty = floatval($item['qty']);
                $itemSubtotal = $price * $qty;

                $itemStmt->execute([
                    $transactionId, $pId, $pName, $varName, $unit, $costPrice, $price, $qty, $itemSubtotal
                ]);

                if ($pId && ($item['type'] ?? 'goods') === 'goods') {
                    $curStmt = $db->prepare("SELECT stock FROM products WHERE id = ?");
                    $curStmt->execute([$pId]);
                    $row = $curStmt->fetch();
                    $prevStock = $row ? floatval($row['stock']) : 0;
                    $nextStock = $prevStock - $qty;

                    $stockStmt->execute([$qty, $pId]);
                    $mutationStmt->execute([
                        $pId, $qty, $prevStock, $nextStock, "Faktur #$invoiceNo", $cashierName
                    ]);
                }
            }

            // Catat ke Buku Hutang / Kasbon jika metode kasbon
            if ($paymentMethod === 'debt' && $customerId) {
                $debtAmount = $total - $amountPaid; // sisa hutang setelah DP
                $db->prepare("
                    INSERT INTO debts (customer_id, transaction_id, invoice_no, total_debt, remaining_debt, status, notes)
                    VALUES (?, ?, ?, ?, ?, 'unpaid', ?)
                ")->execute([$customerId, $transactionId, $invoiceNo, $debtAmount, $debtAmount, "Kasbon Faktur $invoiceNo"]);

                $db->prepare("UPDATE customers SET total_debt = total_debt + ? WHERE id = ?")
                   ->execute([$debtAmount, $customerId]);

                // Jika ada DP tunai
                if ($amountPaid > 0) {
                    $debtId = $db->lastInsertId();
                    $db->prepare("
                        INSERT INTO debt_payments (debt_id, customer_id, amount_paid, payment_method, cashier_name, notes)
                        VALUES (?, ?, ?, 'cash', ?, 'DP Transaksi Kasir')
                    ")->execute([$debtId, $customerId, $amountPaid, $cashierName]);
                }
            }

            // Jika transaksi melunasi Open Bill
            if ($openBillId) {
                $db->prepare("UPDATE open_bills SET status = 'settled', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$openBillId]);
            }

            $db->commit();

            echo json_encode([
                'success' => true,
                'transaction_id' => $transactionId,
                'invoice_no' => $invoiceNo,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'split_cash' => $splitCash,
                'split_non_cash' => $splitNonCash,
                'points_used' => $pointsUsed,
                'points_discount' => $pointsDiscount,
                'points_earned' => $pointsEarned,
                'cashier_name' => $cashierName,
                'customer_name' => $customerName,
                'order_ref' => $orderRef,
                'handled_by' => $handledBy,
                'created_at' => date('d M Y, H:i')
            ]);
        } catch (Throwable $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Gagal memproses transaksi: ' . $e->getMessage()]);
        }
        break;

    // =========================================================================
    // 4. KELOLA KATEGORI (Master Data)
    // =========================================================================
    case 'categories_list':
        $cats = $db->query("
            SELECT c.*, COUNT(p.id) as product_count 
            FROM categories c 
            LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1 
            GROUP BY c.id 
            ORDER BY c.sort_order ASC, c.name ASC
        ")->fetchAll();
        echo json_encode(['success' => true, 'data' => $cats]);
        break;

    case 'categories_save':
        $id = $input['id'] ?? null;
        $name = trim($input['name'] ?? '');
        $icon = trim($input['icon'] ?? 'tag');
        $imageUrl = trim($input['image_url'] ?? '');
        $color = trim($input['color'] ?? 'teal');
        $sortOrder = intval($input['sort_order'] ?? 0);

        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Nama kategori wajib diisi.']);
            exit;
        }

        if ($id) {
            $stmt = $db->prepare("UPDATE categories SET name = ?, icon = ?, image_url = ?, color = ?, sort_order = ? WHERE id = ?");
            $stmt->execute([$name, $icon, $imageUrl, $color, $sortOrder, $id]);
        } else {
            $stmt = $db->prepare("INSERT INTO categories (name, icon, image_url, color, sort_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $icon, $imageUrl, $color, $sortOrder]);
            $id = $db->lastInsertId();
        }
        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Kategori berhasil disimpan']);
        break;

    case 'categories_delete':
        $id = intval($input['id'] ?? 0);
        if ($id > 0) {
            $db->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        }
        echo json_encode(['success' => true, 'message' => 'Kategori berhasil dihapus']);
        break;

    // =========================================================================
    // 5. KELOLA PRODUK & VARIAN (Master Data)
    // =========================================================================
    case 'products_list':
        $stmt = $db->query("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.id DESC
        ");
        $prods = $stmt->fetchAll();
        $varStmt = $db->query("SELECT * FROM product_variants WHERE is_active = 1");
        $vars = $varStmt->fetchAll();
        $vMap = [];
        foreach ($vars as $v) { $vMap[$v['product_id']][] = $v; }
        foreach ($prods as &$p) { $p['variants'] = $vMap[$p['id']] ?? []; }
        echo json_encode(['success' => true, 'data' => $prods]);
        break;

    case 'products_save':
        $id = $input['id'] ?? null;
        $catId = !empty($input['category_id']) ? intval($input['category_id']) : null;
        $code = trim($input['code'] ?? '');
        $name = trim($input['name'] ?? '');
        $unit = trim($input['unit'] ?? 'pcs');
        $costPrice = floatval($input['cost_price'] ?? 0);
        $sellPrice = floatval($input['sell_price'] ?? 0);
        $stock = floatval($input['stock'] ?? 0);
        $minStock = floatval($input['min_stock_alert'] ?? 5);
        $isScale = intval($input['is_scale'] ?? 0);
        $hasVariants = intval($input['has_variants'] ?? 0);
        $imageUrl = trim($input['image_url'] ?? '');
        $commType = trim($input['commission_type'] ?? 'none');
        $commVal = floatval($input['commission_val'] ?? 0);
        $wholesaleQty = floatval($input['wholesale_min_qty'] ?? 0);
        $wholesalePrice = floatval($input['wholesale_price'] ?? 0);

        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Nama produk wajib diisi.']);
            exit;
        }

        if ($id) {
            $stmt = $db->prepare("
                UPDATE products SET category_id = ?, code = ?, name = ?, unit = ?, cost_price = ?, sell_price = ?, 
                       stock = ?, min_stock_alert = ?, is_scale = ?, has_variants = ?, image_url = ?, 
                       commission_type = ?, commission_val = ?, wholesale_min_qty = ?, wholesale_price = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            $stmt->execute([
                $catId, $code, $name, $unit, $costPrice, $sellPrice, 
                $stock, $minStock, $isScale, $hasVariants, $imageUrl, 
                $commType, $commVal, $wholesaleQty, $wholesalePrice, $id
            ]);
        } else {
            $stmt = $db->prepare("
                INSERT INTO products (
                    category_id, code, name, unit, cost_price, sell_price, stock, min_stock_alert,
                    is_scale, has_variants, image_url, commission_type, commission_val, wholesale_min_qty, wholesale_price
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $catId, $code, $name, $unit, $costPrice, $sellPrice, $stock, $minStock,
                $isScale, $hasVariants, $imageUrl, $commType, $commVal, $wholesaleQty, $wholesalePrice
            ]);
            $id = $db->lastInsertId();
        }

        if ($hasVariants && !empty($input['variants'])) {
            $db->prepare("DELETE FROM product_variants WHERE product_id = ?")->execute([$id]);
            $vStmt = $db->prepare("INSERT INTO product_variants (product_id, name, cost_price, sell_price, stock) VALUES (?, ?, ?, ?, ?)");
            foreach ($input['variants'] as $v) {
                if (!empty($v['name'])) {
                    $vStmt->execute([$id, trim($v['name']), floatval($v['cost_price'] ?? 0), floatval($v['sell_price']), intval($v['stock'] ?? 0)]);
                }
            }
        }

        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Produk berhasil disimpan']);
        break;

    case 'products_delete':
        $id = intval($input['id'] ?? 0);
        if ($id > 0) {
            $db->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
        }
        echo json_encode(['success' => true]);
        break;

    // =========================================================================
    // 6. PELANGGAN & BUKU KASBON / HUTANG
    // =========================================================================
    case 'customers_list':
        $customers = $db->query("SELECT * FROM customers ORDER BY total_debt DESC, name ASC")->fetchAll();
        echo json_encode(['success' => true, 'data' => $customers]);
        break;

    case 'customers_save':
        $id = $input['id'] ?? null;
        $name = trim($input['name'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $address = trim($input['address'] ?? '');
        $tier = in_array($input['tier'] ?? '', ['regular', 'vip', 'wholesale']) ? $input['tier'] : 'regular';
        $points = intval($input['points'] ?? 0);
        $discountPercent = floatval($input['discount_percent'] ?? 0);
        $debtLimit = floatval($input['debt_limit'] ?? 500000);
        $notes = trim($input['notes'] ?? '');

        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Nama pelanggan wajib diisi.']);
            exit;
        }

        if ($id) {
            $db->prepare("UPDATE customers SET name = ?, phone = ?, address = ?, tier = ?, points = ?, discount_percent = ?, debt_limit = ?, notes = ? WHERE id = ?")
               ->execute([$name, $phone, $address, $tier, $points, $discountPercent, $debtLimit, $notes, $id]);
        } else {
            $db->prepare("INSERT INTO customers (name, phone, address, tier, points, discount_percent, debt_limit, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
               ->execute([$name, $phone, $address, $tier, $points, $discountPercent, $debtLimit, $notes]);
            $id = $db->lastInsertId();
        }
        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Data pelanggan & tier member berhasil disimpan']);
        break;

    case 'debts_list':
        $debts = $db->query("
            SELECT d.*, c.name as customer_name, c.phone as customer_phone, c.debt_limit 
            FROM debts d 
            JOIN customers c ON d.customer_id = c.id 
            WHERE d.status != 'paid' 
            ORDER BY d.id DESC
        ")->fetchAll();
        echo json_encode(['success' => true, 'data' => $debts]);
        break;

    case 'debt_pay':
        $debtId = intval($input['debt_id'] ?? 0);
        $amountPaid = floatval($input['amount'] ?? 0);
        $method = $input['payment_method'] ?? 'cash';
        $activeUser = getActiveUser($db);

        if ($debtId <= 0 || $amountPaid <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID Hutang dan nominal pembayaran wajib valid.']);
            exit;
        }

        $dStmt = $db->prepare("SELECT * FROM debts WHERE id = ?");
        $dStmt->execute([$debtId]);
        $debt = $dStmt->fetch();
        if (!$debt) {
            echo json_encode(['success' => false, 'error' => 'Catatan hutang tidak ditemukan.']);
            exit;
        }

        $remaining = max(0, floatval($debt['remaining_debt']) - $amountPaid);
        $newStatus = ($remaining == 0) ? 'paid' : 'partial';

        $db->beginTransaction();
        try {
            $db->prepare("UPDATE debts SET remaining_debt = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
               ->execute([$remaining, $newStatus, $debtId]);

            $db->prepare("UPDATE customers SET total_debt = MAX(0, total_debt - ?) WHERE id = ?")
               ->execute([$amountPaid, $debt['customer_id']]);

            $db->prepare("
                INSERT INTO debt_payments (debt_id, customer_id, amount_paid, payment_method, cashier_name, notes)
                VALUES (?, ?, ?, ?, ?, 'Pelunasan Cicilan Kasbon')
            ")->execute([$debtId, $debt['customer_id'], $amountPaid, $method, $activeUser['name']]);

            $db->commit();
            echo json_encode([
                'success' => true, 
                'remaining' => $remaining, 
                'status' => $newStatus, 
                'message' => 'Pembayaran kasbon sebesar Rp ' . number_format($amountPaid, 0, ',', '.') . ' berhasil dicatat.'
            ]);
        } catch (Throwable $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Gagal mencatat pembayaran: ' . $e->getMessage()]);
        }
        break;

    // =========================================================================
    // 7. ARUS KAS & PENGELUARAN (Petty Cash)
    // =========================================================================
    case 'expenses_list':
        $date = $_GET['date'] ?? date('Y-m-d');
        $stmt = $db->prepare("SELECT * FROM expenses WHERE DATE(created_at) = ? ORDER BY id DESC");
        $stmt->execute([$date]);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'expenses_save':
        $category = trim($input['category'] ?? 'Operasional');
        $amount = floatval($input['amount'] ?? 0);
        $notes = trim($input['notes'] ?? '');
        $activeUser = getActiveUser($db);

        if ($amount <= 0 || $notes === '') {
            echo json_encode(['success' => false, 'error' => 'Nominal pengeluaran dan catatan keterangan wajib diisi.']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO expenses (category, amount, notes, cashier_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$category, $amount, $notes, $activeUser['name']]);
        echo json_encode(['success' => true, 'id' => $db->lastInsertId(), 'message' => 'Pengeluaran kas berhasil dicatat.']);
        break;

    case 'expenses_delete':
        $id = intval($input['id'] ?? 0);
        if ($id > 0) {
            $db->prepare("DELETE FROM expenses WHERE id = ?")->execute([$id]);
        }
        echo json_encode(['success' => true]);
        break;

    case 'expense_categories_list':
        $cats = $db->query("
            SELECT ec.*, COUNT(e.id) as expense_count, COALESCE(SUM(e.amount), 0) as total_spent 
            FROM expense_categories ec 
            LEFT JOIN expenses e ON e.category = ec.name 
            GROUP BY ec.id 
            ORDER BY ec.name ASC
        ")->fetchAll();
        echo json_encode(['success' => true, 'data' => $cats]);
        break;

    case 'expense_categories_save':
        $id = $input['id'] ?? null;
        $name = trim($input['name'] ?? '');
        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Nama kategori pengeluaran wajib diisi.']);
            exit;
        }
        if ($id) {
            $stmt = $db->prepare("UPDATE expense_categories SET name = ? WHERE id = ?");
            $stmt->execute([$name, $id]);
        } else {
            $stmt = $db->prepare("INSERT INTO expense_categories (name) VALUES (?)");
            $stmt->execute([$name]);
            $id = $db->lastInsertId();
        }
        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Kategori pengeluaran berhasil disimpan.']);
        break;

    case 'expense_categories_delete':
        $id = intval($input['id'] ?? 0);
        if ($id > 0) {
            $db->prepare("DELETE FROM expense_categories WHERE id = ?")->execute([$id]);
        }
        echo json_encode(['success' => true]);
        break;

    // =========================================================================
    // 8. SHIFT KASIR & LACI UANG
    // =========================================================================
    case 'shifts_current':
        $activeUser = getActiveUser($db);
        $stmt = $db->prepare("SELECT * FROM shifts WHERE cashier_id = ? AND status = 'open' ORDER BY id DESC LIMIT 1");
        $stmt->execute([$activeUser['id']]);
        $shift = $stmt->fetch();
        echo json_encode(['success' => true, 'data' => $shift ?: null]);
        break;

    case 'shifts_open':
        $startingCash = floatval($input['starting_cash'] ?? 0);
        $activeUser = getActiveUser($db);

        // Tutup shift lama jika belum ditutup
        $db->prepare("UPDATE shifts SET status = 'closed', closed_at = CURRENT_TIMESTAMP WHERE cashier_id = ? AND status = 'open'")
           ->execute([$activeUser['id']]);

        $stmt = $db->prepare("INSERT INTO shifts (cashier_id, cashier_name, starting_cash, status) VALUES (?, ?, ?, 'open')");
        $stmt->execute([$activeUser['id'], $activeUser['name'], $startingCash]);
        echo json_encode(['success' => true, 'id' => $db->lastInsertId(), 'message' => 'Shift kasir berhasil dibuka dengan modal Rp ' . number_format($startingCash, 0, ',', '.')]);
        break;

    case 'shifts_close':
        $endingActual = floatval($input['ending_cash_actual'] ?? 0);
        $activeUser = getActiveUser($db);

        $curStmt = $db->prepare("SELECT * FROM shifts WHERE cashier_id = ? AND status = 'open' ORDER BY id DESC LIMIT 1");
        $curStmt->execute([$activeUser['id']]);
        $shift = $curStmt->fetch();

        if (!$shift) {
            echo json_encode(['success' => false, 'error' => 'Tidak ada shift yang sedang terbuka.']);
            exit;
        }

        // Hitung total penjualan tunai selama shift ini
        $cashSalesStmt = $db->prepare("
            SELECT COALESCE(SUM(amount_paid - change_amount), 0) as cash_in 
            FROM transactions 
            WHERE cashier_id = ? AND payment_method IN ('cash', 'split') AND created_at >= ?
        ");
        $cashSalesStmt->execute([$activeUser['id'], $shift['opened_at']]);
        $cashIn = floatval($cashSalesStmt->fetch()['cash_in']);

        // Hitung pengeluaran selama shift
        $expStmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as exp FROM expenses WHERE cashier_name = ? AND created_at >= ?");
        $expStmt->execute([$activeUser['name'], $shift['opened_at']]);
        $cashOut = floatval($expStmt->fetch()['exp']);

        $expectedSystem = floatval($shift['starting_cash']) + $cashIn - $cashOut;
        $diff = $endingActual - $expectedSystem;

        $db->prepare("
            UPDATE shifts SET 
                ending_cash_actual = ?, ending_cash_system = ?, cash_difference = ?, 
                status = 'closed', closed_at = CURRENT_TIMESTAMP, notes = ?
            WHERE id = ?
        ")->execute([$endingActual, $expectedSystem, $diff, trim($input['notes'] ?? ''), $shift['id']]);

        echo json_encode([
            'success' => true,
            'starting_cash' => $shift['starting_cash'],
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'expected_system' => $expectedSystem,
            'actual_cash' => $endingActual,
            'difference' => $diff,
            'message' => 'Shift berhasil ditutup.'
        ]);
        break;

    // =========================================================================
    // 9. KELOLA KARYAWAN & PENGGUNA
    // =========================================================================
    case 'employees_list':
        $users = $db->query("SELECT id, name, username, role, pin, phone, is_active, last_login FROM users ORDER BY role ASC, name ASC")->fetchAll();
        echo json_encode(['success' => true, 'data' => $users]);
        break;

    case 'employees_save':
        $id = $input['id'] ?? null;
        $name = trim($input['name'] ?? '');
        $username = trim($input['username'] ?? '');
        $role = $input['role'] ?? 'cashier';
        $pin = trim($input['pin'] ?? '1234');
        $phone = trim($input['phone'] ?? '');
        $isActive = intval($input['is_active'] ?? 1);

        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Nama karyawan wajib diisi.']);
            exit;
        }

        if ($id) {
            $stmt = $db->prepare("UPDATE users SET name = ?, username = ?, role = ?, pin = ?, phone = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$name, $username, $role, $pin, $phone, $isActive, $id]);
        } else {
            $stmt = $db->prepare("INSERT INTO users (name, username, role, pin, phone, is_active) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $username, $role, $pin, $phone, $isActive]);
            $id = $db->lastInsertId();
        }
        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Data karyawan berhasil disimpan']);
        break;

    // =========================================================================
    // 10. LAPORAN KOMPREHENSIF BACK-OFFICE ADMIN
    // =========================================================================
    case 'reports_admin':
        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // Ringkasan Keuangan
        $summaryStmt = $db->prepare("
            SELECT 
                COUNT(*) as total_transactions,
                COALESCE(SUM(total), 0) as total_turnover,
                COALESCE(SUM(discount), 0) as total_discount,
                COALESCE(SUM(tax), 0) as total_tax,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END), 0) as turnover_cash,
                COALESCE(SUM(CASE WHEN payment_method = 'qris' THEN total ELSE 0 END), 0) as turnover_qris,
                COALESCE(SUM(CASE WHEN payment_method = 'debt' THEN total ELSE 0 END), 0) as turnover_debt
            FROM transactions 
            WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'completed'
        ");
        $summaryStmt->execute([$startDate, $endDate]);
        $summary = $summaryStmt->fetch();

        // HPP Modal
        $costStmt = $db->prepare("
            SELECT COALESCE(SUM(ti.cost_price * ti.qty), 0) as total_cost 
            FROM transaction_items ti 
            JOIN transactions t ON ti.transaction_id = t.id 
            WHERE DATE(t.created_at) BETWEEN ? AND ? AND t.status = 'completed'
        ");
        $costStmt->execute([$startDate, $endDate]);
        $totalCost = floatval($costStmt->fetch()['total_cost']);

        // Pengeluaran Kas (Expenses)
        $expStmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as total_expenses FROM expenses WHERE DATE(created_at) BETWEEN ? AND ?");
        $expStmt->execute([$startDate, $endDate]);
        $totalExpenses = floatval($expStmt->fetch()['total_expenses']);

        $grossProfit = max(0, floatval($summary['total_turnover']) - $totalCost);
        $netProfit = $grossProfit - $totalExpenses;
        $marginPercent = (floatval($summary['total_turnover']) > 0) ? round(($grossProfit / floatval($summary['total_turnover'])) * 100, 1) : 0;

        $summary['total_cost'] = $totalCost;
        $summary['total_expenses'] = $totalExpenses;
        $summary['gross_profit'] = $grossProfit;
        $summary['net_profit'] = $netProfit;
        $summary['margin_percent'] = $marginPercent;

        // Top 5 Terlaris
        $topStmt = $db->prepare("
            SELECT ti.product_name, SUM(ti.qty) as total_qty, SUM(ti.subtotal) as total_sales
            FROM transaction_items ti 
            JOIN transactions t ON ti.transaction_id = t.id 
            WHERE DATE(t.created_at) BETWEEN ? AND ? AND t.status = 'completed'
            GROUP BY ti.product_name 
            ORDER BY total_qty DESC LIMIT 5
        ");
        $topStmt->execute([$startDate, $endDate]);
        $topProducts = $topStmt->fetchAll();

        // Peringatan Stok Menipis (<= min_stock_alert)
        $lowStockStmt = $db->query("SELECT id, name, stock, unit, min_stock_alert FROM products WHERE is_active = 1 AND type = 'goods' AND stock <= min_stock_alert ORDER BY stock ASC");
        $lowStock = $lowStockStmt->fetchAll();

        // Rekap Komisi Karyawan (handled_by)
        $commStmt = $db->prepare("
            SELECT t.handled_by as employee_name, COUNT(t.id) as total_orders, SUM(t.total) as total_revenue
            FROM transactions t 
            WHERE DATE(t.created_at) BETWEEN ? AND ? AND t.status = 'completed' AND t.handled_by != ''
            GROUP BY t.handled_by 
            ORDER BY total_revenue DESC
        ");
        $commStmt->execute([$startDate, $endDate]);
        $commRecap = $commStmt->fetchAll();

        echo json_encode([
            'success' => true,
            'summary' => $summary,
            'top_products' => $topProducts,
            'low_stock' => $lowStock,
            'employee_commissions' => $commRecap
        ]);
        break;

    // =========================================================================
    // 11. PENGATURAN TOKO LENGKAP & INTEGRASI API
    // =========================================================================
    case 'settings_update':
        $storeName = trim($input['store_name'] ?? 'POS Offline Pro');
        $storeType = trim($input['store_type'] ?? 'Jenis Toko');
        $storePhone = trim($input['store_phone'] ?? '');
        $storeAddress = trim($input['store_address'] ?? '');
        $receiptFooter = trim($input['receipt_footer'] ?? '');
        $storeLogo = $input['store_logo'] ?? '';
        $printLogoReceipt = intval($input['print_logo_receipt'] ?? 1);
        
        $featureBarcode = intval($input['feature_barcode'] ?? 1);
        $featureVariants = intval($input['feature_variants'] ?? 1);
        $featureCustomer = intval($input['feature_customer'] ?? 1);
        $featureOpenBill = intval($input['feature_open_bill'] ?? 1);
        $autoPrint = intval($input['auto_print'] ?? 0);
        $taxEnabled = intval($input['tax_enabled'] ?? 1);
        $taxPercent = floatval($input['tax_percent'] ?? 11);
        $paperSize = $input['paper_size'] ?? '58mm';
        $adminPin = trim($input['admin_pin'] ?? '1234');
        $soundFx = intval($input['sound_fx_enabled'] ?? 1);

        $tgToken = trim($input['telegram_bot_token'] ?? '');
        $tgChatId = trim($input['telegram_chat_id'] ?? '');
        $tgDaily = intval($input['telegram_daily_report'] ?? 1);
        $tgStock = intval($input['telegram_low_stock'] ?? 1);

        $waMode = $input['wa_mode'] ?? 'wame';
        $waUrl = trim($input['wa_gateway_url'] ?? '');
        $waToken = trim($input['wa_gateway_token'] ?? '');

        $qrisProvider = $input['qris_gateway_provider'] ?? 'manual';
        $qrisKey = trim($input['qris_api_key'] ?? '');
        $qrisMerchant = trim($input['qris_merchant_code'] ?? '');

        $stmt = $db->prepare("
            UPDATE settings SET 
                store_name = ?, store_type = ?, store_phone = ?, store_address = ?, receipt_footer = ?,
                store_logo = ?, print_logo_receipt = ?,
                feature_barcode = ?, feature_variants = ?, feature_customer = ?, feature_open_bill = ?,
                auto_print = ?, tax_enabled = ?, tax_percent = ?, paper_size = ?, admin_pin = ?, sound_fx_enabled = ?,
                telegram_bot_token = ?, telegram_chat_id = ?, telegram_daily_report = ?, telegram_low_stock = ?,
                wa_mode = ?, wa_gateway_url = ?, wa_gateway_token = ?,
                qris_gateway_provider = ?, qris_api_key = ?, qris_merchant_code = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = (SELECT id FROM settings ORDER BY id DESC LIMIT 1)
        ");
        $stmt->execute([
            $storeName, $storeType, $storePhone, $storeAddress, $receiptFooter,
            $storeLogo, $printLogoReceipt,
            $featureBarcode, $featureVariants, $featureCustomer, $featureOpenBill,
            $autoPrint, $taxEnabled, $taxPercent, $paperSize, $adminPin, $soundFx,
            $tgToken, $tgChatId, $tgDaily, $tgStock,
            $waMode, $waUrl, $waToken,
            $qrisProvider, $qrisKey, $qrisMerchant
        ]);

        echo json_encode(['success' => true, 'message' => 'Semua pengaturan toko & integrasi berhasil disimpan.']);
        break;

    // =========================================================================
    // 12. TES NOTIFIKASI TELEGRAM BOT OWNER
    // =========================================================================
    case 'telegram_test':
        $token = trim($input['bot_token'] ?? '');
        $chatId = trim($input['chat_id'] ?? '');

        if ($token === '' || $chatId === '') {
            $st = $db->query("SELECT telegram_bot_token, telegram_chat_id FROM settings ORDER BY id DESC LIMIT 1")->fetch();
            $token = $token ?: ($st['telegram_bot_token'] ?? '');
            $chatId = $chatId ?: ($st['telegram_chat_id'] ?? '');
        }

        if ($token === '' || $chatId === '') {
            echo json_encode(['success' => false, 'error' => 'Bot Token dan Chat ID Telegram wajib diisi.']);
            exit;
        }

        $text = "*TES NOTIFIKASI BOT TELEGRAM*\n\n"
              . "- Koneksi POS Offline Pro ke HP Owner berhasil.\n"
              . "- Waktu: " . date('d M Y, H:i:s') . "\n"
              . "- Sistem kasir siap mengirimkan rekap omzet harian dan alert stok menipis.";

        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        $payload = json_encode([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $res = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            echo json_encode(['success' => false, 'error' => 'Gagal menghubungi server Telegram: ' . $err]);
        } else {
            $json = json_decode($res, true);
            if (!empty($json['ok'])) {
                echo json_encode(['success' => true, 'message' => 'Pesan tes berhasil dikirim ke Telegram Owner!']);
            } else {
                echo json_encode(['success' => false, 'error' => $json['description'] ?? 'Token atau Chat ID tidak valid.']);
            }
        }
        break;

    // =========================================================================
    // 13. LAPORAN RINGKAS HARIAN (Untuk Layar Kasir / Dashboard Kasir)
    // =========================================================================
    case 'reports_daily':
        $date = $_GET['date'] ?? date('Y-m-d');
        $summaryStmt = $db->prepare("
            SELECT 
                COUNT(*) as total_transactions,
                COALESCE(SUM(total), 0) as total_turnover,
                COALESCE(SUM(discount), 0) as total_discount,
                COALESCE(SUM(tax), 0) as total_tax,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END), 0) as total_cash,
                COALESCE(SUM(CASE WHEN payment_method = 'qris' THEN total ELSE 0 END), 0) as total_qris
            FROM transactions 
            WHERE DATE(created_at) = ? AND status = 'completed'
        ");
        $summaryStmt->execute([$date]);
        $summary = $summaryStmt->fetch();

        $profitStmt = $db->prepare("
            SELECT COALESCE(SUM(ti.subtotal), 0) as total_sales, COALESCE(SUM(ti.cost_price * ti.qty), 0) as total_cost
            FROM transaction_items ti 
            JOIN transactions t ON ti.transaction_id = t.id 
            WHERE DATE(t.created_at) = ? AND t.status = 'completed'
        ");
        $profitStmt->execute([$date]);
        $profitData = $profitStmt->fetch();
        $totalSales = floatval($profitData['total_sales']);
        $totalCost = floatval($profitData['total_cost']);
        $grossProfit = max(0, $totalSales - $totalCost);
        $marginPercent = ($totalSales > 0) ? round(($grossProfit / $totalSales) * 100, 1) : 0;

        // Hitung Total Pengeluaran Hari Ini
        $expStmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as exp FROM expenses WHERE DATE(created_at) = ?");
        $expStmt->execute([$date]);
        $totalExpenses = floatval($expStmt->fetch()['exp']);
        $netProfit = $grossProfit - $totalExpenses;

        $summary['total_cost'] = $totalCost;
        $summary['total_expenses'] = $totalExpenses;
        $summary['gross_profit'] = $grossProfit;
        $summary['net_profit'] = $netProfit;
        $summary['margin_percent'] = $marginPercent;

        // Tren 7 Hari
        $sevenDays = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days", strtotime($date)));
            $dayName = date('D', strtotime($d));
            $tStmt = $db->prepare("SELECT COALESCE(SUM(total), 0) as amt FROM transactions WHERE DATE(created_at) = ? AND status = 'completed'");
            $tStmt->execute([$d]);
            $sevenDays[] = [
                'date' => $d,
                'day' => $dayName,
                'amount' => floatval($tStmt->fetch()['amt'])
            ];
        }

        // Daftar Transaksi Hari Ini (untuk Layar Riwayat Kasir & Cetak Ulang Struk)
        $txStmt = $db->prepare("
            SELECT t.* 
            FROM transactions t
            WHERE DATE(t.created_at) = ? AND t.status = 'completed'
            ORDER BY t.id DESC LIMIT 100
        ");
        $txStmt->execute([$date]);
        $todayTransactions = $txStmt->fetchAll();

        foreach ($todayTransactions as &$tx) {
            $iStmt = $db->prepare("SELECT product_name as name, variant_name, unit, sell_price as price, qty, subtotal FROM transaction_items WHERE transaction_id = ?");
            $iStmt->execute([$tx['id']]);
            $tx['items'] = $iStmt->fetchAll();
        }
        unset($tx);

        echo json_encode([
            'success' => true,
            'summary' => $summary,
            'seven_days' => $sevenDays,
            'today_transactions' => $todayTransactions
        ]);
        break;

    case 'open_bills_list':
        $stmt = $db->query("SELECT * FROM open_bills WHERE status = 'open' ORDER BY id DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'open_bill_save':
        $billName = trim($input['bill_name'] ?? '');
        $items = $input['items'] ?? [];
        $activeUser = getActiveUser($db);

        if (empty($items)) {
            echo json_encode(['success' => false, 'error' => 'Pesanan masih kosong.']);
            exit;
        }
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += floatval($item['price']) * floatval($item['qty']);
        }
        $billNo = 'BILL-' . date('Ymd-His');
        $db->prepare("INSERT INTO open_bills (bill_no, bill_name, items_json, subtotal, tax, total, cashier_name, status) VALUES (?, ?, ?, ?, 0, ?, ?, 'open')")
           ->execute([$billNo, $billName ?: 'Tagihan ' . date('H:i'), json_encode($items), $subtotal, $subtotal, $activeUser['name']]);
        echo json_encode(['success' => true, 'id' => $db->lastInsertId(), 'bill_no' => $billNo]);
        break;

    case 'open_bill_delete':
        $id = intval($input['id'] ?? 0);
        if ($id > 0) {
            $db->prepare("DELETE FROM open_bills WHERE id = ?")->execute([$id]);
        }
        echo json_encode(['success' => true]);
        break;

    // =========================================================================
    // 14. MANAGEMENT DATA: BACKUP, RESTORE, STATS & RESET DATA
    // =========================================================================
    case 'database_stats':
        $dbFile = __DIR__ . '/../app/database/pos.sqlite';
        $sizeBytes = file_exists($dbFile) ? filesize($dbFile) : 0;
        $sizeFormatted = round($sizeBytes / 1024, 1) . ' KB';
        $prodCount = $db->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
        $txCount = $db->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
        $custCount = $db->query("SELECT COUNT(*) FROM customers")->fetchColumn();
        $lastModified = file_exists($dbFile) ? date('d M Y, H:i', filemtime($dbFile)) : '-';
        echo json_encode([
            'success' => true,
            'size' => $sizeFormatted,
            'products_count' => $prodCount,
            'transactions_count' => $txCount,
            'customers_count' => $custCount,
            'last_modified' => $lastModified
        ]);
        break;

    case 'backup_download':
        $dbFile = __DIR__ . '/../app/database/pos.sqlite';
        if (!file_exists($dbFile)) {
            http_response_code(404);
            echo "File database tidak ditemukan.";
            exit;
        }
        $filename = "backup_pos_nodera_" . date('Y-m-d_His') . ".sqlite";
        header('Content-Type: application/x-sqlite3');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($dbFile));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        readfile($dbFile);
        exit;

    case 'backup_restore':
        if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'File backup tidak ditemukan atau gagal diunggah.']);
            exit;
        }
        $tmpPath = $_FILES['backup_file']['tmp_name'];
        $header = file_get_contents($tmpPath, false, null, 0, 16);
        if (strpos($header, 'SQLite format 3') !== 0) {
            echo json_encode(['success' => false, 'error' => 'File bukan format database SQLite yang valid.']);
            exit;
        }
        $dbFile = __DIR__ . '/../app/database/pos.sqlite';
        if (file_exists($dbFile)) {
            copy($dbFile, $dbFile . '.emergency_bak_' . time());
        }
        if (move_uploaded_file($tmpPath, $dbFile)) {
            echo json_encode(['success' => true, 'message' => 'Database berhasil dipulihkan! Halaman akan dimuat ulang.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Gagal menimpa database. Periksa izin folder.']);
        }
        break;

    case 'factory_reset':
        $pin = trim($input['pin'] ?? '');
        $mode = trim($input['mode'] ?? 'transactions_only');
        $curPin = $db->query("SELECT admin_pin FROM settings LIMIT 1")->fetchColumn() ?: '1234';
        if ($pin !== $curPin) {
            echo json_encode(['success' => false, 'error' => 'PIN Keamanan Admin salah.']);
            exit;
        }
        $db->beginTransaction();
        try {
            $db->exec("DELETE FROM transaction_items");
            $db->exec("DELETE FROM transactions");
            $db->exec("DELETE FROM open_bill_items");
            $db->exec("DELETE FROM open_bills");
            $db->exec("DELETE FROM debts");
            $db->exec("DELETE FROM debt_payments");
            $db->exec("DELETE FROM expenses");
            $db->exec("DELETE FROM stock_mutations");
            $db->exec("DELETE FROM stock_adjustments");
            $db->exec("DELETE FROM shifts");
            $db->exec("UPDATE customers SET total_debt = 0, points = 0");

            if ($mode === 'full_reset') {
                $db->exec("DELETE FROM products");
                $db->exec("DELETE FROM categories");
                $db->exec("DELETE FROM expense_categories");
            }
            $db->commit();
            echo json_encode([
                'success' => true, 
                'message' => ($mode === 'full_reset') 
                    ? 'Database berhasil di-reset total ke setelan pabrik.' 
                    : 'Seluruh data riwayat transaksi & kas kasir berhasil dibersihkan! Data produk & pelanggan tetap tersimpan aman.'
            ]);
        } catch (Throwable $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Gagal mereset data: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Aksi API tidak valid: ' . $action]);
        break;
}
