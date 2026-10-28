/**
 * NODERA POS - Universal UMKM Client Logic
 * Powered by Alpine.js & Modern SaaS Architecture
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('posApp', () => ({
        // App State & Routing
        currentView: 'pos', // 'pos' (Terminal Kasir Landscape) atau 'backoffice' (Portal Admin)
        backofficeTab: 'dashboard', // 'dashboard', 'products', 'categories', 'stock', 'users', 'settings'
        themeMode: 'dark', // 'dark' | 'light'
        currentLang: 'id',
        
        // Auth State
        currentUser: { id: 1, name: 'Owner / Admin', role: 'admin', username: 'admin' },
        pinInput: '',
        pinError: '',
        
        // Active Data
        settings: {
            store_name: 'NODERA POS',
            store_subtitle: 'Universal Point of Sale',
            store_phone: '0812-3456-7890',
            store_address: 'Jl. Raya Bisnis No. 88, Jakarta',
            receipt_footer: 'Terima kasih atas kunjungan Anda!',
            tax_percent: 0,
            currency_symbol: 'Rp',
            paper_size: '58mm',
            theme_mode: 'dark'
        },
        categories: [],
        products: [],
        users: [],
        lang: {},
        
        // POS Terminal State
        selectedCategory: null,
        searchQuery: '',
        cart: [],
        orderRef: '',
        customerName: '',
        orderNotes: '',
        discount: 0,
        paymentMethod: 'cash',
        cashTendered: 0,
        lastTransaction: null,
        
        // Modals
        activeModal: null, // 'lock', 'payment', 'receipt', 'product_form', 'category_form', 'stock_adjust', 'user_form'
        
        // Backoffice Forms
        productForm: { id: null, category_id: '', code: '', name: '', type: 'goods', cost_price: 0, sell_price: 0, stock: 0, min_stock_alert: 5, is_active: 1 },
        categoryForm: { id: null, name: '', color: 'sky', icon: 'tag', sort_order: 0 },
        stockForm: { product_id: null, product_name: '', current_stock: 0, type: 'in', qty: 10, notes: '' },
        userForm: { id: null, name: '', username: '', role: 'cashier', pin: '1234', phone: '', is_active: 1 },
        
        // Backoffice Reports & Audit Data
        reportDate: new Date().toISOString().split('T')[0],
        reportsData: { summary: {}, by_cashier: [], top_products: [], transactions: [] },
        stockMutations: [],
        
        // Initialization
        async init() {
            // Cek preferensi tema lokal
            const savedTheme = localStorage.getItem('nodera_theme') || 'dark';
            this.setTheme(savedTheme);

            await this.loadInitialData();

            // Shortcut Keyboard: F12 untuk Bayar, Esc untuk tutup modal
            window.addEventListener('keydown', (e) => {
                if (e.key === 'F12') {
                    e.preventDefault();
                    if (this.currentView === 'pos' && !this.isCartEmpty) {
                        this.openPaymentModal();
                    }
                } else if (e.key === 'Escape') {
                    if (this.activeModal && this.activeModal !== 'lock') {
                        this.activeModal = null;
                    }
                }
            });

            this.refreshIcons();
        },

        refreshIcons() {
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        },

        setTheme(mode) {
            this.themeMode = mode;
            localStorage.setItem('nodera_theme', mode);
            if (mode === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        toggleTheme() {
            const next = this.themeMode === 'dark' ? 'light' : 'dark';
            this.setTheme(next);
        },

        // API: Load All Data
        async loadInitialData() {
            try {
                const res = await fetch(`api.php?action=init&lang=${this.currentLang}`);
                const data = await res.json();
                if (data.success) {
                    this.settings = data.settings || this.settings;
                    this.categories = data.categories || [];
                    this.products = data.products || [];
                    this.users = data.users || [];
                    this.currentUser = data.active_user || this.currentUser;
                    this.lang = data.lang || {};

                    // Sync theme dari pengaturan jika ada
                    if (this.settings.theme_mode && !localStorage.getItem('nodera_theme')) {
                        this.setTheme(this.settings.theme_mode);
                    }
                }
            } catch (err) {
                console.error('Gagal memuat data awal:', err);
            }
            this.refreshIcons();
        },

        // Switch Language
        async setLanguage(lang) {
            this.currentLang = lang;
            await this.loadInitialData();
        },

        // Navigation Switcher
        switchView(view) {
            if (view === 'backoffice' && this.currentUser.role !== 'admin') {
                alert('Akses Terbatas: Hanya akun Admin yang dapat membuka Back-Office.');
                return;
            }
            this.currentView = view;
            if (view === 'backoffice') {
                this.loadBackofficeTab(this.backofficeTab);
            }
            this.refreshIcons();
        },

        loadBackofficeTab(tab) {
            this.backofficeTab = tab;
            if (tab === 'dashboard') {
                this.loadReports();
            } else if (tab === 'stock') {
                this.loadStockMutations();
            }
            this.refreshIcons();
        },

        // =====================================================================
        // AUTH & QUICK PIN LOCK (KASIR SHIFT SWITCH)
        // =====================================================================
        openLockModal() {
            this.pinInput = '';
            this.pinError = '';
            this.activeModal = 'lock';
            this.refreshIcons();
        },

        pressPin(num) {
            if (this.pinInput.length < 6) {
                this.pinInput += num;
                this.pinError = '';
            }
        },

        backspacePin() {
            this.pinInput = this.pinInput.slice(0, -1);
            this.pinError = '';
        },

        clearPin() {
            this.pinInput = '';
            this.pinError = '';
        },

        async submitPin() {
            if (!this.pinInput) {
                this.pinError = 'Masukkan PIN Anda.';
                return;
            }

            try {
                const res = await fetch('api.php?action=auth_login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ pin: this.pinInput })
                });
                const data = await res.json();
                if (data.success) {
                    this.currentUser = data.user;
                    this.activeModal = null;
                    this.pinInput = '';
                    this.pinError = '';
                    await this.loadInitialData();
                } else {
                    this.pinError = data.error || 'PIN salah. Silakan coba lagi.';
                    this.pinInput = '';
                }
            } catch (err) {
                this.pinError = 'Gagal menghubungi server.';
            }
        },

        // =====================================================================
        // POS TERMINAL CALCULATIONS & CART
        // =====================================================================
        get filteredProducts() {
            return this.products.filter(p => {
                const matchCat = this.selectedCategory === null || p.category_id == this.selectedCategory;
                const matchSearch = !this.searchQuery || 
                    p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (p.code && p.code.toLowerCase().includes(this.searchQuery.toLowerCase()));
                return matchCat && matchSearch;
            });
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },

        get taxAmount() {
            const pct = parseFloat(this.settings.tax_percent || 0);
            return pct > 0 ? (this.subtotal * (pct / 100)) : 0;
        },

        get total() {
            return Math.max(0, this.subtotal - this.discount + this.taxAmount);
        },

        get changeAmount() {
            if (this.paymentMethod !== 'cash') return 0;
            return Math.max(0, this.cashTendered - this.total);
        },

        get isCartEmpty() {
            return this.cart.length === 0;
        },

        get totalItemsCount() {
            return this.cart.reduce((sum, item) => sum + item.qty, 0);
        },

        addToCart(product) {
            // Cek stok jika barang fisik
            if (product.type === 'goods' && product.stock <= 0) {
                alert(`Stok produk "${product.name}" habis.`);
                return;
            }

            const idx = this.cart.findIndex(i => i.product_id === product.id);
            if (idx > -1) {
                if (product.type === 'goods' && this.cart[idx].qty >= product.stock) {
                    alert(`Stok tidak mencukupi. Sisa stok hanya ${product.stock}.`);
                    return;
                }
                this.cart[idx].qty += 1;
            } else {
                this.cart.push({
                    product_id: product.id,
                    name: product.name,
                    code: product.code || '',
                    price: parseFloat(product.sell_price),
                    cost_price: parseFloat(product.cost_price || 0),
                    qty: 1,
                    type: product.type
                });
            }
            this.refreshIcons();
        },

        updateQty(index, delta) {
            const item = this.cart[index];
            const p = this.products.find(prod => prod.id === item.product_id);

            if (delta > 0 && p && p.type === 'goods' && item.qty >= p.stock) {
                alert(`Stok maksimal tercapai (${p.stock} item).`);
                return;
            }

            item.qty += delta;
            if (item.qty <= 0) {
                this.cart.splice(index, 1);
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            this.cart = [];
            this.orderRef = '';
            this.customerName = '';
            this.orderNotes = '';
            this.cashTendered = 0;
            this.discount = 0;
        },

        handleSearchEnter() {
            if (!this.searchQuery) return;
            const match = this.products.find(p => p.code && p.code.toLowerCase() === this.searchQuery.toLowerCase().trim());
            if (match) {
                this.addToCart(match);
                this.searchQuery = '';
            }
        },

        openPaymentModal() {
            if (this.isCartEmpty) return;
            this.cashTendered = this.total;
            this.activeModal = 'payment';
            this.refreshIcons();
        },

        setCashTendered(amount) {
            if (amount === 'exact') {
                this.cashTendered = this.total;
            } else if (typeof amount === 'number') {
                this.cashTendered = amount;
            }
        },

        async processCheckout() {
            if (this.isCartEmpty) return;

            const payload = {
                customer_name: this.customerName,
                order_ref: this.orderRef,
                items: this.cart,
                payment_method: this.paymentMethod,
                amount_paid: this.cashTendered,
                discount: this.discount,
                tax: this.taxAmount,
                notes: this.orderNotes
            };

            try {
                const res = await fetch('api.php?action=checkout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    this.lastTransaction = data;
                    this.lastTransaction.items = [...this.cart];
                    this.clearCart();
                    this.activeModal = 'receipt';
                    await this.loadInitialData(); // Refresh stok barang
                } else {
                    alert(data.error || 'Terjadi kesalahan saat memproses pembayaran.');
                }
            } catch (err) {
                alert('Gagal menghubungi server kasir.');
            }
            this.refreshIcons();
        },

        printThermal() {
            window.print();
        },

        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        },

        // =====================================================================
        // BACK-OFFICE CRUD: KATEGORI
        // =====================================================================
        openCategoryModal(cat = null) {
            if (cat) {
                this.categoryForm = { ...cat };
            } else {
                this.categoryForm = { id: null, name: '', color: 'sky', icon: 'tag', sort_order: this.categories.length + 1 };
            }
            this.activeModal = 'category_form';
            this.refreshIcons();
        },

        async saveCategory() {
            if (!this.categoryForm.name.trim()) {
                alert('Nama kategori wajib diisi.');
                return;
            }

            try {
                const res = await fetch('api.php?action=categories_save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.categoryForm)
                });
                const data = await res.json();
                if (data.success) {
                    this.activeModal = null;
                    await this.loadInitialData();
                } else {
                    alert(data.error || 'Gagal menyimpan kategori.');
                }
            } catch (e) {
                alert('Gagal menghubungi server.');
            }
        },

        async deleteCategory(id) {
            if (!confirm('Hapus kategori ini? Produk dalam kategori ini akan menjadi tanpa kategori.')) return;
            try {
                const res = await fetch('api.php?action=categories_delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const data = await res.json();
                if (data.success) {
                    await this.loadInitialData();
                }
            } catch (e) {}
        },

        // =====================================================================
        // BACK-OFFICE CRUD: PRODUK & JASA
        // =====================================================================
        openProductModal(prod = null) {
            if (prod) {
                this.productForm = {
                    id: prod.id,
                    category_id: prod.category_id || '',
                    code: prod.code || '',
                    name: prod.name,
                    type: prod.type || 'goods',
                    cost_price: parseFloat(prod.cost_price || 0),
                    sell_price: parseFloat(prod.sell_price),
                    stock: parseInt(prod.stock || 0),
                    min_stock_alert: parseInt(prod.min_stock_alert || 5),
                    is_active: parseInt(prod.is_active || 1)
                };
            } else {
                this.productForm = {
                    id: null,
                    category_id: this.categories.length > 0 ? this.categories[0].id : '',
                    code: '',
                    name: '',
                    type: 'goods',
                    cost_price: 0,
                    sell_price: 0,
                    stock: 0,
                    min_stock_alert: 5,
                    is_active: 1
                };
            }
            this.activeModal = 'product_form';
            this.refreshIcons();
        },

        get productMarginPercent() {
            const sell = parseFloat(this.productForm.sell_price || 0);
            const cost = parseFloat(this.productForm.cost_price || 0);
            if (sell <= 0) return 0;
            return Math.round(((sell - cost) / sell) * 100);
        },

        async saveProduct() {
            if (!this.productForm.name.trim()) {
                alert('Nama produk atau layanan wajib diisi.');
                return;
            }
            if (parseFloat(this.productForm.sell_price) <= 0) {
                alert('Harga jual harus lebih besar dari Rp 0.');
                return;
            }

            try {
                const res = await fetch('api.php?action=products_save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.productForm)
                });
                const data = await res.json();
                if (data.success) {
                    this.activeModal = null;
                    await this.loadInitialData();
                } else {
                    alert(data.error || 'Gagal menyimpan produk.');
                }
            } catch (e) {
                alert('Gagal menghubungi server.');
            }
        },

        async deleteProduct(id) {
            if (!confirm('Hapus produk ini dari katalog?')) return;
            try {
                const res = await fetch('api.php?action=products_delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const data = await res.json();
                if (data.success) {
                    await this.loadInitialData();
                }
            } catch (e) {}
        },

        // =====================================================================
        // BACK-OFFICE CRUD: STOK & INVENTORI
        // =====================================================================
        openStockAdjustModal(prod) {
            this.stockForm = {
                product_id: prod.id,
                product_name: prod.name,
                current_stock: parseInt(prod.stock || 0),
                type: 'in', // 'in' = kulakan masuk, 'out' = rusak/keluar, 'adjustment' = opname fisik
                qty: 10,
                notes: 'Penambahan stok kulakan'
            };
            this.activeModal = 'stock_adjust';
            this.refreshIcons();
        },

        async saveStockAdjustment() {
            if (this.stockForm.qty <= 0 && this.stockForm.type !== 'adjustment') {
                alert('Jumlah stok harus lebih besar dari 0.');
                return;
            }

            try {
                const res = await fetch('api.php?action=stock_adjust', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.stockForm)
                });
                const data = await res.json();
                if (data.success) {
                    this.activeModal = null;
                    await this.loadInitialData();
                    await this.loadStockMutations();
                } else {
                    alert(data.error || 'Gagal menyesuaikan stok.');
                }
            } catch (e) {
                alert('Gagal menghubungi server.');
            }
        },

        async loadStockMutations() {
            try {
                const res = await fetch('api.php?action=stock_mutations');
                const data = await res.json();
                if (data.success) {
                    this.stockMutations = data.data || [];
                }
            } catch (e) {}
            this.refreshIcons();
        },

        // =====================================================================
        // BACK-OFFICE CRUD: AKUN & KARYAWAN
        // =====================================================================
        openUserModal(u = null) {
            if (u) {
                this.userForm = { ...u, pin: u.pin || '1234' };
            } else {
                this.userForm = { id: null, name: '', username: '', role: 'cashier', pin: '1234', phone: '', is_active: 1 };
            }
            this.activeModal = 'user_form';
            this.refreshIcons();
        },

        async saveUser() {
            if (!this.userForm.name.trim()) {
                alert('Nama karyawan wajib diisi.');
                return;
            }
            if (this.userForm.pin.length < 4) {
                alert('PIN minimal 4 angka.');
                return;
            }

            try {
                const res = await fetch('api.php?action=users_save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.userForm)
                });
                const data = await res.json();
                if (data.success) {
                    this.activeModal = null;
                    await this.loadInitialData();
                } else {
                    alert(data.error || 'Gagal menyimpan akun pengguna.');
                }
            } catch (e) {
                alert('Gagal menghubungi server.');
            }
        },

        async deleteUser(id) {
            if (!confirm('Hapus akun karyawan ini?')) return;
            try {
                const res = await fetch('api.php?action=users_delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const data = await res.json();
                if (data.success) {
                    await this.loadInitialData();
                } else {
                    alert(data.error || 'Gagal menghapus akun.');
                }
            } catch (e) {}
        },

        // =====================================================================
        // BACK-OFFICE: PENGATURAN TOKO & LAPORAN
        // =====================================================================
        async saveSettings() {
            try {
                const res = await fetch('api.php?action=settings_update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.settings)
                });
                const data = await res.json();
                if (data.success) {
                    alert('Pengaturan toko berhasil disimpan!');
                    await this.loadInitialData();
                } else {
                    alert(data.error || 'Gagal menyimpan pengaturan.');
                }
            } catch (e) {
                alert('Gagal menghubungi server.');
            }
        },

        async loadReports() {
            try {
                const res = await fetch(`api.php?action=reports_daily&date=${this.reportDate}`);
                const data = await res.json();
                if (data.success) {
                    this.reportsData = data;
                }
            } catch (e) {}
            this.refreshIcons();
        },

        // Format Uang Rupiah / Simbol
        formatMoney(num) {
            const val = parseFloat(num) || 0;
            const sym = this.settings.currency_symbol || 'Rp';
            return sym + ' ' + Math.round(val).toLocaleString('id-ID');
        }
    }));
});
