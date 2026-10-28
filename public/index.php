<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>NODERA POS - Universal UMKM Landscape Terminal</title>
    
    <!-- Offline First CSS -->
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
    
    <!-- Alpine.js & Lucide Icons -->
    <script src="assets/js/alpine.min.js" defer></script>
    <script src="assets/js/lucide.min.js"></script>
    <script src="assets/js/pos-app.js"></script>

    <style>
        /* Sleek Modern POS Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        
        /* Thermal Receipt Print Stylesheet */
        @media print {
            body * { visibility: hidden !important; }
            #thermal-receipt-area, #thermal-receipt-area * { visibility: visible !important; }
            #thermal-receipt-area {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                width: 58mm !important;
                font-family: 'Courier New', Courier, monospace !important;
                font-size: 11px !important;
                line-height: 1.25 !important;
                color: #000 !important;
                background: #fff !important;
                padding: 4px !important;
            }
        }

        /* Smart Mobile Landscape Guard */
        @media screen and (orientation: portrait) and (max-width: 768px) {
            #landscape-guard-overlay { display: flex !important; }
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased overflow-hidden select-none" x-data="posApp()">

    <!-- ORIENTATION GUARD (Hanya muncul jika HP dipegang tegak/portrait) -->
    <div id="landscape-guard-overlay" class="fixed inset-0 bg-slate-950 z-50 hidden flex-col items-center justify-center p-6 text-center text-slate-100">
        <div class="w-16 h-16 rounded-2xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center mb-4 animate-pulse">
            <i data-lucide="smartphone" class="w-8 h-8 transform rotate-90"></i>
        </div>
        <h2 class="text-xl font-bold tracking-tight mb-2">Putar Layar ke Mode Landscape</h2>
        <p class="text-slate-400 text-xs max-w-xs mb-6">Aplikasi Kasir NODERA dirancang mendatar (Landscape) agar optimal untuk kasir tablet dan desktop.</p>
        <button @click="toggleFullscreen()" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-semibold shadow-lg shadow-sky-500/20 transition">
            Aktifkan Layar Penuh
        </button>
    </div>

    <!-- MAIN APP CANVAS -->
    <div class="flex flex-col h-screen w-screen overflow-hidden bg-slate-950">
        
        <!-- HEADER TOPBAR -->
        <header class="bg-slate-900/90 backdrop-blur-md border-b border-slate-800/80 px-4 py-2 flex items-center justify-between z-20">
            <!-- Brand Info -->
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-sky-500 text-slate-950 flex items-center justify-center font-black text-sm shadow-md shadow-sky-500/20">
                    N
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="font-bold text-slate-100 text-sm tracking-tight" x-text="settings.store_name || 'NODERA POS'"></h1>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-medium text-emerald-400 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Online
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400" x-text="settings.store_subtitle || 'Universal Point of Sale'"></p>
                </div>
            </div>

            <!-- Global Action Bar -->
            <div class="flex items-center space-x-2.5">
                <!-- Pemroses Pembayaran / Kasir Bertugas -->
                <div class="relative" x-data="{ openCashierSelect: false }">
                    <button @click="openCashierSelect = !openCashierSelect" 
                            class="flex items-center space-x-2 bg-slate-800/80 border border-slate-700/70 hover:border-sky-500/50 text-slate-200 px-3 py-1.5 rounded-lg text-xs transition">
                        <div class="w-6 h-6 rounded-full bg-sky-500 text-slate-950 flex items-center justify-center font-bold text-xs">
                            <span x-text="activeCashier.name ? activeCashier.name.charAt(0) : 'K'"></span>
                        </div>
                        <div class="text-left">
                            <div class="text-[9px] text-sky-400 uppercase tracking-wider font-semibold">Kasir Pemroses:</div>
                            <div class="font-bold text-slate-100 text-xs truncate max-w-[120px]" x-text="activeCashier.name || 'Pilih Kasir'"></div>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                    </button>

                    <!-- Dropdown Pilih Kasir -->
                    <div x-show="openCashierSelect" @click.away="openCashierSelect = false" 
                         class="absolute right-0 mt-2 w-56 bg-slate-900 border border-slate-800 rounded-xl shadow-2xl py-1.5 z-30" style="display: none;">
                        <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-800/80">
                            Pilih Kasir Bertugas (Shift)
                        </div>
                        <template x-for="c in cashiers" :key="c.id">
                            <button @click="switchCashier(c); openCashierSelect = false" 
                                    class="w-full text-left px-3 py-2 text-xs flex items-center justify-between hover:bg-slate-800 transition"
                                    :class="activeCashier.id === c.id ? 'bg-sky-500/10 text-sky-400 font-semibold' : 'text-slate-300'">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-slate-800 text-slate-300 flex items-center justify-center text-[10px] font-bold" x-text="c.name.charAt(0)"></span>
                                    <span x-text="c.name"></span>
                                </div>
                                <span class="text-[9px] uppercase px-1.5 py-0.5 rounded font-mono"
                                      :class="c.role === 'admin' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-sky-500/10 text-sky-400 border border-sky-500/20'"
                                      x-text="c.role"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Language Selector -->
                <div class="flex items-center bg-slate-800/80 p-0.5 rounded-lg border border-slate-700/60 text-xs font-medium">
                    <button @click="setLanguage('id')" :class="currentLang === 'id' ? 'bg-sky-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'" class="px-2 py-1 rounded-md transition">ID</button>
                    <button @click="setLanguage('en')" :class="currentLang === 'en' ? 'bg-sky-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'" class="px-2 py-1 rounded-md transition">EN</button>
                    <button @click="setLanguage('fr')" :class="currentLang === 'fr' ? 'bg-sky-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'" class="px-2 py-1 rounded-md transition">FR</button>
                </div>

                <!-- Staff & Reports Actions -->
                <button @click="openEmployeeModal()" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg text-xs font-medium border border-slate-700/60 transition">
                    <i data-lucide="users" class="w-3.5 h-3.5 text-sky-400"></i>
                    <span class="hidden md:inline">Staf</span>
                </button>

                <button @click="openReportsModal()" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg text-xs font-medium border border-slate-700/60 transition">
                    <i data-lucide="bar-chart-2" class="w-3.5 h-3.5 text-emerald-400"></i>
                    <span class="hidden md:inline">Laporan</span>
                </button>

                <button @click="toggleFullscreen()" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg border border-slate-700/60 transition" title="Layar Penuh">
                    <i data-lucide="maximize-2" class="w-4 h-4"></i>
                </button>
            </div>
        </header>

        <!-- SPLIT WORKSPACE: 68% KATALOG (LEFT) | 32% CART & CHECKOUT (RIGHT) -->
        <div class="flex-1 flex overflow-hidden">
            
            <!-- LEFT WORKSPACE: KATALOG UNIVERSAL -->
            <main class="w-8/12 flex flex-col bg-slate-950 p-3.5 overflow-hidden border-r border-slate-800/80">
                
                <!-- Search & Category Filters -->
                <div class="mb-3 flex items-center gap-2.5">
                    <!-- Search Input with Barcode Auto-Enter -->
                    <div class="relative flex-1">
                        <input type="text" x-model="searchQuery" @keydown.enter="handleSearchEnter()"
                               :placeholder="lang.search_placeholder || 'Cari nama barang, jasa, atau scan barcode...'" 
                               class="w-full pl-9 pr-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-100 placeholder-slate-500 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none transition">
                        <i data-lucide="search" class="w-4 h-4 text-slate-500 absolute left-3 top-2.5"></i>
                        <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-200 text-xs">✕</button>
                    </div>

                    <!-- Category Pills -->
                    <div class="flex items-center gap-1.5 overflow-x-auto py-0.5 max-w-[50%]">
                        <button @click="selectedCategory = null" 
                                :class="selectedCategory === null ? 'bg-sky-500 text-slate-950 font-bold shadow-sm shadow-sky-500/20' : 'bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800'"
                                class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition">
                            Semua
                        </button>
                        <template x-for="cat in categories" :key="cat.id">
                            <button @click="selectedCategory = cat.id" 
                                    :class="selectedCategory === cat.id ? 'bg-sky-500 text-slate-950 font-bold shadow-sm shadow-sky-500/20' : 'bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800'"
                                    class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition">
                                <span x-text="cat.name"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- BENTO PRODUCT GRID (SCROLLABLE) -->
                <div class="flex-1 overflow-y-auto pr-1">
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2.5">
                        <template x-for="p in filteredProducts" :key="p.id">
                            <div @click="addToCart(p)" 
                                 class="rounded-xl border border-slate-800 bg-slate-900/60 p-3.5 hover:border-sky-500/60 hover:bg-slate-800/40 cursor-pointer transition flex flex-col justify-between active:scale-[0.98] group">
                                <div>
                                    <!-- Header Badge & Stock -->
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-md"
                                              :class="p.type === 'service' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-sky-500/10 text-sky-400 border border-sky-500/20'"
                                              x-text="p.category_name || (p.type === 'service' ? 'Layanan' : 'Barang')"></span>
                                        <template x-if="p.stock >= 0">
                                            <span class="text-[10px] text-slate-400 font-mono" x-text="'Stok: ' + p.stock"></span>
                                        </template>
                                    </div>

                                    <!-- Product Name -->
                                    <h3 class="font-bold text-slate-100 text-xs leading-snug line-clamp-2 group-hover:text-sky-300 transition" x-text="p.name"></h3>
                                    
                                    <!-- SKU / Code if available -->
                                    <p class="text-[10px] text-slate-500 font-mono mt-0.5" x-text="p.code || ''"></p>
                                </div>

                                <!-- Price Tag & Add Trigger -->
                                <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between">
                                    <span class="font-black text-sm text-sky-400" x-text="formatMoney(p.price)"></span>
                                    <span class="w-6 h-6 rounded-lg bg-slate-800 group-hover:bg-sky-500 group-hover:text-slate-950 flex items-center justify-center text-xs font-bold text-slate-300 transition">
                                        +
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State -->
                    <div x-show="filteredProducts.length === 0" class="h-64 flex flex-col items-center justify-center text-slate-500">
                        <i data-lucide="package-search" class="w-10 h-10 mb-2 stroke-1"></i>
                        <p class="text-xs">Tidak ada produk atau jasa yang cocok.</p>
                    </div>
                </div>
            </main>

            <!-- RIGHT WORKSPACE: ORDER CART & CHECKOUT PANEL -->
            <aside class="w-4/12 flex flex-col bg-slate-900 border-l border-slate-800/80 overflow-hidden shadow-2xl">
                
                <!-- Cart Header & Order Reference -->
                <div class="p-3.5 border-b border-slate-800/80 bg-slate-900/90">
                    <div class="flex items-center justify-between mb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-100 text-sm tracking-tight" x-text="lang.cart_title || 'Pesanan Aktif'"></span>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 font-mono font-medium" x-text="totalItemsCount + ' item'"></span>
                        </div>
                        <button @click="clearCart()" x-show="!isCartEmpty" class="text-[11px] text-rose-400 hover:text-rose-300 font-medium transition">
                            Kosongkan
                        </button>
                    </div>

                    <!-- Universal Order Reference & Staff Assignment -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <label class="block text-[10px] font-medium text-slate-400 mb-1">Meja / No. Ref / Catatan:</label>
                            <input type="text" x-model="orderRef" placeholder="Contoh: Meja 04 / Takeaway" 
                                   class="w-full px-2.5 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-slate-200 placeholder-slate-600 focus:border-sky-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[10px] font-medium text-slate-400 mb-1">Staf / Pramuniaga:</label>
                            <select x-model="activeStaff.id" @change="activeStaff.name = $event.target.options[$event.target.selectedIndex].text"
                                    class="w-full px-2 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-slate-200 focus:border-sky-500 focus:outline-none">
                                <option value="">-- Tanpa Staf --</option>
                                <template x-for="s in staffs" :key="s.id">
                                    <option :value="s.id" x-text="s.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- CART ITEMS LIST (HIGH DENSITY ROWS) -->
                <div class="flex-1 overflow-y-auto p-3 divide-y divide-slate-800/60">
                    <template x-for="(item, idx) in cart" :key="idx">
                        <div class="py-2.5 flex items-center justify-between gap-2.5">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-xs text-slate-200 truncate" x-text="item.name"></h4>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5" x-text="formatMoney(item.price)"></div>
                            </div>

                            <!-- Quantity Stepper -->
                            <div class="flex items-center space-x-1 bg-slate-950 rounded-lg p-0.5 border border-slate-800">
                                <button @click="updateQty(idx, -1)" class="w-6 h-6 rounded bg-slate-900 hover:bg-slate-800 text-slate-300 flex items-center justify-center font-bold text-xs transition">-</button>
                                <span class="w-7 text-center font-bold text-xs text-slate-100 font-mono" x-text="item.qty"></span>
                                <button @click="updateQty(idx, 1)" class="w-6 h-6 rounded bg-slate-900 hover:bg-slate-800 text-slate-300 flex items-center justify-center font-bold text-xs transition">+</button>
                            </div>

                            <!-- Line Total -->
                            <div class="w-20 text-right font-black text-xs text-slate-100 font-mono" x-text="formatMoney(item.price * item.qty)"></div>

                            <!-- Delete -->
                            <button @click="removeFromCart(idx)" class="text-slate-500 hover:text-rose-400 text-xs p-1 transition">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <div x-show="isCartEmpty" class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-500">
                        <i data-lucide="shopping-bag" class="w-10 h-10 mb-2 stroke-1"></i>
                        <p class="text-xs" x-text="lang.empty_cart"></p>
                    </div>
                </div>

                <!-- CART FOOTER & CHECKOUT -->
                <div class="p-3.5 border-t border-slate-800/80 bg-slate-950/80">
                    <!-- Calculations -->
                    <div class="space-y-1.5 text-xs mb-3.5">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-200 font-mono" x-text="formatMoney(subtotal)"></span>
                        </div>
                        <template x-if="discount > 0">
                            <div class="flex justify-between text-rose-400 font-medium">
                                <span>Diskon</span>
                                <span class="font-mono" x-text="'-' + formatMoney(discount)"></span>
                            </div>
                        </template>
                        <template x-if="taxAmount > 0">
                            <div class="flex justify-between text-slate-400">
                                <span>Pajak (<span x-text="settings.tax_percent"></span>%)</span>
                                <span class="font-mono text-slate-200" x-text="formatMoney(taxAmount)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between text-sm font-extrabold text-slate-100 pt-2 border-t border-slate-800">
                            <span>TOTAL PEMBAYARAN</span>
                            <span class="text-emerald-400 text-lg font-black font-mono" x-text="formatMoney(total)"></span>
                        </div>
                    </div>

                    <!-- Big Checkout Button -->
                    <button @click="openPaymentModal()" 
                            :disabled="isCartEmpty"
                            :class="!isCartEmpty ? 'bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black shadow-lg shadow-emerald-500/20' : 'bg-slate-800 text-slate-500 cursor-not-allowed'"
                            class="w-full py-3.5 px-4 rounded-xl text-xs uppercase tracking-wider transition flex items-center justify-center gap-2">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        <span>SELESAIKAN PEMBAYARAN (F12)</span>
                    </button>

                    <!-- Pemroses Pembayaran Audit Watermark -->
                    <div class="mt-2 text-center text-[10px] text-slate-500">
                        Kasir Pemroses: <span class="font-bold text-slate-300" x-text="activeCashier.name"></span>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- MODAL 1: CHECKOUT & MULTI-PAYMENT -->
    <div x-show="activeModal === 'payment'" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-5 shadow-2xl overflow-hidden" @click.away="activeModal = null">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                <div>
                    <h3 class="font-bold text-slate-100 text-base">Pembayaran Transaksi</h3>
                    <p class="text-xs text-slate-400">Kasir: <span class="font-bold text-sky-400" x-text="activeCashier.name"></span></p>
                </div>
                <button @click="activeModal = null" class="text-slate-400 hover:text-slate-200">✕</button>
            </div>

            <!-- Total Amount Card -->
            <div class="bg-slate-950 border border-slate-800/80 rounded-xl p-4 text-center mb-4">
                <span class="text-xs text-slate-400 font-medium uppercase tracking-wider">Total Tagihan:</span>
                <div class="text-2xl font-black text-emerald-400 font-mono mt-0.5" x-text="formatMoney(total)"></div>
            </div>

            <!-- Payment Methods -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button @click="paymentMethod = 'cash'" 
                        :class="paymentMethod === 'cash' ? 'border-sky-500 bg-sky-500/10 text-sky-400 font-bold' : 'border-slate-800 bg-slate-950 text-slate-400'"
                        class="p-3 border rounded-xl text-xs flex flex-col items-center justify-center gap-1.5 transition">
                    <i data-lucide="banknote" class="w-5 h-5"></i>
                    <span>Tunai</span>
                </button>
                <button @click="paymentMethod = 'qris'" 
                        :class="paymentMethod === 'qris' ? 'border-sky-500 bg-sky-500/10 text-sky-400 font-bold' : 'border-slate-800 bg-slate-950 text-slate-400'"
                        class="p-3 border rounded-xl text-xs flex flex-col items-center justify-center gap-1.5 transition">
                    <i data-lucide="qr-code" class="w-5 h-5"></i>
                    <span>QRIS</span>
                </button>
                <button @click="paymentMethod = 'transfer'" 
                        :class="paymentMethod === 'transfer' ? 'border-sky-500 bg-sky-500/10 text-sky-400 font-bold' : 'border-slate-800 bg-slate-950 text-slate-400'"
                        class="p-3 border rounded-xl text-xs flex flex-col items-center justify-center gap-1.5 transition">
                    <i data-lucide="arrow-right-left" class="w-5 h-5"></i>
                    <span>Transfer</span>
                </button>
            </div>

            <!-- Cash Input Section -->
            <div x-show="paymentMethod === 'cash'" class="mb-4">
                <label class="block text-xs font-medium text-slate-300 mb-1">Jumlah Uang Diterima:</label>
                <input type="number" x-model.number="cashTendered" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xl font-mono font-bold text-slate-100 focus:border-sky-500 focus:outline-none mb-2">
                
                <!-- Quick Money Buttons -->
                <div class="grid grid-cols-4 gap-1.5 mb-3">
                    <button @click="setCashTendered('exact')" class="px-2 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-xs font-semibold text-slate-200">Uang Pas</button>
                    <button @click="setCashTendered(20000)" class="px-2 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-xs font-semibold text-slate-200">20.000</button>
                    <button @click="setCashTendered(50000)" class="px-2 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-xs font-semibold text-slate-200">50.000</button>
                    <button @click="setCashTendered(100000)" class="px-2 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-xs font-semibold text-slate-200">100.000</button>
                </div>

                <!-- Kembalian Display -->
                <div class="flex justify-between items-center p-3 bg-slate-950 border border-slate-800 rounded-xl text-xs font-bold">
                    <span class="text-slate-400">Kembalian:</span>
                    <span class="text-base font-black text-emerald-400 font-mono" x-text="formatMoney(changeAmount)"></span>
                </div>
            </div>

            <!-- Dynamic QRIS Preview -->
            <div x-show="paymentMethod === 'qris'" class="mb-4 text-center p-4 bg-slate-950 border border-slate-800 rounded-xl">
                <p class="text-xs text-slate-300 font-medium mb-3">Pindai QRIS untuk Pembayaran Otomatis</p>
                <div class="inline-block bg-white p-3 rounded-xl shadow-inner">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=00020101021126570011ID.NODERAPAY.WWW01189360091100213049020208123456785204541153033605802ID5914NODERA%20POS6007JAKARTA61051234062070703A016304ABCD" 
                         class="w-40 h-40 rounded" alt="QRIS">
                </div>
                <p class="text-[10px] text-slate-400 mt-2">BCA • Mandiri • BRI • GoPay • OVO • Dana • ShopeePay</p>
            </div>

            <!-- Submit Transaction Button -->
            <button @click="processCheckout()" 
                    :disabled="paymentMethod === 'cash' && cashTendered < total"
                    :class="(paymentMethod !== 'cash' || cashTendered >= total) ? 'bg-sky-500 hover:bg-sky-600 text-slate-950 font-bold shadow-lg shadow-sky-500/20' : 'bg-slate-800 text-slate-500 cursor-not-allowed'"
                    class="w-full py-3.5 rounded-xl text-xs uppercase tracking-wider transition">
                KONFIRMASI & CETAK STRUK
            </button>
        </div>
    </div>

    <!-- MODAL 2: PRATINJAU STRUK THERMAL (ESC/POS) -->
    <div x-show="activeModal === 'receipt'" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-sm w-full p-5 shadow-2xl flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between border-b border-slate-800 pb-2.5 mb-3">
                <h3 class="font-bold text-slate-100 text-sm">Pratinjau Struk Pembelian</h3>
                <button @click="activeModal = null" class="text-slate-400 hover:text-slate-200">✕</button>
            </div>

            <!-- Printable Receipt Area -->
            <div class="flex-1 overflow-y-auto bg-white p-4 border border-slate-200 text-black font-mono text-xs rounded-xl space-y-1.5" id="thermal-receipt-area">
                <div class="text-center">
                    <div class="font-extrabold text-sm" x-text="settings.store_name"></div>
                    <div class="text-[10px] text-gray-600" x-text="settings.store_address"></div>
                    <div class="text-[10px] text-gray-600" x-text="'Telp: ' + settings.store_phone"></div>
                </div>
                <div class="border-t border-dashed border-gray-400 my-1.5"></div>
                
                <div class="text-[10px] space-y-0.5">
                    <div class="flex justify-between"><span>Faktur:</span><b x-text="lastTransaction?.invoice_no"></b></div>
                    <div class="flex justify-between"><span>Waktu:</span><span x-text="lastTransaction?.created_at"></span></div>
                    <div class="flex justify-between"><span>Kasir:</span><b x-text="lastTransaction?.cashier_name"></b></div>
                    <template x-if="lastTransaction?.staff_name">
                        <div class="flex justify-between"><span>Staf:</span><span x-text="lastTransaction?.staff_name"></span></div>
                    </template>
                    <template x-if="lastTransaction?.order_ref">
                        <div class="flex justify-between"><span>Ref/Meja:</span><span x-text="lastTransaction?.order_ref"></span></div>
                    </template>
                </div>
                
                <div class="border-t border-dashed border-gray-400 my-1.5"></div>

                <!-- Items -->
                <div class="space-y-1 text-[11px]">
                    <template x-for="item in lastTransaction?.items || []" :key="item.product_id">
                        <div>
                            <div class="font-bold" x-text="item.name"></div>
                            <div class="flex justify-between text-gray-700">
                                <span x-text="item.qty + ' x ' + formatMoney(item.price)"></span>
                                <span class="font-bold" x-text="formatMoney(item.price * item.qty)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="border-t border-dashed border-gray-400 my-1.5"></div>

                <div class="space-y-0.5 text-xs">
                    <div class="flex justify-between"><span>Subtotal:</span><span x-text="formatMoney(lastTransaction?.subtotal)"></span></div>
                    <template x-if="lastTransaction?.discount > 0">
                        <div class="flex justify-between"><span>Diskon:</span><span x-text="'-' + formatMoney(lastTransaction?.discount)"></span></div>
                    </template>
                    <div class="flex justify-between font-extrabold text-sm pt-1 border-t border-gray-300">
                        <span>TOTAL:</span><span x-text="formatMoney(lastTransaction?.total)"></span>
                    </div>
                    <div class="flex justify-between text-[11px]"><span>Bayar (<span x-text="lastTransaction?.payment_method"></span>):</span><span x-text="formatMoney(lastTransaction?.amount_paid)"></span></div>
                    <div class="flex justify-between text-[11px]"><span>Kembali:</span><span x-text="formatMoney(lastTransaction?.change_amount)"></span></div>
                </div>

                <div class="border-t border-dashed border-gray-400 my-1.5"></div>
                <div class="text-center text-[10px] text-gray-500 pt-1" x-text="settings.footer_receipt"></div>
            </div>

            <!-- Print Trigger -->
            <div class="mt-3.5 flex gap-2">
                <button @click="printThermal()" class="flex-1 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs shadow transition flex items-center justify-center gap-1.5">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak Struk Thermal</span>
                </button>
                <button @click="activeModal = null" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl text-xs transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 3: MANAJEMEN KARYAWAN & HAK AKSES -->
    <div x-show="activeModal === 'employee'" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-3xl w-full p-5 shadow-2xl flex flex-col max-h-[90vh]" @click.away="activeModal = null">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                <div>
                    <h3 class="font-bold text-slate-100 text-base flex items-center gap-2">
                        <i data-lucide="users" class="w-5 h-5 text-sky-400"></i>
                        <span>Manajemen Karyawan & Hak Akses</span>
                    </h3>
                    <p class="text-xs text-slate-400">Atur akun kasir pemroses pembayaran, staf, dan admin toko.</p>
                </div>
                <button @click="activeModal = null" class="text-slate-400 hover:text-slate-200">✕</button>
            </div>

            <div class="flex-1 grid grid-cols-1 md:grid-cols-5 gap-4 overflow-y-auto">
                <!-- Form Tambah / Edit Karyawan -->
                <div class="md:col-span-2 bg-slate-950 p-4 rounded-xl border border-slate-800/80">
                    <h4 class="font-bold text-xs text-slate-200 uppercase tracking-wider mb-3" x-text="employeeForm.id ? 'Edit Data Karyawan' : 'Tambah Karyawan Baru'"></h4>
                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block text-slate-400 font-medium mb-1">Nama Lengkap:</label>
                            <input type="text" x-model="employeeForm.name" placeholder="Contoh: Siti Rahma" class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:border-sky-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 font-medium mb-1">Peran / Jabatan:</label>
                            <select x-model="employeeForm.role" class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:border-sky-500 focus:outline-none">
                                <option value="cashier">Kasir (Pemroses Pembayaran)</option>
                                <option value="admin">Admin / Pemilik Toko</option>
                                <option value="staff">Staf / Pramuniaga</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-400 font-medium mb-1">Nomor Kontak / WhatsApp:</label>
                            <input type="text" x-model="employeeForm.phone" placeholder="0812xxxx" class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:border-sky-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 font-medium mb-1">PIN Cepat (4 Angka):</label>
                            <input type="text" x-model="employeeForm.pin" placeholder="1234" maxlength="4" class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 font-mono focus:border-sky-500 focus:outline-none">
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button @click="saveEmployee()" class="flex-1 py-2 bg-sky-500 hover:bg-sky-600 text-slate-950 font-bold rounded-lg transition">Simpan</button>
                            <button @click="resetEmployeeForm()" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg">Reset</button>
                        </div>
                    </div>
                </div>

                <!-- Daftar Karyawan & Audit Shift Hari Ini -->
                <div class="md:col-span-3 space-y-2">
                    <h4 class="font-bold text-xs text-slate-200 uppercase tracking-wider mb-2">Daftar Karyawan Aktif</h4>
                    <template x-for="emp in employees" :key="emp.id">
                        <div class="p-3 bg-slate-950 border border-slate-800 rounded-xl hover:border-slate-700 transition flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs text-slate-100" x-text="emp.name"></span>
                                    <span class="text-[9px] px-2 py-0.5 rounded font-mono uppercase"
                                          :class="emp.role === 'admin' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : (emp.role === 'cashier' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'bg-slate-800 text-slate-300')"
                                          x-text="emp.role"></span>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    <span>Transaksi Hari Ini: <b class="text-sky-400" x-text="emp.today_transactions || 0"></b> (<b class="text-emerald-400 font-mono" x-text="formatMoney(emp.today_turnover)"></b>)</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button @click="editEmployee(emp)" class="px-2 py-1 text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 rounded">Edit</button>
                                <button @click="deleteEmployee(emp.id)" class="px-2 py-1 text-xs bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 rounded">Hapus</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 4: LAPORAN PENJUALAN & AUDIT KASIR -->
    <div x-show="activeModal === 'reports'" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-3xl w-full p-5 shadow-2xl flex flex-col max-h-[90vh]" @click.away="activeModal = null">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                <div>
                    <h3 class="font-bold text-slate-100 text-base flex items-center gap-2">
                        <i data-lucide="trending-up" class="w-5 h-5 text-emerald-400"></i>
                        <span>Laporan Penjualan & Audit Kasir</span>
                    </h3>
                    <p class="text-xs text-slate-400">Rekap transaksi harian dan audit kasir pemroses pembayaran.</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="date" x-model="reportDate" @change="openReportsModal()" class="px-2.5 py-1 text-xs bg-slate-950 border border-slate-700 rounded-lg text-slate-200">
                    <button @click="activeModal = null" class="text-slate-400 hover:text-slate-200">✕</button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto space-y-4">
                <!-- Summary Metrics Cards -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="p-3.5 bg-slate-950 border border-slate-800 rounded-xl">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Omset Harian</div>
                        <div class="text-xl font-black text-emerald-400 font-mono mt-1" x-text="formatMoney(reportsData.summary?.total_turnover)"></div>
                        <div class="text-[10px] text-slate-400 mt-0.5" x-text="(reportsData.summary?.total_transactions || 0) + ' Transaksi Selesai'"></div>
                    </div>
                    <div class="p-3.5 bg-slate-950 border border-slate-800 rounded-xl">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Uang Fisik Kasir (Tunai)</div>
                        <div class="text-xl font-black text-sky-400 font-mono mt-1" x-text="formatMoney(reportsData.summary?.total_cash)"></div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Uang Masuk di Laci Kasir</div>
                    </div>
                    <div class="p-3.5 bg-slate-950 border border-slate-800 rounded-xl">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Non-Tunai (QRIS & Bank)</div>
                        <div class="text-xl font-black text-purple-400 font-mono mt-1" x-text="formatMoney(parseFloat(reportsData.summary?.total_qris || 0) + parseFloat(reportsData.summary?.total_transfer || 0))"></div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Langsung ke Rekening Toko</div>
                    </div>
                </div>

                <!-- Audit Transaksi per Kasir Pemroses Pembayaran -->
                <div class="bg-slate-950 p-4 rounded-xl border border-slate-800/80">
                    <h4 class="font-bold text-xs text-slate-200 uppercase tracking-wider mb-2.5">Audit Transaksi per Kasir Pemroses:</h4>
                    <div class="divide-y divide-slate-800 text-xs">
                        <template x-for="c in reportsData.by_cashier || []" :key="c.cashier_name">
                            <div class="py-2.5 flex justify-between items-center">
                                <div>
                                    <span class="font-bold text-slate-100" x-text="c.cashier_name"></span>
                                    <span class="text-slate-400 text-[11px] block" x-text="c.total_transactions + ' transaksi diproses'"></span>
                                </div>
                                <div class="text-right">
                                    <span class="font-black text-sm text-sky-400 font-mono" x-text="formatMoney(c.total_amount)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- 10 Produk / Jasa Terlaris -->
                <div class="bg-slate-950 p-4 rounded-xl border border-slate-800/80">
                    <h4 class="font-bold text-xs text-slate-200 uppercase tracking-wider mb-2.5">10 Produk & Jasa Paling Laris:</h4>
                    <div class="divide-y divide-slate-800 text-xs">
                        <template x-for="p in reportsData.top_products || []" :key="p.product_name">
                            <div class="py-2 flex justify-between items-center">
                                <span class="text-slate-300 font-medium" x-text="p.product_name"></span>
                                <div class="text-right font-mono">
                                    <span class="text-slate-400" x-text="p.total_qty + ' terjual • '"></span>
                                    <span class="font-bold text-emerald-400" x-text="formatMoney(p.total_sales)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
