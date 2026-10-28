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
        authUsers: [],
        
        // Auth & PIN Lock Screen
        isLocked: false,
        selectedAuthUser: null,
        pinInput: '',
        pinError: '',

        // In-App Modern Toast & Confirm Dialog
        toast: {
            show: false,
            message: '',
            type: 'success', // 'success' | 'error' | 'info'
            timeout: null
        },
        confirmDialog: {
            open: false,
            title: '',
            message: '',
            confirmText: 'Lanjutkan',
            confirmClass: 'bg-rose-600 hover:bg-rose-700',
            action: null
        },
        saveBillModal: {
            open: false,
            billName: '',
            quickPresets: ['Meja 01', 'Meja 02', 'Meja 03', 'Meja 04', 'Bungkus', 'Pesanan Online']
        },

        // Open Bill Search
        openBillSearchQuery: '',

        // History Filter State
        historyFilterDate: '',
        todayDate: '',
        historyData: null,
        historySearchQuery: '',
        isLoadingHistory: false,

        // Getters for Open Bills & History
        get filteredOpenBills() {
            if (!this.openBillSearchQuery) return this.openBills;
            const q = this.openBillSearchQuery.toLowerCase();
            return this.openBills.filter(b => 
                (b.bill_name && b.bill_name.toLowerCase().includes(q)) ||
                (b.bill_no && b.bill_no.toLowerCase().includes(q))
            );
        },

        get displayTransactions() {
            const list = (this.historyData && this.historyData.today_transactions) 
                ? this.historyData.today_transactions 
                : (this.dashboardData.today_transactions || []);
            if (!this.historySearchQuery) return list;
            const q = this.historySearchQuery.toLowerCase();
            return list.filter(tx => 
                (tx.invoice_no && tx.invoice_no.toLowerCase().includes(q)) ||
                (tx.customer_name && tx.customer_name.toLowerCase().includes(q)) ||
                (tx.payment_method && tx.payment_method.toLowerCase().includes(q))
            );
        },

        get displaySummary() {
            if (this.historyData && this.historyData.summary) {
                return this.historyData.summary;
            }
            return this.dashboardData.summary || {};
        },
        
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
        paymentMethod: 'cash', // 'cash', 'digital', 'debt', 'split'
        digitalSubMethod: 'qris', // 'qris' atau 'bank'
        cashTendered: 0,
        cashTenderedStr: '0',
        selectedCustomerId: '',
        selectedCustomer: null,
        memberSearchQuery: '',
        showMemberSearchResults: false,
        kasbonMemberSearchQuery: '',
        showKasbonSearchResults: false,
        showModalMemberSearch: false,
        usePoints: false,
        splitCash: 0,
        splitNonCash: 0,
        handledBy: '',

        // Receipt Modal
        receiptModal: false,
        lastTransaction: null,

        // Expense Modal (Catat Kas Keluar Kasir)
        expenseCategories: [],
        expenseModal: {
            open: false,
            form: {
                category: 'Operasional Toko',
                amount: 0,
                notes: ''
            }
        },

        // Audio FX Synthesizer (Zero External Files, 100% Offline)
        _audioCtx: null,
        playAudioTone(freq, type = 'sine', duration = 0.08, startTime = 0) {
            if (this.settings && this.settings.sound_fx_enabled == 0) return;
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                if (!this._audioCtx) this._audioCtx = new AudioCtx();
                const ctx = this._audioCtx;
                if (ctx.state === 'suspended') ctx.resume();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = type;
                osc.frequency.setValueAtTime(freq, ctx.currentTime + startTime);
                gain.gain.setValueAtTime(0.12, ctx.currentTime + startTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + startTime + duration);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(ctx.currentTime + startTime);
                osc.stop(ctx.currentTime + startTime + duration);
            } catch (e) {}
        },
        playBeepSound() {
            this.playAudioTone(1800, 'sine', 0.05);
        },
        playSuccessSound() {
            this.playAudioTone(523.25, 'triangle', 0.1, 0);     // C5
            this.playAudioTone(659.25, 'triangle', 0.1, 0.08);  // E5
            this.playAudioTone(783.99, 'triangle', 0.18, 0.16); // G5
        },

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
            this.todayDate = this.getLocalDateString();
            this.historyFilterDate = this.todayDate;
            await this.loadInitialData();
            await this.loadDashboardMetrics();

            // Support Hash Routing so F5 keeps active screen
            const initialHash = window.location.hash.replace('#', '');
            if (['dashboard', 'pos', 'open_bills', 'history'].includes(initialHash)) {
                this.navigate(initialHash, false);
            }

            window.addEventListener('hashchange', () => {
                const h = window.location.hash.replace('#', '');
                if (['dashboard', 'pos', 'open_bills', 'history'].includes(h) && this.currentView !== h) {
                    this.navigate(h, false);
                }
            });

            // Keyboard shortcuts: F2 for member search, F12 for payment, Escape to close
            window.addEventListener('keydown', (e) => {
                if (this.isLocked) {
                    if (e.key >= '0' && e.key <= '9') {
                        e.preventDefault();
                        this.pressPin(e.key);
                    } else if (e.key === 'Backspace') {
                        e.preventDefault();
                        this.backspacePin();
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        this.clearPin();
                    }
                    return;
                }

                if (e.key === 'F12') {
                    e.preventDefault();
                    if (this.currentView === 'pos' && !this.isCartEmpty) {
                        this.openPayment();
                    }
                } else if (e.key === 'F2') {
                    e.preventDefault();
                    if (this.currentView === 'pos') {
                        const input = document.querySelector('input[x-ref="cartMemberSearchInput"]');
                        if (input) {
                            input.focus();
                            input.select();
                        }
                    }
                } else if (e.key === 'Escape') {
                    this.closeAllModals();
                    this.showMemberSearchResults = false;
                    this.showKasbonSearchResults = false;
                    this.showModalMemberSearch = false;
                }
            });

            this.refreshIcons();
        },

        refreshIcons() {
            if (window.lucide) {
                window.lucide.createIcons();
            }
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
            setTimeout(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            }, 80);
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
                    this.expenseCategories = data.expense_categories || [];
                    this.currentUser = data.active_user || this.currentUser;
                    this.authUsers = data.users || [];
                    if (!this.selectedAuthUser && this.authUsers.length > 0) {
                        this.selectedAuthUser = this.currentUser || this.authUsers[0];
                    }
                }
            } catch (err) {
                console.error('Error loading init data:', err);
            }
            this.refreshIcons();
        },

        getLocalDateString(d = new Date()) {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },

        formatDisplayDate(dateStr) {
            if (!dateStr) return 'Hari Ini';
            try {
                const parts = dateStr.split('-');
                if (parts.length === 3) {
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const day = parseInt(parts[2], 10);
                    const monthIdx = parseInt(parts[1], 10) - 1;
                    const year = parts[0];
                    return day + ' ' + (months[monthIdx] || '') + ' ' + year;
                }
                return dateStr;
            } catch (e) {
                return dateStr;
            }
        },

        async loadHistory(targetDate = null) {
            if (targetDate) {
                this.historyFilterDate = targetDate;
            }
            if (!this.historyFilterDate) {
                this.historyFilterDate = this.todayDate || this.getLocalDateString();
            }
            this.isLoadingHistory = true;
            try {
                const res = await fetch('api.php?action=reports_daily&date=' + encodeURIComponent(this.historyFilterDate));
                const data = await res.json();
                if (data.success) {
                    this.historyData = data;
                    if (this.historyFilterDate === (this.todayDate || this.getLocalDateString())) {
                        this.dashboardData = data;
                    }
                }
            } catch (err) {
                this.showToast('Gagal memuat riwayat transaksi.', 'error');
            } finally {
                this.isLoadingHistory = false;
                this.refreshIcons();
            }
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
        navigate(view, updateHash = true) {
            this.currentView = view;
            if (updateHash && window.location.hash !== '#' + view) {
                window.location.hash = view;
            }
            if (view === 'dashboard') {
                this.loadDashboardMetrics();
            } else if (view === 'history') {
                this.loadHistory(this.historyFilterDate || this.todayDate || this.getLocalDateString());
            } else if (view === 'open_bills') {
                this.openBillSearchQuery = '';
                this.loadInitialData();
            } else if (view === 'pos') {
                this.searchQuery = '';
                this.selectedCategory = null;
            }
            this.refreshIcons();
        },

        reprintReceipt(tx) {
            this.lastTransaction = {
                ...tx,
                items: (tx.items || []).map(i => ({
                    ...i,
                    name: i.product_name || i.name,
                    price: parseFloat(i.sell_price || i.price || 0),
                    qty: parseFloat(i.qty || 1),
                    unit: i.unit || 'pcs'
                }))
            };
            this.receiptModal = true;
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

        get filteredMembersList() {
            if (!this.memberSearchQuery || !this.memberSearchQuery.trim()) {
                return this.customers.slice(0, 10);
            }
            const q = this.memberSearchQuery.toLowerCase().trim();
            return this.customers.filter(c => 
                (c.name && c.name.toLowerCase().includes(q)) || 
                (c.phone && c.phone.includes(q))
            );
        },

        get filteredKasbonMembersList() {
            if (!this.kasbonMemberSearchQuery || !this.kasbonMemberSearchQuery.trim()) {
                return this.customers.slice(0, 10);
            }
            const q = this.kasbonMemberSearchQuery.toLowerCase().trim();
            return this.customers.filter(c => 
                (c.name && c.name.toLowerCase().includes(q)) || 
                (c.phone && c.phone.includes(q))
            );
        },

        selectMember(customer) {
            if (!customer) return;
            this.selectedCustomerId = customer.id;
            this.selectedCustomer = customer;
            this.customerName = customer.name;
            this.memberSearchQuery = '';
            this.showMemberSearchResults = false;
            this.kasbonMemberSearchQuery = '';
            this.showKasbonSearchResults = false;
            this.showModalMemberSearch = false;
            this.applyMemberPricing();
            this.cashTendered = this.total;
            this.cashTenderedStr = this.total.toString();
            this.splitCash = Math.round(this.total / 2);
            this.splitNonCash = Math.max(0, this.total - this.splitCash);
            this.refreshIcons();
        },

        clearSelectedMember() {
            this.selectedCustomerId = '';
            this.selectedCustomer = null;
            this.customerName = '';
            this.usePoints = false;
            this.memberSearchQuery = '';
            this.showMemberSearchResults = false;
            this.kasbonMemberSearchQuery = '';
            this.showKasbonSearchResults = false;
            this.showModalMemberSearch = false;
            this.applyMemberPricing();
            this.cashTendered = this.total;
            this.cashTenderedStr = this.total.toString();
            this.splitCash = Math.round(this.total / 2);
            this.splitNonCash = Math.max(0, this.total - this.splitCash);
            this.refreshIcons();
        },

        onCustomerSelectChange() {
            if (!this.selectedCustomerId) {
                this.selectedCustomer = null;
                this.customerName = '';
                this.usePoints = false;
            } else {
                this.selectedCustomer = this.customers.find(c => c.id == this.selectedCustomerId) || null;
                this.customerName = this.selectedCustomer ? this.selectedCustomer.name : '';
            }
            this.applyMemberPricing();
            this.refreshIcons();
        },

        applyMemberPricing() {
            const isWholesale = this.selectedCustomer && this.selectedCustomer.tier === 'wholesale';
            this.cart.forEach(item => {
                const prod = this.products.find(p => p.id === item.product_id);
                if (!prod) return;
                if (isWholesale && prod.wholesale_price > 0) {
                    item.price = parseFloat(prod.wholesale_price);
                    item.is_wholesale = true;
                } else {
                    if (item.variant_name && prod.variants) {
                        const v = prod.variants.find(x => x.name === item.variant_name);
                        if (v) item.price = parseFloat(v.sell_price);
                    } else {
                        item.price = parseFloat(prod.sell_price);
                    }
                    item.is_wholesale = false;
                }
            });
        },

        addToCartDirect(product, qty = 1, variantName = null, overridePrice = null, overrideCost = null) {
            const isWholesale = this.selectedCustomer && this.selectedCustomer.tier === 'wholesale';
            let price = overridePrice !== null ? overridePrice : parseFloat(product.sell_price);
            let isWholesaleApplied = false;

            if (isWholesale && product.wholesale_price > 0 && overridePrice === null) {
                price = parseFloat(product.wholesale_price);
                isWholesaleApplied = true;
            }
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
                    is_wholesale: isWholesaleApplied,
                    is_scale: product.is_scale == 1
                });
            }
            this.playBeepSound();
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
            this.selectedCustomerId = '';
            this.selectedCustomer = null;
            this.usePoints = false;
            this.memberSearchQuery = '';
            this.showMemberSearchResults = false;
            this.activeOpenBillId = null;
        },

        // =====================================================================
        // CALCULATIONS & BILLING
        // =====================================================================
        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },

        get memberDiscountAmount() {
            if (!this.selectedCustomer || !this.selectedCustomer.discount_percent) return 0;
            const pct = parseFloat(this.selectedCustomer.discount_percent) || 0;
            return pct > 0 ? (this.subtotal * (pct / 100)) : 0;
        },

        get potentialPointsDiscount() {
            if (!this.selectedCustomer || !this.selectedCustomer.points) return 0;
            const redeemVal = parseFloat(this.settings.point_redeem_value) || 100;
            const maxMoney = (parseInt(this.selectedCustomer.points) || 0) * redeemVal;
            const afterDiscountSubtotal = Math.max(0, this.subtotal - this.memberDiscountAmount);
            return Math.min(maxMoney, afterDiscountSubtotal);
        },

        get potentialPointsUsed() {
            if (!this.selectedCustomer || this.potentialPointsDiscount <= 0) return 0;
            const redeemVal = parseFloat(this.settings.point_redeem_value) || 100;
            return Math.ceil(this.potentialPointsDiscount / redeemVal);
        },

        get pointsDiscount() {
            if (!this.usePoints || !this.selectedCustomer || !this.selectedCustomer.points) return 0;
            return this.potentialPointsDiscount;
        },

        get pointsUsed() {
            if (!this.usePoints || !this.selectedCustomer || this.pointsDiscount <= 0) return 0;
            return this.potentialPointsUsed;
        },

        get taxAmount() {
            if (!this.settings.tax_enabled) return 0;
            const pct = parseFloat(this.settings.tax_percent) || 0;
            const taxable = Math.max(0, this.subtotal - this.memberDiscountAmount - this.pointsDiscount);
            return pct > 0 ? (taxable * (pct / 100)) : 0;
        },

        get total() {
            const base = this.subtotal - this.memberDiscountAmount - this.pointsDiscount;
            return Math.max(0, Math.round(base + this.taxAmount));
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

        get maxSevenDaysSales() {
            if (!this.dashboardData || !this.dashboardData.seven_days || this.dashboardData.seven_days.length === 0) return 100000;
            const amounts = this.dashboardData.seven_days.map(d => parseFloat(d.amount) || 0);
            return Math.max(100000, ...amounts);
        },

        get totalSevenDaysSales() {
            if (!this.dashboardData || !this.dashboardData.seven_days) return 0;
            return this.dashboardData.seven_days.reduce((s, d) => s + (parseFloat(d.amount) || 0), 0);
        },

        // =====================================================================
        // AUTHENTICATION & PIN LOCK SYSTEM
        // =====================================================================
        openLockScreen() {
            this.isLocked = true;
            this.pinInput = '';
            this.pinError = '';
            this.selectedAuthUser = this.currentUser || (this.authUsers && this.authUsers.length > 0 ? this.authUsers[0] : null);
            this.refreshIcons();
        },

        selectAuthUser(u) {
            this.selectedAuthUser = u;
            this.pinInput = '';
            this.pinError = '';
        },

        pressPin(digit) {
            if (this.pinInput.length < 6) {
                this.pinInput += digit.toString();
                this.pinError = '';
                if (this.pinInput.length >= 4) {
                    this.submitPin();
                }
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
            if (this.pinInput.length < 4) return;
            try {
                const payload = {
                    pin: this.pinInput,
                    user_id: this.selectedAuthUser ? this.selectedAuthUser.id : null
                };
                const res = await fetch('api.php?action=auth_login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    this.currentUser = data.user;
                    this.isLocked = false;
                    this.pinInput = '';
                    this.pinError = '';
                    await this.loadInitialData();
                    await this.loadDashboardMetrics();
                } else {
                    this.pinError = data.error || 'PIN salah. Silakan coba lagi.';
                    this.pinInput = '';
                }
            } catch (err) {
                this.pinError = 'Terjadi kesalahan jaringan.';
            }
            this.refreshIcons();
        },

        async logout() {
            await fetch('api.php?action=auth_logout');
            this.openLockScreen();
        },

        // =====================================================================
        // PAYMENT SCREEN (3-Column Landscape)
        // =====================================================================
        openPayment() {
            if (this.isCartEmpty) return;
            this.paymentMethod = 'cash';
            this.digitalSubMethod = 'qris';
            this.showModalMemberSearch = false;
            this.kasbonMemberSearchQuery = '';
            this.showKasbonSearchResults = false;
            this.cashTendered = this.total;
            this.cashTenderedStr = this.total.toString();
            this.splitCash = Math.round(this.total / 2);
            this.splitNonCash = Math.max(0, this.total - this.splitCash);
            this.paymentModal = true;
            this.refreshIcons();
        },

        // =====================================================================
        // TOAST & IN-APP CONFIRMATION SYSTEM
        // =====================================================================
        showToast(message, type = 'success') {
            if (this.toast.timeout) clearTimeout(this.toast.timeout);
            this.toast.message = message;
            this.toast.type = type;
            this.toast.show = true;
            this.toast.timeout = setTimeout(() => {
                this.toast.show = false;
            }, 3200);
            this.refreshIcons();
        },

        showConfirm(title, message, action, options = {}) {
            this.confirmDialog.title = title || 'Konfirmasi Tindakan';
            this.confirmDialog.message = message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            this.confirmDialog.action = action;
            this.confirmDialog.confirmText = options.confirmText || 'Ya, Lanjutkan';
            this.confirmDialog.confirmClass = options.confirmClass || 'bg-rose-600 hover:bg-rose-700';
            this.confirmDialog.open = true;
            this.refreshIcons();
        },

        handleConfirm() {
            const act = this.confirmDialog.action;
            this.confirmDialog.open = false;
            if (typeof act === 'function') {
                act();
            }
        },

        copyBankNo() {
            const no = this.settings.bank_account_no || '1234-5678-90';
            if (navigator.clipboard) {
                navigator.clipboard.writeText(no).then(() => {
                    this.showToast('Nomor rekening ' + no + ' berhasil disalin!', 'success');
                }).catch(() => {
                    this.showToast('Nomor rekening: ' + no, 'info');
                });
            } else {
                this.showToast('Nomor rekening: ' + no, 'info');
            }
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

            // Validasi Kasbon: Wajib Member Terdaftar
            if (this.paymentMethod === 'debt') {
                if (!this.selectedCustomerId || !this.selectedCustomer) {
                    this.showToast('Transaksi Kasbon (Tempo) hanya dapat diberikan kepada Member terdaftar.', 'error');
                    return;
                }
                const curDebt = parseFloat(this.selectedCustomer.total_debt) || 0;
                const limit = parseFloat(this.selectedCustomer.debt_limit) || 500000;
                if (curDebt + this.total > limit) {
                    this.showConfirm(
                        'Peringatan Limit Kasbon',
                        'Total kasbon pelanggan (' + this.formatMoney(curDebt + this.total) + ') melebihi batas limit aman (' + this.formatMoney(limit) + '). Tetap izinkan kasbon?',
                        () => this.executeCheckoutPayload(),
                        { confirmText: 'Tetap Izinkan', confirmClass: 'bg-amber-600 hover:bg-amber-700' }
                    );
                    return;
                }
            }

            if (this.paymentMethod === 'cash' && this.cashTendered < this.total) {
                this.showToast('Jumlah uang tunai kurang dari total tagihan.', 'error');
                return;
            }

            if (this.paymentMethod === 'split' && (this.splitCash + this.splitNonCash) < this.total) {
                this.showToast('Total pembayaran split (Tunai + Non-Tunai) masih kurang dari total belanja.', 'error');
                return;
            }

            await this.executeCheckoutPayload();
        },

        async executeCheckoutPayload() {
            let finalPaymentMethod = this.paymentMethod;
            let finalAmountPaid = this.total;
            if (this.paymentMethod === 'cash') {
                finalAmountPaid = this.cashTendered;
            } else if (this.paymentMethod === 'digital') {
                finalPaymentMethod = this.digitalSubMethod === 'bank' ? 'transfer' : 'qris';
                finalAmountPaid = this.total;
            } else if (this.paymentMethod === 'qris') {
                finalPaymentMethod = 'qris';
                finalAmountPaid = this.total;
            } else if (this.paymentMethod === 'bank') {
                finalPaymentMethod = 'transfer';
                finalAmountPaid = this.total;
            } else if (this.paymentMethod === 'debt') {
                finalAmountPaid = 0;
            } else if (this.paymentMethod === 'split') {
                finalAmountPaid = this.splitCash + this.splitNonCash;
            }

            const payload = {
                items: this.cart,
                customer_id: this.selectedCustomerId || null,
                customer_name: this.selectedCustomer ? this.selectedCustomer.name : 'Pelanggan Umum',
                payment_method: finalPaymentMethod,
                amount_paid: finalAmountPaid,
                points_used: this.pointsUsed,
                points_discount: this.pointsDiscount,
                split_cash: this.splitCash,
                split_non_cash: this.splitNonCash,
                handled_by: this.handledBy || this.currentUser.name,
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
                    this.playSuccessSound();
                    this.lastTransaction = data;
                    this.lastTransaction.items = [...this.cart];
                    this.lastTransaction.customer_tier = this.selectedCustomer ? this.selectedCustomer.tier : 'Umum';
                    this.lastTransaction.points_used = this.pointsUsed;
                    this.lastTransaction.points_discount = this.pointsDiscount;
                    this.clearCart();
                    this.paymentModal = false;
                    this.receiptModal = true;
                    await this.loadInitialData();
                    await this.loadDashboardMetrics();
                    this.showToast('Transaksi pembayaran berhasil diproses!', 'success');

                    // Auto-print jika diaktifkan di pengaturan
                    if (this.settings.auto_print == 1) {
                        setTimeout(() => this.printReceipt(), 300);
                    }
                } else {
                    this.showToast(data.error || 'Gagal memproses pembayaran.', 'error');
                }
            } catch (err) {
                this.showToast('Gagal menghubungi server kasir.', 'error');
            }
            this.refreshIcons();
        },

        printReceipt() {
            window.print();
        },

        // =====================================================================
        // EXPENSE (Catat Kas Keluar Kasir)
        // =====================================================================
        openExpenseModal() {
            const defCat = (this.expenseCategories && this.expenseCategories.length > 0) ? this.expenseCategories[0].name : 'Operasional Toko';
            this.expenseModal.form = { category: defCat, amount: 0, notes: '' };
            this.expenseModal.open = true;
            this.refreshIcons();
        },

        async saveExpense() {
            if (this.expenseModal.form.amount <= 0 || !this.expenseModal.form.notes) {
                this.showToast('Nominal dan keterangan pengeluaran wajib diisi.', 'error');
                return;
            }
            try {
                const res = await fetch('api.php?action=expenses_save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.expenseModal.form)
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast('Kas keluar ' + this.formatMoney(this.expenseModal.form.amount) + ' berhasil dicatat!', 'success');
                    this.expenseModal.open = false;
                    await this.loadDashboardMetrics();
                } else {
                    this.showToast(data.error || 'Gagal mencatat pengeluaran.', 'error');
                }
            } catch (err) {
                this.showToast('Gagal menghubungi server.', 'error');
            }
            this.refreshIcons();
        },

        // =====================================================================
        // OPEN BILL (Simpan Tagihan / Kasbon)
        // =====================================================================
        openSaveBillModal() {
            if (this.isCartEmpty) {
                this.showToast('Keranjang belanja masih kosong.', 'error');
                return;
            }
            this.saveBillModal.billName = (this.selectedCustomer ? this.selectedCustomer.name : '') || ('Meja ' + (this.openBills.length + 1));
            this.saveBillModal.open = true;
            this.refreshIcons();
        },

        async submitSaveBill() {
            const billName = (this.saveBillModal.billName || '').trim();
            if (!billName) {
                this.showToast('Nama tagihan / nomor meja wajib diisi.', 'error');
                return;
            }

            try {
                const res = await fetch('api.php?action=open_bill_save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        bill_name: billName,
                        customer_name: this.selectedCustomer ? this.selectedCustomer.name : 'Pelanggan Umum',
                        items: this.cart
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.saveBillModal.open = false;
                    this.clearCart();
                    await this.loadInitialData();
                    this.showToast('Tagihan "' + billName + '" berhasil disimpan ke Open Bill.', 'success');
                } else {
                    this.showToast(data.error || 'Gagal menyimpan open bill.', 'error');
                }
            } catch (err) {
                this.showToast('Gagal menyimpan open bill.', 'error');
            }
        },

        loadOpenBill(bill) {
            try {
                const items = JSON.parse(bill.items_json);
                this.cart = items;
                this.customerName = bill.bill_name;
                this.activeOpenBillId = bill.id;
                this.navigate('pos');
                this.showToast('Tagihan "' + bill.bill_name + '" dimuat ke kasir.', 'info');
            } catch (e) {
                this.showToast('Gagal memuat rincian bill.', 'error');
            }
        },

        deleteOpenBill(bill) {
            this.showConfirm(
                'Hapus Tagihan Open Bill?',
                'Tagihan "' + bill.bill_name + '" (' + this.formatMoney(bill.total) + ') akan dihapus secara permanen dari daftar.',
                async () => {
                    try {
                        const res = await fetch('api.php?action=open_bill_delete', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: bill.id })
                        });
                        const data = await res.json();
                        if (data.success) {
                            await this.loadInitialData();
                            this.showToast('Tagihan "' + bill.bill_name + '" berhasil dihapus.', 'success');
                        } else {
                            this.showToast(data.error || 'Gagal menghapus tagihan.', 'error');
                        }
                    } catch (err) {
                        this.showToast('Terjadi kesalahan saat menghapus tagihan.', 'error');
                    }
                },
                { confirmText: 'Hapus Tagihan', confirmClass: 'bg-rose-600 hover:bg-rose-700' }
            );
        },

        getBillItemCount(bill) {
            try {
                const items = JSON.parse(bill.items_json);
                if (!Array.isArray(items)) return 0;
                return items.reduce((s, i) => s + (parseFloat(i.qty) || 1), 0);
            } catch (e) {
                return 0;
            }
        },

        getBillItemList(bill) {
            try {
                const items = JSON.parse(bill.items_json);
                if (!Array.isArray(items) || items.length === 0) return 'Tidak ada item';
                return items.map(i => i.name + ' (' + (i.qty || 1) + ')').join(', ');
            } catch (e) {
                return '';
            }
        },

        getBillSummary(bill) {
            try {
                const items = JSON.parse(bill.items_json);
                if (!Array.isArray(items) || items.length === 0) return 'Tidak ada item';
                const totalQty = items.reduce((s, i) => s + (parseFloat(i.qty) || 1), 0);
                const names = items.map(i => i.name + ' (' + (i.qty || 1) + ')').slice(0, 3).join(', ');
                return totalQty + ' item: ' + names + (items.length > 3 ? '...' : '');
            } catch (e) {
                return '';
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
                    this.showToast('Pengaturan toko berhasil diperbarui!', 'success');
                    await this.loadInitialData();
                } else {
                    this.showToast(data.error || 'Gagal menyimpan pengaturan.', 'error');
                }
            } catch (err) {
                this.showToast('Gagal menyimpan pengaturan.', 'error');
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
