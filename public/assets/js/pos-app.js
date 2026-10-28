// public/assets/js/pos-app.js - Universal POS Engine

document.addEventListener('alpine:init', () => {
    Alpine.data('posApp', () => ({
        // Core State
        loading: true,
        settings: {},
        categories: [],
        products: [],
        employees: [],
        cashiers: [],
        staffs: [],
        lang: {},
        currentLang: 'id',

        // Session & Shift
        activeCashier: { id: null, name: 'Kasir Utama', role: 'cashier' },
        activeStaff: { id: null, name: '' },
        customerName: '',
        customerPhone: '',
        orderRef: '',
        orderNotes: '',

        // Catalog & Filters
        selectedCategory: null,
        searchQuery: '',

        // Cart
        cart: [],
        discount: 0,
        taxPercent: 0,

        // Checkout & Payment
        paymentMethod: 'cash',
        cashTendered: 0,

        // Modals
        activeModal: null, // 'payment', 'employee', 'reports', 'settings', 'receipt'
        lastTransaction: null,

        // Employee CRUD Form
        employeeForm: { id: null, name: '', phone: '', role: 'cashier', pin: '1234', is_active: 1 },
        
        // Reports
        reportsData: { summary: {}, by_cashier: [], top_products: [], transactions: [] },
        reportDate: new Date().toISOString().split('T')[0],

        async init() {
            await this.loadInitialData();
            this.loading = false;
            this.initShortcuts();
            this.refreshIcons();
        },

        refreshIcons() {
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        },

        initShortcuts() {
            window.addEventListener('keydown', (e) => {
                if (e.key === 'F12') {
                    e.preventDefault();
                    if (!this.isCartEmpty && !this.activeModal) {
                        this.openPaymentModal();
                    }
                } else if (e.key === 'Escape') {
                    if (this.activeModal) {
                        this.activeModal = null;
                    }
                }
            });
        },

        async loadInitialData() {
            try {
                const res = await fetch(`api.php?action=init&lang=${this.currentLang}`);
                const data = await res.json();
                if (data.success) {
                    this.settings = data.settings;
                    this.categories = data.categories;
                    this.products = data.products;
                    this.employees = data.employees;
                    this.cashiers = data.cashiers;
                    this.staffs = data.staffs;
                    this.lang = data.lang;

                    // Set default cashier jika belum terpilih
                    if (!this.activeCashier.id && this.cashiers.length > 0) {
                        this.activeCashier = this.cashiers[0];
                    }
                }
            } catch (err) {
                console.error("Gagal memuat konfigurasi POS:", err);
            }
        },

        async setLanguage(langCode) {
            this.currentLang = langCode;
            await this.loadInitialData();
            this.refreshIcons();
        },

        // Filter Produk Universal
        get filteredProducts() {
            return this.products.filter(p => {
                const matchCategory = this.selectedCategory === null || p.category_id == this.selectedCategory;
                const matchSearch = !this.searchQuery || 
                    p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                    (p.code && p.code.toLowerCase().includes(this.searchQuery.toLowerCase()));
                return matchCategory && matchSearch;
            });
        },

        // Kalkulasi Keranjang
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

        // Cart Manipulations
        addToCart(product) {
            const idx = this.cart.findIndex(i => i.product_id === product.id);
            if (idx > -1) {
                this.cart[idx].qty += 1;
            } else {
                this.cart.push({
                    product_id: product.id,
                    name: product.name,
                    code: product.code || '',
                    price: parseFloat(product.price),
                    cost_price: parseFloat(product.cost_price || 0),
                    qty: 1,
                    type: product.type
                });
            }
            this.refreshIcons();
        },

        updateQty(index, delta) {
            this.cart[index].qty += delta;
            if (this.cart[index].qty <= 0) {
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
            this.customerPhone = '';
            this.orderNotes = '';
            this.cashTendered = 0;
            this.discount = 0;
        },

        // Barcode Scanner Handler (tekan enter pada kolom search)
        handleSearchEnter() {
            if (!this.searchQuery) return;
            const match = this.products.find(p => p.code && p.code.toLowerCase() === this.searchQuery.toLowerCase().trim());
            if (match) {
                this.addToCart(match);
                this.searchQuery = '';
            }
        },

        // Payment & Checkout Flow
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
                cashier_id: this.activeCashier.id,
                cashier_name: this.activeCashier.name,
                staff_id: this.activeStaff.id,
                staff_name: this.activeStaff.name,
                customer_name: this.customerName,
                customer_phone: this.customerPhone,
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
                    this.loadInitialData(); // Refresh stok
                    this.refreshIcons();
                } else {
                    alert(data.error || 'Terjadi kesalahan saat memproses pembayaran.');
                }
            } catch (err) {
                alert('Gagal menghubungi server kasir.');
            }
        },

        // Staf / Karyawan
        async openEmployeeModal() {
            try {
                const res = await fetch('api.php?action=employees');
                const data = await res.json();
                if (data.success) {
                    this.employees = data.data;
                }
            } catch (e) {}
            this.activeModal = 'employee';
            this.refreshIcons();
        },

        editEmployee(emp) {
            this.employeeForm = { ...emp };
        },

        resetEmployeeForm() {
            this.employeeForm = { id: null, name: '', phone: '', role: 'cashier', pin: '1234', is_active: 1 };
        },

        async saveEmployee() {
            if (!this.employeeForm.name.trim()) {
                alert('Nama karyawan wajib diisi.');
                return;
            }

            try {
                const res = await fetch('api.php?action=save_employee', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.employeeForm)
                });
                const data = await res.json();
                if (data.success) {
                    this.resetEmployeeForm();
                    await this.openEmployeeModal();
                    await this.loadInitialData();
                } else {
                    alert(data.error || 'Gagal menyimpan.');
                }
            } catch (e) {
                alert('Gagal menghubungi server.');
            }
        },

        async deleteEmployee(id) {
            if (!confirm('Hapus karyawan ini dari daftar?')) return;
            try {
                const res = await fetch('api.php?action=delete_employee', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const data = await res.json();
                if (data.success) {
                    await this.openEmployeeModal();
                    await this.loadInitialData();
                }
            } catch (e) {}
        },

        switchCashier(cashier) {
            this.activeCashier = cashier;
        },

        // Laporan
        async openReportsModal() {
            try {
                const res = await fetch(`api.php?action=reports&date=${this.reportDate}`);
                const data = await res.json();
                if (data.success) {
                    this.reportsData = data;
                }
            } catch (e) {}
            this.activeModal = 'reports';
            this.refreshIcons();
        },

        // Cetak Struk
        printThermal() {
            window.print();
        },

        // Helper Format Rupiah
        formatMoney(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        },

        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    }));
});
