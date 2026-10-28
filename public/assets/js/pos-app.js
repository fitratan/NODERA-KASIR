// public/assets/js/pos-app.js

document.addEventListener('alpine:init', () => {
    Alpine.data('posApp', () => ({
        // State data
        loading: true,
        settings: {},
        categories: [],
        products: [],
        employees: [],
        cashiers: [],
        workers: [],
        lang: {},
        currentLang: 'id',

        // Active Session
        activeCashier: { id: null, name: 'Kasir Utama', role: 'cashier' },
        activeWorker: { id: null, name: '' },

        // Vehicle / Customer Details
        vehiclePlate: '',
        vehicleType: '',
        customerName: '',
        customerPhone: '',
        orderNotes: '',

        // Filter & Search
        selectedCategory: null,
        searchQuery: '',
        serviceOnly: false,

        // Cart
        cart: [],
        discount: 0,
        taxPercent: 0,

        // Payment
        paymentMethod: 'cash',
        cashTendered: 0,

        // Modals
        activeModal: null, // 'payment', 'employee', 'reports', 'settings', 'receipt', 'queue_receipt'
        lastTransaction: null,
        lastQueueTicket: null,

        // Employee Management
        employeeForm: { id: null, name: '', phone: '', role: 'worker', pin: '1234', commission_rate: 5000, is_active: 1 },
        reportsData: { summary: {}, by_cashier: [], by_worker: [], transactions: [] },
        reportDate: new Date().toISOString().split('T')[0],

        async init() {
            await this.loadInitialData();
            this.loading = false;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
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
                    this.workers = data.workers;
                    this.lang = data.lang;

                    // Set default cashier jika belum dipilih
                    if (!this.activeCashier.id && this.cashiers.length > 0) {
                        this.activeCashier = this.cashiers[0];
                    }
                }
            } catch (err) {
                console.error("Gagal memuat data awal:", err);
            }
        },

        async setLanguage(langCode) {
            this.currentLang = langCode;
            await this.loadInitialData();
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        // Filtered products
        get filteredProducts() {
            return this.products.filter(p => {
                const matchCategory = this.selectedCategory === null || p.category_id == this.selectedCategory;
                const matchSearch = !this.searchQuery || 
                    p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                    (p.code && p.code.toLowerCase().includes(this.searchQuery.toLowerCase()));
                const matchService = !this.serviceOnly || p.type === 'service';
                return matchCategory && matchSearch && matchService;
            });
        },

        // Cart Computations
        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },

        get taxAmount() {
            return (this.subtotal * (parseFloat(this.settings.tax_percent || 0) / 100));
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

        // Cart Actions
        addToCart(product) {
            const existingIndex = this.cart.findIndex(i => i.product_id === product.id);
            if (existingIndex > -1) {
                this.cart[existingIndex].qty += 1;
            } else {
                this.cart.push({
                    product_id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    cost_price: parseFloat(product.cost_price || 0),
                    qty: 1,
                    type: product.type,
                    worker_commission: parseFloat(product.default_worker_commission || 0)
                });
            }

            // Jika item pertama adalah cuci motor, otomatis set teks kendaraan jika ada nama motor di nama jasa
            if (product.type === 'service' && !this.vehicleType) {
                if (product.name.includes('Matic')) this.vehicleType = 'Matic';
                else if (product.name.includes('Bebek')) this.vehicleType = 'Bebek';
                else if (product.name.includes('Maxi') || product.name.includes('NMax')) this.vehicleType = 'Maxi (NMax/PCX)';
                else if (product.name.includes('Sport') || product.name.includes('Moge')) this.vehicleType = 'Sport/Moge';
                else if (product.name.includes('Mobil')) this.vehicleType = 'Mobil';
            }
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
            this.vehiclePlate = '';
            this.vehicleType = '';
            this.customerName = '';
            this.customerPhone = '';
            this.orderNotes = '';
            this.cashTendered = 0;
            this.discount = 0;
        },

        // Payment Flow
        openPaymentModal() {
            if (this.isCartEmpty) return;
            this.cashTendered = this.total; // Default uang pas
            this.activeModal = 'payment';
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        setCashTendered(amount) {
            if (amount === 'exact') {
                this.cashTendered = this.total;
            } else {
                this.cashTendered = amount;
            }
        },

        async processCheckout() {
            if (this.isCartEmpty) return;

            const payload = {
                cashier_id: this.activeCashier.id,
                cashier_name: this.activeCashier.name,
                worker_id: this.activeWorker.id,
                worker_name: this.activeWorker.name,
                vehicle_plate: this.vehiclePlate,
                vehicle_type: this.vehicleType,
                customer_name: this.customerName,
                customer_phone: this.customerPhone,
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
                    this.loadInitialData(); // Refresh stok & queue
                } else {
                    alert(data.error || 'Terjadi kesalahan saat memproses pembayaran.');
                }
            } catch (err) {
                alert('Gagal menghubungi server.');
            }
        },

        async createQueueTicket() {
            if (!this.vehiclePlate) {
                alert('Silakan ketik nomor plat kendaraan terlebih dahulu (contoh: B 1234 ABC)!');
                return;
            }

            const serviceName = this.cart.length > 0 ? this.cart[0].name : 'Cuci Motor Standar';

            const payload = {
                vehicle_plate: this.vehiclePlate,
                vehicle_type: this.vehicleType,
                service_name: serviceName,
                customer_name: this.customerName,
                customer_phone: this.customerPhone,
                worker_id: this.activeWorker.id,
                worker_name: this.activeWorker.name
            };

            try {
                const res = await fetch('api.php?action=create_queue', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    this.lastQueueTicket = data;
                    this.activeModal = 'queue_receipt';
                } else {
                    alert(data.error);
                }
            } catch (err) {
                alert('Gagal membuat tiket antrean.');
            }
        },

        // Employee Management
        async openEmployeeModal() {
            try {
                const res = await fetch('api.php?action=employees');
                const data = await res.json();
                if (data.success) {
                    this.employees = data.data;
                }
            } catch (e) {}
            this.activeModal = 'employee';
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        editEmployee(emp) {
            this.employeeForm = { ...emp };
        },

        resetEmployeeForm() {
            this.employeeForm = { id: null, name: '', phone: '', role: 'worker', pin: '1234', commission_rate: 5000, is_active: 1 };
        },

        async saveEmployee() {
            if (!this.employeeForm.name) {
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
                    alert(data.error);
                }
            } catch (e) {
                alert('Gagal menyimpan karyawan.');
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

        switchWorker(worker) {
            this.activeWorker = worker;
        },

        // Reports
        async openReportsModal() {
            try {
                const res = await fetch(`api.php?action=reports&date=${this.reportDate}`);
                const data = await res.json();
                if (data.success) {
                    this.reportsData = data;
                }
            } catch (e) {}
            this.activeModal = 'reports';
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        // Thermal Print
        printThermal() {
            window.print();
        },

        // Helper format uang
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
                if (screen.orientation && screen.orientation.lock) {
                    screen.orientation.lock('landscape').catch(() => {});
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    }));
});
