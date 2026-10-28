<?php
// public/api.php - Universal POS API
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../app/config/database.php';

try {
    $db = Database::getConnection();
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($action) {
    case 'init':
        // Settings
        $settingsStmt = $db->query("SELECT key, value FROM settings");
        $settings = [];
        while ($row = $settingsStmt->fetch()) {
            $settings[$row['key']] = $row['value'];
        }

        // Categories
        $categories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, id ASC")->fetchAll();

        // Products
        $products = $db->query("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            ORDER BY p.id ASC
        ")->fetchAll();

        // Employees
        $employees = $db->query("SELECT * FROM employees WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        $cashiers = array_values(array_filter($employees, fn($e) => in_array($e['role'], ['cashier', 'admin'])));
        $staffs = array_values(array_filter($employees, fn($e) => in_array($e['role'], ['staff', 'cashier', 'admin'])));

        // Language
        $langCode = $_GET['lang'] ?? $settings['default_language'] ?? 'id';
        $langFile = __DIR__ . "/../app/lang/{$langCode}.json";
        if (!file_exists($langFile)) {
            $langFile = __DIR__ . "/../app/lang/id.json";
        }
        $langData = json_decode(file_get_contents($langFile), true);

        echo json_encode([
            'success' => true,
            'settings' => $settings,
            'categories' => $categories,
            'products' => $products,
            'employees' => $employees,
            'cashiers' => $cashiers,
            'staffs' => $staffs,
            'lang' => $langData
        ]);
        break;

    case 'checkout':
        if (empty($input['items'])) {
            echo json_encode(['success' => false, 'error' => 'Keranjang pesanan masih kosong.']);
            exit;
        }

        $cashierId = $input['cashier_id'] ?? $input['employee_id'] ?? null;
        $cashierName = trim($input['cashier_name'] ?? '');
        $staffId = $input['staff_id'] ?? null;
        $staffName = trim($input['staff_name'] ?? '');

        // Resolve nama jika hanya ID yang dikirim
        if ($cashierId && (empty($cashierName) || $cashierName === 'Kasir Bertugas')) {
            $eStmt = $db->prepare("SELECT name FROM employees WHERE id = ?");
            $eStmt->execute([$cashierId]);
            $row = $eStmt->fetch();
            if ($row) {
                $cashierName = $row['name'];
            }
        }
        if (empty($cashierName)) {
            $cashierName = 'Kasir Bertugas';
        }

        if ($staffId && empty($staffName)) {
            $sStmt = $db->prepare("SELECT name FROM employees WHERE id = ?");
            $sStmt->execute([$staffId]);
            $sRow = $sStmt->fetch();
            if ($sRow) {
                $staffName = $sRow['name'];
            }
        }
        $customerName = trim($input['customer_name'] ?? '');
        $customerPhone = trim($input['customer_phone'] ?? '');
        $orderRef = trim($input['order_ref'] ?? '');
        $paymentMethod = $input['payment_method'] ?? 'cash';
        $amountPaid = floatval($input['amount_paid'] ?? 0);
        $discount = floatval($input['discount'] ?? 0);
        $tax = floatval($input['tax'] ?? 0);
        $notes = trim($input['notes'] ?? '');

        // Hitung total
        $subtotal = 0;
        foreach ($input['items'] as $item) {
            $subtotal += floatval($item['price']) * intval($item['qty']);
        }
        $total = max(0, $subtotal - $discount + $tax);

        if ($paymentMethod === 'cash' && $amountPaid < $total) {
            echo json_encode(['success' => false, 'error' => 'Jumlah uang tunai kurang dari total tagihan.']);
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
                    invoice_no, cashier_id, cashier_name, staff_id, staff_name, 
                    customer_name, customer_phone, order_ref, 
                    subtotal, discount, tax, total, payment_method, amount_paid, 
                    change_amount, status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', ?)
            ");
            $stmt->execute([
                $invoiceNo, $cashierId, $cashierName, $staffId, $staffName,
                $customerName, $customerPhone, $orderRef,
                $subtotal, $discount, $tax, $total, $paymentMethod, $amountPaid,
                $changeAmount, $notes
            ]);
            $transactionId = $db->lastInsertId();

            $itemStmt = $db->prepare("
                INSERT INTO transaction_items (
                    transaction_id, product_id, product_name, price, cost_price, qty, subtotal
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stockStmt = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock > 0");

            foreach ($input['items'] as $item) {
                $pId = $item['product_id'] ?? null;
                $pName = $item['name'];
                $price = floatval($item['price']);
                $costPrice = floatval($item['cost_price'] ?? 0);
                $qty = intval($item['qty']);
                $itemSubtotal = $price * $qty;

                $itemStmt->execute([
                    $transactionId, $pId, $pName, $price, $costPrice, $qty, $itemSubtotal
                ]);

                if ($pId) {
                    $stockStmt->execute([$qty, $pId]);
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
                'staff_name' => $staffName,
                'order_ref' => $orderRef,
                'customer_name' => $customerName,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Throwable $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Gagal memproses transaksi: ' . $e->getMessage()]);
        }
        break;

    case 'employees':
        // Ambil data karyawan beserta statistik shift transaksi hari ini
        $stmt = $db->query("
            SELECT e.*,
                COALESCE((
                    SELECT COUNT(*) 
                    FROM transactions t 
                    WHERE t.cashier_id = e.id AND DATE(t.created_at) = DATE('now')
                ), 0) as today_transactions,
                COALESCE((
                    SELECT SUM(t.total) 
                    FROM transactions t 
                    WHERE t.cashier_id = e.id AND DATE(t.created_at) = DATE('now')
                ), 0) as today_turnover
            FROM employees e 
            ORDER BY 
                CASE e.role WHEN 'admin' THEN 1 WHEN 'cashier' THEN 2 ELSE 3 END,
                e.name ASC
        ");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'save_employee':
        $id = $input['id'] ?? null;
        $name = trim($input['name'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $role = $input['role'] ?? 'cashier';
        $pin = trim($input['pin'] ?? '1234');
        $isActive = intval($input['is_active'] ?? 1);

        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Nama karyawan tidak boleh kosong.']);
            exit;
        }

        if ($id) {
            $stmt = $db->prepare("
                UPDATE employees 
                SET name = ?, phone = ?, role = ?, pin = ?, is_active = ? 
                WHERE id = ?
            ");
            $stmt->execute([$name, $phone, $role, $pin, $isActive, $id]);
        } else {
            $stmt = $db->prepare("
                INSERT INTO employees (name, phone, role, pin, is_active) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $phone, $role, $pin, $isActive]);
            $id = $db->lastInsertId();
        }

        echo json_encode(['success' => true, 'id' => $id]);
        break;

    case 'delete_employee':
        $id = intval($input['id'] ?? 0);
        if ($id > 0) {
            $db->prepare("DELETE FROM employees WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'ID karyawan tidak valid.']);
        }
        break;

    case 'reports':
        $date = $_GET['date'] ?? date('Y-m-d');
        
        // Ringkasan Finansial Harian
        $summaryStmt = $db->prepare("
            SELECT 
                COUNT(*) as total_transactions,
                COALESCE(SUM(total), 0) as total_turnover,
                COALESCE(SUM(discount), 0) as total_discount,
                COALESCE(SUM(tax), 0) as total_tax,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END), 0) as total_cash,
                COALESCE(SUM(CASE WHEN payment_method = 'qris' THEN total ELSE 0 END), 0) as total_qris,
                COALESCE(SUM(CASE WHEN payment_method = 'transfer' THEN total ELSE 0 END), 0) as total_transfer,
                COALESCE(SUM(CASE WHEN payment_method = 'debt' THEN total ELSE 0 END), 0) as total_debt
            FROM transactions 
            WHERE DATE(created_at) = ?
        ");
        $summaryStmt->execute([$date]);
        $summary = $summaryStmt->fetch();

        // Audit Transaksi per Kasir Pemroses Pembayaran
        $cashierStmt = $db->prepare("
            SELECT 
                cashier_name, 
                COUNT(*) as total_transactions, 
                SUM(total) as total_amount 
            FROM transactions 
            WHERE DATE(created_at) = ? 
            GROUP BY cashier_name 
            ORDER BY total_amount DESC
        ");
        $cashierStmt->execute([$date]);
        $byCashier = $cashierStmt->fetchAll();

        // 10 Produk / Jasa Terlaris Hari Ini
        $topProductsStmt = $db->prepare("
            SELECT 
                ti.product_name, 
                SUM(ti.qty) as total_qty, 
                SUM(ti.subtotal) as total_sales
            FROM transaction_items ti
            JOIN transactions t ON ti.transaction_id = t.id
            WHERE DATE(t.created_at) = ?
            GROUP BY ti.product_name
            ORDER BY total_qty DESC
            LIMIT 10
        ");
        $topProductsStmt->execute([$date]);
        $topProducts = $topProductsStmt->fetchAll();

        // Daftar Transaksi Hari Ini
        $trxStmt = $db->prepare("
            SELECT t.*,
                (SELECT COUNT(*) FROM transaction_items ti WHERE ti.transaction_id = t.id) as item_count
            FROM transactions t 
            WHERE DATE(t.created_at) = ? 
            ORDER BY t.id DESC 
            LIMIT 50
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

    case 'update_settings':
        $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (:k, :v)");
        foreach ($input as $k => $v) {
            $stmt->execute([':k' => $k, ':v' => (string)$v]);
        }
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Aksi tidak valid.']);
        break;
}
