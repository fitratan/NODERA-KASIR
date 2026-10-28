-- Schema SQLite untuk Kasir Desktop & Mobile by NODERA
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT
);

CREATE TABLE IF NOT EXISTS employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT,
    role TEXT NOT NULL DEFAULT 'cashier', -- 'cashier', 'worker', 'admin'
    pin TEXT DEFAULT '1234',
    commission_rate REAL DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    icon TEXT DEFAULT 'tag',
    sort_order INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER,
    name TEXT NOT NULL,
    code TEXT,
    type TEXT NOT NULL DEFAULT 'service', -- 'service' (jasa), 'goods' (barang)
    price REAL NOT NULL,
    cost_price REAL DEFAULT 0,
    stock INTEGER DEFAULT -1, -- -1 = unlimited
    default_worker_commission REAL DEFAULT 0,
    image_url TEXT,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    invoice_no TEXT NOT NULL UNIQUE,
    cashier_id INTEGER,
    cashier_name TEXT NOT NULL,
    worker_id INTEGER,
    worker_name TEXT,
    customer_name TEXT,
    customer_phone TEXT,
    vehicle_plate TEXT,
    vehicle_type TEXT,
    subtotal REAL NOT NULL,
    discount REAL DEFAULT 0,
    tax REAL DEFAULT 0,
    total REAL NOT NULL,
    payment_method TEXT NOT NULL DEFAULT 'cash', -- 'cash', 'qris', 'transfer', 'debt'
    amount_paid REAL NOT NULL,
    change_amount REAL DEFAULT 0,
    status TEXT DEFAULT 'completed', -- 'completed', 'in_progress', 'cancelled'
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cashier_id) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (worker_id) REFERENCES employees(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS transaction_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_id INTEGER NOT NULL,
    product_id INTEGER,
    product_name TEXT NOT NULL,
    price REAL NOT NULL,
    cost_price REAL DEFAULT 0,
    qty INTEGER NOT NULL DEFAULT 1,
    subtotal REAL NOT NULL,
    worker_commission REAL DEFAULT 0,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS queue_tickets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ticket_no TEXT NOT NULL,
    vehicle_plate TEXT NOT NULL,
    vehicle_type TEXT,
    service_name TEXT NOT NULL,
    customer_name TEXT,
    customer_phone TEXT,
    worker_id INTEGER,
    worker_name TEXT,
    status TEXT DEFAULT 'waiting', -- 'waiting', 'washing', 'drying', 'ready', 'paid'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (worker_id) REFERENCES employees(id) ON DELETE SET NULL
);
