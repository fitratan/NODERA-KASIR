/**
 * NODERA POS PRO - Client Application Engine
 * Precise Recreation & Enhancement of POS Offline Pro (Yusril Asrul)
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('posApp', () => ({
        // Routing: 'dashboard', 'pos', 'products', 'history', 'settings', 'open_bills'
        currentView: 'dashboard',
        posOrientation: localStorage.getItem('nodera_pos_orientation') || 'landscape',
        portraitCartDrawerOpen: false,
        modeSelectModalOpen: false,
        shortcutModalOpen: false,
        rememberPosOrientation: true,
        activePriceTier: 'h1', 
        posSubView: 'pos', // Sub-view dalam POS: 'pos' (katalog+cart) | 'open_bills' | 'history' | 'expenses'
        profileDropdownOpenTop: false,
        profileDropdownOpenPos: false,
        posMobileSidebarOpen: false,
        isFullscreen: false,
        allowPortraitPos: false,
        waitingToRestoreFullscreen: false,

        // i18n & Theme (nodera-laravel TailAdmin Standard)
        currentLang: localStorage.getItem('nodera_lang') || 'id',
        theme: localStorage.getItem('theme') || (document.documentElement.classList.contains('dark') ? 'dark' : 'light'),
        availableLanguages: window.availableLanguages || [
            { code: 'id', name: 'Bahasa Indonesia', short: 'ID', flag: '🇮🇩' },
            { code: 'en', name: 'English', short: 'EN', flag: '🇬🇧' },
            { code: 'fr', name: 'Français', short: 'FR', flag: '🇫🇷' },
            { code: 'es', name: 'Español', short: 'ES', flag: '🇪🇸' },
            { code: 'tl', name: 'Tagalog', short: 'TL', flag: '🇵🇭' }
        ],

        // Core Data
        settings: {
            store_name: 'Toko Saya',
            store_type: 'Ritel & Penjualan',
            store_phone: '',
            store_address: '',
            receipt_footer: 'Terima Kasih! Silahkan Datang Kembali',
            feature_barcode: 1,
            feature_variants: 1,
            feature_customer: 1,
            feature_open_bill: 1,
            feature_price_tiers: 1,
            feature_negative_stock: 0,
            feature_discount: 1,
            feature_cashier_add_customer: 1,
            debt_enabled: 1,
            require_pin_void: 1,
            require_pin_discount: 1,
            sound_fx_enabled: 1,
            edc_enabled: 1,
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
        currentUser: null,
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

        // View Mode Persistence: Grid (Bento) vs Table (Dense List)
        catalogViewMode: localStorage.getItem('nodera_catalog_view') || 'grid',
        historyViewMode: localStorage.getItem('nodera_history_view') || 'grid',

        // Wholesale Templates
        wholesaleTemplates: [],

        // Cart Discount Modal (% vs Flat Nominal)
        cartDiscount: {
            type: 'percent', // 'percent' | 'nominal'
            value: 0,
            reason: '',
            adminPin: '',
            pinError: '',
            otpDigits: ['', '', '', '', '', '']
        },
        discountModalOpen: false,
        qrisModalOpen: false,

        // Cashier Add Customer Modal (Langsung dari POS)
        cashierAddCustomerModalOpen: false,
        cashierCustomerSaving: false,
        cashierCustomerError: '',
        cashierCustomerForm: {
            name: '',
            phone: '',
            tier: 'regular',
            debt_limit: 500000,
            notes: ''
        },

        // Void Transaction Modal
        voidModal: {
            open: false,
            tx: null,
            reason: 'Kesalahan input kasir',
            adminPin: '',
            loading: false,
            error: ''
        },

        // Sales Return Modal (Retur & Refund Kasir)
        posReturnModal: {
            open: false,
            txData: null,
            items: [],
            refundMethod: 'cash',
            reason: 'Barang Cacat/Salah',
            notes: '',
            loading: false,
            saving: false
        },

        // Cashier Shift Opname & Buka Shift
        currentShift: null,
        openShiftModalOpen: false,
        openShiftStartingCash: 100000,
        openShiftNotes: '',
        openShiftLoading: false,
        openShiftError: '',

        shiftModal: {
            open: false,
            step: 'input', // 'input' | 'result'
            mode: 'close',
            startingCash: 0,
            endingCashActual: 0,
            notes: '',
            loading: false,
            error: '',
            data: null, // live metrics from shifts_current
            result: null
        },

        // Screen Lock (Kunci Layar Sementara)
        isScreenLocked: false,
        lockPinInput: '',
        lockPinError: '',
        lockDigitalClock: '',
        lockDigitalDate: '',

        // Pemindai Barcode Kamera & Hardware
        cameraScannerModal: {
            open: false,
            scanner: null,
            target: 'pos',
            continuous: true,
            lastScannedCode: '',
            cameras: [],
            currentCameraIndex: 0
        },

        // Web Bluetooth & Direct ESC/POS Printer
        bluetoothPrinter: {
            device: null,
            server: null,
            characteristic: null,
            status: 'disconnected'
        },
        printerScannerModalOpen: false,
        printerScannerError: '',
        allowPortraitPos: false,
        dateFilterModal: {
            open: false,
            target: 'history', // 'history' | 'expenses'
            preset: 'today',
            startDate: '',
            endDate: '',
            selectedDate: ''
        },

        // View Modes: Grid vs Table across all POS sub-views
        catalogViewMode: localStorage.getItem('nodera_catalog_view') || 'grid', // 'grid' | 'table'
        historyViewMode: localStorage.getItem('nodera_history_view') || 'grid', // 'grid' | 'table'
        openBillsViewMode: localStorage.getItem('nodera_openbills_view') || 'grid', // 'grid' | 'table'
        expenseViewMode: localStorage.getItem('nodera_expense_view') || 'grid', // 'grid' | 'table'

        // Thermal Printer Settings Modal in POS
        printerSettingsModalOpen: false,

        // Expenses (Kas Keluar) State
        expenses: [],
        expenseSearchQuery: '',
        expenseFilterStartDate: '',
        expenseFilterEndDate: '',
        isLoadingExpenses: false,

        // Open Bill Search
        openBillSearchQuery: '',
        isLoadingOpenBills: false,

        // Products & Categories Page State
        productSearchQuery: '',
        productFilterCategory: '',
        catalogViewMode: localStorage.getItem('pos_catalog_view_mode') || 'grid',
        categorySearchQuery: '',

        // History Filter State
        historyFilterDate: '',
        historyFilterStartDate: '',
        historyFilterEndDate: '',
        todayDate: '',
        historyData: null,
        historySearchQuery: '',
        isLoadingHistory: false,

        // Auth Admin Modal & Switch Cashier Modal & OTP State
        adminPinModalOpen: false,
        adminAuthTab: 'pin', // 'pin' | 'password'
        adminAuthEmail: '',
        adminAuthPassword: '',
        adminPinInput: '',
        adminPinError: '',
        adminAuthLoading: false,

        switchCashierModalOpen: false,
        selectedSwitchUser: null,
        switchPinInput: '',
        switchPinError: '',

        otpDigits: ['', '', '', '', '', ''],
        loginLoading: false,
        loginIdentifier: '',
        devSupportModalOpen: false,

        // Desktop License Modal State
        licenseModalOpen: false,
        licenseKeyInput: '',
        licenseError: '',
        licenseSuccess: '',
        licenseLoading: false,
        hwidCopied: false,
        licenseStatus: null,

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
                (tx.cashier_name && tx.cashier_name.toLowerCase().includes(q)) ||
                (tx.payment_method && tx.payment_method.toLowerCase().includes(q)) ||
                (tx.items && Array.isArray(tx.items) && tx.items.some(it => 
                    (it.name && it.name.toLowerCase().includes(q)) || 
                    (it.product_name && it.product_name.toLowerCase().includes(q))
                ))
            );
        },

        get displaySummary() {
            if (this.historyData && this.historyData.summary) {
                return this.historyData.summary;
            }
            return this.dashboardData.summary || {};
        },

        get filteredExpenses() {
            if (!this.expenses) return [];
            if (!this.expenseSearchQuery) return this.expenses;
            const q = this.expenseSearchQuery.toLowerCase();
            return this.expenses.filter(e => 
                (e.category && e.category.toLowerCase().includes(q)) ||
                (e.notes && e.notes.toLowerCase().includes(q)) ||
                (e.cashier_name && e.cashier_name.toLowerCase().includes(q))
            );
        },

        get expenseTotal() {
            if (!this.filteredExpenses) return 0;
            return this.filteredExpenses.reduce((sum, e) => sum + (parseFloat(e.amount) || 0), 0);
        },

        get expenseHourlySales() {
            const hourlyMap = {};
            for (let h = 8; h <= 22; h++) {
                hourlyMap[h] = 0;
            }
            (this.filteredExpenses || []).forEach(exp => {
                if (!exp.created_at) return;
                const timePart = exp.created_at.split(' ')[1] || '';
                const hour = parseInt(timePart.split(':')[0], 10);
                if (!isNaN(hour) && hour >= 8 && hour <= 22) {
                    hourlyMap[hour] = (hourlyMap[hour] || 0) + (parseFloat(exp.amount) || 0);
                }
            });
            const result = [];
            for (let h = 8; h <= 22; h++) {
                result.push({
                    hour: (h < 10 ? '0' : '') + h + ':00',
                    hour_num: h,
                    total: hourlyMap[h] || 0
                });
            }
            return result;
        },

        get expenseCategoryBreakdown() {
            const map = {};
            const total = this.expenseTotal || 0;
            (this.filteredExpenses || []).forEach(exp => {
                const cat = exp.category || 'Operasional';
                map[cat] = (map[cat] || 0) + (parseFloat(exp.amount) || 0);
            });
            
            const palette = [
                { bg: 'bg-rose-500', dot: 'bg-rose-500' },
                { bg: 'bg-amber-500', dot: 'bg-amber-500' },
                { bg: 'bg-indigo-500', dot: 'bg-indigo-500' },
                { bg: 'bg-emerald-500', dot: 'bg-emerald-500' },
                { bg: 'bg-sky-500', dot: 'bg-sky-500' },
                { bg: 'bg-purple-500', dot: 'bg-purple-500' }
            ];

            let colorIdx = 0;
            const list = Object.keys(map).map(catName => {
                const amt = map[catName];
                const pct = total > 0 ? (amt / total) * 100 : 0;
                const color = palette[colorIdx % palette.length];
                colorIdx++;
                return {
                    name: catName,
                    amount: amt,
                    percent: Math.round(pct * 10) / 10,
                    colorClass: color.bg,
                    dotColorClass: color.dot
                };
            });
            return list.sort((a, b) => b.amount - a.amount);
        },

        get isOwnerOrAdmin() {
            return Boolean(this.currentUser && (this.currentUser.role === 'admin' || this.currentUser.role === 'owner'));
        },

        get totalDiscount() {
            return this.totalDiscountAmount;
        },

        get openBillModeLabel() {
            const mode = this.settings.open_bill_mode || 'table';
            switch (mode) {
                case 'rack': return this.t('bill_mode_rack', 'Nomor Rak');
                case 'vehicle': return this.t('bill_mode_vehicle', 'Plat / Kendaraan');
                case 'room': return this.t('bill_mode_room', 'Kamar / Ruangan');
                case 'queue': return this.t('bill_mode_queue', 'Nomor Antrean');
                case 'custom': return this.t('bill_mode_custom', 'Nama Pesanan');
                case 'table':
                default:
                    return this.t('bill_mode_table', 'Nomor Meja');
            }
        },

        get openBillPresetsList() {
            if (this.settings && this.settings.open_bill_presets) {
                return this.settings.open_bill_presets.split(',').map(s => s.trim()).filter(Boolean);
            }
            return (this.saveBillModal && this.saveBillModal.quickPresets) ? this.saveBillModal.quickPresets : ['Meja 01', 'Meja 02', 'Meja 03', 'Meja 04', 'Bungkus'];
        },

        get quickCashSuggestions() {
            return [10000, 20000, 50000, 100000];
        },

        get displayedCatalogProducts() {
            let list = this.products || [];
            if (this.productFilterCategory) {
                list = list.filter(p => p.category_id == this.productFilterCategory);
            }
            if (this.productSearchQuery) {
                const q = this.productSearchQuery.toLowerCase();
                list = list.filter(p => 
                    (p.name && p.name.toLowerCase().includes(q)) ||
                    (p.code && p.code.toLowerCase().includes(q)) ||
                    (p.category_name && p.category_name.toLowerCase().includes(q))
                );
            }
            // Urutkan: Produk yang stoknya masih ada di atas, produk yang stoknya 0/habis di bawah sendiri
            return list.slice().sort((a, b) => {
                const stockA = parseFloat(a.stock) || 0;
                const stockB = parseFloat(b.stock) || 0;
                const isOutA = stockA <= 0;
                const isOutB = stockB <= 0;
                if (isOutA && !isOutB) return 1;
                if (!isOutA && isOutB) return -1;
                return 0;
            });
        },

        get displayedCategories() {
            let list = this.categories || [];
            if (this.categorySearchQuery) {
                const q = this.categorySearchQuery.toLowerCase();
                list = list.filter(c => c.name && c.name.toLowerCase().includes(q));
            }
            return list;
        },

        getCategoryCount(catId) {
            return (this.products || []).filter(p => p.category_id == catId).length;
        },

        getCategoryName(catId) {
            if (!catId) return 'Semua Kategori';
            const cat = (this.categories || []).find(c => String(c.id) === String(catId));
            return cat ? cat.name : 'Semua Kategori';
        },

        openCategoryProducts(catId) {
            this.productFilterCategory = catId ? String(catId) : '';
            this.productSearchQuery = '';
            this.navigate('products');
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
        paymentMethod: 'cash', // 'cash', 'qris', 'bank', 'edc', 'debt', 'split'
        edcApprovalCode: '',
        selectedBankAccountId: '',
        digitalSubMethod: 'qris', // 'qris' atau 'bank'
        cashTendered: 0,
        cashTenderedStr: '0',
        isCashTenderedUntouched: true,
        selectedCustomerId: '',
        selectedCustomer: null,
        memberSearchQuery: '',
        showMemberSearchResults: false,
        portraitMemberSearchQuery: '',
        showPortraitMemberSearchResults: false,
        kasbonMemberSearchQuery: '',
        showKasbonSearchResults: false,
        showModalMemberSearch: false,
        usePoints: false,
        useTaxInvoice: false,
        splitCash: 0,
        splitNonCash: 0,
        handledBy: '',
        debtHasDueDate: false,
        debtDueDate: '',
        initDebtDueDate() {
            const d = new Date();
            d.setDate(d.getDate() + 30);
            this.debtDueDate = d.toISOString().split('T')[0];
        },

        // Stock Alert Modal (Pencegahan Penambahan Stok 0)
        stockAlertModal: {
            open: false,
            productName: '',
            productStock: 0,
            unit: 'pcs',
            isExceeded: false
        },

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
            if (this.settings && (Number(this.settings.sound_fx_enabled) === 0 || Number(this.settings.sound_enabled) === 0)) return;
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
        playErrorSound() {
            this.playAudioTone(300, 'sawtooth', 0.15, 0);
            this.playAudioTone(220, 'sawtooth', 0.2, 0.12);
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
        // =====================================================================
        // MODE SELECT & ORIENTATION HANDLERS
        // =====================================================================
        openModeSelectModal() {
            this.modeSelectModalOpen = true;
            this.$nextTick(() => this.refreshIcons());
        },

        choosePosMode(mode) {
            this.posOrientation = mode;
        },

        confirmPosMode() {
            this.modeSelectModalOpen = false;
            localStorage.setItem('nodera_pos_orientation', this.posOrientation);
            if (this.rememberPosOrientation) {
                localStorage.setItem('nodera_remember_orientation', 'true');
            }
            this.startSelling();
        },

        setPosOrientation(mode) {
            this.posOrientation = mode;
            localStorage.setItem('nodera_pos_orientation', mode);
            if (window.AndroidBridge && window.AndroidBridge.setOrientation) {
                window.AndroidBridge.setOrientation(mode);
            }
            if (this.currentView === 'pos') {
                if (mode === 'landscape') {
                    this.requestLandscapeMode();
                } else {
                    this.forcePortraitMode();
                }
            }
            this.showToast((this.t('alert_pos_mode_changed') || 'Orientasi POS diubah ke ') + (mode === 'landscape' ? 'LANSKAP' : 'POTRET'), 'info');
            this.$nextTick(() => this.refreshIcons());
        },

        getProductPrice(product, tier = 'h1') {
            if (!product) return 0;
            // Jika modular Multi-Tier Harga Grosir dinonaktifkan (feature_price_tiers == 0), selalu gunakan harga H1 (ecer)
            if (this.settings && Number(this.settings.feature_price_tiers) === 0) {
                return Number(product.price_h1 !== undefined && product.price_h1 !== null && product.price_h1 !== '' ? product.price_h1 : (product.sell_price ?? 0));
            }
            const t = tier || 'h1';
            if (t === 'h2') {
                if (product.price_h2 !== null && product.price_h2 !== undefined && product.price_h2 !== '') {
                    return Number(product.price_h2);
                }
                if (product.wholesale_price !== null && product.wholesale_price !== undefined && product.wholesale_price !== '') {
                    return Number(product.wholesale_price);
                }
                return Number(product.price_h1 !== undefined && product.price_h1 !== null && product.price_h1 !== '' ? product.price_h1 : (product.sell_price ?? 0));
            }
            if (t === 'h3') {
                // H3 (Bisa Gratis 0, Grosir Spesial, atau Sampel Promo)
                if (product.price_h3 !== null && product.price_h3 !== undefined && product.price_h3 !== '') {
                    return Number(product.price_h3);
                }
                if (product.price_h2 !== null && product.price_h2 !== undefined && product.price_h2 !== '') {
                    return Number(product.price_h2);
                }
                if (product.wholesale_price !== null && product.wholesale_price !== undefined && product.wholesale_price !== '') {
                    return Number(product.wholesale_price);
                }
                return Number(product.price_h1 !== undefined && product.price_h1 !== null && product.price_h1 !== '' ? product.price_h1 : (product.sell_price ?? 0));
            }
            return Number(product.price_h1 !== undefined && product.price_h1 !== null && product.price_h1 !== '' ? product.price_h1 : (product.sell_price ?? 0));
        },

        getTierName(tier) {
            if (tier === 'h1') return this.settings?.tier_h1_name || 'H1 (Normal)';
            if (tier === 'h2') return this.settings?.tier_h2_name || 'H2 (Grosir)';
            if (tier === 'h3') return this.settings?.tier_h3_name || 'H3 (Partai)';
            return tier ? tier.toUpperCase() : 'H1';
        },

        isTierAvailable(tier) {
            if (this.settings && this.settings.feature_price_tiers == 0) return tier === 'h1';
            if (tier === 'h1') return true;
            if (tier === 'h2') {
                return (this.products || []).some(p => p.price_h2 !== null && p.price_h2 !== '' && p.price_h2 !== undefined);
            }
            if (tier === 'h3') {
                return (this.products || []).some(p => p.price_h3 !== null && p.price_h3 !== '' && p.price_h3 !== undefined);
            }
            return false;
        },

        hasAnyMultiTier() {
            if (this.settings && this.settings.feature_price_tiers == 0) return false;
            return this.isTierAvailable('h2') || this.isTierAvailable('h3');
        },

        setPriceTier(tier) {
            // Jika modular Multi-Tier Harga Grosir dinonaktifkan, paksa ke H1
            if (this.settings && Number(this.settings.feature_price_tiers) === 0) {
                this.activePriceTier = 'h1';
                (this.cart || []).forEach(it => {
                    const prod = (this.products || []).find(p => p.id == it.product_id);
                    if (prod) {
                        it.price = this.getProductPrice(prod, 'h1');
                        it.price_tier = 'h1';
                        it.is_wholesale = false;
                    }
                });
                this.recalculatePaymentAmounts();
                return;
            }
            this.activePriceTier = tier;
            (this.cart || []).forEach(it => {
                const prod = (this.products || []).find(p => p.id == it.product_id);
                if (prod) {
                    it.price = this.getProductPrice(prod, tier);
                    it.price_tier = tier;
                    it.is_wholesale = (tier === 'h2' || tier === 'h3');
                }
            });
            this.recalculatePaymentAmounts();
            this.showToast((this.t('alert_pricing_tier') || 'Tingkat Harga Keranjang: ') + tier.toUpperCase(), 'info');
            this.$nextTick(() => this.refreshIcons());
        },

        setItemPriceTier(idx, tier) {
            if (this.settings && Number(this.settings.feature_price_tiers) === 0) return;
            const item = this.cart[idx];
            if (!item) return;
            const prod = (this.products || []).find(p => p.id == item.product_id);
            if (!prod) return;

            item.price_tier = tier;
            item.price = this.getProductPrice(prod, tier);
            item.is_wholesale = (tier === 'h2' || tier === 'h3');
            this.recalculatePaymentAmounts();
            this.showToast(`${item.name} set ke ${tier.toUpperCase()} (${this.formatMoney(item.price)})`, 'info');
            this.$nextTick(() => this.refreshIcons());
        },

        cycleItemPriceTier(idx) {
            if (this.settings && Number(this.settings.feature_price_tiers) === 0) return;
            const item = this.cart[idx];
            if (!item) return;
            const prod = (this.products || []).find(p => p.id == item.product_id);
            if (!prod) return;

            const currentTier = item.price_tier || this.activePriceTier || 'h1';
            const hasH2 = prod.price_h2 !== null && prod.price_h2 !== undefined && prod.price_h2 !== '';
            const hasH3 = prod.price_h3 !== null && prod.price_h3 !== undefined && prod.price_h3 !== '';

            if (!hasH2 && !hasH3) return;

            let nextTier = 'h1';
            if (currentTier === 'h1') {
                nextTier = hasH2 ? 'h2' : (hasH3 ? 'h3' : 'h1');
            } else if (currentTier === 'h2') {
                nextTier = hasH3 ? 'h3' : 'h1';
            } else {
                nextTier = 'h1';
            }

            item.price_tier = nextTier;
            item.price = this.getProductPrice(prod, nextTier);
            item.is_wholesale = (nextTier === 'h2' || nextTier === 'h3');
            this.recalculatePaymentAmounts();
            this.showToast(`${item.name} diubah ke ${nextTier.toUpperCase()} (${this.formatMoney(item.price)})`, 'info');
            this.$nextTick(() => this.refreshIcons());
        },

        // 5-Minute Inactivity Auto-Lock
        initAutoLock() {
            let timer = null;
            const resetTimer = () => {
                if (timer) clearTimeout(timer);
                if (this.currentUser && !this.isLocked) {
                    timer = setTimeout(() => {
                        if (this.currentUser && !this.isLocked) {
                            this.lockScreen();
                            this.showToast(this.t('alert_auto_lock_notice'), 'info');
                        }
                    }, 5 * 60 * 1000);
                }
            };
            ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll'].forEach(evt => {
                window.addEventListener(evt, resetTimer, { passive: true });
            });
            resetTimer();
        },

        // Keyboard Shortcuts Listeners
        initKeyboardShortcuts() {
            window.addEventListener('keydown', (e) => {
                if (this.isLocked || this.isScreenLocked || !this.currentUser) return;
                if (e.key === 'F2') {
                    e.preventDefault();
                    const sInput = document.querySelector('input[x-model="searchQuery"]');
                    if (sInput) sInput.focus();
                } else if (e.key === 'F3') {
                    e.preventDefault();
                    if (this.settings && this.settings.feature_barcode == 0) {
                        this.showToast('Fitur scanner barcode kamera dinonaktifkan di pengaturan.', 'warning');
                        return;
                    }
                    if (this.cameraScannerModal.open) {
                        this.closeCameraScanner();
                    } else {
                        this.openCameraScanner(this.currentView === 'products' ? 'catalog' : 'pos');
                    }
                } else if (e.key === 'F4') {
                    e.preventDefault();
                    this.openPosOpenBills();
                } else if (e.key === 'F9') {
                    if (this.currentView === 'pos' && this.cart.length > 0 && !this.paymentModal) {
                        e.preventDefault();
                        this.openPayment();
                    }
                } else if (e.key === 'F10') {
                    if (this.currentView === 'pos' && this.cart.length > 0) {
                        e.preventDefault();
                        this.openHoldBillModal();
                    }
                } else if (e.key === 'F12') {
                    e.preventDefault();
                    this.lockScreen();
                } else if (e.key === 'Escape') {
                    if (this.cameraScannerModal.open) this.closeCameraScanner();
                    if (this.shortcutModalOpen) this.shortcutModalOpen = false;
                    if (this.portraitCartDrawerOpen) this.portraitCartDrawerOpen = false;
                    if (this.modeSelectModalOpen) this.modeSelectModalOpen = false;
                }
            });
        },

        async init() {
            // Sinkronkan Tema Tampilan (nodera-laravel TailAdmin Standard)
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                this.theme = 'dark';
                document.documentElement.classList.add('dark');
                document.documentElement.setAttribute('data-color-scheme', 'dark');
            } else {
                this.theme = 'light';
                document.documentElement.classList.remove('dark');
                document.documentElement.setAttribute('data-color-scheme', 'light');
            }

            this.todayDate = this.getLocalDateString();
            this.initAutoLock();
            this.initKeyboardShortcuts();
            this.initBarcodeScannerListener();
            this.historyFilterDate = this.todayDate;
            await this.loadInitialData();
            await this.loadDashboardMetrics();

            // Pulihkan status kunci layar (Screen Lock Persistence saat Reload/F5)
            if (localStorage.getItem('nodera_pos_locked') === '1') {
                this.isScreenLocked = true;
                this.updateLockClock();
                if (this._lockClockInterval) clearInterval(this._lockClockInterval);
                this._lockClockInterval = setInterval(() => this.updateLockClock(), 1000);
            }

            // Support Hash Routing so F5 keeps active screen
            const initialHash = window.location.hash.replace('#', '');
            if (['dashboard', 'pos', 'open_bills', 'history', 'products', 'categories', 'expenses'].includes(initialHash)) {
                this.navigate(initialHash, false);
            } else if (window.AndroidBridge && window.AndroidBridge.setOrientation) {
                // Tampilan default Android APK
                if (this.currentView === 'pos' && this.posOrientation === 'landscape') {
                    window.AndroidBridge.setOrientation('landscape');
                }
            }

            window.addEventListener('hashchange', () => {
                const h = window.location.hash.replace('#', '');
                if (['dashboard', 'pos', 'open_bills', 'history', 'products', 'categories', 'expenses'].includes(h) && this.currentView !== h) {
                    this.navigate(h, false);
                }
            });

            // Listeners untuk memulihkan fullscreen otomatis jika kembali dari RawBT / app switching Android
            window.addEventListener('focus', () => {
                if (this.currentView === 'pos' && this.waitingToRestoreFullscreen) {
                    this.attemptRestoreFullscreen();
                }
            });
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible' && this.currentView === 'pos' && this.waitingToRestoreFullscreen) {
                    this.attemptRestoreFullscreen();
                }
            });
            const onAnyTouchRestoreFs = () => {
                if (this.currentView === 'pos' && this.waitingToRestoreFullscreen && !document.fullscreenElement) {
                    this.attemptRestoreFullscreen();
                }
            };
            window.addEventListener('touchstart', onAnyTouchRestoreFs, { capture: true, passive: true });
            window.addEventListener('click', onAnyTouchRestoreFs, { capture: true, passive: true });

            // Sinkronisasi otomatis jika pengaturan toko diubah dari Admin di tab/jendela lain
            window.addEventListener('storage', (e) => {
                if (e.key === 'nodera_settings_updated') {
                    this.loadInitialData();
                }
            });
            if (window.BroadcastChannel) {
                try {
                    const syncCh = new BroadcastChannel('nodera_sync');
                    syncCh.onmessage = (ev) => {
                        if (ev.data && ev.data.type === 'settings_updated') {
                            this.loadInitialData();
                        }
                    };
                } catch(e) {}
            }
            window.addEventListener('focus', () => {
                this.loadInitialData();
            });
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    this.loadInitialData();
                }
            });

            // Keyboard shortcuts: Physical keyboard listener untuk PIN Cepat, Numpad, F2, F12, Escape
            window.addEventListener('keydown', (e) => {
                // Helper extract digit fisik (Keyboard Utama & Numpad)
                const getPhysicalDigit = () => {
                    if (e.key >= '0' && e.key <= '9') return e.key;
                    if (e.code && e.code.startsWith('Numpad') && e.code.length > 6) {
                        const num = e.code.replace('Numpad', '');
                        if (num >= '0' && num <= '9') return num;
                    }
                    return null;
                };

                // Priority 1: Screen Lock Overlay (AFK Lock)
                if (this.isScreenLocked) {
                    const digit = getPhysicalDigit();
                    if (digit !== null) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.pressLockPin(digit);
                    } else if (e.key === 'Backspace') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.backspaceLockPin();
                    } else if (e.key === 'Escape' || e.key === 'Delete' || e.key === 'c' || e.key === 'C') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.clearLockPin();
                    } else if (e.key === 'Enter' || e.code === 'NumpadEnter') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.submitUnlockScreen(true);
                    }
                    return;
                }

                // Priority 2: Modal PIN Cepat Admin / Supervisor
                if (this.adminPinModalOpen && this.adminAuthTab === 'pin') {
                    const digit = getPhysicalDigit();
                    if (digit !== null) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.pressAdminPin(digit);
                    } else if (e.key === 'Backspace') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.backspaceAdminPin();
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.clearAdminPin();
                        this.adminPinModalOpen = false;
                    } else if (e.key === 'Delete' || e.key === 'c' || e.key === 'C') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.clearAdminPin();
                    } else if (e.key === 'Enter' || e.code === 'NumpadEnter') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.submitAdminAuth();
                    }
                    return;
                }

                // Priority 3: Modal Ganti Kasir (Switch Cashier PIN)
                if (this.switchCashierModalOpen) {
                    const digit = getPhysicalDigit();
                    if (digit !== null) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.pressSwitchPin(digit);
                    } else if (e.key === 'Backspace') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.backspaceSwitchPin();
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.clearSwitchPin();
                        this.switchCashierModalOpen = false;
                    } else if (e.key === 'Delete' || e.key === 'c' || e.key === 'C') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.clearSwitchPin();
                    } else if (e.key === 'Enter' || e.code === 'NumpadEnter') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        this.submitSwitchCashier();
                    }
                    return;
                }

                // Priority 4: Quick Lock POS (isLocked)
                if (this.isLocked) {
                    const digit = getPhysicalDigit();
                    if (digit !== null) {
                        e.preventDefault();
                        this.pressPin(digit);
                    } else if (e.key === 'Backspace') {
                        e.preventDefault();
                        this.backspacePin();
                    } else if (e.key === 'Escape' || e.key === 'Delete') {
                        e.preventDefault();
                        this.clearPin();
                    } else if (e.key === 'Enter' || e.code === 'NumpadEnter') {
                        e.preventDefault();
                        this.submitPin();
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
            if (this._iconDebounce) clearTimeout(this._iconDebounce);
            this._iconDebounce = setTimeout(() => {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            }, 30);
        },

        closeAllModals() {
            this.scaleModal.open = false;
            this.variantModal.open = false;
            this.paymentModal = false;
            this.receiptModal = false;
            this.stockAlertModal.open = false;
            this.discountModalOpen = false;
            this.qrisModalOpen = false;
            this.refreshIcons();
        },

        async loadInitialData() {
            try {
                const res = await fetch('api.php?action=init');
                const data = await res.json();
                if (data.success) {
                    this.settings = data.settings || this.settings;
                    if (this.settings) {
                        if (this.settings.tax_percent !== undefined && this.settings.tax_percent !== null) {
                            this.settings.tax_percent = Number(this.settings.tax_percent);
                            this.settings.tax_rate = this.settings.tax_percent;
                        } else if (this.settings.tax_rate !== undefined && this.settings.tax_rate !== null) {
                            this.settings.tax_percent = Number(this.settings.tax_rate);
                        }
                        if (this.settings.feature_open_bill === undefined && this.settings.feature_table_management !== undefined) {
                            this.settings.feature_open_bill = this.settings.feature_table_management;
                        }
                        if (this.settings.sound_fx_enabled === undefined && this.settings.sound_enabled !== undefined) {
                            this.settings.sound_fx_enabled = this.settings.sound_enabled;
                        }
                    }
                    this.categories = data.categories || [];
                    this.products = data.products || [];
                    this.openBills = data.open_bills || [];
                    this.customers = data.customers || [];
                    this.expenseCategories = data.expense_categories || [];
                    this.currentUser = data.active_user || null;
                    this.authUsers = data.users || [];
                    if (!this.selectedAuthUser && this.authUsers.length > 0) {
                        this.selectedAuthUser = this.currentUser || this.authUsers[0];
                    }
                    this.useTaxInvoice = (this.settings && Number(this.settings.tax_enabled) === 1);
                    this.wholesaleTemplates = data.wholesale_templates || [];
                    this.currentShift = data.current_shift || null;
                    if (data.expenses) {
                        this.expenses = data.expenses || [];
                    }
                    if (data.license) {
                        this.licenseStatus = data.license;
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

        formatHistoryFilterDisplay() {
            if (!this.historyFilterStartDate && !this.historyFilterEndDate) {
                const d = this.historyFilterDate || this.todayDate || this.getLocalDateString();
                return this.formatDisplayDate(d);
            }
            if (this.historyFilterStartDate === this.historyFilterEndDate) {
                return this.formatDisplayDate(this.historyFilterStartDate);
            }
            return `${this.formatDisplayDate(this.historyFilterStartDate)} - ${this.formatDisplayDate(this.historyFilterEndDate)}`;
        },

        formatExpenseFilterDisplay() {
            if (!this.expenseFilterStartDate && !this.expenseFilterEndDate) {
                return 'Hari Ini (' + this.formatDisplayDate(this.todayDate || this.getLocalDateString()) + ')';
            }
            if (this.expenseFilterStartDate === this.expenseFilterEndDate) {
                return this.formatDisplayDate(this.expenseFilterStartDate);
            }
            return `${this.formatDisplayDate(this.expenseFilterStartDate)} - ${this.formatDisplayDate(this.expenseFilterEndDate)}`;
        },

        async loadHistory(startDate = null, endDate = null, manual = false) {
            const s = startDate || this.historyFilterStartDate || this.historyFilterDate || this.todayDate || this.getLocalDateString();
            const e = endDate || this.historyFilterEndDate || s;
            this.historyFilterStartDate = s;
            this.historyFilterEndDate = e;
            this.historyFilterDate = s;
            this.isLoadingHistory = true;
            try {
                const url = (s === e) 
                    ? 'api.php?action=reports_daily&date=' + encodeURIComponent(s)
                    : 'api.php?action=reports_daily&start_date=' + encodeURIComponent(s) + '&end_date=' + encodeURIComponent(e);
                const res = await fetch(url);
                const data = await res.json();
                if (data.success) {
                    this.historyData = data;
                    if (s === (this.todayDate || this.getLocalDateString()) && e === s) {
                        this.dashboardData = data;
                    }
                    if (manual) {
                        this.showToast('Data riwayat transaksi berhasil diperbarui', 'success');
                    }
                } else {
                    this.showToast(data.error || 'Gagal memuat riwayat transaksi', 'error');
                }
            } catch (err) {
                this.showToast(this.t('alert_load_history_failed') || 'Gagal memuat riwayat transaksi', 'error');
            } finally {
                this.isLoadingHistory = false;
                this.refreshIcons();
            }
        },

        async loadExpenses(startDate = null, endDate = null, manual = false) {
            this.isLoadingExpenses = true;
            try {
                const s = startDate || this.expenseFilterStartDate || this.todayDate || this.getLocalDateString();
                const e = endDate || this.expenseFilterEndDate || s;
                this.expenseFilterStartDate = s;
                this.expenseFilterEndDate = e;
                const res = await fetch(`api.php?action=expenses_list&start_date=${encodeURIComponent(s)}&end_date=${encodeURIComponent(e)}`);
                const data = await res.json();
                if (data.success) {
                    this.expenses = data.expenses || data.data || [];
                    if (manual) {
                        this.showToast('Data pengeluaran kas berhasil diperbarui', 'success');
                    }
                } else {
                    this.showToast(data.error || this.t('alert_load_expenses_failed') || 'Gagal memuat pengeluaran kas', 'error');
                }
            } catch (err) {
                console.error('Error loading expenses:', err);
                this.showToast('Gagal memuat data pengeluaran', 'error');
            } finally {
                this.isLoadingExpenses = false;
                this.refreshIcons();
            }
        },

        async loadOpenBills(manual = false) {
            this.isLoadingOpenBills = true;
            try {
                const res = await fetch('api.php?action=init');
                const data = await res.json();
                if (data.success && data.data) {
                    this.openBills = Array.isArray(data.data.open_bills) ? data.data.open_bills : [];
                    if (manual) {
                        this.showToast('Data tagihan (Open Bill) berhasil diperbarui', 'success');
                    }
                } else {
                    if (manual) this.showToast('Gagal memuat tagihan', 'error');
                }
            } catch (err) {
                console.error('Error loading open bills:', err);
                this.showToast(this.t('alert_load_bills_failed') || 'Gagal memuat tagihan', 'error');
            } finally {
                this.isLoadingOpenBills = false;
                this.refreshIcons();
            }
        },

        openDateFilterModal(target = 'history') {
            this.dateFilterModal.target = target;
            this.dateFilterModal.preset = 'today';
            const nowStr = this.todayDate || this.getLocalDateString();
            if (target === 'expenses') {
                this.dateFilterModal.startDate = this.expenseFilterStartDate || nowStr;
                this.dateFilterModal.endDate = this.expenseFilterEndDate || nowStr;
            } else {
                this.dateFilterModal.startDate = this.historyFilterStartDate || this.historyFilterDate || nowStr;
                this.dateFilterModal.endDate = this.historyFilterEndDate || this.historyFilterDate || nowStr;
            }
            this.dateFilterModal.selectedDate = this.dateFilterModal.startDate;
            this.dateFilterModal.open = true;
            this.$nextTick(() => {
                this.refreshIcons();
            });
        },

        setDatePreset(preset) {
            this.dateFilterModal.preset = preset;
            const now = new Date();
            const todayStr = this.getLocalDateString(now);
            if (preset === 'today') {
                this.dateFilterModal.startDate = todayStr;
                this.dateFilterModal.endDate = todayStr;
                this.dateFilterModal.selectedDate = todayStr;
            } else if (preset === 'yesterday') {
                const d = new Date();
                d.setDate(d.getDate() - 1);
                const yStr = this.getLocalDateString(d);
                this.dateFilterModal.startDate = yStr;
                this.dateFilterModal.endDate = yStr;
                this.dateFilterModal.selectedDate = yStr;
            } else if (preset === 'last7days') {
                const d = new Date();
                d.setDate(d.getDate() - 6);
                this.dateFilterModal.startDate = this.getLocalDateString(d);
                this.dateFilterModal.endDate = todayStr;
                this.dateFilterModal.selectedDate = this.dateFilterModal.startDate;
            } else if (preset === 'thisMonth') {
                const y = now.getFullYear();
                const m = String(now.getMonth() + 1).padStart(2, '0');
                this.dateFilterModal.startDate = `${y}-${m}-01`;
                this.dateFilterModal.endDate = todayStr;
                this.dateFilterModal.selectedDate = this.dateFilterModal.startDate;
            }
        },

        isYesterday(dateStr) {
            if (!dateStr) return false;
            const d = new Date();
            d.setDate(d.getDate() - 1);
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return dateStr === `${year}-${month}-${day}`;
        },

        applyDateFilter() {
            this.dateFilterModal.open = false;
            if (this.dateFilterModal.target === 'expenses') {
                this.expenseFilterStartDate = this.dateFilterModal.startDate;
                this.expenseFilterEndDate = this.dateFilterModal.endDate;
                this.loadExpenses(this.expenseFilterStartDate, this.expenseFilterEndDate);
            } else {
                this.historyFilterStartDate = this.dateFilterModal.startDate;
                this.historyFilterEndDate = this.dateFilterModal.endDate;
                this.historyFilterDate = this.dateFilterModal.startDate;
                this.loadHistory(this.dateFilterModal.startDate, this.dateFilterModal.endDate);
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
            if (view === 'pos') {
                if (this.posOrientation === 'landscape') {
                    this.requestLandscapeMode();
                } else {
                    this.forcePortraitMode();
                }
                this.posSubView = 'pos';
                this.searchQuery = '';
                this.selectedCategory = null;
            } else {
                this.forcePortraitMode();
            }
            if (view === 'dashboard') {
                this.loadDashboardMetrics();
            } else if (view === 'history') {
                this.loadHistory(this.historyFilterDate || this.todayDate || this.getLocalDateString());
            } else if (view === 'open_bills') {
                this.openBillSearchQuery = '';
                this.loadInitialData();
            } else if (view === 'products') {
                this.productSearchQuery = '';
                this.loadInitialData();
            } else if (view === 'categories') {
                this.categorySearchQuery = '';
                this.loadInitialData();
            } else if (view === 'expenses') {
                this.expenseSearchQuery = '';
                this.loadExpenses();
            }
            this.refreshIcons();
        },

        toggleCatalogViewMode() {
            this.catalogViewMode = this.catalogViewMode === 'grid' ? 'table' : 'grid';
            localStorage.setItem('pos_catalog_view_mode', this.catalogViewMode);
            this.$nextTick(() => this.refreshIcons());
        },

        async forcePortraitMode() {
            try {
                if (window.AndroidBridge && window.AndroidBridge.setOrientation) {
                    window.AndroidBridge.setOrientation('portrait');
                }
                // 1. Kunci orientasi ke portrait terlebih dahulu
                if (screen.orientation && screen.orientation.lock) {
                    await screen.orientation.lock('portrait-primary').catch(() => {
                        return screen.orientation.lock('portrait').catch(() => {});
                    });
                } else if (screen.lockOrientation) {
                    screen.lockOrientation('portrait');
                } else if (screen.webkitLockOrientation) {
                    screen.webkitLockOrientation('portrait');
                } else if (screen.mozLockOrientation) {
                    screen.mozLockOrientation('portrait');
                }
            } catch (e) {
                console.warn('Orientation lock portrait notice:', e);
            }

            // 2. Pertahankan Fullscreen di mode Portrait (tidak memaksa keluar ke windowed)
            try {
                const el = document.documentElement;
                if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                    const reqFs = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
                    if (reqFs) {
                        await reqFs.call(el).catch(() => {});
                    }
                }
                this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement);
            } catch (e) {}

            // 3. Lepaskan lock orientasi agar rotasi alami HP pengguna kembali bebas setelah portrait tercapai
            setTimeout(() => {
                try {
                    if (screen.orientation && screen.orientation.unlock) {
                        screen.orientation.unlock();
                    } else if (screen.unlockOrientation) {
                        screen.unlockOrientation();
                    }
                } catch (e) {}
            }, 600);
        },

        unlockLandscapeMode() {
            this.forcePortraitMode();
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
        // POS SUB-VIEWS & SCREEN CONTROLS
        // =====================================================================
        openPosOpenBills() {
            if (this.settings && this.settings.feature_open_bill == 0) {
                this.showToast('Fitur Open Bill (Tagihan Tertunda) dinonaktifkan di pengaturan modular.', 'warning');
                return;
            }
            this.posSubView = 'open_bills';
            this.openBillSearchQuery = '';
            this.refreshIcons();
        },

        openPosHistory() {
            this.posSubView = 'history';
            this.loadHistory(this.historyFilterDate || this.todayDate);
            this.refreshIcons();
        },

        openPosExpenses() {
            this.posSubView = 'expenses';
            this.loadExpenses();
            this.refreshIcons();
        },

        startSelling() {
            this.navigate('pos');
            this.posSubView = 'pos';
            if (this.posOrientation === 'landscape') {
                this.requestLandscapeMode();
            } else {
                this.forcePortraitMode();
            }
            this.refreshIcons();
        },

        async requestLandscapeMode() {
            try {
                if (window.AndroidBridge && window.AndroidBridge.setOrientation) {
                    window.AndroidBridge.setOrientation('landscape');
                }
                const el = document.documentElement;
                if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                    const reqFs = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
                    if (reqFs) {
                        await reqFs.call(el).catch(() => {});
                    }
                    this.isFullscreen = true;
                }
                if (screen.orientation && screen.orientation.lock) {
                    await screen.orientation.lock('landscape').catch(() => {});
                } else if (screen.lockOrientation) {
                    screen.lockOrientation('landscape');
                } else if (screen.webkitLockOrientation) {
                    screen.webkitLockOrientation('landscape');
                } else if (screen.mozLockOrientation) {
                    screen.mozLockOrientation('landscape');
                }
            } catch (e) {
                console.warn('Orientation lock notice:', e);
            }
        },

        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().then(() => {
                    this.isFullscreen = true;
                }).catch(err => {
                    console.warn('Fullscreen error:', err);
                });
            } else {
                document.exitFullscreen().then(() => {
                    this.isFullscreen = false;
                }).catch(err => {
                    console.warn('Exit fullscreen error:', err);
                });
            }
        },

        attemptRestoreFullscreen() {
            try {
                if (document.fullscreenElement || document.webkitFullscreenElement) {
                    this.waitingToRestoreFullscreen = false;
                    this.isFullscreen = true;
                    return;
                }
                const el = document.documentElement;
                const reqFs = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
                if (reqFs) {
                    reqFs.call(el).then(() => {
                        this.isFullscreen = true;
                        this.waitingToRestoreFullscreen = false;
                    }).catch(() => {});
                }
                if (this.posOrientation === 'landscape' && screen.orientation && screen.orientation.lock) {
                    screen.orientation.lock('landscape').catch(() => {});
                }
            } catch (e) {}
        },

        getTxItemCount(tx) {
            if (!tx) return 0;
            if (Array.isArray(tx.items)) {
                return tx.items.reduce((s, i) => s + (parseFloat(i.qty) || 1), 0);
            }
            if (tx.items_json) {
                try {
                    const items = JSON.parse(tx.items_json);
                    if (Array.isArray(items)) {
                        return items.reduce((s, i) => s + (parseFloat(i.qty) || 1), 0);
                    }
                } catch (e) {}
            }
            return 0;
        },

        getTxItemList(tx) {
            if (!tx) return '';
            let items = [];
            if (Array.isArray(tx.items)) {
                items = tx.items;
            } else if (tx.items_json) {
                try {
                    items = JSON.parse(tx.items_json);
                } catch (e) {}
            }
            if (!Array.isArray(items) || items.length === 0) return 'Daftar item transaksi';
            return items.map(i => (i.product_name || i.name || 'Item') + ' (' + (i.qty || 1) + ')').join(', ');
        },

        // =====================================================================
        // ADMIN AUTHORIZATION MODAL (PIN & PASSWORD)
        // =====================================================================
        openAdminPinModal() {
            this.adminPinModalOpen = true;
            this.adminAuthTab = 'password';
            this.adminAuthEmail = '';
            this.adminAuthPassword = '';
            this.adminPinInput = '';
            this.adminPinError = '';
            this.adminAuthLoading = false;
            this.$nextTick(() => this.refreshIcons());
        },

        pressAdminPin(digit) {
            if (this.adminPinInput.length < 6) {
                this.adminPinInput += digit.toString();
                this.adminPinError = '';
                if (this.adminPinInput.length === 6) {
                    this.submitAdminAuth();
                }
            }
        },

        backspaceAdminPin() {
            this.adminPinInput = this.adminPinInput.slice(0, -1);
            this.adminPinError = '';
        },

        clearAdminPin() {
            this.adminPinInput = '';
            this.adminPinError = '';
        },

        async submitAdminAuth() {
            this.adminAuthLoading = true;
            this.adminPinError = '';
            try {
                if (this.adminAuthTab === 'pin') {
                    const res = await fetch('api.php?action=admin_verify_pin', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ pin: this.adminPinInput, purpose: 'admin_login', redirect: true })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.adminPinModalOpen = false;
                        this.showToast(this.t('alert_auth_admin_success'), 'success');
                        window.location.href = 'admin/';
                        return;
                    } else {
                        this.adminPinError = data.error || 'PIN Admin / Supervisor salah.';
                        this.adminPinInput = '';
                    }
                } else {
                    const res = await fetch('api.php?action=auth_login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ user: this.adminAuthEmail, password: this.adminAuthPassword })
                    });
                    const data = await res.json();
                    if (data.success && data.user && data.user.role === 'admin') {
                        this.adminPinModalOpen = false;
                        this.showToast(this.t('alert_auth_admin_success'), 'success');
                        window.location.href = 'admin/';
                        return;
                    } else if (data.success && data.user && data.user.role !== 'admin') {
                        this.adminPinError = 'Akses ditolak: Akun kasir tidak memiliki hak akses admin.';
                    } else {
                        this.adminPinError = data.error || 'Autentikasi admin gagal.';
                    }
                }
            } catch (e) {
                this.adminPinError = 'Gagal menghubungi server.';
            } finally {
                this.adminAuthLoading = false;
                this.refreshIcons();
            }
        },

        // =====================================================================
        // SWITCH CASHIER MODAL
        // =====================================================================
        openSwitchCashierModal() {
            this.switchCashierModalOpen = true;
            this.selectedSwitchUser = (this.authUsers && this.authUsers.length > 0) ? this.authUsers[0] : null;
            this.switchPinInput = '';
            this.switchPinError = '';
            this.$nextTick(() => this.refreshIcons());
        },

        selectSwitchCashierUser(u) {
            this.selectedSwitchUser = u;
            this.switchPinInput = '';
            this.switchPinError = '';
        },

        pressSwitchPin(digit) {
            if (this.switchPinInput.length < 6) {
                this.switchPinInput += digit.toString();
                this.switchPinError = '';
                if (this.switchPinInput.length === 6) {
                    this.submitSwitchCashier();
                }
            }
        },

        backspaceSwitchPin() {
            this.switchPinInput = this.switchPinInput.slice(0, -1);
            this.switchPinError = '';
        },

        clearSwitchPin() {
            this.switchPinInput = '';
            this.switchPinError = '';
        },

        async submitSwitchCashier() {
            if (!this.selectedSwitchUser) return;
            try {
                const res = await fetch('api.php?action=auth_login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        pin: this.switchPinInput,
                        user_id: this.selectedSwitchUser.id,
                        user: this.selectedSwitchUser.id
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.currentUser = data.user;
                    this.switchCashierModalOpen = false;
                    this.showToast(this.t('alert_cashier_switched') + this.currentUser.name, 'success');
                    await this.loadInitialData();
                    await this.loadDashboardMetrics();
                    if (!this.currentShift) {
                        this.openStartShiftModal();
                    }
                } else {
                    this.switchPinError = data.error || 'PIN kasir salah.';
                    this.switchPinInput = '';
                }
            } catch (e) {
                this.switchPinError = 'Gagal menghubungi server.';
            }
            this.refreshIcons();
        },

        // =====================================================================
        // OTP LOGIN SYSTEM (4-Digit Auto-Focus)
        // =====================================================================
        onOtpInput(e, idx) {
            const val = e.target.value;
            if (val && idx < 5) {
                const nextInput = document.getElementById(e.target.id.replace(idx, idx + 1));
                if (nextInput) nextInput.focus();
            }
            if (this.otpDigits.join('').length === 6) {
                this.submitOtpLogin();
            }
        },

        onOtpKeydown(e, idx) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.submitOtpLogin();
                return;
            }
            if (e.key === 'Backspace' && !this.otpDigits[idx] && idx > 0) {
                const prevInput = document.getElementById(e.target.id.replace(idx, idx - 1));
                if (prevInput) prevInput.focus();
            }
        },

        onOtpPaste(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text').trim();
            if (/^\d{6}$/.test(paste)) {
                for (let i = 0; i < 6; i++) {
                    this.otpDigits[i] = paste[i];
                }
                this.submitOtpLogin();
            }
        },

        async submitOtpLogin() {
            const pin = this.otpDigits.join('');
            if (pin.length < 6) {
                this.pinError = 'PIN kasir wajib 6 digit angka.';
                return;
            }
            this.loginLoading = true;
            this.pinError = '';
            try {
                const payload = { pin: pin };
                if (this.loginIdentifier && this.loginIdentifier.trim() !== '') {
                    payload.user = this.loginIdentifier.trim();
                }
                const res = await fetch('api.php?action=auth_login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    this.currentUser = data.user;
                    this.isLocked = false;
                    this.otpDigits = ['', '', '', '', '', ''];
                    this.loginIdentifier = '';
                    await this.loadInitialData();
                    await this.loadDashboardMetrics();
                    this.showToast(this.t('alert_welcome_login') || ('Selamat bertugas, ' + this.currentUser.name), 'success');
                } else {
                    this.pinError = data.error || 'PIN salah. Silakan coba lagi.';
                    this.otpDigits = ['', '', '', '', '', ''];
                    const firstInput = document.getElementById('login-otp-0') || document.getElementById('lock-otp-0');
                    if (firstInput) firstInput.focus();
                }
            } catch (e) {
                this.pinError = 'Gagal menghubungi server.';
            } finally {
                this.loginLoading = false;
                this.refreshIcons();
            }
        },

        // =====================================================================
        // DESKTOP LICENSE MODAL CONTROLS
        // =====================================================================
        openDesktopLicenseModal() {
            this.licenseModalOpen = true;
            this.licenseKeyInput = '';
            this.licenseError = '';
            this.licenseSuccess = '';
            this.loadLicense();
        },

        closeDesktopLicenseModal() {
            this.licenseModalOpen = false;
            this.licenseError = '';
            this.licenseSuccess = '';
        },

        copyHwidLogin() {
            const hwid = this.licenseStatus?.hwid || '';
            if (!hwid) return;
            navigator.clipboard.writeText(hwid).then(() => {
                this.hwidCopied = true;
                this.showToast(this.t('alert_hwid_copied'), 'success');
                setTimeout(() => { this.hwidCopied = false; }, 3000);
            });
        },

        async loadLicense() {
            try {
                const res = await fetch('api.php?action=license_info');
                const data = await res.json();
                if (data.success) {
                    this.licenseStatus = data.license;
                }
            } catch (e) {}
            this.refreshIcons();
        },

        async claimFreeTrial() {
            this.licenseLoading = true;
            this.licenseError = '';
            try {
                const res = await fetch('api.php?action=license_trial', { method: 'POST' });
                const data = await res.json();
                if (data.success) {
                    this.licenseSuccess = 'Trial 7 hari berhasil diaktifkan!';
                    await this.loadLicense();
                    setTimeout(() => {
                        this.closeDesktopLicenseModal();
                        window.location.reload();
                    }, 1200);
                } else {
                    this.licenseError = data.error || 'Gagal mengaktifkan trial.';
                }
            } catch (e) {
                this.licenseError = 'Gagal menghubungi server.';
            } finally {
                this.licenseLoading = false;
            }
        },

        async submitDesktopLicense() {
            if (!this.licenseKeyInput) {
                this.licenseError = 'Silakan masukkan License Key.';
                return;
            }
            this.licenseLoading = true;
            this.licenseError = '';
            try {
                const res = await fetch('api.php?action=license_save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ license_key: this.licenseKeyInput.trim() })
                });
                const data = await res.json();
                if (data.success) {
                    this.licenseSuccess = data.message || 'Lisensi resmi berhasil diverifikasi & disimpan!';
                    await this.loadLicense();
                    setTimeout(() => {
                        this.closeDesktopLicenseModal();
                        window.location.reload();
                    }, 1200);
                } else {
                    this.licenseError = data.error || data.message || 'License Key tidak valid.';
                }
            } catch (e) {
                this.licenseError = 'Gagal menghubungi server.';
            } finally {
                this.licenseLoading = false;
            }
        },

        // =====================================================================
        // PRODUCT SELECTION & SMART DIALOGS (Volume vs Nominal & Variants)
        // =====================================================================
        get filteredProducts() {
            let list = this.products.filter(p => {
                const matchCat = !this.selectedCategory || this.selectedCategory === 'all' || p.category_id == this.selectedCategory;
                const matchSearch = !this.searchQuery || 
                    p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (p.code && p.code.toLowerCase().includes(this.searchQuery.toLowerCase()));
                return matchCat && matchSearch;
            });

            // Urutkan: Produk yang stoknya masih ada di atas, produk yang stoknya 0/habis di bawah sendiri
            return list.slice().sort((a, b) => {
                const stockA = parseFloat(a.stock) || 0;
                const stockB = parseFloat(b.stock) || 0;
                const isOutA = stockA <= 0;
                const isOutB = stockB <= 0;
                if (isOutA && !isOutB) return 1;
                if (!isOutA && isOutB) return -1;
                return 0;
            });
        },

        handleProductClick(product) {
            // 0. Cek stok: jika stok habis (<= 0), blokir jika stok minus TIDAK diizinkan
            const curStock = parseFloat(product.stock) || 0;
            const allowNegative = this.settings && Number(this.settings.feature_negative_stock) === 1;
            if (!allowNegative && curStock <= 0) {
                this.playErrorSound();
                this.showStockAlert(product);
                return;
            }

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

            // 2. Jika produk punya varian (hanya aktif jika modular feature_variants == 1)
            const variantsEnabled = this.settings && Number(this.settings.feature_variants) === 1;
            if (variantsEnabled && product.has_variants == 1 && product.variants && product.variants.length > 0) {
                this.variantModal.product = product;
                this.variantModal.selectedVariant = product.variants[0];
                this.variantModal.open = true;
                this.refreshIcons();
                return;
            }

            // 3. Cek batas stok di keranjang jika produk biasa (hanya jika stok minus TIDAK diizinkan)
            const inCart = this.cart.find(i => i.product_id === product.id && !i.variant_name);
            if (!allowNegative && inCart && (inCart.qty + 1) > curStock) {
                this.showStockLimitAlert(product, curStock);
                return;
            }

            // 4. Produk biasa -> langsung tambahkan ke keranjang
            this.addToCartDirect(product, 1);
        },

        confirmScaleItem() {
            const p = this.scaleModal.product;
            if (!p) return;
            const qty = this.scaleModal.calculatedQty;
            if (qty <= 0) return;

            const curStock = parseFloat(p.stock) || 0;
            const allowNegative = this.settings && Number(this.settings.feature_negative_stock) === 1;
            if (!allowNegative) {
                if (curStock <= 0) {
                    this.scaleModal.open = false;
                    this.showStockAlert(p);
                    return;
                }
                if (qty > curStock) {
                    this.scaleModal.open = false;
                    this.showStockLimitAlert(p, curStock);
                    return;
                }
            }

            const unitPrice = this.getProductPrice(p, this.activePriceTier || 'h1');
            this.addToCartDirect(p, qty, null, unitPrice);
            this.scaleModal.open = false;
        },

        confirmVariantItem(variant) {
            const p = this.variantModal.product;
            if (!p || !variant) return;

            const curStock = parseFloat(p.stock) || 0;
            const allowNegative = this.settings && Number(this.settings.feature_negative_stock) === 1;
            if (!allowNegative && curStock <= 0) {
                this.variantModal.open = false;
                this.showStockAlert(p);
                return;
            }

            this.addToCartDirect(p, 1, variant.name, parseFloat(variant.sell_price), parseFloat(variant.cost_price || 0), null, variant.id);
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

        get filteredPortraitMembersList() {
            if (!this.portraitMemberSearchQuery || !this.portraitMemberSearchQuery.trim()) {
                return (this.customers || []).slice(0, 10);
            }
            const q = this.portraitMemberSearchQuery.toLowerCase().trim();
            return (this.customers || []).filter(c => 
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
            this.portraitMemberSearchQuery = '';
            this.showPortraitMemberSearchResults = false;
            this.kasbonMemberSearchQuery = '';
            this.showKasbonSearchResults = false;
            this.showModalMemberSearch = false;
            
            // Sinkronkan tingkat harga H1 / H2 / H3 berdasarkan kategori member (jika feature_price_tiers aktif)
            if (this.settings && this.settings.feature_price_tiers == 0) {
                this.setPriceTier('h1');
            } else if (customer.tier === 'wholesale') {
                this.setPriceTier('h2');
            } else if (customer.tier === 'vip') {
                this.setPriceTier('h3');
            } else {
                this.setPriceTier('h1');
            }

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
            this.portraitMemberSearchQuery = '';
            this.showPortraitMemberSearchResults = false;
            this.kasbonMemberSearchQuery = '';
            this.showKasbonSearchResults = false;
            this.showModalMemberSearch = false;
            
            // Kembalikan ke tier normal H1 saat member dibatalkan
            this.setPriceTier('h1');

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
                this.setPriceTier('h1');
            } else {
                this.selectedCustomer = this.customers.find(c => c.id == this.selectedCustomerId) || null;
                this.customerName = this.selectedCustomer ? this.selectedCustomer.name : '';
                if (this.selectedCustomer) {
                    if (this.selectedCustomer.tier === 'wholesale') this.setPriceTier('h2');
                    else if (this.selectedCustomer.tier === 'vip') this.setPriceTier('h3');
                    else this.setPriceTier('h1');
                }
            }
            this.refreshIcons();
        },

        applyMemberPricing() {
            this.setPriceTier(this.activePriceTier || 'h1');
        },

        addToCart(product, qty = 1, specificTier = null) {
            return this.addToCartDirect(product, qty, null, null, null, specificTier);
        },

        addToCartDirect(product, qty = 1, variantName = null, overridePrice = null, overrideCost = null, specificTier = null, variantId = null) {
            const allowNegative = this.settings && Number(this.settings.feature_negative_stock) === 1;
            const curStock = parseFloat(product.stock) || 0;
            if (!allowNegative && curStock <= 0) {
                this.showStockAlert(product);
                return;
            }
            this.playBeepSound();
            const tier = specificTier || this.activePriceTier || 'h1';
            let price = overridePrice !== null ? overridePrice : this.getProductPrice(product, tier);
            const cost = overrideCost !== null ? overrideCost : parseFloat(product.cost_price || 0);

            const vId = variantId || (variantName && Array.isArray(product.variants) ? (product.variants.find(v => v.name === variantName)?.id || null) : null);

            // Cek apakah item yang sama persis (termasuk varian dan tier harga) sudah ada
            const existingIdx = this.cart.findIndex(i => 
                i.product_id === product.id && 
                ((vId && i.variant_id === vId) || i.variant_name === variantName) &&
                i.price_tier === tier &&
                product.is_scale != 1 // Scale item dibuat baris terpisah agar nominal akurat
            );

            if (existingIdx > -1) {
                this.cart[existingIdx].qty += qty;
                if (overridePrice === null) {
                    this.cart[existingIdx].price = price;
                    this.cart[existingIdx].price_tier = tier;
                    this.cart[existingIdx].is_wholesale = (tier === 'h2' || tier === 'h3');
                }
            } else {
                this.cart.push({
                    product_id: product.id,
                    variant_id: vId,
                    name: product.name,
                    variant_name: variantName,
                    unit: product.unit || 'pcs',
                    type: product.type || 'goods',
                    price: price,
                    cost_price: cost,
                    qty: qty,
                    price_tier: tier,
                    is_wholesale: (tier === 'h2' || tier === 'h3'),
                    is_scale: product.is_scale == 1
                });
            }
            this.playBeepSound();
            this.refreshIcons();
        },

        showStockAlert(product) {
            this.stockAlertModal.productName = product.name;
            this.stockAlertModal.productStock = parseFloat(product.stock) || 0;
            this.stockAlertModal.unit = product.unit || 'pcs';
            this.stockAlertModal.isExceeded = false;
            this.stockAlertModal.open = true;
            this.playErrorSound();
            this.showToast(product.name + ': ' + this.t('alert_stock_empty'), 'error');
            this.refreshIcons();
        },

        showStockLimitAlert(product, availableStock) {
            this.stockAlertModal.productName = product.name;
            this.stockAlertModal.productStock = availableStock;
            this.stockAlertModal.unit = product.unit || 'pcs';
            this.stockAlertModal.isExceeded = true;
            this.stockAlertModal.open = true;
            this.playErrorSound();
            this.showToast(product.name + ': ' + this.t('alert_stock_limit') + ' (' + availableStock + ' ' + (product.unit || 'pcs') + ')', 'warning');
            this.refreshIcons();
        },

        updateCartQty(idx, delta) {
            const item = this.cart[idx];
            if (!item) return;

            if (delta > 0) {
                const prod = this.products.find(p => p.id === item.product_id);
                if (prod) {
                    const curStock = parseFloat(prod.stock) || 0;
                    const allowNegative = this.settings && Number(this.settings.feature_negative_stock) === 1;
                    if (!allowNegative && (item.qty + delta) > curStock) {
                        this.showStockLimitAlert(prod, curStock);
                        return;
                    }
                }
            }

            item.qty += delta;
            if (item.qty <= 0) {
                this.cart.splice(idx, 1);
            }
            this.recalculatePaymentAmounts();
        },

        validateCartItemQty(idx) {
            const item = this.cart[idx];
            if (!item) return;

            let val = parseFloat(item.qty);
            if (isNaN(val) || val <= 0) {
                item.qty = 1;
                this.recalculatePaymentAmounts();
                return;
            }

            const prod = this.products.find(p => p.id === item.product_id);
            if (prod) {
                const curStock = parseFloat(prod.stock) || 0;
                const allowNegative = this.settings && Number(this.settings.feature_negative_stock) === 1;
                if (!allowNegative && curStock <= 0) {
                    this.showStockAlert(prod);
                    this.cart.splice(idx, 1);
                    this.recalculatePaymentAmounts();
                    return;
                }
                if (!allowNegative && val > curStock) {
                    this.showStockLimitAlert(prod, curStock);
                    item.qty = curStock;
                    this.recalculatePaymentAmounts();
                    return;
                }
            }

            item.qty = val;
            this.recalculatePaymentAmounts();
        },

        recalculatePaymentAmounts() {
            if (this.paymentMethod === 'cash') {
                this.cashTendered = this.total;
                this.cashTenderedStr = this.total.toString();
            } else if (this.paymentMethod === 'split') {
                this.splitCash = Math.round(this.total / 2);
                this.splitNonCash = Math.max(0, this.total - this.splitCash);
            }
        },

        getPaymentMethodLabel(method) {
            switch (method) {
                case 'cash': return this.t('cash', 'Tunai');
                case 'qris': return this.t('qris', 'QRIS');
                case 'bank': return this.t('bank_transfer', 'Transfer Bank');
                case 'edc': return this.t('edc_card', 'Mesin EDC');
                case 'debt': return this.t('debt_kasbon', 'Kasbon');
                case 'split': return this.t('split_payment', 'Split Bill');
                default: return this.t('payment_method', 'Pilih Metode Pembayaran');
            }
        },

        removeFromCart(idx) {
            this.cart.splice(idx, 1);
            this.recalculatePaymentAmounts();
            this.refreshIcons();
        },

        removeCartItem(idx) {
            this.removeFromCart(idx);
        },

        decreaseCartQty(idx) {
            this.updateCartQty(idx, -1);
        },

        increaseCartQty(idx) {
            this.updateCartQty(idx, 1);
        },

        clearCart() {
            this.cart = [];
            this.activePriceTier = 'h1';
            this.customerName = '';
            this.selectedCustomerId = '';
            this.selectedCustomer = null;
            this.usePoints = false;
            this.memberSearchQuery = '';
            this.showMemberSearchResults = false;
            this.portraitMemberSearchQuery = '';
            this.showPortraitMemberSearchResults = false;
            this.activeOpenBillId = null;
            this.recalculatePaymentAmounts();
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

        get cartDiscountAmount() {
            if (!this.cartDiscount || !this.cartDiscount.value) return 0;
            const val = parseFloat(this.cartDiscount.value) || 0;
            if (val <= 0) return 0;
            if (this.cartDiscount.type === 'percent') {
                return (this.subtotal * Math.min(100, val)) / 100;
            } else {
                return Math.min(this.subtotal, val);
            }
        },

        get totalDiscountAmount() {
            return this.memberDiscountAmount + this.cartDiscountAmount;
        },

        get potentialPointsDiscount() {
            if (!this.settings || this.settings.loyalty_points_enabled == 0) return 0;
            if (!this.selectedCustomer || !this.selectedCustomer.points) return 0;
            const redeemVal = parseFloat(this.settings.point_redeem_value) || 100;
            const maxMoney = (parseInt(this.selectedCustomer.points) || 0) * redeemVal;
            const afterDiscountSubtotal = Math.max(0, this.subtotal - this.totalDiscountAmount);
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
            if (!this.settings || Number(this.settings.tax_enabled) === 0 || !this.useTaxInvoice) return 0;
            const pct = parseFloat(this.settings.tax_percent) || 0;
            const taxable = Math.max(0, this.subtotal - this.totalDiscountAmount - this.pointsDiscount);
            return pct > 0 ? (taxable * (pct / 100)) : 0;
        },

        get cartTotalQty() {
            if (!this.cart || this.cart.length === 0) return 0;
            return this.cart.reduce((sum, it) => sum + (parseFloat(it.qty) || 0), 0);
        },

        get total() {
            const base = this.subtotal - this.totalDiscountAmount - this.pointsDiscount;
            let finalAmount = Math.max(0, Math.round(base + this.taxAmount));
            const rounding = this.settings.cash_rounding || 'none';
            if (this.paymentMethod === 'cash' && rounding !== 'none') {
                const r = parseInt(rounding, 10);
                if (r > 0) {
                    finalAmount = Math.ceil(finalAmount / r) * r;
                }
            }
            return finalAmount;
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

        // Discount Modal Methods
        openDiscountModal() {
            if (this.settings && this.settings.feature_discount == 0) {
                this.showToast('Fitur diskon dinonaktifkan di pengaturan modular.', 'warning');
                return;
            }
            if (document.activeElement && typeof document.activeElement.blur === 'function') {
                document.activeElement.blur();
            }
            this.cartDiscount.otpDigits = ['', '', '', '', '', ''];
            this.cartDiscount.adminPin = '';
            this.cartDiscount.pinError = '';
            this.discountModalOpen = true;
            this.refreshIcons();
            if (this.settings && this.settings.require_pin_discount == 1 && Number(this.cartDiscount.value) > 0) {
                this.$nextTick(() => {
                    const el = document.getElementById('discount-otp-0');
                    if (el) el.focus();
                });
            }
        },

        onDiscountOtpInput(e, idx) {
            const val = e.target.value;
            if (val && idx < 5) {
                const nextInput = document.getElementById('discount-otp-' + (idx + 1));
                if (nextInput) nextInput.focus();
            }
            this.cartDiscount.adminPin = (this.cartDiscount.otpDigits || []).join('');
            if (this.cartDiscount.adminPin.length === 6) {
                this.applyCartDiscount();
            }
        },

        onDiscountOtpKeydown(e, idx) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.cartDiscount.adminPin = (this.cartDiscount.otpDigits || []).join('');
                this.applyCartDiscount();
                return;
            }
            if (e.key === 'Backspace' && !this.cartDiscount.otpDigits[idx] && idx > 0) {
                const prevInput = document.getElementById('discount-otp-' + (idx - 1));
                if (prevInput) prevInput.focus();
            }
        },

        onDiscountOtpPaste(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text').trim();
            if (/^\d{6}$/.test(paste)) {
                this.cartDiscount.otpDigits = ['', '', '', '', '', ''];
                for (let i = 0; i < 6; i++) {
                    this.cartDiscount.otpDigits[i] = paste[i] || '';
                }
                this.cartDiscount.adminPin = this.cartDiscount.otpDigits.join('');
                if (this.cartDiscount.adminPin.length === 6) {
                    this.applyCartDiscount();
                }
            }
        },

        setQuickDiscount(arg1, arg2) {
            if (document.activeElement && typeof document.activeElement.blur === 'function') {
                document.activeElement.blur();
            }
            if (typeof arg1 === 'string' && (arg1 === 'percent' || arg1 === 'nominal')) {
                this.cartDiscount.type = arg1;
                this.cartDiscount.value = parseFloat(arg2) || 0;
            } else if (typeof arg2 === 'string' && (arg2 === 'percent' || arg2 === 'nominal')) {
                this.cartDiscount.type = arg2;
                this.cartDiscount.value = parseFloat(arg1) || 0;
            } else {
                this.cartDiscount.value = parseFloat(arg1) || 0;
            }
        },

        async applyCartDiscount() {
            if (document.activeElement && typeof document.activeElement.blur === 'function') {
                document.activeElement.blur();
            }
            if (this.cartDiscount.otpDigits && this.cartDiscount.otpDigits.length > 0) {
                const joined = this.cartDiscount.otpDigits.join('');
                if (joined) this.cartDiscount.adminPin = joined;
            }
            // Validasi PIN jika require_pin_discount aktif di modular
            const reqPin = this.settings && this.settings.require_pin_discount == 1;
            const hasDiscountVal = Number(this.cartDiscount.value) > 0;
            if (reqPin && hasDiscountVal) {
                if (!this.cartDiscount.adminPin) {
                    this.cartDiscount.pinError = 'PIN Owner/Admin wajib diisi.';
                    this.showToast('PIN Owner/Admin diperlukan untuk diskon.', 'warning');
                    this.$nextTick(() => {
                        const el = document.getElementById('discount-otp-0');
                        if (el) el.focus();
                    });
                    return;
                }
                try {
                    const res = await fetch('api.php?action=admin_verify_pin', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ pin: this.cartDiscount.adminPin })
                    });
                    const d = await res.json();
                    if (!d.success) {
                        this.cartDiscount.pinError = d.error || 'PIN salah. Diskon ditolak!';
                        this.cartDiscount.otpDigits = ['', '', '', '', '', ''];
                        this.cartDiscount.adminPin = '';
                        this.showToast('PIN Admin salah. Diskon ditolak!', 'error');
                        this.$nextTick(() => {
                            const f = document.getElementById('discount-otp-0');
                            if (f) f.focus();
                        });
                        return;
                    }
                } catch (e) {
                    this.showToast('Gagal memverifikasi PIN.', 'error');
                    return;
                }
            }
            this.discountModalOpen = false;
            this.cartDiscount.adminPin = '';
            this.cartDiscount.pinError = '';
            this.cartDiscount.otpDigits = ['', '', '', '', '', ''];
            if (this.paymentModal && typeof this.recalculatePaymentAmounts === 'function') {
                this.recalculatePaymentAmounts();
            }
            this.refreshIcons();
        },

        clearCartDiscount() {
            if (document.activeElement && typeof document.activeElement.blur === 'function') {
                document.activeElement.blur();
            }
            this.cartDiscount = { type: 'percent', value: 0, reason: '', adminPin: '', pinError: '', otpDigits: ['', '', '', '', '', ''] };
            this.discountModalOpen = false;
            if (this.paymentModal && typeof this.recalculatePaymentAmounts === 'function') {
                this.recalculatePaymentAmounts();
            }
            this.refreshIcons();
        },

        // Cashier Direct Customer Registration
        openAddCustomerModal(initialName = '') {
            if (this.settings && this.settings.feature_cashier_add_customer == 0) {
                this.showToast('Fitur tambah member oleh kasir dinonaktifkan Admin.', 'warning');
                return;
            }
            this.cashierCustomerForm = {
                name: (typeof initialName === 'string' ? initialName : '') || this.memberSearchQuery || this.portraitMemberSearchQuery || '',
                phone: '',
                tier: 'regular',
                allow_debt: true,
                saved_debt_limit: 500000,
                debt_limit: 500000,
                notes: ''
            };
            this.cashierCustomerError = '';
            this.cashierCustomerSaving = false;
            this.showMemberSearchResults = false;
            this.showPortraitMemberSearchResults = false;
            this.cashierAddCustomerModalOpen = true;
            this.refreshIcons();
            this.$nextTick(() => {
                const el = document.getElementById('cashier-customer-name-input');
                if (el) el.focus();
            });
        },

        async saveNewCustomerFromPos() {
            const name = (this.cashierCustomerForm.name || '').trim();
            const phone = (this.cashierCustomerForm.phone || '').trim();
            if (!name) {
                this.cashierCustomerError = 'Nama pelanggan wajib diisi.';
                return;
            }
            if (!phone) {
                this.cashierCustomerError = 'Nomor WhatsApp / HP wajib diisi.';
                return;
            }
            this.cashierCustomerSaving = true;
            this.cashierCustomerError = '';
            try {
                const debtLimit = (this.cashierCustomerForm.allow_debt === false) 
                    ? 0 
                    : (parseFloat(this.cashierCustomerForm.debt_limit) || 500000);

                const res = await fetch('api.php?action=customers_save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name: name,
                        phone: phone,
                        tier: this.cashierCustomerForm.tier || 'regular',
                        debt_limit: debtLimit,
                        notes: (this.cashierCustomerForm.notes || '').trim()
                    })
                });
                const data = await res.json();
                if (data.success && data.customer) {
                    const newCust = data.customer;
                    const existingIdx = this.customers.findIndex(c => c.id === newCust.id);
                    if (existingIdx >= 0) {
                        this.customers[existingIdx] = newCust;
                    } else {
                        this.customers.unshift(newCust);
                    }
                    this.selectMember(newCust);
                    this.memberSearchQuery = '';
                    this.portraitMemberSearchQuery = '';
                    this.cashierAddCustomerModalOpen = false;
                    this.showToast('Member ' + newCust.name + ' berhasil didaftarkan & dipilih!', 'success');
                } else {
                    this.cashierCustomerError = data.error || 'Gagal menyimpan data member.';
                }
            } catch (err) {
                this.cashierCustomerError = 'Koneksi ke server POS gagal.';
            } finally {
                this.cashierCustomerSaving = false;
            }
        },

        // View Mode Switchers
        toggleCatalogView(mode) {
            this.catalogViewMode = mode;
            localStorage.setItem('nodera_catalog_view', mode);
            this.refreshIcons();
        },

        toggleHistoryView(mode) {
            this.historyViewMode = mode;
            localStorage.setItem('nodera_history_view', mode);
            this.refreshIcons();
        },

        toggleOpenBillsView(mode) {
            this.openBillsViewMode = mode;
            localStorage.setItem('nodera_openbills_view', mode);
            this.refreshIcons();
        },

        toggleExpenseView(mode) {
            this.expenseViewMode = mode;
            localStorage.setItem('nodera_expense_view', mode);
            this.refreshIcons();
        },

        openPrinterSettingsModal() {
            this.printerSettingsModalOpen = true;
            this.$nextTick(() => this.refreshIcons());
        },

        async savePrinterSettings() {
            try {
                const res = await fetch('api.php?action=settings_update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.settings)
                });
                const d = await res.json();
                if (d.success) {
                    this.showToast(this.t('alert_printer_saved'), 'success');
                    this.printerSettingsModalOpen = false;
                } else {
                    this.showToast(d.error || this.t('alert_printer_save_failed'), 'error');
                }
            } catch (e) {
                this.showToast(this.t('alert_server_error'), 'error');
            }
        },

        async testPrintThermalReceipt() {
            const method = this.settings.printer_connection_type || 'browser';
            if (method === 'rawbt') {
                return await this.testPrintRawBT();
            }
            if (method === 'quickprinter') {
                return this.testPrintQuickPrinter();
            }
            if (method === 'bluetooth') {
                if (this.bluetoothPrinter.status === 'connected' && this.bluetoothPrinter.characteristic) {
                    const bytes = await this.buildTestPrintBytes();
                    return this.sendRawBytes(bytes);
                }
                if (/Android/i.test(navigator.userAgent)) {
                    this.showToast(this.t('alert_printer_ble_redirect'), 'info');
                    return await this.testPrintRawBT();
                }
            }

            const width = this.settings.paper_size === '80mm' ? '300px' : '220px';
            const printWin = window.open('', '_blank', 'width=420,height=600');
            if (!printWin) {
                this.showToast(this.t('alert_popup_blocked'), 'warning');
                return;
            }
            const logoHtml = (this.settings && Number(this.settings.print_logo_receipt) === 1 && this.settings.store_logo) 
                ? `<div class="text-center" style="margin-bottom: 6px;"><img src="${this.settings.store_logo}" style="max-height: 48px; max-width: 120px; filter: grayscale(100%); display: inline-block;"></div>` 
                : '';
            const html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Tes Cetak Thermal ${this.settings.paper_size || '58mm'}</title>
                    <style>
                        body { font-family: monospace; font-size: 12px; margin: 0; padding: 8px; width: ${width}; color: #000; line-height: 1.35; }
                        .text-center { text-align: center; }
                        .text-right { text-align: right; }
                        .font-bold { font-weight: bold; }
                        .border-b { border-bottom: 1px dashed #000; margin: 6px 0; }
                        .flex { display: flex; justify-content: space-between; }
                        @media print { @page { margin: 2mm; size: auto; } }
                    </style>
                </head>
                <body onload="window.print(); window.close();">
                    ${logoHtml}
                    <div class="text-center font-bold" style="font-size: 14px;">${this.settings.store_name || 'KASIR POS'}</div>
                    ${this.settings.store_address ? `<div class="text-center" style="font-size: 10px;">${this.settings.store_address}</div>` : ''}
                    ${this.settings.store_phone ? `<div class="text-center" style="font-size: 10px;">Telp: ${this.settings.store_phone}</div>` : ''}
                    <div class="border-b"></div>
                    <div class="text-center font-bold">TES CETAK PRINTER THERMAL</div>
                    <div class="text-center font-bold" style="font-size: 11px;">KERTAS: ${this.settings.paper_size || '58mm'}</div>
                    <div class="border-b"></div>
                    <div class="flex"><span>Waktu Tes</span><span>${new Date().toLocaleTimeString('id-ID')}</span></div>
                    <div class="flex"><span>Kasir</span><span>${this.currentUser ? this.currentUser.name : 'Kasir'}</span></div>
                    <div class="border-b"></div>
                    <div class="flex"><span>1x Produk Uji A</span><span>Rp 25.000</span></div>
                    <div class="flex"><span>2x Produk Uji B</span><span>Rp 50.000</span></div>
                    <div class="border-b"></div>
                    <div class="flex font-bold"><span>Total Pembelian</span><span>Rp 75.000</span></div>
                    <div class="flex"><span>Tunai (Cash)</span><span>Rp 100.000</span></div>
                    <div class="flex font-bold"><span>Kembalian</span><span>Rp 25.000</span></div>
                    <div class="border-b"></div>
                    <div class="text-center" style="font-size: 10px; margin-top: 6px;">${this.settings.receipt_footer || 'Terima Kasih atas Kunjungan Anda!'}</div>
                    <div class="text-center" style="font-size: 9px; margin-top: 4px;">*** PRINTER TEST BERHASIL ***</div>
                </body>
                </html>
            `;
            printWin.document.write(html);
            printWin.document.close();
        },

        // Wholesale Quick Add
        quickAddWholesale(product, template) {
            if (!product || !template) return;
            const minQty = parseFloat(template.min_qty) || 1;
            let price = parseFloat(product.sell_price) || 0;
            if (template.discount_percent && template.discount_percent > 0) {
                price = price - (price * (parseFloat(template.discount_percent) / 100));
            } else if (product.wholesale_price && product.wholesale_price > 0) {
                price = parseFloat(product.wholesale_price);
            }
            this.addToCartDirect(product, minQty, price);
            this.showToast(this.t('alert_wholesale_added') + ` (Qty >= ${minQty})`, 'success');
        },

        // Void Transaction Methods
        openVoidModal(tx) {
            this.voidModal.tx = tx;
            this.voidModal.reason = 'Kesalahan input kasir';
            this.voidModal.adminPin = '';
            this.voidModal.error = '';
            this.voidModal.open = true;
            this.$nextTick(() => this.refreshIcons());
        },

        async submitVoidTransaction() {
            if (!this.voidModal.tx) return;
            this.voidModal.loading = true;
            this.voidModal.error = '';
            try {
                const res = await fetch('api.php?action=transaction_void', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: this.voidModal.tx.id,
                        transaction_id: this.voidModal.tx.id,
                        reason: this.voidModal.reason,
                        admin_pin: this.voidModal.adminPin
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast((this.voidModal.tx ? this.voidModal.tx.invoice_no + ': ' : '') + this.t('alert_void_success'), 'success');
                    this.voidModal.open = false;
                    await this.loadHistory(this.historyFilterDate);
                    await this.loadDashboardMetrics();
                } else {
                    this.voidModal.error = data.error || 'Gagal membatalkan transaksi.';
                }
            } catch (e) {
                this.voidModal.error = 'Gagal menghubungi server.';
            } finally {
                this.voidModal.loading = false;
            }
        },

        // Sales Return Methods (Kasir POS)
        async openPosReturnModal(tx) {
            this.posReturnModal.open = true;
            this.posReturnModal.loading = true;
            this.posReturnModal.saving = false;
            this.posReturnModal.txData = null;
            this.posReturnModal.items = [];
            this.posReturnModal.refundMethod = tx.payment_method === 'debt' ? 'debt_deduction' : 'cash';
            this.posReturnModal.reason = 'Barang Cacat/Salah';
            this.posReturnModal.notes = '';
            try {
                const res = await fetch(`api.php?action=returns_get_transaction&query=${encodeURIComponent(tx.invoice_no)}`);
                const d = await res.json();
                if (d.success) {
                    this.posReturnModal.txData = d.data;
                    this.posReturnModal.items = (d.data.items || []).map(it => ({
                        transaction_item_id: it.id,
                        product_id: it.product_id,
                        variant_id: it.variant_id,
                        product_name: it.product_name,
                        unit: it.unit || 'pcs',
                        price: parseFloat(it.price || 0),
                        max_qty: parseFloat(it.returnable_qty || 0),
                        qty: 0,
                        selected: false,
                        condition_type: 'good'
                    }));
                } else {
                    this.showToast(d.error || 'Gagal memuat rincian transaksi retur.', 'error');
                    this.posReturnModal.open = false;
                }
            } catch (e) {
                this.showToast('Gagal terhubung ke server.', 'error');
                this.posReturnModal.open = false;
            } finally {
                this.posReturnModal.loading = false;
                this.$nextTick(() => this.refreshIcons());
            }
        },
        get posReturnTotalRefund() {
            if (!this.posReturnModal.items) return 0;
            return this.posReturnModal.items.reduce((sum, it) => {
                if (it.selected && it.qty > 0) {
                    return sum + (it.qty * it.price);
                }
                return sum;
            }, 0);
        },
        async submitPosReturn() {
            const selectedItems = (this.posReturnModal.items || []).filter(it => it.selected && it.qty > 0);
            if (selectedItems.length === 0) {
                this.showToast('Pilih minimal satu barang yang akan diretur.', 'warning');
                return;
            }
            for (const it of selectedItems) {
                if (it.qty > it.max_qty) {
                    this.showToast(`Qty retur untuk ${it.product_name} melebihi batas maksimal (${it.max_qty}).`, 'error');
                    return;
                }
            }
            this.posReturnModal.saving = true;
            try {
                const payload = {
                    transaction_id: this.posReturnModal.txData.id,
                    refund_method: this.posReturnModal.refundMethod,
                    reason: this.posReturnModal.reason,
                    notes: this.posReturnModal.notes,
                    items: selectedItems.map(it => ({
                        transaction_item_id: it.transaction_item_id,
                        product_id: it.product_id,
                        variant_id: it.variant_id,
                        product_name: it.product_name,
                        qty: it.qty,
                        refund_price: it.price,
                        condition_type: it.condition_type
                    }))
                };
                const res = await fetch('api.php?action=returns_create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const d = await res.json();
                if (d.success) {
                    this.showToast(d.message || 'Retur penjualan berhasil diproses!', 'success');
                    this.posReturnModal.open = false;
                    await this.loadInitialData();
                    await this.loadDashboardMetrics();
                    await this.loadHistory(this.historyFilterDate);
                } else {
                    this.showToast(d.error || 'Gagal memproses retur.', 'error');
                }
            } catch (e) {
                this.showToast('Terjadi kesalahan sistem saat memproses retur.', 'error');
            } finally {
                this.posReturnModal.saving = false;
            }
        },

        // =====================================================================
        // SCREEN LOCK METHODS (KUNCI LAYAR SEMENTARA KASIR)
        // =====================================================================
        lockScreen() {
            this.closeAllModals();
            this.printerSettingsModalOpen = false;
            this.printerScannerModalOpen = false;
            this.isScreenLocked = true;
            localStorage.setItem('nodera_pos_locked', '1');
            this.lockPinInput = '';
            this.lockPinError = '';
            this.updateLockClock();
            if (this._lockClockInterval) clearInterval(this._lockClockInterval);
            this._lockClockInterval = setInterval(() => this.updateLockClock(), 1000);
            if (document.activeElement && typeof document.activeElement.blur === 'function') {
                document.activeElement.blur();
            }
            this.$nextTick(() => {
                this.refreshIcons();
                const el = document.getElementById('pos-screen-lock-modal');
                if (el) el.focus();
            });
        },

        updateLockClock() {
            const now = new Date();
            this.lockDigitalClock = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
            this.lockDigitalDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' });
        },

        pressLockPin(digit) {
            if (this.lockPinInput.length < 6) {
                this.lockPinInput += digit.toString();
                this.lockPinError = '';
                if (this.lockPinInput.length === 6) {
                    this.submitUnlockScreen(false);
                }
            }
        },

        backspaceLockPin() {
            this.lockPinInput = this.lockPinInput.slice(0, -1);
            this.lockPinError = '';
        },

        clearLockPin() {
            this.lockPinInput = '';
            this.lockPinError = '';
        },

        async submitUnlockScreen(isExplicit = false) {
            if (!this.lockPinInput) return;
            if (this.lockPinInput.length < 6) {
                if (isExplicit) {
                    this.lockPinError = 'PIN wajib 6 digit angka.';
                }
                return;
            }
            const inputPin = this.lockPinInput.trim();

            const onUnlockSuccess = (msg) => {
                this.isScreenLocked = false;
                localStorage.removeItem('nodera_pos_locked');
                if (this._lockClockInterval) clearInterval(this._lockClockInterval);
                this.lockPinInput = '';
                this.lockPinError = '';
                this.showToast(msg || this.t('alert_unlock_screen'), 'success');
                this.$nextTick(() => this.refreshIcons());
            };

            // 1. Cek apakah cocok via backend auth
            try {
                const res = await fetch('api.php?action=auth_login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        pin: inputPin,
                        user_id: this.currentUser ? this.currentUser.id : null
                    })
                });
                const data = await res.json();
                if (data.success) {
                    return onUnlockSuccess(this.t('alert_unlock_screen'));
                }
            } catch (e) {}

            // 2. Cek apakah cocok dengan supervisor / admin PIN (tanpa menimpa kasir aktif)
            try {
                const res = await fetch('api.php?action=admin_verify_pin', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ pin: inputPin, purpose: 'unlock' })
                });
                const data = await res.json();
                if (data.success) {
                    return onUnlockSuccess(this.t('alert_unlock_screen'));
                }
            } catch (e) {}

            this.lockPinError = this.t('alert_pin_invalid', 'PIN salah. Silakan coba lagi.');
            this.lockPinInput = '';
        },

        emergencyUnlock() {
            this.isScreenLocked = false;
            localStorage.removeItem('nodera_pos_locked');
            if (this._lockClockInterval) clearInterval(this._lockClockInterval);
            this.lockPinInput = '';
            this.lockPinError = '';
            this.showToast(this.t('alert_unlock_screen'), 'info');
            this.$nextTick(() => this.refreshIcons());
        },

        lockscreenSwitchUser() {
            this.isScreenLocked = false;
            localStorage.removeItem('nodera_pos_locked');
            if (this._lockClockInterval) clearInterval(this._lockClockInterval);
            this.openSwitchCashierModal();
        },

        // =====================================================================
        // SHIFT METHODS: BUKA SHIFT & TUTUP SHIFT OPNAME
        // =====================================================================
        openStartShiftModal() {
            this.openShiftModalOpen = true;
            this.openShiftStartingCash = 100000;
            this.openShiftNotes = '';
            this.openShiftError = '';
            this.openShiftLoading = false;
            this.$nextTick(() => this.refreshIcons());
        },

        setQuickStartingCash(amount) {
            this.openShiftStartingCash = amount;
        },

        async submitOpenShift() {
            this.openShiftLoading = true;
            this.openShiftError = '';
            try {
                const res = await fetch('api.php?action=shifts_open', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        starting_cash: this.openShiftStartingCash,
                        notes: this.openShiftNotes
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.currentShift = data.shift;
                    this.openShiftModalOpen = false;
                    this.showToast(this.t('alert_shift_opened'), 'success');
                    await this.loadInitialData();
                } else {
                    this.openShiftError = data.error || 'Gagal membuka shift.';
                }
            } catch (e) {
                this.openShiftError = 'Gagal menghubungi server.';
            } finally {
                this.openShiftLoading = false;
                this.$nextTick(() => this.refreshIcons());
            }
        },

        async openCloseShiftModal() {
            this.shiftModal.open = true;
            this.shiftModal.step = 'input';
            this.shiftModal.loading = true;
            this.shiftModal.error = '';
            this.shiftModal.result = null;
            this.shiftModal.notes = '';

            try {
                const res = await fetch('api.php?action=shifts_current');
                const data = await res.json();
                if (data.success && data.data) {
                    this.shiftModal.data = data.data;
                    this.shiftModal.endingCashActual = parseFloat(data.data.expected_cash || 0);
                } else {
                    this.shiftModal.data = null;
                    this.shiftModal.endingCashActual = 0;
                }
            } catch (e) {
                this.shiftModal.data = null;
            } finally {
                this.shiftModal.loading = false;
                this.$nextTick(() => this.refreshIcons());
            }
        },

        setQuickEndingCash(delta) {
            this.shiftModal.endingCashActual = Math.max(0, (parseFloat(this.shiftModal.endingCashActual) || 0) + delta);
        },

        async submitCloseShift() {
            this.shiftModal.loading = true;
            this.shiftModal.error = '';
            try {
                const res = await fetch('api.php?action=shifts_close', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        ending_cash_actual: this.shiftModal.endingCashActual,
                        notes: this.shiftModal.notes
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.shiftModal.result = data.shift || data;
                    this.shiftModal.step = 'result';
                    this.currentShift = null;
                    this.showToast(this.t('alert_shift_closed'), 'success');
                } else {
                    this.shiftModal.error = data.error || 'Gagal menutup shift.';
                }
            } catch (e) {
                this.shiftModal.error = 'Gagal menghubungi server.';
            } finally {
                this.shiftModal.loading = false;
                this.$nextTick(() => this.refreshIcons());
            }
        },

        printShiftReport(shiftData) {
            const s = shiftData || this.shiftModal.result;
            if (!s) return;

            const printWin = window.open('', '_blank', 'width=420,height=600');
            if (!printWin) {
                this.showToast(this.t('alert_popup_blocked'), 'warning');
                return;
            }

            const diff = parseFloat(s.difference || 0);
            const diffText = diff === 0 ? 'PAS (0)' : (diff > 0 ? '+Rp ' + this.formatNumber(diff) : '-Rp ' + this.formatNumber(Math.abs(diff)));

            const html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Rekap Shift - ${s.cashier_name || 'Kasir'}</title>
                    <style>
                        body { font-family: monospace; font-size: 12px; margin: 0; padding: 10px; line-height: 1.4; color: #000; }
                        .text-center { text-align: center; }
                        .text-right { text-align: right; }
                        .font-bold { font-weight: bold; }
                        .border-b { border-bottom: 1px dashed #000; margin: 6px 0; }
                        .flex { display: flex; justify-content: space-between; }
                        @media print { @page { margin: 2mm; size: auto; } }
                    </style>
                </head>
                <body onload="window.print(); window.close();">
                    <div class="text-center font-bold" style="font-size: 14px;">${s.store_name || 'KASIR POS'}</div>
                    <div class="text-center" style="font-size: 10px;">${s.store_address || ''}</div>
                    <div class="border-b"></div>
                    <div class="text-center font-bold">REKAP TUTUP SHIFT KASIR</div>
                    <div class="border-b"></div>
                    <div class="flex"><span>Kasir</span><span class="font-bold">${s.cashier_name || '-'}</span></div>
                    <div class="flex"><span>Buka</span><span>${s.opened_at || '-'}</span></div>
                    <div class="flex"><span>Tutup</span><span>${s.closed_at || '-'}</span></div>
                    <div class="flex"><span>Jml Transaksi</span><span>${s.tx_count || 0} Trx</span></div>
                    <div class="border-b"></div>
                    <div class="flex"><span>Modal Awal Laci</span><span>Rp ${this.formatNumber(s.starting_cash || 0)}</span></div>
                    <div class="flex"><span>Penjualan Tunai</span><span>+ Rp ${this.formatNumber(s.cash_in || 0)}</span></div>
                    <div class="flex"><span>Pengeluaran Kas</span><span>- Rp ${this.formatNumber(s.cash_out || 0)}</span></div>
                    <div class="border-b"></div>
                    <div class="flex font-bold"><span>Total Kas Seharusnya</span><span>Rp ${this.formatNumber(s.expected_system || 0)}</span></div>
                    <div class="flex font-bold"><span>Total Kas Fisik Aktual</span><span>Rp ${this.formatNumber(s.actual_cash || 0)}</span></div>
                    <div class="flex font-bold" style="font-size: 13px;"><span>SELISIH KAS</span><span>${diffText}</span></div>
                    <div class="border-b"></div>
                    <div class="flex"><span>Non-Tunai (QRIS)</span><span>Rp ${this.formatNumber(s.qris_in || 0)}</span></div>
                    <div class="flex"><span>Kasbon / Hutang</span><span>Rp ${this.formatNumber(s.debt_in || 0)}</span></div>
                    <div class="flex font-bold"><span>Total Omzet Shift</span><span>Rp ${this.formatNumber(s.total_sales || 0)}</span></div>
                    ${s.notes ? `<div class="border-b"></div><div>Catatan: ${s.notes}</div>` : ''}
                    <div class="border-b"></div>
                    <br><br>
                    <div class="flex text-center" style="margin-top: 15px;">
                        <div style="flex: 1;">Kasir Bertugas<br><br><br>( ${s.cashier_name || '...'} )</div>
                        <div style="flex: 1;">Supervisor / Owner<br><br><br>( ............... )</div>
                    </div>
                </body>
                </html>
            `;
            printWin.document.write(html);
            printWin.document.close();
        },

        finishShiftAndSwitchCashier() {
            this.shiftModal.open = false;
            this.shiftModal.result = null;
            this.openSwitchCashierModal();
        },

        // =====================================================================
        // MIKHMON-STYLE THERMAL PRINTER ENGINE (ESC/POS, BLE, RAWBT, QUICKPRINTER)
        // =====================================================================
        padReceiptLine(left, right, width) {
            left = String(left || '');
            right = String(right || '');
            const spaceNeeded = width - left.length - right.length;
            if (spaceNeeded < 1) {
                const maxLeft = Math.max(0, width - right.length - 1);
                return left.slice(0, maxLeft) + ' ' + right;
            }
            return left + ' '.repeat(spaceNeeded) + right;
        },

        getEscPosLogoBytes: async function(imageSrc, paperSize = '58') {
            if (!imageSrc || typeof imageSrc !== 'string' || imageSrc.trim() === '') {
                return null;
            }
            if (!this._logoCache) this._logoCache = {};
            const cacheKey = imageSrc.slice(0, 80) + '_' + paperSize;
            if (this._logoCache[cacheKey]) {
                return this._logoCache[cacheKey];
            }

            return new Promise((resolve) => {
                const img = new Image();
                img.crossOrigin = 'Anonymous';
                img.onload = () => {
                    try {
                        const origW = img.naturalWidth || img.width;
                        const origH = img.naturalHeight || img.height;
                        if (!origW || !origH) {
                            resolve(null);
                            return;
                        }

                        // Batasi ukuran logo agar proporsional, tajam, dan hemat kertas thermal
                        const maxPrintW = (paperSize === '80' || paperSize === '80mm') ? 340 : 220;
                        const maxPrintH = (paperSize === '80' || paperSize === '80mm') ? 80 : 64;

                        let scale = 1;
                        if (origW > maxPrintW) {
                            scale = maxPrintW / origW;
                        }
                        if ((origH * scale) > maxPrintH) {
                            scale = maxPrintH / origH;
                        }

                        let scaledW = Math.round(origW * scale);
                        let scaledH = Math.round(origH * scale);
                        if (scaledW < 8) scaledW = 8;
                        if (scaledH < 8) scaledH = 8;

                        // Width dots must be multiple of 8
                        const wBytes = Math.ceil(scaledW / 8);
                        const widthDots = wBytes * 8;
                        const heightDots = scaledH;

                        const canvas = document.createElement('canvas');
                        canvas.width = widthDots;
                        canvas.height = heightDots;
                        const ctx = canvas.getContext('2d');

                        // Clean white background
                        ctx.fillStyle = '#FFFFFF';
                        ctx.fillRect(0, 0, widthDots, heightDots);

                        // Center image horizontally
                        const offsetX = Math.floor((widthDots - scaledW) / 2);
                        ctx.drawImage(img, offsetX, 0, scaledW, scaledH);

                        const imgData = ctx.getImageData(0, 0, widthDots, heightDots).data;
                        const rasterData = [];
                        let hasBlackPixel = false;

                        for (let y = 0; y < heightDots; y++) {
                            for (let byteIdx = 0; byteIdx < wBytes; byteIdx++) {
                                let byteVal = 0;
                                for (let bit = 0; bit < 8; bit++) {
                                    const x = (byteIdx * 8) + bit;
                                    const pixelIdx = (y * widthDots + x) * 4;
                                    const r = imgData[pixelIdx];
                                    const g = imgData[pixelIdx + 1];
                                    const b = imgData[pixelIdx + 2];
                                    const a = imgData[pixelIdx + 3];

                                    let isBlack = false;
                                    if (a > 60) {
                                        const lum = 0.299 * r + 0.587 * g + 0.114 * b;
                                        if (lum < 170) {
                                            isBlack = true;
                                            hasBlackPixel = true;
                                        }
                                    }
                                    if (isBlack) {
                                        byteVal |= (1 << (7 - bit));
                                    }
                                }
                                rasterData.push(byteVal);
                            }
                        }

                        if (!hasBlackPixel) {
                            resolve(null);
                            return;
                        }

                        // ESC/POS Raster bit image: GS v 0 0 xL xH yL yH [data...]
                        const xL = wBytes & 0xFF;
                        const xH = (wBytes >> 8) & 0xFF;
                        const yL = heightDots & 0xFF;
                        const yH = (heightDots >> 8) & 0xFF;

                        const cmd = [
                            0x1B, 0x61, 0x01,                   // ESC a 1 (Center)
                            0x1D, 0x76, 0x30, 0x00,             // GS v 0 0
                            xL, xH, yL, yH
                        ];

                        for (let d = 0; d < rasterData.length; d++) {
                            cmd.push(rasterData[d]);
                        }
                        cmd.push(0x0A); // LF
                        cmd.push(0x1B, 0x61, 0x01); // Center

                        const resultBytes = new Uint8Array(cmd);
                        this._logoCache[cacheKey] = resultBytes;
                        resolve(resultBytes);
                    } catch (e) {
                        console.warn('Gagal memproses bitmap logo:', e);
                        resolve(null);
                    }
                };
                img.onerror = (err) => {
                    console.warn('Gagal memuat logo gambar:', err);
                    resolve(null);
                };
                img.src = imageSrc;
            });
        },

        async buildTestPrintBytes() {
            const encoder = new TextEncoder();
            const storeName = this.settings.store_name || 'POS Offline Pro';
            const paper = this.settings.paper_size || '58mm';
            const size = (paper === '80mm') ? '80' : '58';

            const cmds = [
                0x1B, 0x40,       // ESC @ : Inisialisasi printer
                0x1B, 0x61, 0x01  // ESC a 1 : Center
            ];

            // Cetak Logo jika diaktifkan
            if (this.settings && Number(this.settings.print_logo_receipt) === 1 && this.settings.store_logo) {
                try {
                    const logoBytes = await this.getEscPosLogoBytes(this.settings.store_logo, size);
                    if (logoBytes && logoBytes.length > 0) {
                        for (let b = 0; b < logoBytes.length; b++) {
                            cmds.push(logoBytes[b]);
                        }
                        cmds.push(0x0A);
                    }
                } catch (e) {
                    console.warn('Gagal memproses logo:', e);
                }
            }

            const textBytes = encoder.encode(
                `\n${storeName}\n` +
                `sukses terhubung\n` +
                `Kertas: ${paper}\n` +
                `Waktu: ${new Date().toLocaleTimeString('id-ID')}\n` +
                `Printer Thermal Siap!\n\n\n\n`
            );

            for (let i = 0; i < textBytes.length; i++) {
                cmds.push(textBytes[i]);
            }
            cmds.push(0x1D, 0x56, 0x00); // GS V 0 (Auto-cut)
            return new Uint8Array(cmds);
        },

        async buildReceiptEscPosBytes(tx, paperSize = null) {
            tx = tx || this.lastTransaction || {};
            const size = paperSize || (this.settings.paper_size === '80mm' ? '80' : '58');
            const width = (size === '80') ? 48 : 32;
            const sepDash = '-'.repeat(width);
            const encoder = new TextEncoder();

            const cmds = [];
            const add = (arr) => { for (let i = 0; i < arr.length; i++) cmds.push(arr[i]); };
            const addText = (str) => {
                const b = encoder.encode(str);
                for (let i = 0; i < b.length; i++) cmds.push(b[i]);
            };

            // ESC @ : Inisialisasi printer
            add([0x1B, 0x40]);

            // ESC 3 24 : Atur jarak baris rapat/hemat kertas (24 dots per baris)
            add([0x1B, 0x33, 0x18]);

            // Header Toko (Center)
            add([0x1B, 0x61, 0x01]);

            // Cetak Logo Toko jika aktif (Kompak, tidak boros kertas)
            if (this.settings && Number(this.settings.print_logo_receipt) === 1 && this.settings.store_logo) {
                try {
                    const logoBytes = await this.getEscPosLogoBytes(this.settings.store_logo, size);
                    if (logoBytes && logoBytes.length > 0) {
                        for (let b = 0; b < logoBytes.length; b++) {
                            cmds.push(logoBytes[b]);
                        }
                    }
                } catch (e) {
                    console.warn('Gagal memproses logo toko:', e);
                }
            }

            const storeName = this.settings.store_name || 'POS Offline Pro';
            add([0x1B, 0x45, 0x01]); // Bold ON
            add([0x1D, 0x21, 0x11]); // Double height & width
            addText(storeName + '\n');
            add([0x1D, 0x21, 0x00]); // Normal size
            add([0x1B, 0x45, 0x00]); // Bold OFF

            if (this.settings.store_type) addText(this.settings.store_type + '\n');
            if (this.settings.store_address) addText(this.settings.store_address + '\n');
            if (this.settings.store_phone) addText(this.settings.store_phone + '\n');

            addText(sepDash + '\n');

            // Metadata Transaksi (Center, Persis 100% Sesuai Tampilan Aplikasi Modal)
            add([0x1B, 0x61, 0x01]); // Center
            add([0x1B, 0x45, 0x01]); // Bold ON
            addText((tx.invoice_no || 'INV-DRAFT') + '\n');
            add([0x1B, 0x45, 0x00]); // Bold OFF
            addText((tx.created_at || 'Hari ini') + '\n');

            // Baris Pelanggan: HANYA jika terdaftar sebagai Member (Pelanggan umum di-skip)
            const custName = tx.customer_name || (this.selectedCustomer ? this.selectedCustomer.name : '');
            if (custName && !['pelanggan umum', 'umum'].includes(custName.trim().toLowerCase())) {
                const tier = tx.customer_tier ? ` (${tx.customer_tier.toUpperCase()})` : '';
                add([0x1B, 0x61, 0x00]); // Left
                addText(this.padReceiptLine('Member:', custName + tier, width) + '\n');
            }

            addText(sepDash + '\n');

            // Items (Left)
            add([0x1B, 0x61, 0x00]);
            const items = tx.items || this.cart || [];
            items.forEach(it => {
                const name = it.name || it.product_name || 'Item';
                const variant = it.variant_name ? ` (${it.variant_name})` : '';
                const fullName = name + variant;
                const qty = parseFloat(it.qty || 1);
                const price = parseFloat(it.price || it.sell_price || 0);
                const sub = qty * price;
                const subStr = this.formatNumber(sub);
                const qtyStr = `${this.formatQty(qty, it.unit)} x ${this.formatNumber(price)}`;

                addText(fullName + '\n');
                addText(this.padReceiptLine('  ' + qtyStr, subStr, width) + '\n');
            });

            addText(sepDash + '\n');

            // Totals (Persis Urutan & Format Modal Aplikasi)
            const subtotal = parseFloat(tx.subtotal || this.subtotal || 0);
            const disc = parseFloat(tx.discount_total || tx.points_discount || this.totalDiscountAmount || 0);
            const tax = parseFloat(tx.tax || 0);
            const total = parseFloat(tx.total || this.total || 0);
            const tendered = parseFloat(tx.amount_paid || tx.cash_tendered || this.cashTendered || 0);
            const change = parseFloat(tx.change_amount || tx.change_due || this.changeDue || 0);
            const method = (tx.payment_method || this.paymentMethod || 'Tunai').toUpperCase();

            addText(this.padReceiptLine(this.t('receipt_subtotal') || 'Subtotal', this.formatMoney(subtotal), width) + '\n');
            if (disc > 0) {
                addText(this.padReceiptLine(this.t('receipt_discount') || 'Diskon', '-' + this.formatMoney(disc), width) + '\n');
            }
            if (tax > 0) {
                const taxRate = (this.settings && this.settings.tax_percent !== undefined && this.settings.tax_percent !== null && this.settings.tax_percent !== '') ? this.settings.tax_percent : 11;
                addText(this.padReceiptLine((this.t('receipt_tax') || 'PPN') + ` ${taxRate}%`, this.formatMoney(tax), width) + '\n');
            }

            add([0x1B, 0x45, 0x01]); // Bold ON
            addText(this.padReceiptLine(this.t('receipt_total') || 'Total', this.formatMoney(total), width) + '\n');
            add([0x1B, 0x45, 0x00]); // Bold OFF

            addText(this.padReceiptLine(this.t('receipt_method') || 'Metode', method, width) + '\n');
            if (tendered > 0) {
                addText(this.padReceiptLine(this.t('receipt_paid') || 'Bayar', this.formatMoney(tendered), width) + '\n');
                addText(this.padReceiptLine(this.t('receipt_change') || 'Kembalian', this.formatMoney(change), width) + '\n');
            }

            if (tx.points_earned && tx.points_earned > 0) {
                addText(sepDash + '\n');
                add([0x1B, 0x61, 0x01]); // Center
                addText(`${this.t('receipt_bonus_points') || 'Bonus Poin'}: +${tx.points_earned} ${this.t('receipt_loyalty_points') || 'Poin Loyalty'}\n`);
            }

            addText(sepDash + '\n');

            // Footer (Center)
            add([0x1B, 0x61, 0x01]);
            const footer = this.settings.receipt_footer || this.t('receipt_default_footer') || 'Terima kasih atas kunjungan Anda!';
            addText(footer + '\n\n'); // 2 newline hemat kertas (tidak boros tail margin)
            add([0x1D, 0x56, 0x00]); // Auto-cut

            return new Uint8Array(cmds);
        },

        buildQuickPrinterReceiptText(tx) {
            tx = tx || this.lastTransaction || {};
            const storeName = this.settings.store_name || 'POS Offline Pro';
            let text = "<center><big><bold>" + storeName + "\n";
            if (this.settings.store_type) text += "<center>" + this.settings.store_type + "\n";
            if (this.settings.store_address) text += "<center>" + this.settings.store_address + "\n";
            if (this.settings.store_phone) text += "<center>" + this.settings.store_phone + "\n";
            text += "<line0>\n";
            text += (this.t('number_prefix') || 'No') + " :;; " + (tx.invoice_no || 'INV') + "\n";
            text += (this.t('receipt_date') || 'Tgl') + " :;; " + (tx.created_at || 'Hari ini') + "\n";
            text += (this.t('receipt_cashier') || 'Kasir') + " :;; " + (tx.cashier_name || this.currentUser?.name || 'Kasir') + "\n";
            const custName = tx.customer_name || (this.selectedCustomer ? this.selectedCustomer.name : '');
            if (custName && !['pelanggan umum', 'umum'].includes(custName.trim().toLowerCase())) {
                const tier = tx.customer_tier ? ` (${tx.customer_tier.toUpperCase()})` : '';
                text += "Member :;; " + custName + tier + "\n";
            }
            text += "<line0>\n";
            const items = tx.items || this.cart || [];
            items.forEach(it => {
                const name = it.product_name || it.name || 'Item';
                const qty = it.qty || 1;
                const sub = (it.sell_price || it.price || 0) * qty;
                text += name + "\n";
                text += "  " + qty + " x " + this.formatMoney(it.sell_price || it.price || 0) + " :;; " + this.formatMoney(sub) + "\n";
            });
            text += "<line0>\n";
            text += "<bold>" + (this.t('receipt_total') || 'TOTAL').toUpperCase() + " :;; " + this.formatMoney(tx.total || this.total || 0) + "\n";
            const tendered = tx.cash_tendered || this.cashTendered || 0;
            if (tendered > 0) {
                text += (this.t('receipt_paid') || 'Bayar') + " :;; " + this.formatMoney(tendered) + "\n";
                text += (this.t('receipt_change') || 'Kembali') + " :;; " + this.formatMoney(tx.change_due || this.changeDue || 0) + "\n";
            }
            text += "<line0>\n";
            text += "<center>" + (this.settings.receipt_footer || this.t('receipt_default_footer') || 'Terima kasih atas kunjungan Anda!') + "\n<cut>";
            return text;
        },

        async tryRawBtWebSocket(bytes) {
            return new Promise((resolve) => {
                let resolved = false;
                let ws = null;
                const timer = setTimeout(() => {
                    if (!resolved) {
                        resolved = true;
                        try { if (ws) ws.close(); } catch (e) {}
                        resolve(false);
                    }
                }, 350);

                try {
                    ws = new WebSocket('ws://127.0.0.1:40213/');
                    ws.binaryType = 'arraybuffer';
                    ws.onopen = () => {
                        if (!resolved) {
                            resolved = true;
                            clearTimeout(timer);
                            const buffer = bytes.buffer.slice(bytes.byteOffset, bytes.byteOffset + bytes.byteLength);
                            ws.send(buffer);
                            setTimeout(() => {
                                try { ws.close(); } catch (e) {}
                            }, 400);
                            resolve(true);
                        }
                    };
                    ws.onerror = () => {
                        if (!resolved) {
                            resolved = true;
                            clearTimeout(timer);
                            resolve(false);
                        }
                    };
                } catch (e) {
                    if (!resolved) {
                        resolved = true;
                        clearTimeout(timer);
                        resolve(false);
                    }
                }
            });
        },

        async printViaRawBT(bytes) {
            try {
                if (!bytes) {
                    this.showToast(this.t('alert_receipt_empty'), 'error');
                    return false;
                }
                const uint8 = (bytes instanceof Uint8Array) ? bytes : new Uint8Array(bytes);

                // OPSI 1: RawBT Background Service (WebSocket Port 40213)
                // Jika aktif di background Android/PC, cetak instan tanpa berpindah aplikasi (Layar tetap Fullscreen!)
                const wsSuccess = await this.tryRawBtWebSocket(uint8);
                if (wsSuccess) {
                    this.showToast(this.t('alert_receipt_sent_rawbt'), 'success');
                    return true;
                }

                // OPSI 2: Android Intent URL Scheme RawBT
                let bin = '';
                for (let i = 0; i < uint8.length; i++) {
                    bin += String.fromCharCode(uint8[i]);
                }
                const b64 = window.btoa(bin);
                const intentUrl = 'intent:base64,' + b64 + '#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;S.browser_fallback_url=https%3A%2F%2Fplay.google.com%2Fstore%2Fapps%2Fdetails%3Fid%3Dru.a402d.rawbtprinter;end;';

                // Siapkan pemulihan otomatis ke fullscreen saat kembali ke browser
                this.waitingToRestoreFullscreen = true;

                const link = document.createElement('a');
                link.href = intentUrl;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                setTimeout(() => {
                    if (link.parentNode) link.parentNode.removeChild(link);
                }, 1000);
                this.showToast(this.t('alert_receipt_sending_rawbt'), 'info');
                return true;
            } catch (e) {
                this.showToast(this.t('alert_receipt_rawbt_failed') + ' ' + (e.message || e), 'error');
                return false;
            }
        },

        printViaQuickPrinter(text) {
            try {
                const textEncoded = encodeURI(text);
                const intentUrl = 'intent://' + textEncoded + '#Intent;scheme=quickprinter;package=pe.diegoveloper.printerserverapp;end;';
                window.location.href = intentUrl;
                this.showToast(this.t('alert_receipt_sending_qp'), 'info');
                return true;
            } catch (e) {
                this.showToast(this.t('alert_receipt_qp_failed') + ' ' + (e.message || e), 'error');
                return false;
            }
        },

        async sendRawBytes(bytes) {
            if (!this.bluetoothPrinter.characteristic) {
                this.showToast(this.t('alert_bt_no_char'), 'warning');
                return false;
            }
            try {
                const chunkSize = 100;
                const uint8 = (bytes instanceof Uint8Array) ? bytes : new Uint8Array(bytes);
                for (let offset = 0; offset < uint8.length; offset += chunkSize) {
                    const chunk = uint8.slice(offset, offset + chunkSize);
                    if (this.bluetoothPrinter.characteristic.writeValueWithoutResponse) {
                        await this.bluetoothPrinter.characteristic.writeValueWithoutResponse(chunk);
                    } else {
                        await this.bluetoothPrinter.characteristic.writeValue(chunk);
                    }
                    await new Promise(r => setTimeout(r, 25));
                }
                this.showToast(this.t('alert_receipt_sent_bt'), 'success');
                return true;
            } catch (err) {
                console.warn('Gagal mengirim byte ESC/POS:', err);
                this.showToast(this.t('alert_receipt_bt_failed') + ' ' + (err.message || err), 'error');
                return false;
            }
        },

        async testPrintRawBT() {
            const bytes = await this.buildTestPrintBytes();
            return this.printViaRawBT(bytes);
        },

        testPrintQuickPrinter() {
            const text = "<center><big><bold>" + (this.settings.store_name || 'POS Offline Pro') + "\n" +
                         "<center>sukses terhubung\n<line0>\n<center>Printer Siap Digunakan!\n<cut>";
            return this.printViaQuickPrinter(text);
        },

        async selectRawBtMethod() {
            this.settings.printer_connection_type = 'rawbt';
            this.savePrinterSettingsSilently();
            this.showToast(this.t('alert_rawbt_set'), 'success');
            await this.testPrintRawBT();
        },

        copyChromeFlagUrl() {
            const flagUrl = 'chrome://flags/#unsafely-treat-insecure-origin-as-secure';
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(flagUrl).then(() => {
                    this.showToast(this.t('alert_flag_copied'), 'success');
                }).catch(() => {
                    this.showToast('Flag: ' + flagUrl, 'info');
                });
            } else {
                this.showToast('Flag: ' + flagUrl, 'info');
            }
        },

        openPrinterScannerModal() {
            this.printerScannerError = '';
            this.printerScannerModalOpen = true;
            this.$nextTick(() => this.refreshIcons());
        },

        async connectBluetoothPrinter(mode = 'printers') {
            const KNOWN_SERVICES = [
                '000018f0-0000-1000-8000-00805f9b34fb', // Standard POS Print
                '49535343-fe7d-4ae5-8fa9-9fafd205e455', // ISSC Transparent Serial
                'e7810a71-73ae-499d-8c15-faa9aef0c3f2', // Vendor Serial
                '0000ae00-0000-1000-8000-00805f9b34fb', // Vendor 2
                '0000ae30-0000-1000-8000-00805f9b34fb', // Vendor 3
                '0000ff00-0000-1000-8000-00805f9b34fb', // Vendor 4
                '0000fee7-0000-1000-8000-00805f9b34fb', // Vendor 5
                '0000ffff-0000-1000-8000-00805f9b34fb'  // Generic
            ];

            const PRINTER_PREFIXES = [
                'POS', 'pos', 'Print', 'print', 'RPP', 'rpp', 'MPT', 'mpt', 'MTP', 'mtp',
                'PT-', 'pt-', 'PT', 'pt', 'VSC', 'vsc', 'Panda', 'panda', 'Iware', 'iware',
                'Eppos', 'eppos', 'Zjiang', 'zjiang', 'GOOJPRT', 'goojprt', 'Thermal', 'thermal',
                'XP-', 'xp-', 'XP', 'JP-', 'JP', 'EP-', 'EP', 'Bluetooth', 'bluetooth',
                'Mobile', 'ESC', 'SPP', '58', '80'
            ];

            this.printerScannerError = '';

            if (!navigator.bluetooth || !navigator.bluetooth.requestDevice) {
                const isAndroidApp = !!(window.AndroidBridge && window.AndroidBridge.isAndroidApp && window.AndroidBridge.isAndroidApp());
                const isAndroid = isAndroidApp || /Android/i.test(navigator.userAgent);
                this.printerScannerModalOpen = true;
                if (isAndroidApp) {
                    this.printerScannerError = 'Pada aplikasi Android APK, pencetakan Bluetooth menggunakan driver perantara RawBT agar kompatibel 100% tanpa batasan browser. Pilih opsi "Gunakan RawBT" di bawah!';
                    this.settings.printer_connection_type = 'rawbt';
                    this.savePrinterSettingsSilently();
                } else if (isAndroid) {
                    this.printerScannerError = (this.t('bt_android_http_restriction') || 'Google Chrome di HP membatasi Web Bluetooth pada koneksi HTTP Wi-Fi lokal. Silakan gunakan opsi "RawBT Android" di bawah untuk langsung mencetak tanpa batasan!');
                } else {
                    this.printerScannerError = (this.t('bt_browser_unsupported') || 'Web Bluetooth tidak didukung browser ini. Silakan gunakan Google Chrome / Edge atau opsi RawBT / Driver OS.');
                }
                this.showToast(this.printerScannerError, 'warning');
                return;
            }

            try {
                this.showToast(this.t('alert_bt_scanning'), 'info');
                let reqOptions = {};
                if (mode === 'all') {
                    reqOptions = {
                        acceptAllDevices: true,
                        optionalServices: KNOWN_SERVICES
                    };
                } else {
                    reqOptions = {
                        filters: PRINTER_PREFIXES.map(pfx => ({ namePrefix: pfx })),
                        optionalServices: KNOWN_SERVICES
                    };
                }

                const device = await navigator.bluetooth.requestDevice(reqOptions);
                if (!device) return;

                this.bluetoothPrinter.device = device;
                this.settings.printer_bt_name = device.name || 'Printer Bluetooth (' + (device.id ? device.id.slice(0, 5) : 'POS') + ')';
                this.settings.printer_connection_type = 'bluetooth';
                this.bluetoothPrinter.status = 'connecting';

                if (device.gatt) {
                    const server = await device.gatt.connect();
                    this.bluetoothPrinter.server = server;
                    let charFound = null;

                    for (let i = 0; i < KNOWN_SERVICES.length; i++) {
                        try {
                            const service = await server.getPrimaryService(KNOWN_SERVICES[i]);
                            if (service) {
                                const chars = await service.getCharacteristics();
                                for (let j = 0; j < chars.length; j++) {
                                    const c = chars[j];
                                    if (c.properties.write || c.properties.writeWithoutResponse) {
                                        charFound = c;
                                        break;
                                    }
                                }
                                if (charFound) break;
                            }
                        } catch (e) {}
                    }

                    if (!charFound) {
                        try {
                            const allServices = await server.getPrimaryServices();
                            for (let k = 0; k < allServices.length; k++) {
                                try {
                                    const sChars = await allServices[k].getCharacteristics();
                                    for (let m = 0; m < sChars.length; m++) {
                                        const sc = sChars[m];
                                        if (sc.properties.write || sc.properties.writeWithoutResponse) {
                                            charFound = sc;
                                            break;
                                        }
                                    }
                                    if (charFound) break;
                                } catch (e) {}
                            }
                        } catch (e) {}
                    }

                    if (charFound) {
                        this.bluetoothPrinter.characteristic = charFound;
                        this.bluetoothPrinter.status = 'connected';
                    } else {
                        this.bluetoothPrinter.status = 'paired';
                    }
                } else {
                    this.bluetoothPrinter.status = 'paired';
                }

                this.showToast(this.t('alert_bt_selected') + this.settings.printer_bt_name, 'success');
                this.savePrinterSettingsSilently();
                this.printerScannerModalOpen = false;
            } catch (err) {
                if (err.name === 'NotFoundError' || err.name === 'AbortError' || (err.message && err.message.toLowerCase().includes('cancelled'))) {
                    return; // user cancelled chooser
                }
                console.warn('Bluetooth connection error:', err);
                this.printerScannerError = (this.t('error_scan_bt_failed') || 'Gagal memindai printer Bluetooth') + ': ' + (err.message || 'Perangkat tidak merespons');
                this.showToast(this.printerScannerError, 'warning');
            }
        },

        async scanUsbPrinter() {
            try {
                if (!navigator.serial) {
                    this.showToast(this.t('alert_web_serial_req'), 'warning');
                    return;
                }
                const port = await navigator.serial.requestPort();
                const info = port.getInfo();
                const portName = 'USB Thermal (VID:' + (info.usbVendorId ? info.usbVendorId.toString(16) : 'auto') + ')';
                this.settings.printer_bt_name = portName;
                this.settings.printer_connection_type = 'network';
                this.showToast(this.t('alert_usb_detected') + portName, 'success');
                this.savePrinterSettingsSilently();
            } catch (err) {
                if (err.name !== 'NotFoundError') {
                    this.showToast((this.t('error_scan_usb_failed') || 'Gagal scan USB') + ': ' + (err.message || err), 'warning');
                }
            }
        },

        disconnectBluetoothPrinter() {
            if (this.bluetoothPrinter.device && this.bluetoothPrinter.device.gatt && this.bluetoothPrinter.device.gatt.connected) {
                this.bluetoothPrinter.device.gatt.disconnect();
            }
            this.bluetoothPrinter.device = null;
            this.bluetoothPrinter.server = null;
            this.bluetoothPrinter.characteristic = null;
            this.bluetoothPrinter.status = 'disconnected';
            this.showToast(this.t('alert_bt_disconnected'), 'info');
        },

        async savePrinterSettingsSilently() {
            try {
                await fetch('api.php?action=settings_update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.settings)
                });
            } catch (e) {}
        },

        async printBluetoothEscPos(tx) {
            tx = tx || this.lastTransaction;
            const method = this.settings.printer_connection_type || 'browser';
            if (method === 'rawbt' || (/Android/i.test(navigator.userAgent) && !this.bluetoothPrinter.characteristic)) {
                const bytes = await this.buildReceiptEscPosBytes(tx);
                return this.printViaRawBT(bytes);
            }
            if (this.bluetoothPrinter.status === 'connected' && this.bluetoothPrinter.characteristic) {
                const bytes = await this.buildReceiptEscPosBytes(tx);
                return this.sendRawBytes(bytes);
            }
            this.reprintReceipt(tx);
            setTimeout(() => { window.print(); }, 200);
        },

        // WhatsApp Receipt Sharing (NodeRA WA Gateway + Fallback)
        async shareReceiptWhatsApp(tx) {
            const targetTx = tx || this.lastTransaction;
            if (!targetTx) return;
            const storeName = this.settings.store_name || 'NODERA POS';
            const phone = targetTx.customer_phone || this.selectedCustomer?.phone || '';
            const cashierName = targetTx.cashier_name || this.currentUser?.name || 'Kasir';
            const items = targetTx.items || [];
            let itemsText = '';
            items.forEach(it => {
                const name = it.name || it.product_name || 'Item';
                const qty = it.qty || 1;
                const sub = this.formatMoney((it.price || it.sell_price || 0) * qty);
                itemsText += `- ${name} x${qty} : ${sub}\n`;
            });
            const text = `*${this.t('receipt_whatsapp_header') || 'STRUK PEMBELIAN'} - ${storeName}*\n` +
                `${this.t('receipt_invoice') || 'No. Invoice'}: ${targetTx.invoice_no}\n` +
                `${this.t('receipt_date') || 'Tanggal'}: ${targetTx.created_at || 'Hari Ini'}\n` +
                `${this.t('receipt_cashier') || 'Kasir'}: ${cashierName}\n` +
                `--------------------------------\n` +
                itemsText +
                `--------------------------------\n` +
                `*${(this.t('receipt_total') || 'TOTAL').toUpperCase()}: ${this.formatMoney(targetTx.total)}*\n` +
                `${this.t('receipt_method') || 'Metode'}: ${targetTx.payment_method?.toUpperCase()}\n\n` +
                `${this.settings.receipt_footer || this.t('receipt_default_footer') || 'Terima kasih telah berbelanja!'}\n` +
                `Powered by dgtlnetsolution.com`;

            const cleanPhone = phone ? phone.replace(/^0/, '62').replace(/[^0-9]/g, '') : '';

            // Try NodeRA WA Gateway API first if phone exists
            if (cleanPhone) {
                try {
                    this.showToast(this.t('alert_wa_sending'), 'info');
                    const res = await fetch('api.php?action=whatsapp_send_receipt', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            phone: cleanPhone,
                            receipt_text: text,
                            invoice_no: targetTx.invoice_no
                        })
                    });
                    const d = await res.json();
                    if (d.success) {
                        this.showToast(this.t('alert_wa_sent') + ' (+' + cleanPhone + ')', 'success');
                        if (d.manual && d.link) {
                            window.open(d.link, '_blank');
                        }
                        return;
                    }
                } catch (e) {
                    console.warn('NodeRA WA Gateway error, falling back to manual link:', e);
                }
            }

            // Fallback to manual wa.me link
            const waUrl = cleanPhone 
                ? `https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`
                : `https://wa.me/?text=${encodeURIComponent(text)}`;
            window.open(waUrl, '_blank');
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
                if (this.pinInput.length === 6) {
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
            if (this.pinInput.length < 6) return;
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
            try {
                await fetch('api.php?action=auth_logout');
            } catch (e) {}
            localStorage.removeItem('nodera_pos_locked');
            this.isScreenLocked = false;
            this.currentUser = null;
            this.cart = [];
            window.location.reload();
        },

        // =====================================================================
        // PAYMENT SCREEN (3-Column Landscape)
        // =====================================================================
        openPayment() {
            if (this.isCartEmpty) {
                this.showToast(this.t('alert_cart_empty'), 'error');
                return;
            }
            this.portraitCartDrawerOpen = false;
            this.paymentMethod = 'cash';
            this.digitalSubMethod = 'qris';
            this.qrisModalOpen = false;
            this.showModalMemberSearch = false;
            this.kasbonMemberSearchQuery = '';
            this.showKasbonSearchResults = false;
            this.cashTendered = this.total;
            this.cashTenderedStr = this.total > 0 ? Number(this.total).toLocaleString('id-ID') : '0';
            this.isCashTenderedUntouched = true;
            this.splitCash = Math.round(this.total / 2);
            this.splitNonCash = Math.max(0, this.total - this.splitCash);
            this.paymentModal = true;
            this.refreshIcons();
            setTimeout(() => {
                const el = this.$refs.cashInput || document.querySelector('input[x-ref="cashInput"]');
                if (el) {
                    el.value = this.cashTendered > 0 ? Number(this.cashTendered).toLocaleString('id-ID') : '';
                    // On mobile/touch devices, do not auto-focus to prevent virtual keyboard from covering screen
                    const isTouch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (window.innerWidth <= 1024);
                    if (!isTouch) {
                        el.focus();
                        el.select();
                    }
                }
            }, 80);
        },

        openPaymentModal() {
            this.portraitCartDrawerOpen = false;
            this.openPayment();
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
            this.confirmDialog.title = title || this.t('confirm_action_title') || 'Konfirmasi Tindakan';
            this.confirmDialog.message = message || this.t('confirm_action_message') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            this.confirmDialog.action = action;
            this.confirmDialog.confirmText = options.confirmText || this.t('confirm_yes_continue') || 'Ya, Lanjutkan';
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

        getBankAccounts() {
            if (this.settings && this.settings.bank_accounts) {
                if (Array.isArray(this.settings.bank_accounts)) {
                    return this.settings.bank_accounts;
                }
                try {
                    const parsed = JSON.parse(this.settings.bank_accounts);
                    if (Array.isArray(parsed) && parsed.length > 0) return parsed;
                } catch(e) {}
            }
            return [{
                id: 'bank_1',
                bank_name: this.settings?.bank_name || 'BCA',
                account_no: this.settings?.bank_account_no || '1234-5678-90',
                account_holder: this.settings?.bank_account_holder || this.settings?.store_name || 'Toko POS Pro',
                is_primary: 1
            }];
        },

        getSelectedBankAccount() {
            const list = this.getBankAccounts();
            if (!list.length) return null;
            if (this.selectedBankAccountId) {
                const found = list.find(b => (b.id === this.selectedBankAccountId || b.account_no === this.selectedBankAccountId));
                if (found) return found;
            }
            const prim = list.find(b => b.is_primary);
            return prim || list[0];
        },

        selectBankAccount(acc) {
            this.selectedBankAccountId = acc.id || acc.account_no;
            this.$nextTick(() => this.refreshIcons());
        },

        copyBankNo(acc) {
            const target = acc || this.getSelectedBankAccount();
            const no = target ? (target.account_no || target.no) : (this.settings.bank_account_no || '1234-5678-90');
            const bank = target ? (target.bank_name || 'Bank') : 'Bank';
            if (navigator.clipboard) {
                navigator.clipboard.writeText(no).then(() => {
                    this.showToast((this.t('alert_bank_copied') || 'Nomor rekening disalin!') + ' (' + bank + ': ' + no + ')', 'success');
                }).catch(() => {
                    this.showToast(bank + ': ' + no, 'info');
                });
            } else {
                this.showToast(bank + ': ' + no, 'info');
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
                this.isCashTenderedUntouched = true;
            } else if (amount === 'clear' || amount === 0) {
                this.cashTendered = 0;
                this.isCashTenderedUntouched = false;
            } else {
                const val = parseFloat(amount) || 0;
                // Jika masih dalam status awal saat modal dibuka, klik pertama menggantikan nilai default total
                // Klik berikutnya akan terus bertambah (akumulatif: 50k + 50k = 100k, + 20k = 120k)
                if (this.isCashTenderedUntouched) {
                    this.cashTendered = val;
                    this.isCashTenderedUntouched = false;
                } else {
                    this.cashTendered = (parseFloat(this.cashTendered) || 0) + val;
                }
            }
            const formatted = this.cashTendered > 0 ? Number(this.cashTendered).toLocaleString('id-ID') : '';
            this.cashTenderedStr = formatted;
            const el = this.$refs.cashInput || document.querySelector('input[x-ref="cashInput"]');
            if (el) {
                el.value = formatted;
            }
        },

        onCashInput(e) {
            this.isCashTenderedUntouched = false;
            const input = e.target;
            const oldVal = input.value;
            const cursor = input.selectionStart || 0;
            const digitsBeforeCursor = oldVal.slice(0, cursor).replace(/\D/g, '').length;

            const raw = oldVal.replace(/\D/g, '');
            const num = parseInt(raw, 10) || 0;
            this.cashTendered = num;
            this.cashTenderedStr = num > 0 ? Number(num).toLocaleString('id-ID') : '';

            const formatted = num > 0 ? Number(num).toLocaleString('id-ID') : '';
            input.value = formatted;

            let newCursor = 0;
            let digitCount = 0;
            for (let i = 0; i < formatted.length; i++) {
                if (/\d/.test(formatted[i])) {
                    digitCount++;
                }
                if (digitCount >= digitsBeforeCursor) {
                    newCursor = i + 1;
                    break;
                }
            }
            if (digitCount < digitsBeforeCursor) {
                newCursor = formatted.length;
            }
            input.setSelectionRange(newCursor, newCursor);
        },

        async processCheckout() {
            if (this.isCartEmpty) return;

            // Validasi Kasbon: Wajib Member Terdaftar
            if (this.paymentMethod === 'debt') {
                if (this.settings && this.settings.debt_enabled == 0) {
                    this.showToast('Metode Kasbon sedang dinonaktifkan di pengaturan modular.', 'error');
                    return;
                }
                if (!this.selectedCustomerId || !this.selectedCustomer) {
                    this.showToast(this.t('alert_debt_member_only'), 'error');
                    return;
                }
                const curDebt = parseFloat(this.selectedCustomer.total_debt) || 0;
                const limit = (this.selectedCustomer.debt_limit !== undefined && this.selectedCustomer.debt_limit !== null && this.selectedCustomer.debt_limit !== '')
                    ? parseFloat(this.selectedCustomer.debt_limit)
                    : 0;
                if (limit <= 0) {
                    this.showToast('Pelanggan ini tidak diizinkan kasbon (Limit Kasbon Rp 0). Silakan ubah pengaturan pelanggan di Back-Office.', 'error');
                    return;
                }
                if (curDebt + this.total > limit) {
                    this.showConfirm(
                        this.t('alert_debt_warning_title'),
                        this.t('alert_debt_warning_msg') + ' (' + this.formatMoney(curDebt + this.total) + ' > ' + this.formatMoney(limit) + ')',
                        () => this.executeCheckoutPayload(),
                        { confirmText: this.t('alert_confirm_continue'), confirmClass: 'bg-amber-600 hover:bg-amber-700' }
                    );
                    return;
                }
            }

            if (this.paymentMethod === 'cash' && this.cashTendered < this.total) {
                this.showToast(this.t('alert_cash_insufficient'), 'error');
                return;
            }

            if (this.paymentMethod === 'split' && (this.splitCash + this.splitNonCash) < this.total) {
                this.showToast(this.t('alert_split_insufficient'), 'error');
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
            } else if (this.paymentMethod === 'edc') {
                finalPaymentMethod = 'edc';
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
                discount: this.totalDiscountAmount || 0,
                discount_reason: this.cartDiscount ? (this.cartDiscount.reason || '') : '',
                points_used: this.pointsUsed || 0,
                points_discount: this.pointsDiscount || 0,
                split_cash: this.splitCash || 0,
                split_non_cash: this.splitNonCash || 0,
                handled_by: this.handledBy || (this.currentUser ? this.currentUser.name : 'Kasir Utama'),
                tax: this.taxAmount || 0,
                open_bill_id: this.activeOpenBillId
            };

            if (this.paymentMethod === 'bank' || finalPaymentMethod === 'transfer') {
                const selBank = this.getSelectedBankAccount();
                if (selBank) {
                    payload.bank_name = selBank.bank_name;
                    payload.bank_account_no = selBank.account_no;
                    payload.bank_account_holder = selBank.account_holder;
                }
            }

            if (this.paymentMethod === 'debt' || finalPaymentMethod === 'debt') {
                payload.has_due_date = this.debtHasDueDate ? 1 : 0;
                payload.due_date = this.debtHasDueDate && this.debtDueDate ? this.debtDueDate : null;
            }

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
                    this.playSuccessSound();
                    this.showToast(this.t('alert_payment_success'), 'success');

                    // Auto-print jika diaktifkan di pengaturan
                    if (this.settings.auto_print == 1) {
                        setTimeout(() => this.printReceipt(), 300);
                    }

                    // Auto-kirim struk WhatsApp jika diaktifkan di pengaturan & pelanggan memiliki nomor WA
                    if (this.settings && Number(this.settings.wa_send_receipt) === 1) {
                        const targetPhone = this.lastTransaction?.customer_phone || this.selectedCustomer?.phone;
                        if (targetPhone) {
                            setTimeout(() => this.shareReceiptWhatsApp(this.lastTransaction), 600);
                        }
                    }
                } else {
                    this.showToast(data.error || this.t('alert_payment_failed'), 'error');
                }
            } catch (err) {
                this.showToast(this.t('alert_server_error'), 'error');
            }
            this.refreshIcons();
        },

        async printReceipt(forceMethod = null) {
            const isAndroid = /Android/i.test(navigator.userAgent);
            const method = forceMethod || this.settings.printer_connection_type || (isAndroid ? 'rawbt' : 'browser');

            if (method === 'rawbt') {
                const bytes = await this.buildReceiptEscPosBytes(this.lastTransaction);
                return await this.printViaRawBT(bytes);
            }
            if (method === 'quickprinter') {
                const text = this.buildQuickPrinterReceiptText(this.lastTransaction);
                return this.printViaQuickPrinter(text);
            }
            if (method === 'bluetooth') {
                if (this.bluetoothPrinter.status === 'connected' && this.bluetoothPrinter.characteristic) {
                    const bytes = await this.buildReceiptEscPosBytes(this.lastTransaction);
                    return this.sendRawBytes(bytes);
                }
                if (isAndroid) {
                    this.showToast(this.t('alert_printer_ble_redirect') || 'Mencetak via RawBT Android...', 'info');
                    const bytes = await this.buildReceiptEscPosBytes(this.lastTransaction);
                    return await this.printViaRawBT(bytes);
                }
                this.openPrinterScannerModal();
                return false;
            }

            window.print();
        },

        // =====================================================================
        // PEMINDAI BARCODE KAMERA & HARDWARE KEYBOARD WEDGE (USB / BT)
        // =====================================================================
        openCameraScanner(target = 'pos') {
            this.cameraScannerModal.target = target;
            this.cameraScannerModal.open = true;
            this.cameraScannerModal.lastScannedCode = '';
            this.$nextTick(() => {
                this.startCameraScanner();
                this.refreshIcons();
            });
        },

        closeCameraScanner() {
            this.stopCameraScanner();
            this.cameraScannerModal.open = false;
            this.refreshIcons();
        },

        async startCameraScanner() {
            if (typeof Html5Qrcode === 'undefined') {
                this.showToast('Pustaka pemindai barcode belum selesai dimuat', 'error');
                return;
            }

            if (this.cameraScannerModal.scanner) {
                try {
                    await this.cameraScannerModal.scanner.stop();
                    await this.cameraScannerModal.scanner.clear();
                } catch (e) {}
                this.cameraScannerModal.scanner = null;
            }

            try {
                const container = document.getElementById('camera-scanner-reader');
                if (!container) return;

                const scanner = new Html5Qrcode("camera-scanner-reader");
                this.cameraScannerModal.scanner = scanner;

                const cameras = await Html5Qrcode.getCameras().catch(() => []);
                this.cameraScannerModal.cameras = cameras || [];

                const qrConfig = {
                    fps: 15,
                    qrbox: (viewfinderWidth, viewfinderHeight) => {
                        const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                        return {
                            width: Math.floor(minEdge * 0.8),
                            height: Math.floor(minEdge * 0.6)
                        };
                    },
                    aspectRatio: 1.0
                };

                const onScanSuccess = (decodedText) => {
                    const code = (decodedText || '').trim();
                    if (!code) return;
                    if (this.cameraScannerModal.lastScannedCode === code) return;
                    this.cameraScannerModal.lastScannedCode = code;

                    this.handleScannedBarcode(code, this.cameraScannerModal.target);

                    setTimeout(() => {
                        if (this.cameraScannerModal.lastScannedCode === code) {
                            this.cameraScannerModal.lastScannedCode = '';
                        }
                    }, 1800);

                    if (!this.cameraScannerModal.continuous) {
                        this.closeCameraScanner();
                    }
                };

                let cameraChoice = { facingMode: "environment" };
                if (this.cameraScannerModal.cameras.length > 0) {
                    const backCam = this.cameraScannerModal.cameras.find(c => /back|rear|environment/i.test(c.label));
                    if (backCam) {
                        cameraChoice = backCam.id;
                    }
                }

                await scanner.start(
                    cameraChoice,
                    qrConfig,
                    onScanSuccess,
                    () => {}
                );
            } catch (err) {
                console.warn("Camera scanner error:", err);
                this.showToast("Kamera tidak dapat diakses: " + (err.message || "Izin ditolak"), "error");
            }
        },

        async stopCameraScanner() {
            if (this.cameraScannerModal.scanner) {
                try {
                    await this.cameraScannerModal.scanner.stop();
                    await this.cameraScannerModal.scanner.clear();
                } catch (e) {}
                this.cameraScannerModal.scanner = null;
            }
        },

        async switchCamera() {
            if (!this.cameraScannerModal.cameras || this.cameraScannerModal.cameras.length < 2) return;
            this.cameraScannerModal.currentCameraIndex = (this.cameraScannerModal.currentCameraIndex + 1) % this.cameraScannerModal.cameras.length;
            const nextCam = this.cameraScannerModal.cameras[this.cameraScannerModal.currentCameraIndex];
            if (this.cameraScannerModal.scanner) {
                try {
                    await this.cameraScannerModal.scanner.stop();
                    await this.cameraScannerModal.scanner.start(
                        nextCam.id,
                        { fps: 15, qrbox: { width: 250, height: 160 }, aspectRatio: 1.0 },
                        (decodedText) => {
                            const code = (decodedText || '').trim();
                            if (!code) return;
                            if (this.cameraScannerModal.lastScannedCode === code) return;
                            this.cameraScannerModal.lastScannedCode = code;
                            this.handleScannedBarcode(code, this.cameraScannerModal.target);
                            setTimeout(() => {
                                if (this.cameraScannerModal.lastScannedCode === code) {
                                    this.cameraScannerModal.lastScannedCode = '';
                                }
                            }, 1800);
                            if (!this.cameraScannerModal.continuous) this.closeCameraScanner();
                        },
                        () => {}
                    );
                } catch (e) {
                    this.showToast("Gagal beralih kamera", "error");
                }
            }
        },

        handleScannedBarcode(rawCode, target = 'pos') {
            const code = (rawCode || '').trim();
            if (!code) return;

            if (target === 'catalog') {
                this.productSearchQuery = code;
                this.playBeepSound();
                this.showToast(`Filter barcode: ${code}`, 'info');
                return;
            }

            // Default target: POS kasir (Tambah produk otomatis ke keranjang)
            const foundProduct = (this.products || []).find(p => {
                const c = (p.code || '').trim().toLowerCase();
                const s = (p.sku || '').trim().toLowerCase();
                const b = (p.barcode || '').trim().toLowerCase();
                const query = code.toLowerCase();
                return c === query || s === query || b === query;
            });

            if (foundProduct) {
                this.addToCart(foundProduct, 1);
                this.playBeepSound();
                this.showToast(`${foundProduct.name} (+1)`, 'success');
            } else {
                this.playErrorSound();
                this.showToast(`Barcode "${code}" tidak ditemukan!`, 'error');
                if (this.currentView === 'pos') {
                    this.searchQuery = code;
                }
            }
        },

        initBarcodeScannerListener() {
            let buffer = '';
            let lastKeyTime = 0;
            const MAX_GAP = 85; // 85ms optimal untuk Bluetooth Scanner HID di HP & USB Scanner PC

            window.addEventListener('keydown', (e) => {
                if (this.cameraScannerModal?.open) return;

                const now = Date.now();
                const gap = now - lastKeyTime;
                lastKeyTime = now;

                if (gap > MAX_GAP && e.key !== 'Enter') {
                    buffer = '';
                }

                if (e.key === 'Enter') {
                    const scanned = buffer.trim();
                    buffer = '';

                    if (scanned.length >= 3) {
                        const activeEl = document.activeElement;
                        const isPosSearch = activeEl && activeEl.id === 'pos-search-input';

                        if (activeEl && ['INPUT', 'TEXTAREA'].includes(activeEl.tagName) && !isPosSearch) {
                            return;
                        }

                        e.preventDefault();
                        e.stopPropagation();

                        if (isPosSearch) {
                            this.searchQuery = '';
                            activeEl.value = '';
                        }

                        this.handleScannedBarcode(scanned, 'pos');
                    }
                } else if (e.key.length === 1) {
                    buffer += e.key;
                    if (buffer.length > 60) {
                        buffer = buffer.slice(-60);
                    }
                }
            }, true);
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
                this.showToast(this.t('alert_expense_required'), 'error');
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
                    this.showToast(this.t('alert_expense_saved'), 'success');
                    this.expenseModal.open = false;
                    await this.loadDashboardMetrics();
                } else {
                    this.showToast(data.error || this.t('alert_expense_failed'), 'error');
                }
            } catch (err) {
                this.showToast(this.t('alert_server_error'), 'error');
            }
            this.refreshIcons();
        },

        async deleteExpense(exp) {
            if (!exp || !exp.id) return;
            this.confirmDialog = {
                open: true,
                title: this.t('alert_delete_expense_title'),
                message: this.t('alert_delete_expense_msg') + ` ("${exp.category}" - ${this.formatMoney(exp.amount)})`,
                confirmText: this.t('alert_confirm_delete'),
                confirmClass: 'bg-rose-600 hover:bg-rose-700',
                action: async () => {
                    try {
                        const res = await fetch('api.php?action=expenses_delete', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: exp.id })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.showToast(this.t('alert_expense_deleted'), 'success');
                            this.expenses = this.expenses.filter(e => e.id !== exp.id);
                            await this.loadDashboardMetrics();
                        } else {
                            this.showToast(data.error || this.t('alert_expense_failed'), 'error');
                        }
                    } catch (err) {
                        this.showToast(this.t('alert_server_error'), 'error');
                    }
                    this.refreshIcons();
                }
            };
        },

        // =====================================================================
        // OPEN BILL & HOLD BILL (Simpan Tagihan / Kasbon)
        // =====================================================================
        openHoldBillModal() {
            this.openSaveBillModal();
        },

        openSaveBillModal() {
            if (this.settings && this.settings.feature_open_bill == 0) {
                this.showToast('Fitur Simpan Tagihan (Open Bill) dinonaktifkan di pengaturan modular.', 'warning');
                return;
            }
            if (this.isCartEmpty) {
                this.showToast(this.t('alert_cart_empty'), 'error');
                return;
            }
            this.portraitCartDrawerOpen = false;
            const prefix = (this.settings && this.settings.open_bill_mode === 'customer') ? 'Pelanggan ' : ((this.settings && this.settings.open_bill_mode === 'order_id') ? 'Pesanan #' : 'Meja ');
            this.saveBillModal.billName = (this.selectedCustomer ? this.selectedCustomer.name : '') || (prefix + ((this.openBills ? this.openBills.length : 0) + 1));
            this.saveBillModal.open = true;
            this.$nextTick(() => {
                this.refreshIcons();
                const inp = document.querySelector('input[x-model="saveBillModal.billName"]');
                if (inp) {
                    inp.focus();
                    inp.select();
                }
            });
        },

        async submitSaveBill() {
            const billName = (this.saveBillModal.billName || '').trim();
            if (!billName) {
                this.showToast(this.t('alert_bill_name_required'), 'error');
                return;
            }

            try {
                const res = await fetch('api.php?action=open_bill_save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        bill_name: billName,
                        customer_id: this.selectedCustomerId || (this.selectedCustomer ? this.selectedCustomer.id : null),
                        customer_name: this.selectedCustomer ? this.selectedCustomer.name : 'Pelanggan Umum',
                        items: this.cart
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.saveBillModal.open = false;
                    this.portraitCartDrawerOpen = false;
                    this.clearCart();
                    await this.loadInitialData();
                    this.openPosOpenBills();
                    this.showToast(this.t('alert_bill_saved') + ' ("' + billName + '")', 'success');
                    this.$nextTick(() => this.refreshIcons());
                } else {
                    this.showToast(data.error || this.t('alert_bill_save_failed'), 'error');
                }
            } catch (err) {
                this.showToast(this.t('alert_bill_save_failed'), 'error');
            }
        },

        loadOpenBill(bill) {
            try {
                const items = typeof bill.items_json === 'string' ? JSON.parse(bill.items_json) : (bill.items_json || []);
                this.cart = items;
                this.customerName = bill.bill_name;
                this.activeOpenBillId = bill.id;

                // Pulihkan data customer bila ada
                if (bill.customer_id) {
                    const foundCust = (this.customers || []).find(c => c.id == bill.customer_id);
                    if (foundCust) {
                        this.selectedCustomer = foundCust;
                        this.selectedCustomerId = foundCust.id;
                    }
                } else if (bill.customer_name && bill.customer_name !== 'Pelanggan Umum') {
                    const foundCust = (this.customers || []).find(c => c.name.toLowerCase() === bill.customer_name.toLowerCase());
                    if (foundCust) {
                        this.selectedCustomer = foundCust;
                        this.selectedCustomerId = foundCust.id;
                    }
                }

                // Langsung arahkan kembali ke view Kasir (posSubView = 'pos')
                this.posSubView = 'pos';
                this.navigate('pos');
                this.showToast(this.t('alert_bill_loaded') + ' ("' + bill.bill_name + '")', 'info');
                this.$nextTick(() => this.refreshIcons());
            } catch (e) {
                console.error('Gagal memuat open bill:', e);
                this.showToast(this.t('alert_bill_load_failed'), 'error');
            }
        },

        deleteOpenBill(bill) {
            this.showConfirm(
                this.t('alert_delete_bill_title'),
                this.t('alert_delete_bill_msg') + ' ("' + bill.bill_name + '" - ' + this.formatMoney(bill.total) + ')',
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
                            this.showToast(this.t('alert_bill_deleted') + ' ("' + bill.bill_name + '")', 'success');
                        } else {
                            this.showToast(data.error || this.t('alert_bill_delete_failed'), 'error');
                        }
                    } catch (err) {
                        this.showToast(this.t('alert_bill_delete_failed'), 'error');
                    }
                },
                { confirmText: this.t('alert_confirm_delete'), confirmClass: 'bg-rose-600 hover:bg-rose-700' }
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
                if (!Array.isArray(items) || items.length === 0) return this.t('no_items') || 'Tidak ada item';
                return items.map(i => i.name + ' (' + (i.qty || 1) + ')').join(', ');
            } catch (e) {
                return '';
            }
        },

        getBillSummary(bill) {
            try {
                const items = JSON.parse(bill.items_json);
                if (!Array.isArray(items) || items.length === 0) return this.t('no_items') || 'Tidak ada item';
                const totalQty = items.reduce((s, i) => s + (parseFloat(i.qty) || 1), 0);
                const names = items.map(i => i.name + ' (' + (i.qty || 1) + ')').slice(0, 3).join(', ');
                return totalQty + ' ' + (this.t('items') || 'item') + ': ' + names + (items.length > 3 ? '...' : '');
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
                    this.showToast(this.t('alert_store_settings_saved'), 'success');
                    await this.loadInitialData();
                } else {
                    this.showToast(data.error || this.t('alert_server_error'), 'error');
                }
            } catch (err) {
                this.showToast(this.t('alert_server_error'), 'error');
            }
        },

        // Cubic Bezier Smooth Spline Generator
        generateSplinePath(dataPoints, width = 400, height = 120, padding = 15) {
            if (!dataPoints || dataPoints.length === 0) return { stroke: '', fill: '', points: [], maxVal: 0 };
            const n = dataPoints.length;
            const values = dataPoints.map(p => typeof p === 'object' ? (p.total ?? p.amount ?? p.turnover ?? 0) : Number(p) || 0);
            const maxVal = Math.max(...values, 1000);
            const minVal = 0;
            const range = maxVal - minVal || 1;
            
            const usableWidth = width - (padding * 2);
            const usableHeight = height - (padding * 2);
            
            const coords = values.map((val, i) => {
                const x = padding + (i / Math.max(1, n - 1)) * usableWidth;
                const y = height - padding - ((val - minVal) / range) * usableHeight;
                return { x: Math.round(x * 10) / 10, y: Math.round(y * 10) / 10, val };
            });

            if (coords.length === 1) {
                return {
                    stroke: `M ${coords[0].x} ${coords[0].y}`,
                    fill: `M ${coords[0].x} ${coords[0].y} L ${coords[0].x} ${height} Z`,
                    points: coords,
                    maxVal
                };
            }

            let d = `M ${coords[0].x} ${coords[0].y}`;
            for (let i = 0; i < coords.length - 1; i++) {
                const p0 = coords[i === 0 ? 0 : i - 1];
                const p1 = coords[i];
                const p2 = coords[i + 1];
                const p3 = coords[i + 2] || p2;

                const cp1x = p1.x + (p2.x - p0.x) / 6;
                const cp1y = p1.y + (p2.y - p0.y) / 6;
                const cp2x = p2.x - (p3.x - p1.x) / 6;
                const cp2y = p2.y - (p3.y - p1.y) / 6;

                d += ` C ${Math.round(cp1x * 10) / 10} ${Math.round(cp1y * 10) / 10}, ${Math.round(cp2x * 10) / 10} ${Math.round(cp2y * 10) / 10}, ${p2.x} ${p2.y}`;
            }

            const fillD = `${d} L ${coords[coords.length - 1].x} ${height - padding} L ${coords[0].x} ${height - padding} Z`;

            return {
                stroke: d,
                fill: fillD,
                points: coords,
                maxVal
            };
        },

        // i18n & Theme Helper Functions
        t(key, fallback = '') {
            if (!window.posTranslations) return fallback || key;
            const lang = this.currentLang || 'id';
            const dict = window.posTranslations[lang] || window.posTranslations['id'] || {};
            return dict[key] || (window.posTranslations['id'] && window.posTranslations['id'][key]) || fallback || key;
        },

        setLanguage(code) {
            this.currentLang = code;
            localStorage.setItem('nodera_lang', code);
            localStorage.setItem('pos_language', code);
            document.documentElement.lang = code;
            this.$nextTick(() => {
                this.refreshIcons();
            });
        },

        setTheme(mode) {
            this.theme = mode;
            localStorage.setItem('theme', mode);
            if (mode === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.setAttribute('data-color-scheme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.setAttribute('data-color-scheme', 'light');
            }
            this.$nextTick(() => {
                this.refreshIcons();
            });
        },

        toggleTheme() {
            this.setTheme(this.theme === 'dark' ? 'light' : 'dark');
        },

        // Formatters
        formatMoney(num) {
            const val = parseFloat(num) || 0;
            const sym = (this.settings && this.settings.currency_symbol) ? this.settings.currency_symbol : 'Rp';
            return sym + ' ' + Math.round(val).toLocaleString('id-ID');
        },

        formatNumber(num) {
            const val = parseFloat(num) || 0;
            return Math.round(val).toLocaleString('id-ID');
        },

        formatMoneyDisplay(num) {
            if (num === null || num === undefined || num === '') return '';
            const n = parseInt(num, 10);
            if (isNaN(n)) return '';
            return n.toLocaleString('id-ID');
        },

        formatMoneyDisplay(val) {
            if (val === undefined || val === null || val === '') return '';
            const num = Number(val);
            if (isNaN(num)) return '';
            return num.toLocaleString('id-ID');
        },

        maskMoneyInput(eventOrVal, targetObj = null, prop = null, callback = null) {
            let raw = '';
            let event = null;
            if (eventOrVal && eventOrVal.target) {
                event = eventOrVal;
                raw = (event.target.value || '').replace(/[^\d]/g, '');
            } else {
                raw = String(eventOrVal || '').replace(/[^\d]/g, '');
            }
            let num = raw !== '' ? parseInt(raw, 10) : 0;
            if (targetObj && prop) {
                targetObj[prop] = num;
            }
            if (event && event.target) {
                event.target.value = raw !== '' ? (num > 0 ? num.toLocaleString('id-ID') : '0') : '';
            }
            if (typeof callback === 'function') callback(num);
            return num;
        },

        maskNumberInput(eventOrVal, targetObj = null, prop = null, callback = null) {
            let raw = '';
            let event = null;
            if (eventOrVal && eventOrVal.target) {
                event = eventOrVal;
                raw = (event.target.value || '').replace(/[^\d]/g, '');
            } else {
                raw = String(eventOrVal || '').replace(/[^\d]/g, '');
            }
            let num = raw !== '' ? parseInt(raw, 10) : 0;
            if (targetObj && prop) {
                targetObj[prop] = num;
            }
            if (event && event.target) {
                event.target.value = raw !== '' ? (num > 0 ? num.toLocaleString('id-ID') : '0') : '';
            }
            if (typeof callback === 'function') callback(num);
            return num;
        },

        formatQty(qty, unit) {
            const q = parseFloat(qty);
            if (unit === 'kg' || unit === 'liter') {
                return (Math.round(q * 100) / 100) + ' ' + unit;
            }
            return q + ' ' + (unit || 'x');
        }
    }));

    // Global Auto-Rotate / Force Landscape Handler for Mobile
    window.forceLandscapeMode = async function() {
        try {
            if (!document.fullscreenElement) {
                if (document.documentElement.requestFullscreen) {
                    await document.documentElement.requestFullscreen().catch(() => {});
                } else if (document.documentElement.webkitRequestFullscreen) {
                    await document.documentElement.webkitRequestFullscreen().catch(() => {});
                }
            }
            if (screen.orientation && screen.orientation.lock) {
                await screen.orientation.lock('landscape').catch(() => {});
            } else if (screen.lockOrientation) {
                screen.lockOrientation('landscape');
            }
        } catch (err) {
            console.warn('Orientation lock notice:', err);
        }
    };
});
