-- ============================================================================
-- NODERA POS PRO - Universal UMKM Offline System
-- Precise Schema inspired by POS Offline Pro (Yusril Asrul) + Extra Features
-- ============================================================================

-- 1. PENGATURAN TOKO & TOGGLE FITUR MODULAR
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    store_name TEXT NOT NULL DEFAULT 'POS Offline Pro',
    store_type TEXT DEFAULT 'Jenis Toko / Ritel & F&B',
    store_phone TEXT DEFAULT '08111111111',
    store_address TEXT DEFAULT 'Jl. Alamat No 99 Makassar',
    receipt_footer TEXT DEFAULT 'Terima Kasih! Silahkan Datang Kembali',
    
    -- Feature Toggles (Sesuai frame video)
    feature_barcode INTEGER DEFAULT 1,     -- Mode scan barcode
    feature_variants INTEGER DEFAULT 1,    -- Varian produk (Ukuran, rasa, lauk)
    feature_customer INTEGER DEFAULT 1,    -- Fitur data pelanggan
    feature_open_bill INTEGER DEFAULT 1,   -- Fitur simpan tagihan / kasbon / meja
    auto_print INTEGER DEFAULT 0,          -- Cetak struk otomatis
    tax_enabled INTEGER DEFAULT 1,         -- Pajak PPN
    tax_percent REAL DEFAULT 11,           -- Default PPN 11%
    
    currency_symbol TEXT DEFAULT 'Rp',
    paper_size TEXT DEFAULT '58mm',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. AKUN & MANAJEMEN KARYAWAN (Shift & PIN)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    username TEXT UNIQUE,
    role TEXT NOT NULL DEFAULT 'cashier', -- 'admin' atau 'cashier'
    pin TEXT NOT NULL DEFAULT '1234',
    phone TEXT,
    is_active INTEGER DEFAULT 1,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. KATEGORI PRODUK
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    icon TEXT DEFAULT 'tag',
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
    sell_price REAL NOT NULL,      -- Harga jual
    stock REAL DEFAULT 0,          -- Sisa stok (float untuk kg/liter)
    min_stock_alert REAL DEFAULT 5,
    is_scale INTEGER DEFAULT 0,    -- 1 jika produk timbangan/eceran (Mendukung modal Volume vs Nominal)
    has_variants INTEGER DEFAULT 0,-- 1 jika produk punya varian (Nasi Kuning: Rendang, Telur, dll)
    image_url TEXT,                -- URL / Base64 foto produk
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 5. VARIAN PRODUK (Sub-items seperti Nasi Kuning -> Rendang, Telur, Ayam)
CREATE TABLE IF NOT EXISTS product_variants (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    name TEXT NOT NULL,            -- Nama varian (misal: Rendang, Ayam, Telur)
    cost_price REAL DEFAULT 0,
    sell_price REAL NOT NULL,
    stock INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

-- 6. DATA PELANGGAN & HUTANG / KASBON
CREATE TABLE IF NOT EXISTS customers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT,
    address TEXT,
    total_debt REAL DEFAULT 0,     -- Total kasbon/hutang belum lunas
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 7. OPEN BILL / TAGIHAN TERSIMPAN (Meja Cafe / Belum Lunas)
CREATE TABLE IF NOT EXISTS open_bills (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    bill_no TEXT UNIQUE NOT NULL,
    bill_name TEXT NOT NULL,       -- Misal: "Meja 05" atau "Pak Budi"
    customer_id INTEGER REFERENCES customers(id) ON DELETE SET NULL,
    items_json TEXT NOT NULL,      -- JSON rincian belanja
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
    payment_method TEXT DEFAULT 'cash', -- 'cash', 'qris', 'transfer', 'debt'
    amount_paid REAL NOT NULL,
    change_amount REAL DEFAULT 0,
    status TEXT DEFAULT 'completed',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 9. RINCIAN BARANG TRANSAKSI
CREATE TABLE IF NOT EXISTS transaction_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_id INTEGER NOT NULL REFERENCES transactions(id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES products(id) ON DELETE SET NULL,
    product_name TEXT NOT NULL,
    variant_name TEXT,
    unit TEXT DEFAULT 'pcs',
    cost_price REAL DEFAULT 0,
    sell_price REAL NOT NULL,
    qty REAL NOT NULL,             -- Bisa desimal untuk timbangan, misal 0.4 kg
    subtotal REAL NOT NULL
);

-- 10. MUTASI STOK
CREATE TABLE IF NOT EXISTS stock_mutations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    type TEXT NOT NULL,            -- 'in', 'out', 'adjustment', 'sale'
    qty REAL NOT NULL,
    previous_stock REAL NOT NULL,
    current_stock REAL NOT NULL,
    notes TEXT,
    user_name TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_products_cat ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_transactions_date ON transactions(created_at);
