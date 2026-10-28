<?php
// public/api.php
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
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($action) {
    case 'init':
        // Load Settings
        $settingsStmt = $db->query("SELECT key, value FROM settings");
        $settings = [];
        while ($row = $settingsStmt->fetch()) {
            $settings[$row['key']] = $row['value'];
        }

        // Load Categories
        $categories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, id ASC")->fetchAll();

        // Load Products
        $products = $db->query("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            ORDER BY p.id ASC
        ")->fetchAll();

        // Load Employees
        $employees = $db->query("SELECT * FROM employees WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        $cashiers = array_values(array_filter($employees, fn($e) => in_array($e['role'], ['cashier', 'admin'])));
        $workers = array_values(array_filter($employees, fn($e) => in_array($e['role'], ['worker', 'admin'])));

        // Load Language
        $langCode = $_GET['lang'] ?? $settings['default_language'] ?? 'id';
        $langFile = __DIR__ . "/../app/lang/{$langCode}.json";
        if (!file_exists($langFile)) {
            $langFile = __DIR__ . "/../app/lang/id.json";
        }
        $langData = json_decode(file_get_contents($langFile), true);

        // Load Queue Summary
        $activeQueue = $db->query("
            SELECT * FROM queue_tickets 
            WHERE status NOT IN ('paid', 'cancelled') 
            ORDER BY id ASC
        ")->fetchAll();

        echo json_encode([
            'success' => true,
            'settings' => $settings,
            'categories' => $categories,
            'products' => $products,
            'employees' => $employees,
            'cashiers' => $cashiers,
            'workers' => $workers,
            'lang' => $langData,
            'active_queue' => $activeQueue
        ]);
        break;

    case 'checkout':
        if (empty($input['items'])) {
            echo json_encode(['success' => false, 'error' => 'Keranjang kosong.']);
            exit;
        }

        $cashierId = $input['cashier_id'] ?? null;
        $cashierName = trim($input['cashier_name'] ?? 'Kasir Default');
        $workerId = $input['worker_id'] ?? null;
        $workerName = trim($input['worker_name'] ?? '');
        $vehiclePlate = strtoupper(trim($input['vehicle_plate'] ?? ''));
        $vehicleType = trim($input['vehicle_type'] ?? '');
        $customerName = trim($input['customer_name'] ?? '');
        $customerPhone = trim($input['customer_phone'] ?? '');
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
            echo json_encode(['success' => false, 'error' => 'Uang tunai kurang dari total tagihan.']);
            exit;
        }

        $changeAmount = ($paymentMethod === 'cash') ? max(0, $amountPaid - $total) : 0;
        if ($paymentMethod !== 'cash' && $amountPaid === 0.0) {
            $amountPaid = $total;
        }

        $db->beginTransaction();
        try {
            // Generate Invoice No
            $today = date('Ymd');
            $countStmt = $db->query("SELECT COUNT(*) as cnt FROM transactions WHERE DATE(created_at) = DATE('now')");
            $todayCount = intval($countStmt->fetch()['cnt']) + 1;
            $invoiceNo = sprintf("NOD-%s-%04d", $today, $todayCount);

            $stmt = $db->prepare("
                INSERT INTO transactions (
                    invoice_no, cashier_id, cashier_name, worker_id, worker_name, 
                    customer_name, customer_phone, vehicle_plate, vehicle_type, 
                    subtotal, discount, tax, total, payment_method, amount_paid, 
                    change_amount, status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', ?)
            ");
            $stmt->execute([
                $invoiceNo, $cashierId, $cashierName, $workerId, $workerName,
                $customerName, $customerPhone, $vehiclePlate, $vehicleType,
                $subtotal, $discount, $tax, $total, $paymentMethod, $amountPaid,
                $changeAmount, $notes
            ]);
            $transactionId = $db->lastInsertId();

            // Insert Items
            $itemStmt = $db->prepare("
                INSERT INTO transaction_items (
                    transaction_id, product_id, product_name, price, cost_price, qty, subtotal, worker_commission
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stockStmt = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock > 0");

            foreach ($input['items'] as $item) {
                $pId = $item['product_id'] ?? null;
                $pName = $item['name'];
                $price = floatval($item['price']);
                $costPrice = floatval($item['cost_price'] ?? 0);
                $qty = intval($item['qty']);
                $itemSubtotal = $price * $qty;
                $commission = floatval($item['worker_commission'] ?? 0) * $qty;

                $itemStmt->execute([
                    $transactionId, $pId, $pName, $price, $costPrice, $qty, $itemSubtotal, $commission
                ]);

                if ($pId) {
                    $stockStmt->execute([$qty, $pId]);
                }
            }

            // Jika ada tiket antrean plat ini, tandai sebagai paid
            if (!empty($vehiclePlate)) {
                $db->prepare("UPDATE queue_tickets SET status = 'paid' WHERE vehicle_plate = ? AND status != 'paid'")
                   ->execute([$vehiclePlate]);
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
                'worker_name' => $workerName,
                'vehicle_plate' => $vehiclePlate,
                'vehicle_type' => $vehicleType,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Throwable $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Checkout failed: ' . $e->getMessage()]);
        }
        break;

    case 'create_queue':
        $plate = strtoupper(trim($input['vehicle_plate'] ?? ''));
        $type = trim($input['vehicle_type'] ?? 'Motor');
        $service = trim($input['service_name'] ?? 'Cuci Motor Salju');
        $custName = trim($input['customer_name'] ?? '');
        $custPhone = trim($input['customer_phone'] ?? '');
        $workerId = $input['worker_id'] ?? null;
        $workerName = trim($input['worker_name'] ?? '');

        if (empty($plate)) {
            echo json_encode(['success' => false, 'error' => 'Nomor plat kendaraan wajib diisi untuk tiket antrean.']);
            exit;
        }

        // Generate Ticket No A-XX
        $countStmt = $db->query("SELECT COUNT(*) as cnt FROM queue_tickets WHERE DATE(created_at) = DATE('now')");
        $num = intval($countStmt->fetch()['cnt']) + 1;
        $ticketNo = sprintf("A-%02d", $num);

        $stmt = $db->prepare("
            INSERT INTO queue_tickets (ticket_no, vehicle_plate, vehicle_type, service_name, customer_name, customer_phone, worker_id, worker_name, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'waiting')
        ");
        $stmt->execute([$ticketNo, $plate, $type, $service, $custName, $custPhone, $workerId, $workerName]);
        $ticketId = $db->lastInsertId();

        echo json_encode([
            'success' => true,
            'ticket_id' => $ticketId,
            'ticket_no' => $ticketNo,
            'vehicle_plate' => $plate,
            'vehicle_type' => $type,
            'service_name' => $service,
            'worker_name' => $workerName,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        break;

    case 'employees':
        // List karyawan beserta rekap komisi hari ini
        $stmt = $db->query("
            SELECT e.*,
                COALESCE((
                    SELECT SUM(ti.worker_commission) 
                    FROM transaction_items ti 
                    JOIN transactions t ON ti.transaction_id = t.id 
                    WHERE t.worker_id = e.id AND DATE(t.created_at) = DATE('now')
                ), 0) as today_commission,
                COALESCE((
                    SELECT COUNT(DISTINCT t.id) 
                    FROM transactions t 
                    WHERE t.worker_id = e.id AND DATE(t.created_at) = DATE('now')
                ), 0) as today_jobs,
                COALESCE((
                    SELECT COUNT(DISTINCT t.id) 
                    FROM transactions t 
                    WHERE t.cashier_id = e.id AND DATE(t.created_at) = DATE('now')
                ), 0) as today_cashier_transactions,
                COALESCE((
                    SELECT SUM(t.total) 
                    FROM transactions t 
                    WHERE t.cashier_id = e.id AND DATE(t.created_at) = DATE('now')
                ), 0) as today_cashier_total
            FROM employees e 
            ORDER BY e.role DESC, e.name ASC
        ");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'save_employee':
        $id = $input['id'] ?? null;
        $name = trim($input['name'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $role = $input['role'] ?? 'cashier';
        $pin = trim($input['pin'] ?? '1234');
        $commission = floatval($input['commission_rate'] ?? 0);
        $isActive = intval($input['is_active'] ?? 1);

        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Nama karyawan wajib diisi.']);
            exit;
        }

        if ($id) {
            $stmt = $db->prepare("
                UPDATE employees 
                SET name = ?, phone = ?, role = ?, pin = ?, commission_rate = ?, is_active = ? 
                WHERE id = ?
            ");
            $stmt->execute([$name, $phone, $role, $pin, $commission, $isActive, $id]);
        } else {
            $stmt = $db->prepare("
                INSERT INTO employees (name, phone, role, pin, commission_rate, is_active) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $phone, $role, $pin, $commission, $isActive]);
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
            echo json_encode(['success' => false, 'error' => 'ID tidak valid.']);
        }
        break;

    case 'reports':
        $date = $_GET['date'] ?? date('Y-m-d');
        
        // Ringkasan Total
        $summaryStmt = $db->prepare("
            SELECT 
                COUNT(*) as total_transactions,
                COALESCE(SUM(total), 0) as total_turnover,
                COALESCE(SUM(discount), 0) as total_discount,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END), 0) as total_cash,
                COALESCE(SUM(CASE WHEN payment_method = 'qris' THEN total ELSE 0 END), 0) as total_qris,
                COALESCE(SUM(CASE WHEN payment_method = 'transfer' THEN total ELSE 0 END), 0) as total_transfer
            FROM transactions 
            WHERE DATE(created_at) = ?
        ");
        $summaryStmt->execute([$date]);
        $summary = $summaryStmt->fetch();

        // Breakdown per Kasir (Siapa pemroses pembayaran yang menghasilkan berapa)
        $cashierStmt = $db->prepare("
            SELECT 
                cashier_name, 
                COUNT(*) as total_trx, 
                SUM(total) as turnover 
            FROM transactions 
            WHERE DATE(created_at) = ? 
            GROUP BY cashier_name 
            ORDER BY turnover DESC
        ");
        $cashierStmt->execute([$date]);
        $byCashier = $cashierStmt->fetchAll();

        // Breakdown per Petugas Lapangan (Komisi Tukang Cuci)
        $workerStmt = $db->prepare("
            SELECT 
                t.worker_name, 
                COUNT(DISTINCT t.id) as total_jobs, 
                SUM(ti.worker_commission) as total_commission
            FROM transactions t 
            JOIN transaction_items ti ON t.id = ti.transaction_id 
            WHERE DATE(t.created_at) = ? AND t.worker_name IS NOT NULL AND t.worker_name != '' 
            GROUP BY t.worker_name 
            ORDER BY total_commission DESC
        ");
        $workerStmt->execute([$date]);
        $byWorker = $workerStmt->fetchAll();

        // Riwayat Transaksi Hari Ini
        $trxStmt = $db->prepare("
            SELECT * FROM transactions 
            WHERE DATE(created_at) = ? 
            ORDER BY id DESC LIMIT 50
        ");
        $trxStmt->execute([$date]);
        $recentTransactions = $trxStmt->fetchAll();

        echo json_encode([
            'success' => true,
            'date' => $date,
            'summary' => $summary,
            'by_cashier' => $byCashier,
            'by_worker' => $byWorker,
            'transactions' => $recentTransactions
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
        echo json_encode(['success' => false, 'error' => 'Aksi tidak diketahui.']);
        break;
}
