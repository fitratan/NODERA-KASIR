<?php
/**
 * NODERA POS - Universal UMKM REST API
 * Professional Back-Office & Landscape Terminal Endpoints
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

// Helper function: get current active user
function getActiveUser(PDO $db): array {
    if (!empty($_SESSION['user_id'])) {
        $stmt = $db->prepare("SELECT id, name, username, role, phone, is_active FROM users WHERE id = ? AND is_active = 1");
        $stmt->execute([$_SESSION['user_id']]);
        $u = $stmt->fetch();
        if ($u) {
            return $u;
        }
    }
    // Default fallback to first active cashier if no session yet
    $stmt = $db->query("SELECT id, name, username, role, phone, is_active FROM users WHERE is_active = 1 ORDER BY CASE role WHEN 'admin' THEN 1 ELSE 2 END ASC LIMIT 1");
    $default = $stmt->fetch();
    return $default ?: ['id' => 1, 'name' => 'Kasir Bertugas', 'role' => 'cashier', 'username' => 'cashier'];
}

switch ($action) {

    // =========================================================================
    // 1. AUTENTIKASI & GANTI KASIR / SHIFT
    // =========================================================================
    case 'auth_login':
        $pin = trim($input['pin'] ?? '');
        $username = trim($input['username'] ?? '');

        if ($pin === '' && $username === '') {
            echo json_encode(['success' => false, 'error' => 'PIN atau Username wajib diisi.']);
            exit;
        }

        if ($pin !== '') {
            $stmt = $db->prepare("SELECT id, name, username, role, phone, is_active FROM users WHERE pin = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([$pin]);
        } else {
            $stmt = $db->prepare("SELECT id, name, username, role, phone, is_active FROM users WHERE username = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([$username]);
        }

        $user = $stmt->fetch();
        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'PIN tidak valid atau akun dinonaktifkan.']);
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

    case 'auth_check':
        $user = getActiveUser($db);
        echo json_encode(['success' => true, 'user' => $user]);
        break;

    case 'auth_logout':
        unset($_SESSION['user_id']);
        session_destroy();
        echo json_encode(['success' => true]);
        break;

    // =========================================================================
    // 2. INITIALIZATION DATA (POS & BACK-OFFICE)
    // =========================================================================
    case 'init':
        $settings = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1")->fetch() ?: [];
        $categories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, name ASC")->fetchAll();
        $products = $db->query("
            SELECT p.*, c.name as category_name, c.color as category_color 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            ORDER BY c.sort_order ASC, p.name ASC
        ")->fetchAll();
        
        $activeUser = getActiveUser($db);
        $allUsers = $db->query("SELECT id, name, username, role, phone, is_active FROM users WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

        // Language file loader
        $langCode = $_GET['lang'] ?? 'id';
        $allowedLangs = ['id', 'en', 'fr'];
        if (!in_array($langCode, $allowedLangs)) {
            $langCode = 'id';
        }
        $langFile = __DIR__ . '/../app/lang/' . $langCode . '.json';
        $langData = file_exists($langFile) ? json_decode(file_get_contents($langFile), true) : [];

        echo json_encode([
            'success' => true,
            'settings' => $settings,
            'active_user' => $activeUser,
            'users' => $allUsers,
            'categories' => $categories,
            'products' => $products,
            'lang' => $langData
        ]);
        break;

    // =========================================================================
    // 3. TRANSAKSI CHECKOUT TERMINAL KASIR
    // =========================================================================
    case 'checkout':
        if (empty($input['items'])) {
            echo json_encode(['success' => false, 'error' => 'Keranjang pesanan masih kosong.']);
            exit;
        }

        $activeUser = getActiveUser($db);
        $cashierId = $activeUser['id'];
        $cashierName = $activeUser['name'];

        $customerName = trim($input['customer_name'] ?? '');
        $orderRef = trim($input['order_ref'] ?? '');
        $paymentMethod = $input['payment_method'] ?? 'cash';
        $amountPaid = floatval($input['amount_paid'] ?? 0);
        $discount = floatval($input['discount'] ?? 0);
        $tax = floatval($input['tax'] ?? 0);
        $notes = trim($input['notes'] ?? '');

        // Hitung total dan verifikasi harga
        $subtotal = 0;
        $totalCost = 0;
        foreach ($input['items'] as $item) {
            $subtotal += floatval($item['price']) * intval($item['qty']);
            $totalCost += floatval($item['cost_price'] ?? 0) * intval($item['qty']);
        }
        $total = max(0, $subtotal - $discount + $tax);

        if ($paymentMethod === 'cash' && $amountPaid < $total) {
            echo json_encode(['success' => false, 'error' => 'Nominal uang tunai kurang dari total tagihan.']);
            exit;
        }

        $changeAmount = ($paymentMethod === 'cash') ? max(0, $amountPaid - $total) : 0;
        if ($paymentMethod !== 'cash' && $amountPaid === 0.0) {
            $amountPaid = $total;
        }

        $db->beginTransaction();
        try {
            $today = date('Ymd');
            $countStmt = $db->query("SELECT COUNT(*) as cnt FROM transactions WHERE DATE(created_at) = DATE('now')");
            $todayCount = intval($countStmt->fetch()['cnt']) + 1;
            $invoiceNo = sprintf("INV-%s-%04d", $today, $todayCount);

            $stmt = $db->prepare("
                INSERT INTO transactions (
                    invoice_no, cashier_id, cashier_name, customer_name, order_ref, 
                    subtotal, discount, tax, total, payment_method, amount_paid, 
                    change_amount, status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', ?)
            ");
            $stmt->execute([
                $invoiceNo, $cashierId, $cashierName, $customerName, $orderRef,
                $subtotal, $discount, $tax, $total, $paymentMethod, $amountPaid,
                $changeAmount, $notes
            ]);
            $transactionId = $db->lastInsertId();

            $itemStmt = $db->prepare("
                INSERT INTO transaction_items (
                    transaction_id, product_id, product_name, cost_price, sell_price, qty, subtotal
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stockStmt = $db->prepare("UPDATE products SET stock = stock - ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND type = 'goods'");
            $mutationStmt = $db->prepare("
                INSERT INTO stock_mutations (product_id, type, qty, previous_stock, current_stock, notes, user_name)
                VALUES (?, 'sale', ?, ?, ?, ?, ?)
            ");

            foreach ($input['items'] as $item) {
                $pId = $item['product_id'] ?? null;
                $pName = $item['name'];
                $price = floatval($item['price']);
                $costPrice = floatval($item['cost_price'] ?? 0);
                $qty = intval($item['qty']);
                $itemSubtotal = $price * $qty;

                $itemStmt->execute([
                    $transactionId, $pId, $pName, $costPrice, $price, $qty, $itemSubtotal
                ]);

                if ($pId && ($item['type'] ?? 'goods') === 'goods') {
                    // Ambil stok sebelumnya
                    $curStmt = $db->prepare("SELECT stock FROM products WHERE id = ?");
                    $curStmt->execute([$pId]);
                    $row = $curStmt->fetch();
                    $prevStock = $row ? intval($row['stock']) : 0;
                    $nextStock = $prevStock - $qty;

                    $stockStmt->execute([$qty, $pId]);
                    $mutationStmt->execute([
                        $pId, $qty, $prevStock, $nextStock, "Penjualan Faktur #$invoiceNo", $cashierName
                    ]);
                }
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
                'cashier_name' => $cashierName,
                'customer_name' => $customerName,
                'order_ref' => $orderRef,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Throwable $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Gagal memproses transaksi: ' . $e->getMessage()]);
        }
        break;

    // =========================================================================
    // 4. KELOLA PENGATURAN TOKO (Store Settings)
    // =========================================================================
    case 'settings_update':
        $storeName = trim($input['store_name'] ?? 'NODERA POS');
        $storeSubtitle = trim($input['store_subtitle'] ?? '');
        $storePhone = trim($input['store_phone'] ?? '');
        $storeAddress = trim($input['store_address'] ?? '');
        $receiptFooter = trim($input['receipt_footer'] ?? '');
        $taxPercent = floatval($input['tax_percent'] ?? 0);
        $currencySymbol = trim($input['currency_symbol'] ?? 'Rp');
        $paperSize = $input['paper_size'] ?? '58mm';
        $themeMode = $input['theme_mode'] ?? 'dark';

        $stmt = $db->prepare("
            UPDATE settings SET 
                store_name = ?, store_subtitle = ?, store_phone = ?, store_address = ?,
                receipt_footer = ?, tax_percent = ?, currency_symbol = ?, paper_size = ?,
                theme_mode = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = (SELECT id FROM settings ORDER BY id DESC LIMIT 1)
        ");
        $stmt->execute([
            $storeName, $storeSubtitle, $storePhone, $storeAddress,
            $receiptFooter, $taxPercent, $currencySymbol, $paperSize, $themeMode
        ]);

        echo json_encode(['success' => true, 'message' => 'Pengaturan toko berhasil diperbarui.']);
        break;

    // =========================================================================
    // 5. KELOLA KATEGORI (Categories CRUD)
    // =========================================================================
    case 'categories_list':
        $stmt = $db->query("
            SELECT c.*, COUNT(p.id) as total_products 
            FROM categories c 
            LEFT JOIN products p ON p.category_id = c.id 
            GROUP BY c.id 
            ORDER BY c.sort_order ASC, c.name ASC
        ");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'categories_save':
        $id = $input['id'] ?? null;
        $name = trim($input['name'] ?? '');
        $color = $input['color'] ?? 'sky';
        $icon = $input['icon'] ?? 'tag';
        $sortOrder = intval($input['sort_order'] ?? 0);

        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Nama kategori wajib diisi.']);
            exit;
        }

        if ($id) {
            $stmt = $db->prepare("UPDATE categories SET name = ?, color = ?, icon = ?, sort_order = ? WHERE id = ?");
            $stmt->execute([$name, $color, $icon, $sortOrder, $id]);
        } else {
            $stmt = $db->prepare("INSERT INTO categories (name, color, icon, sort_order) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $color, $icon, $sortOrder]);
            $id = $db->lastInsertId();
        }

        echo json_encode(['success' => true, 'id' => $id]);
        break;

    case 'categories_delete':
        $id = intval($input['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
        }
        echo json_encode(['success' => true]);
        break;

    // =========================================================================
    // 6. KELOLA PRODUK & JASA (Products CRUD)
    // =========================================================================
    case 'products_list':
        $categoryId = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? intval($_GET['category_id']) : null;
        $sql = "
            SELECT p.*, c.name as category_name, c.color as category_color 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
        ";
        if ($categoryId) {
            $sql .= " WHERE p.category_id = " . $categoryId;
        }
        $sql .= " ORDER BY p.name ASC";
        $stmt = $db->query($sql);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'products_save':
        $id = $input['id'] ?? null;
        $categoryId = !empty($input['category_id']) ? intval($input['category_id']) : null;
        $code = trim($input['code'] ?? '');
        $name = trim($input['name'] ?? '');
        $type = ($input['type'] ?? 'goods') === 'service' ? 'service' : 'goods';
        $costPrice = floatval($input['cost_price'] ?? 0);
        $sellPrice = floatval($input['sell_price'] ?? 0);
        $stock = ($type === 'service') ? 0 : intval($input['stock'] ?? 0);
        $minStock = intval($input['min_stock_alert'] ?? 5);
        $isActive = intval($input['is_active'] ?? 1);

        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Nama produk/layanan wajib diisi.']);
            exit;
        }
        if ($sellPrice <= 0) {
            echo json_encode(['success' => false, 'error' => 'Harga jual harus lebih besar dari 0.']);
            exit;
        }

        if ($id) {
            $stmt = $db->prepare("
                UPDATE products SET 
                    category_id = ?, code = ?, name = ?, type = ?, cost_price = ?, 
                    sell_price = ?, stock = ?, min_stock_alert = ?, is_active = ?, 
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmt->execute([$categoryId, $code, $name, $type, $costPrice, $sellPrice, $stock, $minStock, $isActive, $id]);
        } else {
            $stmt = $db->prepare("
                INSERT INTO products (category_id, code, name, type, cost_price, sell_price, stock, min_stock_alert, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$categoryId, $code, $name, $type, $costPrice, $sellPrice, $stock, $minStock, $isActive]);
            $id = $db->lastInsertId();

            // Log stok awal
            if ($type === 'goods' && $stock > 0) {
                $user = getActiveUser($db);
                $db->prepare("
                    INSERT INTO stock_mutations (product_id, type, qty, previous_stock, current_stock, notes, user_name)
                    VALUES (?, 'in', ?, 0, ?, 'Stok Awal Produk Baru', ?)
                ")->execute([$id, $stock, $stock, $user['name']]);
            }
        }

        echo json_encode(['success' => true, 'id' => $id]);
        break;

    case 'products_delete':
        $id = intval($input['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
        }
        echo json_encode(['success' => true]);
        break;

    // =========================================================================
    // 7. KELOLA STOK (Inventori, Masuk Stok, & Opname)
    // =========================================================================
    case 'stock_adjust':
        $productId = intval($input['product_id'] ?? 0);
        $type = $input['type'] ?? 'in'; // 'in', 'out', 'adjustment'
        $qty = intval($input['qty'] ?? 0);
        $notes = trim($input['notes'] ?? '');
        $user = getActiveUser($db);

        $prodStmt = $db->prepare("SELECT * FROM products WHERE id = ? AND type = 'goods'");
        $prodStmt->execute([$productId]);
        $prod = $prodStmt->fetch();

        if (!$prod) {
            echo json_encode(['success' => false, 'error' => 'Produk tidak ditemukan atau bukan barang fisik.']);
            exit;
        }

        $prevStock = intval($prod['stock']);
        if ($type === 'in') {
            $newStock = $prevStock + $qty;
        } elseif ($type === 'out') {
            $newStock = max(0, $prevStock - $qty);
        } else { // 'adjustment' / opname langsung set angka fisik
            $newStock = max(0, $qty);
            $qty = abs($newStock - $prevStock);
        }

        $db->beginTransaction();
        try {
            $db->prepare("UPDATE products SET stock = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
               ->execute([$newStock, $productId]);

            $db->prepare("
                INSERT INTO stock_mutations (product_id, type, qty, previous_stock, current_stock, notes, user_name)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ")->execute([$productId, $type, $qty, $prevStock, $newStock, $notes, $user['name']]);

            $db->commit();
            echo json_encode(['success' => true, 'new_stock' => $newStock]);
        } catch (Throwable $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Gagal menyesuaikan stok: ' . $e->getMessage()]);
        }
        break;

    case 'stock_mutations':
        $productId = isset($_GET['product_id']) ? intval($_GET['product_id']) : null;
        $sql = "
            SELECT m.*, p.name as product_name, p.code as product_code 
            FROM stock_mutations m 
            JOIN products p ON m.product_id = p.id 
        ";
        if ($productId) {
            $sql .= " WHERE m.product_id = " . $productId;
        }
        $sql .= " ORDER BY m.created_at DESC LIMIT 100";
        $stmt = $db->query($sql);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    // =========================================================================
    // 8. MANAJEMEN AKUN & KARYAWAN (Users Management)
    // =========================================================================
    case 'users_list':
        $stmt = $db->query("
            SELECT u.*,
                COALESCE((
                    SELECT COUNT(*) 
                    FROM transactions t 
                    WHERE t.cashier_id = u.id AND DATE(t.created_at) = DATE('now')
                ), 0) as today_transactions,
                COALESCE((
                    SELECT SUM(t.total) 
                    FROM transactions t 
                    WHERE t.cashier_id = u.id AND DATE(t.created_at) = DATE('now')
                ), 0) as today_turnover
            FROM users u 
            ORDER BY 
                CASE u.role WHEN 'admin' THEN 1 ELSE 2 END,
                u.name ASC
        ");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'users_save':
        $id = $input['id'] ?? null;
        $name = trim($input['name'] ?? '');
        $username = trim($input['username'] ?? '');
        $role = ($input['role'] ?? 'cashier') === 'admin' ? 'admin' : 'cashier';
        $pin = trim($input['pin'] ?? '1234');
        $phone = trim($input['phone'] ?? '');
        $isActive = intval($input['is_active'] ?? 1);

        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Nama karyawan wajib diisi.']);
            exit;
        }
        if (strlen($pin) < 4) {
            echo json_encode(['success' => false, 'error' => 'PIN minimal 4 angka untuk keamanan.']);
            exit;
        }

        if ($id) {
            $stmt = $db->prepare("
                UPDATE users SET name = ?, username = ?, role = ?, pin = ?, phone = ?, is_active = ? WHERE id = ?
            ");
            $stmt->execute([$name, $username, $role, $pin, $phone, $isActive, $id]);
        } else {
            $stmt = $db->prepare("
                INSERT INTO users (name, username, role, pin, phone, is_active) VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $username, $role, $pin, $phone, $isActive]);
            $id = $db->lastInsertId();
        }

        echo json_encode(['success' => true, 'id' => $id]);
        break;

    case 'users_delete':
        $id = intval($input['id'] ?? 0);
        $user = getActiveUser($db);
        if ($id === intval($user['id'])) {
            echo json_encode(['success' => false, 'error' => 'Tidak dapat menghapus akun yang sedang aktif digunakan.']);
            exit;
        }
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
        }
        echo json_encode(['success' => true]);
        break;

    // =========================================================================
    // 9. LAPORAN PENJUALAN & AUDIT KASIR (Reports)
    // =========================================================================
    case 'reports_daily':
        $date = $_GET['date'] ?? date('Y-m-d');

        // Ringkasan transaksi & omset
        $summaryStmt = $db->prepare("
            SELECT 
                COUNT(*) as total_transactions,
                COALESCE(SUM(total), 0) as total_turnover,
                COALESCE(SUM(discount), 0) as total_discount,
                COALESCE(SUM(tax), 0) as total_tax,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END), 0) as total_cash,
                COALESCE(SUM(CASE WHEN payment_method = 'qris' THEN total ELSE 0 END), 0) as total_qris,
                COALESCE(SUM(CASE WHEN payment_method = 'transfer' THEN total ELSE 0 END), 0) as total_transfer
            FROM transactions 
            WHERE DATE(created_at) = ? AND status = 'completed'
        ");
        $summaryStmt->execute([$date]);
        $summary = $summaryStmt->fetch();

        // Estimasi laba kotor
        $profitStmt = $db->prepare("
            SELECT 
                COALESCE(SUM(ti.subtotal), 0) as total_sales,
                COALESCE(SUM(ti.cost_price * ti.qty), 0) as total_cost
            FROM transaction_items ti 
            JOIN transactions t ON ti.transaction_id = t.id 
            WHERE DATE(t.created_at) = ? AND t.status = 'completed'
        ");
        $profitStmt->execute([$date]);
        $profitData = $profitStmt->fetch();
        $totalSales = floatval($profitData['total_sales']);
        $totalCost = floatval($profitData['total_cost']);
        $summary['gross_profit'] = max(0, $totalSales - $totalCost);

        // Audit kasir pemroses pembayaran
        $cashierStmt = $db->prepare("
            SELECT cashier_name, COUNT(*) as total_transactions, SUM(total) as total_amount
            FROM transactions 
            WHERE DATE(created_at) = ? AND status = 'completed'
            GROUP BY cashier_name 
            ORDER BY total_amount DESC
        ");
        $cashierStmt->execute([$date]);
        $byCashier = $cashierStmt->fetchAll();

        // Top 10 produk terlaris
        $topStmt = $db->prepare("
            SELECT ti.product_name, SUM(ti.qty) as total_qty, SUM(ti.subtotal) as total_sales
            FROM transaction_items ti 
            JOIN transactions t ON ti.transaction_id = t.id 
            WHERE DATE(t.created_at) = ? AND t.status = 'completed'
            GROUP BY ti.product_name 
            ORDER BY total_qty DESC 
            LIMIT 10
        ");
        $topStmt->execute([$date]);
        $topProducts = $topStmt->fetchAll();

        // Daftar transaksi hari ini
        $trxStmt = $db->prepare("
            SELECT id, invoice_no, cashier_name, customer_name, order_ref, total, payment_method, created_at 
            FROM transactions 
            WHERE DATE(created_at) = ? 
            ORDER BY id DESC LIMIT 50
        ");
        $trxStmt->execute([$date]);
        $transactions = $trxStmt->fetchAll();

        echo json_encode([
            'success' => true,
            'date' => $date,
            'summary' => $summary,
            'by_cashier' => $byCashier,
            'top_products' => $topProducts,
            'transactions' => $transactions
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Action API tidak dikenali: ' . htmlspecialchars($action)]);
        break;
}
