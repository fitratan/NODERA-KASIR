-- ============================================================================
-- NODERA POS - Universal UMKM Professional Schema
-- SQLite 3 Database Definition
-- ============================================================================

-- 1. PENGATURAN TOKO (Store Profile & Configuration)
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    store_name TEXT NOT NULL DEFAULT 'NODERA POS',
    store_subtitle TEXT DEFAULT 'Universal Point of Sale',
    store_phone TEXT DEFAULT '0812-3456-7890',
    store_address TEXT DEFAULT 'Jl. Raya Bisnis No. 88, Jakarta',
    receipt_footer TEXT DEFAULT 'Terima kasih atas kunjungan Anda!',
    tax_percent REAL DEFAULT 0,
    currency_symbol TEXT DEFAULT 'Rp',
    paper_size TEXT DEFAULT '58mm', -- '58mm' atau '80mm'
    theme_mode TEXT DEFAULT 'dark',  -- 'dark' atau 'light'
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. AKUN & MANAJEMEN KARYAWAN (Users / Cashiers)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    username TEXT UNIQUE,
    role TEXT NOT NULL DEFAULT 'cashier', -- 'admin' atau 'cashier'
    pin TEXT NOT NULL DEFAULT '1234',     -- PIN 4-digit untuk autentikasi cepat
    phone TEXT,
    is_active INTEGER DEFAULT 1,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. KATEGORI PRODUK & JASA (Customizable Categories)
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    color TEXT DEFAULT 'sky',     -- 'sky', 'emerald', 'amber', 'purple', 'rose', 'indigo'
    icon TEXT DEFAULT 'tag',       -- Lucide icon name
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. MASTER PRODUK & JASA (Products / Services)
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    code TEXT UNIQUE,              -- Barcode / SKU
    name TEXT NOT NULL,
    type TEXT DEFAULT 'goods',     -- 'goods' (barang fisik berstok) atau 'service' (jasa/layanan tanpa stok)
    cost_price REAL DEFAULT 0,     -- Harga modal / beli
    sell_price REAL NOT NULL,      -- Harga jual ke pelanggan
    stock INTEGER DEFAULT 0,       -- Stok saat ini (untuk goods)
    min_stock_alert INTEGER DEFAULT 5, -- Batas minimum notifikasi stok menipis
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 5. RIWAYAT MUTASI STOK (Stock Ledger / Opname)
CREATE TABLE IF NOT EXISTS stock_mutations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    type TEXT NOT NULL,            -- 'in' (kulakan/masuk), 'out' (rusak/keluar), 'adjustment' (opname), 'sale' (terjual)
    qty INTEGER NOT NULL,
    previous_stock INTEGER NOT NULL,
    current_stock INTEGER NOT NULL,
    notes TEXT,
    user_name TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 6. TRANSAKSI PENJUALAN (Sales Transactions)
CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    invoice_no TEXT UNIQUE NOT NULL,
    cashier_id INTEGER REFERENCES users(id),
    cashier_name TEXT NOT NULL,
    customer_name TEXT,
    order_ref TEXT,                -- Meja, Plat No, No Antrean, dll
    subtotal REAL NOT NULL,
    discount REAL DEFAULT 0,
    tax REAL DEFAULT 0,
    total REAL NOT NULL,
    payment_method TEXT DEFAULT 'cash', -- 'cash', 'qris', 'transfer', 'debt'
    amount_paid REAL NOT NULL,
    change_amount REAL DEFAULT 0,
    status TEXT DEFAULT 'completed',    -- 'completed', 'cancelled'
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 7. RINCIAN BARANG TRANSAKSI (Transaction Items)
CREATE TABLE IF NOT EXISTS transaction_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_id INTEGER NOT NULL REFERENCES transactions(id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES products(id) ON DELETE SET NULL,
    product_name TEXT NOT NULL,
    cost_price REAL DEFAULT 0,
    sell_price REAL NOT NULL,
    qty INTEGER NOT NULL,
    subtotal REAL NOT NULL
);

-- Indexing for high-speed queries
CREATE INDEX IF NOT EXISTS idx_products_code ON products(code);
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_transactions_created ON transactions(created_at);
CREATE INDEX IF NOT EXISTS idx_transactions_cashier ON transactions(cashier_id);
CREATE INDEX IF NOT EXISTS idx_mutations_product ON stock_mutations(product_id);
