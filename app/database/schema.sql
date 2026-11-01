-- ============================================================================
-- NODERA POS PRO - Universal UMKM Offline System
-- Canonical Database Schema (v1 to v6 Consolidated)
-- ============================================================================

-- 1. PENGATURAN TOKO & TOGGLE FITUR MODULAR
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    store_name TEXT NOT NULL DEFAULT 'POS Offline Pro',
    store_type TEXT DEFAULT 'Jenis Toko / Ritel & F&B',
    store_phone TEXT DEFAULT '08111111111',
    store_address TEXT DEFAULT 'Jl. Alamat No 99 Makassar',
    receipt_footer TEXT DEFAULT 'Terima Kasih! Silahkan Datang Kembali',
    store_footer_text TEXT DEFAULT 'Powered by dgtlnetsolution.com',
    store_logo TEXT DEFAULT '',
    print_logo_receipt INTEGER DEFAULT 1,
    
    -- Feature Toggles Modular
    feature_barcode INTEGER DEFAULT 1,
    feature_variants INTEGER DEFAULT 1,
    feature_customer INTEGER DEFAULT 1,
    feature_open_bill INTEGER DEFAULT 1,
    auto_print INTEGER DEFAULT 0,
    tax_enabled INTEGER DEFAULT 1,
    tax_name TEXT DEFAULT 'PPN',
    tax_percent REAL DEFAULT 11,
    cash_rounding INTEGER DEFAULT 0,
    open_bill_mode TEXT DEFAULT 'table',
    open_bill_presets TEXT DEFAULT 'Meja 01, Meja 02, Meja 03, Meja 04, Bungkus, Pesanan Online',
    
    currency_symbol TEXT DEFAULT 'Rp',
    paper_size TEXT DEFAULT '58mm',
    printer_connection_type TEXT DEFAULT 'browser',
    printer_bt_name TEXT DEFAULT '',
    sound_fx_enabled INTEGER DEFAULT 1,
    pos_orientation_default TEXT DEFAULT 'landscape',
    auto_lock_minutes INTEGER DEFAULT 5,
    quick_cash_enabled INTEGER DEFAULT 1,
    
    -- Otorisasi & Keamanan
    admin_pin TEXT DEFAULT '888800',
    require_pin_void INTEGER DEFAULT 1,
    require_pin_discount INTEGER DEFAULT 1,
    
    -- Kasbon & Poin Loyalitas
    debt_enabled INTEGER DEFAULT 1,
    loyalty_points_enabled INTEGER DEFAULT 1,
    point_earn_rate REAL DEFAULT 10000,
    point_redeem_value REAL DEFAULT 100,
    
    -- Multi-Level Pricing & Diskon
    feature_price_tiers INTEGER DEFAULT 1,
    tier_h1_name TEXT DEFAULT 'H1 (Normal)',
    tier_h2_name TEXT DEFAULT 'H2 (Grosir)',
    tier_h3_name TEXT DEFAULT 'H3 (Partai)',
    feature_discount INTEGER DEFAULT 1,
    feature_negative_stock INTEGER DEFAULT 0,
    feature_cashier_add_customer INTEGER DEFAULT 1,
    
    -- Pembayaran & Bank
    bank_name TEXT DEFAULT 'BCA',
    bank_account_no TEXT DEFAULT '1234-5678-90',
    bank_account_holder TEXT DEFAULT 'Toko POS Pro',
    bank_accounts TEXT DEFAULT '[]',
    qris_image TEXT DEFAULT '',
    qris_print_on_receipt INTEGER DEFAULT 1,
    qris_gateway_provider TEXT DEFAULT 'manual',
    qris_api_key TEXT DEFAULT '',
    qris_merchant_code TEXT DEFAULT '',
    edc_enabled INTEGER DEFAULT 1,
    
    -- Notifikasi Telegram & WhatsApp
    telegram_bot_token TEXT DEFAULT '',
    telegram_chat_id TEXT DEFAULT '',
    telegram_thread_id TEXT DEFAULT '',
    telegram_target_type TEXT DEFAULT 'private',
    telegram_daily_report INTEGER DEFAULT 1,
    telegram_low_stock INTEGER DEFAULT 1,
    wa_mode TEXT DEFAULT 'wame',
    wa_provider TEXT DEFAULT 'custom',
    wa_gateway_url TEXT DEFAULT '',
    wa_gateway_token TEXT DEFAULT '',
    wa_owner_phone TEXT DEFAULT '',
    wa_send_receipt INTEGER DEFAULT 1,
    wa_receipt_media INTEGER DEFAULT 1,
    wa_session_id TEXT DEFAULT '',
    
    -- Multi-Cabang & Sinkronisasi Cloud
    branch_id INTEGER DEFAULT 1,
    cloud_sync_url TEXT DEFAULT 'https://panel.dgtlnetsolution.com/api/v1/pos/sync',
    cloud_tenant_token TEXT DEFAULT '',
    last_active_timestamp INTEGER DEFAULT 0,
    
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. AKUN & MANAJEMEN KARYAWAN (Shift & PIN)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    username TEXT UNIQUE,
    role TEXT NOT NULL DEFAULT 'cashier', -- 'admin' atau 'cashier'
    pin TEXT NOT NULL DEFAULT '123456',
    phone TEXT,
    employee_code TEXT DEFAULT '',
    email TEXT DEFAULT '',
    password_hash TEXT DEFAULT '',
    salary_type TEXT DEFAULT 'monthly',
    base_salary REAL DEFAULT 0,
    commission_percent REAL DEFAULT 0,
    allowance REAL DEFAULT 0,
    branch_id INTEGER DEFAULT 1,
    is_active INTEGER DEFAULT 1,
    is_synced INTEGER DEFAULT 0,
    synced_at DATETIME DEFAULT NULL,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. KATEGORI PRODUK
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    icon TEXT DEFAULT 'tag',
    image_url TEXT DEFAULT '',
    color TEXT DEFAULT 'teal',
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. MASTER PRODUK & JASA
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    code TEXT,                     -- Barcode / SKU
    name TEXT NOT NULL,
    type TEXT DEFAULT 'goods',     -- 'goods' atau 'service'
    unit TEXT DEFAULT 'pcs',       -- 'pcs', 'kg', 'gram', 'liter', 'porsi', 'paket'
    cost_price REAL DEFAULT 0,     -- Harga modal (HPP)
    sell_price REAL NOT NULL,      -- Harga jual default / H1
    price_h1 REAL DEFAULT 0,       -- Harga Tier 1 (Normal)
    price_h2 REAL DEFAULT 0,       -- Harga Tier 2 (Grosir)
    price_h3 REAL DEFAULT 0,       -- Harga Tier 3 (Partai)
    wholesale_price REAL DEFAULT 0,
    wholesale_min_qty REAL DEFAULT 0,
    commission_type TEXT DEFAULT 'none',
    commission_val REAL DEFAULT 0,
    stock REAL DEFAULT 0,          -- Sisa stok (float untuk kg/liter)
    min_stock_alert REAL DEFAULT 5,
    is_scale INTEGER DEFAULT 0,    -- 1 jika produk timbangan/eceran
    has_variants INTEGER DEFAULT 0,-- 1 jika produk punya varian
    track_expiry INTEGER DEFAULT 0,-- 1 jika produk dipantau tanggal kedaluwarsa
    expiry_date DATE DEFAULT NULL, -- Tanggal kedaluwarsa jika track_expiry = 1
    image_url TEXT,                -- URL / Base64 foto produk
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 5. VARIAN PRODUK
CREATE TABLE IF NOT EXISTS product_variants (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    name TEXT NOT NULL,
    cost_price REAL DEFAULT 0,
    sell_price REAL NOT NULL,
    price_h1 REAL DEFAULT 0,
    price_h2 REAL DEFAULT 0,
    price_h3 REAL DEFAULT 0,
    stock INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

-- 6. DATA PELANGGAN & HUTANG / KASBON
CREATE TABLE IF NOT EXISTS customers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT,
    address TEXT,
    tier TEXT DEFAULT 'regular',   -- 'regular', 'vip', 'wholesale'
    points INTEGER DEFAULT 0,
    discount_percent REAL DEFAULT 0,
    debt_limit REAL DEFAULT 500000,
    total_debt REAL DEFAULT 0,
    notes TEXT DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 7. OPEN BILL / TAGIHAN TERSIMPAN
CREATE TABLE IF NOT EXISTS open_bills (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    bill_no TEXT UNIQUE NOT NULL,
    bill_name TEXT NOT NULL,
    customer_id INTEGER REFERENCES customers(id) ON DELETE SET NULL,
    customer_name TEXT,
    items_json TEXT NOT NULL,
    subtotal REAL NOT NULL,
    tax REAL DEFAULT 0,
    total REAL NOT NULL,
    cashier_name TEXT NOT NULL,
    status TEXT DEFAULT 'open',    -- 'open' atau 'settled'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 8. TRANSAKSI PENJUALAN SELESAI
CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    invoice_no TEXT UNIQUE NOT NULL,
    cashier_id INTEGER REFERENCES users(id),
    cashier_name TEXT NOT NULL,
    customer_id INTEGER REFERENCES customers(id) ON DELETE SET NULL,
    customer_name TEXT,
    order_ref TEXT,
    subtotal REAL NOT NULL,
    discount REAL DEFAULT 0,
    tax REAL DEFAULT 0,
    total REAL NOT NULL,
    payment_method TEXT DEFAULT 'cash', -- 'cash', 'qris', 'transfer', 'edc', 'debt', 'split'
    amount_paid REAL NOT NULL,
    change_amount REAL DEFAULT 0,
    split_cash REAL DEFAULT 0,
    split_non_cash REAL DEFAULT 0,
    handled_by TEXT DEFAULT '',
    points_used INTEGER DEFAULT 0,
    points_discount REAL DEFAULT 0,
    points_earned INTEGER DEFAULT 0,
    status TEXT DEFAULT 'completed', -- 'completed', 'voided'
    void_reason TEXT,
    voided_by TEXT,
    voided_at DATETIME,
    notes TEXT,
    branch_id INTEGER DEFAULT 1,
    is_synced INTEGER DEFAULT 0,
    synced_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 9. RINCIAN BARANG TRANSAKSI
CREATE TABLE IF NOT EXISTS transaction_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_id INTEGER NOT NULL REFERENCES transactions(id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES products(id) ON DELETE SET NULL,
    variant_id INTEGER REFERENCES product_variants(id) ON DELETE SET NULL,
    product_name TEXT NOT NULL,
    variant_name TEXT,
    unit TEXT DEFAULT 'pcs',
    cost_price REAL DEFAULT 0,
    sell_price REAL NOT NULL,
    qty REAL NOT NULL,
    subtotal REAL NOT NULL
);

-- 10. MUTASI STOK
CREATE TABLE IF NOT EXISTS stock_mutations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    type TEXT NOT NULL,            -- 'in', 'out', 'adjustment', 'sale', 'void'
    qty REAL NOT NULL,
    previous_stock REAL NOT NULL,
    current_stock REAL NOT NULL,
    notes TEXT,
    user_name TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 11. BUKU KASBON & PIUTANG PELANGGAN
CREATE TABLE IF NOT EXISTS debts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_id INTEGER NOT NULL REFERENCES customers(id) ON DELETE CASCADE,
    transaction_id INTEGER REFERENCES transactions(id) ON DELETE SET NULL,
    invoice_no TEXT,
    total_debt REAL NOT NULL,
    remaining_debt REAL NOT NULL,
    has_due_date INTEGER DEFAULT 0,-- 1 jika ada batas tanggal jatuh tempo
    due_date DATE DEFAULT NULL,    -- Tanggal jatuh tempo
    status TEXT DEFAULT 'unpaid',  -- 'unpaid', 'partial', 'paid', 'voided'
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 12. RIWAYAT CICILAN KASBON
CREATE TABLE IF NOT EXISTS debt_payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    debt_id INTEGER NOT NULL REFERENCES debts(id) ON DELETE CASCADE,
    customer_id INTEGER NOT NULL REFERENCES customers(id) ON DELETE CASCADE,
    amount_paid REAL NOT NULL,
    payment_method TEXT DEFAULT 'cash', -- 'cash', 'transfer', 'qris'
    cashier_name TEXT NOT NULL,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 13. PENGELUARAN & ARUS KAS (Petty Cash)
CREATE TABLE IF NOT EXISTS expenses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category TEXT NOT NULL,
    amount REAL NOT NULL,
    notes TEXT NOT NULL,
    cashier_name TEXT NOT NULL,
    branch_id INTEGER DEFAULT 1,
    is_synced INTEGER DEFAULT 0,
    synced_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 14. KATEGORI PENGELUARAN
CREATE TABLE IF NOT EXISTS expense_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 15. SHIFT KASIR & MANAJEMEN LACI KAS
CREATE TABLE IF NOT EXISTS shifts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cashier_id INTEGER REFERENCES users(id),
    cashier_name TEXT NOT NULL,
    starting_cash REAL NOT NULL DEFAULT 0,
    ending_cash_actual REAL DEFAULT 0,
    ending_cash_system REAL DEFAULT 0,
    cash_difference REAL DEFAULT 0,
    status TEXT DEFAULT 'open',    -- 'open', 'closed'
    notes TEXT,
    branch_id INTEGER DEFAULT 1,
    is_synced INTEGER DEFAULT 0,
    synced_at DATETIME DEFAULT NULL,
    opened_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    closed_at DATETIME
);

-- 16. PENYESUAIAN STOK (Stock Opname)
CREATE TABLE IF NOT EXISTS stock_adjustments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    type TEXT NOT NULL,            -- 'damaged', 'expired', 'personal', 'diff'
    qty REAL NOT NULL,
    loss_amount REAL DEFAULT 0,
    notes TEXT,
    user_name TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 17. TEMPLATE GROSIR OTOMATIS
CREATE TABLE IF NOT EXISTS wholesale_templates (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    min_qty REAL NOT NULL DEFAULT 3,
    discount_type TEXT NOT NULL DEFAULT 'percent',
    discount_val REAL NOT NULL DEFAULT 10,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 18. MULTI CABANG
CREATE TABLE IF NOT EXISTS branches (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    code TEXT UNIQUE,
    address TEXT,
    phone TEXT,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 19. RETUR PENJUALAN & REFUND
CREATE TABLE IF NOT EXISTS sales_returns (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    return_no TEXT UNIQUE NOT NULL,
    transaction_id INTEGER NOT NULL,
    invoice_no TEXT NOT NULL,
    customer_id INTEGER DEFAULT NULL,
    customer_name TEXT DEFAULT '',
    cashier_id INTEGER DEFAULT NULL,
    cashier_name TEXT DEFAULT '',
    total_refund REAL DEFAULT 0,
    refund_method TEXT DEFAULT 'cash', -- 'cash', 'debt_deduction', 'replacement'
    reason TEXT DEFAULT '',
    notes TEXT DEFAULT '',
    branch_id INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sales_return_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    return_id INTEGER NOT NULL REFERENCES sales_returns(id) ON DELETE CASCADE,
    transaction_item_id INTEGER DEFAULT NULL,
    product_id INTEGER NOT NULL,
    variant_id INTEGER DEFAULT NULL,
    product_name TEXT NOT NULL,
    qty REAL NOT NULL,
    refund_price REAL NOT NULL,
    subtotal REAL NOT NULL,
    condition_type TEXT DEFAULT 'good', -- 'good' (kembali ke stok jual), 'damaged' (rusak/afkir)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Catatan: Indeks performa tinggi dibuat secara otomatis di getDatabase() setelah kolom dipastikan ada.
