<?php
/**
 * NODERA POS PRO - Universal UMKM REST API
 * Tailored precisely to POS Offline Pro (Yusril Asrul) + Extra Features
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
    return $default ?: ['id' => 1, 'name' => 'Kasir Utama', 'role' => 'admin', 'username' => 'admin'];
}

switch ($action) {

    // =========================================================================
    // 1. AUTENTIKASI & SHIFT KASIR
    // =========================================================================
    case 'auth_login':
        $pin = trim($input['pin'] ?? '');
        $username = trim($input['username'] ?? '');

        if ($pin === '' && $username === '') {
            echo json_encode(['success' => false, 'error' => 'PIN wajib diisi.']);
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
            echo json_encode(['success' => false, 'error' => 'PIN salah atau akun tidak aktif.']);
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

    // =========================================================================
    // 2. DATA INISIALISASI LENGKAP
    // =========================================================================
    case 'init':
        $settings = $db->query("SELECT * FROM settings ORDER BY id DESC LIMIT 1")->fetch() ?: [];
        $categories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, name ASC")->fetchAll();
        
        // Produk dengan varian
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

        echo json_encode([
            'success' => true,
            'settings' => $settings,
            'categories' => $categories,
            'products' => $products,
            'open_bills' => $openBills,
            'customers' => $customers,
            'active_user' => $activeUser,
            'users' => $allUsers
        ]);
        break;

    // =========================================================================
    // 3. TRANSAKSI CHECKOUT KASIR
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
        $paymentMethod = $input['payment_method'] ?? 'cash';
        $amountPaid = floatval($input['amount_paid'] ?? 0);
        $discount = floatval($input['discount'] ?? 0);
        $tax = floatval($input['tax'] ?? 0);
        $notes = trim($input['notes'] ?? '');
        $openBillId = !empty($input['open_bill_id']) ? intval($input['open_bill_id']) : null;

        // Hitung total dan HPP modal
        $subtotal = 0;
        $totalCost = 0;
        foreach ($input['items'] as $item) {
            $subtotal += floatval($item['price']) * floatval($item['qty']);
            $totalCost += floatval($item['cost_price'] ?? 0) * floatval($item['qty']);
        }
        $total = max(0, $subtotal - $discount + $tax);

        if ($paymentMethod === 'cash' && $amountPaid < $total) {
            echo json_encode(['success' => false, 'error' => 'Uang tunai yang diterima kurang dari total tagihan.']);
            exit;
        }

        $changeAmount = ($paymentMethod === 'cash') ? max(0, $amountPaid - $total) : 0;
        if ($paymentMethod !== 'cash' && $amountPaid === 0.0) {
            $amountPaid = $total;
        }

        $db->beginTransaction();
        try {
            // Generate Invoice No: INV-YYMMDD-XXX
            $datePrefix = date('ymd');
            $countStmt = $db->query("SELECT COUNT(*) as cnt FROM transactions WHERE DATE(created_at) = DATE('now')");
            $todayCount = intval($countStmt->fetch()['cnt']) + 1;
            $invoiceNo = sprintf("INV-%s-%03d", $datePrefix, $todayCount);

            $stmt = $db->prepare("
                INSERT INTO transactions (
                    invoice_no, cashier_id, cashier_name, customer_id, customer_name, order_ref, 
                    subtotal, discount, tax, total, payment_method, amount_paid, 
                    change_amount, status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', ?)
            ");
            $stmt->execute([
                $invoiceNo, $cashierId, $cashierName, $customerId, $customerName, $orderRef,
                $subtotal, $discount, $tax, $total, $paymentMethod, $amountPaid,
                $changeAmount, $notes
            ]);
            $transactionId = $db->lastInsertId();

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
                'cashier_name' => $cashierName,
                'customer_name' => $customerName,
                'order_ref' => $orderRef,
                'created_at' => date('d M Y, H:i')
            ]);
        } catch (Throwable $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Gagal memproses transaksi: ' . $e->getMessage()]);
        }
        break;

    // =========================================================================
    // 4. FITUR OPEN BILL (Simpan Tagihan / Kasbon)
    // =========================================================================
    case 'open_bill_save':
        $billName = trim($input['bill_name'] ?? '');
        $customerName = trim($input['customer_name'] ?? '');
        $items = $input['items'] ?? [];
        $activeUser = getActiveUser($db);

        if (empty($items)) {
            echo json_encode(['success' => false, 'error' => 'Pesanan masih kosong.']);
            exit;
        }
        if ($billName === '') {
            $billName = 'Tagihan ' . date('H:i');
        }

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += floatval($item['price']) * floatval($item['qty']);
        }
        $settings = $db->query("SELECT tax_enabled, tax_percent FROM settings ORDER BY id DESC LIMIT 1")->fetch();
        $tax = (!empty($settings['tax_enabled']) && floatval($settings['tax_percent']) > 0) ? ($subtotal * floatval($settings['tax_percent']) / 100) : 0;
        $total = $subtotal + $tax;

        $billNo = 'BILL-' . date('Ymd-His');
        $stmt = $db->prepare("
            INSERT INTO open_bills (bill_no, bill_name, items_json, subtotal, tax, total, cashier_name, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'open')
        ");
        $stmt->execute([
            $billNo, $billName, json_encode($items), $subtotal, $tax, $total, $activeUser['name']
        ]);

        echo json_encode(['success' => true, 'id' => $db->lastInsertId(), 'bill_no' => $billNo]);
        break;

    case 'open_bills_list':
        $stmt = $db->query("SELECT * FROM open_bills WHERE status = 'open' ORDER BY id DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'open_bill_delete':
        $id = intval($input['id'] ?? 0);
        if ($id > 0) {
            $db->prepare("DELETE FROM open_bills WHERE id = ?")->execute([$id]);
        }
        echo json_encode(['success' => true]);
        break;

    // =========================================================================
    // 5. LAPORAN & DASHBOARD METRICS (Termasuk Tren 7 Hari)
    // =========================================================================
    case 'reports_daily':
        $date = $_GET['date'] ?? date('Y-m-d');

        // Ringkasan Hari Ini
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

        // Estimasi HPP Modal & Laba Kotor
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
        $grossProfit = max(0, $totalSales - $totalCost);
        $marginPercent = ($totalSales > 0) ? round(($grossProfit / $totalSales) * 100, 1) : 0;

        $summary['total_cost'] = $totalCost;
        $summary['gross_profit'] = $grossProfit;
        $summary['margin_percent'] = $marginPercent;

        // Total Master Data untuk Dashboard
        $totProd = $db->query("SELECT COUNT(*) as cnt FROM products WHERE is_active = 1")->fetch()['cnt'];
        $totCat = $db->query("SELECT COUNT(*) as cnt FROM categories")->fetch()['cnt'];
        $summary['total_products'] = intval($totProd);
        $summary['total_categories'] = intval($totCat);

        // Tren Penjualan 7 Hari Terakhir (Untuk Bar Chart Dashboard)
        $sevenDays = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days", strtotime($date)));
            $dayName = date('D', strtotime($d));
            $tStmt = $db->prepare("SELECT COALESCE(SUM(total), 0) as amt FROM transactions WHERE DATE(created_at) = ? AND status = 'completed'");
            $tStmt->execute([$d]);
            $amt = floatval($tStmt->fetch()['amt']);
            $sevenDays[] = [
                'date' => $d,
                'day' => $dayName,
                'amount' => $amt
            ];
        }

        // Top 10 Produk
        $topStmt = $db->prepare("
            SELECT ti.product_name, SUM(ti.qty) as total_qty, SUM(ti.subtotal) as total_sales
            FROM transaction_items ti 
            JOIN transactions t ON ti.transaction_id = t.id 
            WHERE DATE(t.created_at) = ? AND t.status = 'completed'
            GROUP BY ti.product_name 
            ORDER BY total_qty DESC LIMIT 10
        ");
        $topStmt->execute([$date]);
        $topProducts = $topStmt->fetchAll();

        // Audit per Kasir
        $cashierStmt = $db->prepare("
            SELECT cashier_name, COUNT(*) as total_transactions, SUM(total) as total_amount
            FROM transactions 
            WHERE DATE(created_at) = ? AND status = 'completed'
            GROUP BY cashier_name 
            ORDER BY total_amount DESC
        ");
        $cashierStmt->execute([$date]);
        $byCashier = $cashierStmt->fetchAll();

        echo json_encode([
            'success' => true,
            'summary' => $summary,
            'seven_days' => $sevenDays,
            'top_products' => $topProducts,
            'by_cashier' => $byCashier
        ]);
        break;

    // =========================================================================
    // 6. PENGATURAN TOKO & TOGGLE FITUR
    // =========================================================================
    case 'settings_update':
        $storeName = trim($input['store_name'] ?? 'POS Offline Pro');
        $storeType = trim($input['store_type'] ?? 'Jenis Toko');
        $storePhone = trim($input['store_phone'] ?? '');
        $storeAddress = trim($input['store_address'] ?? '');
        $receiptFooter = trim($input['receipt_footer'] ?? '');
        
        $featureBarcode = intval($input['feature_barcode'] ?? 1);
        $featureVariants = intval($input['feature_variants'] ?? 1);
        $featureCustomer = intval($input['feature_customer'] ?? 1);
        $featureOpenBill = intval($input['feature_open_bill'] ?? 1);
        $autoPrint = intval($input['auto_print'] ?? 0);
        $taxEnabled = intval($input['tax_enabled'] ?? 1);
        $taxPercent = floatval($input['tax_percent'] ?? 11);
        $paperSize = $input['paper_size'] ?? '58mm';

        $stmt = $db->prepare("
            UPDATE settings SET 
                store_name = ?, store_type = ?, store_phone = ?, store_address = ?, receipt_footer = ?,
                feature_barcode = ?, feature_variants = ?, feature_customer = ?, feature_open_bill = ?,
                auto_print = ?, tax_enabled = ?, tax_percent = ?, paper_size = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = (SELECT id FROM settings ORDER BY id DESC LIMIT 1)
        ");
        $stmt->execute([
            $storeName, $storeType, $storePhone, $storeAddress, $receiptFooter,
            $featureBarcode, $featureVariants, $featureCustomer, $featureOpenBill,
            $autoPrint, $taxEnabled, $taxPercent, $paperSize
        ]);

        echo json_encode(['success' => true, 'message' => 'Pengaturan toko berhasil diperbarui.']);
        break;

    // =========================================================================
    // 7. CRUD PRODUK & VARIAN
    // =========================================================================
    case 'products_save':
        $id = $input['id'] ?? null;
        $catId = !empty($input['category_id']) ? intval($input['category_id']) : null;
        $code = trim($input['code'] ?? '');
        $name = trim($input['name'] ?? '');
        $unit = trim($input['unit'] ?? 'pcs');
        $costPrice = floatval($input['cost_price'] ?? 0);
        $sellPrice = floatval($input['sell_price'] ?? 0);
        $stock = floatval($input['stock'] ?? 0);
        $isScale = intval($input['is_scale'] ?? 0);
        $hasVariants = intval($input['has_variants'] ?? 0);
        $imageUrl = trim($input['image_url'] ?? '');

        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Nama produk wajib diisi.']);
            exit;
        }

        if ($id) {
            $stmt = $db->prepare("
                UPDATE products SET category_id = ?, code = ?, name = ?, unit = ?, cost_price = ?, sell_price = ?, stock = ?, is_scale = ?, has_variants = ?, image_url = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?
            ");
            $stmt->execute([$catId, $code, $name, $unit, $costPrice, $sellPrice, $stock, $isScale, $hasVariants, $imageUrl, $id]);
        } else {
            $stmt = $db->prepare("
                INSERT INTO products (category_id, code, name, unit, cost_price, sell_price, stock, is_scale, has_variants, image_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$catId, $code, $name, $unit, $costPrice, $sellPrice, $stock, $isScale, $hasVariants, $imageUrl]);
            $id = $db->lastInsertId();
        }

        // Simpan varian jika diaktifkan
        if ($hasVariants && !empty($input['variants'])) {
            $db->prepare("DELETE FROM product_variants WHERE product_id = ?")->execute([$id]);
            $vStmt = $db->prepare("INSERT INTO product_variants (product_id, name, cost_price, sell_price, stock) VALUES (?, ?, ?, ?, ?)");
            foreach ($input['variants'] as $v) {
                if (!empty($v['name'])) {
                    $vStmt->execute([$id, trim($v['name']), floatval($v['cost_price'] ?? 0), floatval($v['sell_price']), intval($v['stock'] ?? 0)]);
                }
            }
        }

        echo json_encode(['success' => true, 'id' => $id]);
        break;

    case 'products_delete':
        $id = intval($input['id'] ?? 0);
        if ($id > 0) {
            $db->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
        }
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Aksi API tidak valid.']);
        break;
}
