/**
 * NODERA POS PRO - Client Application Engine
 * Precise Recreation & Enhancement of POS Offline Pro (Yusril Asrul)
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('posApp', () => ({
        // Routing: 'dashboard', 'pos', 'products', 'history', 'settings', 'open_bills'
        currentView: 'dashboard', 

        // Core Data
        settings: {
            store_name: 'POS Offline Pro',
            store_type: 'Jenis Toko / Ritel & F&B',
            store_phone: '08111111111',
            store_address: 'Jl. Alamat No 99 Makassar',
            receipt_footer: 'Terima Kasih! Silahkan Datang Kembali',
            feature_barcode: 1,
            feature_variants: 1,
            feature_customer: 1,
            feature_open_bill: 1,
            auto_print: 0,
            tax_enabled: 1,
            tax_percent: 11,
            currency_symbol: 'Rp',
            paper_size: '58mm'
        },
        categories: [],
        products: [],
        openBills: [],
        customers: [],
        currentUser: { id: 1, name: 'Kasir Utama', role: 'admin' },
        
        // POS State
        selectedCategory: null,
        searchQuery: '',
        cart: [],
        customerName: '',
        activeOpenBillId: null,

        // Scale Modal State (Volume vs Nominal)
        scaleModal: {
            open: false,
            product: null,
            mode: 'volume', // 'volume' atau 'nominal'
            volumeVal: 1,
            nominalVal: 20000,
            get calculatedQty() {
                if (!this.product) return 1;
                if (this.mode === 'volume') return parseFloat(this.volumeVal) || 0;
                const price = parseFloat(this.product.sell_price) || 1;
                return Math.round(((parseFloat(this.nominalVal) || 0) / price) * 100) / 100;
            },
            get calculatedTotal() {
                if (!this.product) return 0;
                if (this.mode === 'nominal') return parseFloat(this.nominalVal) || 0;
                return (parseFloat(this.volumeVal) || 0) * parseFloat(this.product.sell_price);
            }
        },

        // Variant Modal State
        variantModal: {
            open: false,
            product: null,
            selectedVariant: null
        },

        // Payment Screen (3-Column Landscape)
        paymentModal: false,
        paymentMethod: 'cash', // 'cash', 'qris', 'transfer'
        cashTendered: 0,
        cashTenderedStr: '0',

        // Receipt Modal
        receiptModal: false,
        lastTransaction: null,

        // Dashboard & Analytics
        dashboardData: {
            summary: {
                total_turnover: 0,
                total_transactions: 0,
                total_products: 0,
                total_categories: 0,
                total_cost: 0,
                gross_profit: 0,
                margin_percent: 0
            },
            seven_days: []
        },

        // Init Lifecycle
        async init() {
            await this.loadInitialData();
            await this.loadDashboardMetrics();

            // Keyboard shortcut F12 for payment
            window.addEventListener('keydown', (e) => {
                if (e.key === 'F12') {
                    e.preventDefault();
                    if (this.currentView === 'pos' && !this.isCartEmpty) {
                        this.openPayment();
                    }
                } else if (e.key === 'Escape') {
                    this.closeAllModals();
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

        closeAllModals() {
            this.scaleModal.open = false;
            this.variantModal.open = false;
            this.paymentModal = false;
            this.receiptModal = false;
            this.refreshIcons();
        },

        async loadInitialData() {
            try {
                const res = await fetch('api.php?action=init');
                const data = await res.json();
                if (data.success) {
                    this.settings = data.settings || this.settings;
                    this.categories = data.categories || [];
                    this.products = data.products || [];
                    this.openBills = data.open_bills || [];
                    this.customers = data.customers || [];
                    this.currentUser = data.active_user || this.currentUser;
                }
            } catch (err) {
                console.error('Error loading init data:', err);
            }
            this.refreshIcons();
        },

        async loadDashboardMetrics() {
            try {
                const res = await fetch('api.php?action=reports_daily');
                const data = await res.json();
                if (data.success) {
                    this.dashboardData = data;
                }
            } catch (err) {}
            this.refreshIcons();
        },

        // Navigation
        navigate(view) {
            this.currentView = view;
            if (view === 'dashboard') {
                this.loadDashboardMetrics();
            } else if (view === 'pos') {
                this.searchQuery = '';
                this.selectedCategory = null;
            }
            this.refreshIcons();
        },

        // =====================================================================
        // PRODUCT SELECTION & SMART DIALOGS (Volume vs Nominal & Variants)
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

        handleProductClick(product) {
            // 1. Jika produk timbangan/eceran (Anggur, Apel, Beras curah)
            if (product.is_scale == 1) {
                this.scaleModal.product = product;
                this.scaleModal.mode = 'volume';
                this.scaleModal.volumeVal = 1;
                this.scaleModal.nominalVal = Math.round(product.sell_price * 0.4);
                this.scaleModal.open = true;
                this.refreshIcons();
                return;
            }

            // 2. Jika produk punya varian (Nasi Kuning: Ayam, Rendang, Telur)
            if (product.has_variants == 1 && product.variants && product.variants.length > 0) {
                this.variantModal.product = product;
                this.variantModal.selectedVariant = product.variants[0];
                this.variantModal.open = true;
                this.refreshIcons();
                return;
            }

            // 3. Produk biasa -> langsung tambahkan ke keranjang
            this.addToCartDirect(product, 1);
        },

        confirmScaleItem() {
            const p = this.scaleModal.product;
            if (!p) return;
            const qty = this.scaleModal.calculatedQty;
            if (qty <= 0) return;

            this.addToCartDirect(p, qty, null, this.scaleModal.calculatedTotal);
            this.scaleModal.open = false;
        },

        confirmVariantItem(variant) {
            const p = this.variantModal.product;
            if (!p || !variant) return;

            this.addToCartDirect(p, 1, variant.name, parseFloat(variant.sell_price), parseFloat(variant.cost_price || 0));
            this.variantModal.open = false;
        },

        addToCartDirect(product, qty = 1, variantName = null, overridePrice = null, overrideCost = null) {
            const price = overridePrice !== null ? overridePrice : parseFloat(product.sell_price);
            const cost = overrideCost !== null ? overrideCost : parseFloat(product.cost_price || 0);

            // Cek apakah item yang sama persis (termasuk varian) sudah ada
            const existingIdx = this.cart.findIndex(i => 
                i.product_id === product.id && 
                i.variant_name === variantName &&
                product.is_scale != 1 // Scale item dibuat baris terpisah agar nominal akurat
            );

            if (existingIdx > -1) {
                this.cart[existingIdx].qty += qty;
            } else {
                this.cart.push({
                    product_id: product.id,
                    name: product.name,
                    variant_name: variantName,
                    unit: product.unit || 'pcs',
                    type: product.type || 'goods',
                    price: price,
                    cost_price: cost,
                    qty: qty,
                    is_scale: product.is_scale == 1
                });
            }
            this.refreshIcons();
        },

        updateCartQty(idx, delta) {
            const item = this.cart[idx];
            item.qty += delta;
            if (item.qty <= 0) {
                this.cart.splice(idx, 1);
            }
        },

        removeFromCart(idx) {
            this.cart.splice(idx, 1);
        },

        clearCart() {
            this.cart = [];
            this.customerName = '';
            this.activeOpenBillId = null;
        },

        // =====================================================================
        // CALCULATIONS & BILLING
        // =====================================================================
        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },

        get taxAmount() {
            if (!this.settings.tax_enabled) return 0;
            const pct = parseFloat(this.settings.tax_percent) || 0;
            return pct > 0 ? (this.subtotal * (pct / 100)) : 0;
        },

        get total() {
            return Math.round(this.subtotal + this.taxAmount);
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

        // =====================================================================
        // PAYMENT SCREEN (3-Column Landscape)
        // =====================================================================
        openPayment() {
            if (this.isCartEmpty) return;
            this.paymentMethod = 'cash';
            this.cashTendered = this.total;
            this.cashTenderedStr = this.total.toString();
            this.paymentModal = true;
            this.refreshIcons();
        },

        // Numpad Keypad handlers
        pressNumpad(val) {
            if (this.cashTenderedStr === '0' || this.cashTenderedStr === this.total.toString()) {
                this.cashTenderedStr = val.toString();
            } else {
                this.cashTenderedStr += val.toString();
            }
            this.cashTendered = parseFloat(this.cashTenderedStr) || 0;
        },

        backspaceNumpad() {
            this.cashTenderedStr = this.cashTenderedStr.slice(0, -1);
            if (this.cashTenderedStr === '') this.cashTenderedStr = '0';
            this.cashTendered = parseFloat(this.cashTenderedStr) || 0;
        },

        clearNumpad() {
            this.cashTenderedStr = '0';
            this.cashTendered = 0;
        },

        setQuickCash(amount) {
            if (amount === 'exact') {
                this.cashTendered = this.total;
            } else {
                this.cashTendered = amount;
            }
            this.cashTenderedStr = this.cashTendered.toString();
        },

        async processCheckout() {
            if (this.isCartEmpty) return;
            if (this.paymentMethod === 'cash' && this.cashTendered < this.total) {
                alert('Jumlah uang tunai kurang dari total tagihan.');
                return;
            }

            const payload = {
                items: this.cart,
                customer_name: this.customerName,
                payment_method: this.paymentMethod,
                amount_paid: this.cashTendered,
                tax: this.taxAmount,
                open_bill_id: this.activeOpenBillId
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
                    this.paymentModal = false;
                    this.receiptModal = true;
                    await this.loadInitialData();
                    await this.loadDashboardMetrics();

                    // Auto-print jika diaktifkan di pengaturan
                    if (this.settings.auto_print == 1) {
                        setTimeout(() => this.printReceipt(), 300);
                    }
                } else {
                    alert(data.error || 'Gagal memproses pembayaran.');
                }
            } catch (err) {
                alert('Gagal menghubungi server kasir.');
            }
            this.refreshIcons();
        },

        printReceipt() {
            window.print();
        },

        // =====================================================================
        // OPEN BILL (Simpan Tagihan / Kasbon)
        // =====================================================================
        async saveOpenBill() {
            if (this.isCartEmpty) return;
            const billName = prompt('Masukkan nama tagihan / nomor meja (Contoh: Meja 04 atau Mas Doni):', this.customerName || 'Meja Baru');
            if (!billName) return;

            try {
                const res = await fetch('api.php?action=open_bill_save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        bill_name: billName,
                        customer_name: this.customerName,
                        items: this.cart
                    })
                });
                const data = await res.json();
                if (data.success) {
                    alert('Tagihan berhasil disimpan ke daftar Open Bill.');
                    this.clearCart();
                    await this.loadInitialData();
                }
            } catch (err) {
                alert('Gagal menyimpan open bill.');
            }
        },

        loadOpenBill(bill) {
            try {
                const items = JSON.parse(bill.items_json);
                this.cart = items;
                this.customerName = bill.bill_name;
                this.activeOpenBillId = bill.id;
                this.navigate('pos');
            } catch (e) {
                alert('Gagal memuat rincian bill.');
            }
        },

        // =====================================================================
        // PENGATURAN TOKO & TOGGLE
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
                    alert('Pengaturan toko berhasil diperbarui!');
                    await this.loadInitialData();
                }
            } catch (err) {
                alert('Gagal menyimpan pengaturan.');
            }
        },

        // Formatters
        formatMoney(num) {
            const val = parseFloat(num) || 0;
            const sym = this.settings.currency_symbol || 'Rp';
            return sym + ' ' + Math.round(val).toLocaleString('id-ID');
        },

        formatQty(qty, unit) {
            const q = parseFloat(qty);
            if (unit === 'kg' || unit === 'liter') {
                return (Math.round(q * 100) / 100) + ' ' + unit;
            }
            return q + ' ' + (unit || 'x');
        }
    }));
});
