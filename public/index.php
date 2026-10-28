<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>NODERA POS - Universal UMKM Landscape Terminal & Back-Office</title>
    
    <!-- Offline First CSS -->
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
    
    <!-- Alpine.js & Lucide Icons -->
    <script src="assets/js/alpine.min.js" defer></script>
    <script src="assets/js/lucide.min.js"></script>
    <script src="assets/js/pos-app.js"></script>

    <style>
        /* Modern Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        
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
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-sans antialiased overflow-hidden select-none transition-colors duration-200" x-data="posApp()">

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

    <!-- MAIN APP WRAPPER -->
    <div class="flex flex-col h-screen w-screen overflow-hidden bg-slate-50 dark:bg-slate-950">
        
        <!-- HEADER TOPBAR -->
        <header class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/80 px-4 py-2 flex items-center justify-between z-20 transition-colors">
            
            <!-- Left: Brand & Store Info -->
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-sky-500 text-slate-950 flex items-center justify-center font-black text-sm shadow-md shadow-sky-500/20">
                    N
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="font-bold text-slate-900 dark:text-slate-100 text-sm tracking-tight" x-text="settings.store_name"></h1>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-medium text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Online
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400" x-text="settings.store_subtitle"></p>
                </div>
            </div>

            <!-- Center: View Switcher (Terminal Kasir vs Back-Office) -->
            <div class="hidden md:flex items-center bg-slate-100 dark:bg-slate-800/80 p-1 rounded-xl border border-slate-200 dark:border-slate-700/60 text-xs font-semibold">
                <button @click="switchView('pos')" 
                        :class="currentView === 'pos' ? 'bg-white dark:bg-sky-500 text-sky-600 dark:text-slate-950 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'"
                        class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg transition">
                    <i data-lucide="monitor" class="w-4 h-4"></i>
                    <span>Terminal Kasir (Landscape)</span>
                </button>
                <button @click="switchView('backoffice')" 
                        :class="currentView === 'backoffice' ? 'bg-white dark:bg-sky-500 text-sky-600 dark:text-slate-950 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'"
                        class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg transition">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Back-Office Admin</span>
                    <template x-if="currentUser.role !== 'admin'">
                        <i data-lucide="lock" class="w-3 h-3 text-amber-500"></i>
                    </template>
                </button>
            </div>

            <!-- Right: Actions & User Shift Profile -->
            <div class="flex items-center space-x-2">
                
                <!-- Quick Cashier Shift Profile / Lock Keypad Trigger -->
                <button @click="openLockModal()" 
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700/60 text-xs transition"
                        title="Klik untuk ganti kasir / kunci layar">
                    <div class="w-6 h-6 rounded-full bg-sky-500 text-slate-950 flex items-center justify-center font-bold text-xs">
                        <span x-text="currentUser.name.charAt(0)"></span>
                    </div>
                    <div class="text-left">
                        <div class="text-[9px] text-sky-600 dark:text-sky-400 uppercase font-semibold">Kasir Bertugas:</div>
                        <div class="font-bold text-slate-800 dark:text-slate-100 text-xs truncate max-w-[120px]" x-text="currentUser.name"></div>
                    </div>
                    <i data-lucide="lock" class="w-3.5 h-3.5 text-slate-400 ml-1"></i>
                </button>

                <!-- Theme Toggle Button -->
                <button @click="toggleTheme()" 
                        class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700/80 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700/60 transition"
                        title="Toggle Tema Terang / Gelap">
                    <template x-if="themeMode === 'dark'">
                        <i data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
                    </template>
                    <template x-if="themeMode === 'light'">
                        <i data-lucide="moon" class="w-4 h-4 text-slate-600"></i>
                    </template>
                </button>

                <!-- Language Switcher -->
                <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-0.5 rounded-lg border border-slate-200 dark:border-slate-700/60 text-xs font-semibold">
                    <button @click="setLanguage('id')" :class="currentLang === 'id' ? 'bg-sky-500 text-white dark:text-slate-950' : 'text-slate-500 dark:text-slate-400'" class="px-2 py-1 rounded-md transition">ID</button>
                    <button @click="setLanguage('en')" :class="currentLang === 'en' ? 'bg-sky-500 text-white dark:text-slate-950' : 'text-slate-500 dark:text-slate-400'" class="px-2 py-1 rounded-md transition">EN</button>
                    <button @click="setLanguage('fr')" :class="currentLang === 'fr' ? 'bg-sky-500 text-white dark:text-slate-950' : 'text-slate-500 dark:text-slate-400'" class="px-2 py-1 rounded-md transition">FR</button>
                </div>

                <!-- Fullscreen Toggle -->
                <button @click="toggleFullscreen()" class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700/80 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700/60 transition" title="Layar Penuh">
                    <i data-lucide="maximize-2" class="w-4 h-4"></i>
                </button>
            </div>
        </header>

        <!-- =================================================================== -->
        <!-- VIEW 1: TERMINAL KASIR (DEDICATED LANDSCAPE POS)                    -->
        <!-- =================================================================== -->
        <div x-show="currentView === 'pos'" class="flex-1 flex overflow-hidden">
            
            <!-- LEFT AREA: 68% KATALOG & PENCARIAN -->
            <main class="w-8/12 flex flex-col bg-slate-100/60 dark:bg-slate-950 p-3.5 overflow-hidden border-r border-slate-200 dark:border-slate-800/80">
                
                <!-- Search & Filter Bar -->
                <div class="mb-3 flex items-center gap-2.5">
                    <!-- Barcode Search Input with Auto-Enter -->
                    <div class="relative flex-1">
                        <input type="text" x-model="searchQuery" @keydown.enter="handleSearchEnter()"
                               placeholder="Cari nama barang, jasa, atau scan barcode..." 
                               class="w-full pl-9 pr-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none transition shadow-sm">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3 top-2.5"></i>
                        <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs">✕</button>
                    </div>

                    <!-- Category Pills Horizontal Scroll -->
                    <div class="flex items-center gap-1.5 overflow-x-auto py-0.5 max-w-[50%]">
                        <button @click="selectedCategory = null" 
                                :class="selectedCategory === null ? 'bg-sky-500 text-white dark:text-slate-950 font-bold shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 border border-slate-200 dark:border-slate-800'"
                                class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition">
                            Semua
                        </button>
                        <template x-for="cat in categories" :key="cat.id">
                            <button @click="selectedCategory = cat.id" 
                                    :class="selectedCategory === cat.id ? 'bg-sky-500 text-white dark:text-slate-950 font-bold shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 border border-slate-200 dark:border-slate-800'"
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
                                 class="rounded-xl border border-slate-200 dark:border-slate-800/90 bg-white dark:bg-slate-900/70 p-3.5 hover:border-sky-500/70 hover:shadow-md cursor-pointer transition flex flex-col justify-between active:scale-[0.98] group shadow-sm">
                                <div>
                                    <!-- Header Badge: Kategori & Status Stok -->
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-md"
                                              :class="p.type === 'service' ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' : 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20'"
                                              x-text="p.category_name || (p.type === 'service' ? 'Layanan' : 'Barang')"></span>
                                        
                                        <template x-if="p.type === 'goods'">
                                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded font-semibold"
                                                  :class="p.stock > p.min_stock_alert ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-500/10' : (p.stock > 0 ? 'text-amber-600 dark:text-amber-400 bg-amber-500/10' : 'text-rose-600 dark:text-rose-400 bg-rose-500/10')"
                                                  x-text="p.stock > 0 ? 'Stok: ' + p.stock : 'Habis'"></span>
                                        </template>
                                        <template x-if="p.type === 'service'">
                                            <span class="text-[10px] text-purple-500 font-mono font-medium">Jasa</span>
                                        </template>
                                    </div>

                                    <!-- Product Name -->
                                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-xs leading-snug line-clamp-2 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition" x-text="p.name"></h3>
                                    
                                    <!-- SKU/Barcode -->
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="p.code || ''"></p>
                                </div>

                                <!-- Price Tag & Add Trigger -->
                                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                                    <span class="font-black text-sm text-sky-600 dark:text-sky-400" x-text="formatMoney(p.sell_price)"></span>
                                    <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 group-hover:bg-sky-500 group-hover:text-white dark:group-hover:text-slate-950 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300 transition">
                                        +
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State -->
                    <div x-show="filteredProducts.length === 0" class="h-64 flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                        <i data-lucide="package-search" class="w-10 h-10 mb-2 stroke-1"></i>
                        <p class="text-xs">Tidak ada produk atau jasa yang cocok.</p>
                    </div>
                </div>
            </main>

            <!-- RIGHT AREA: 32% CART & CHECKOUT TICKET -->
            <aside class="w-4/12 flex flex-col bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800/80 overflow-hidden shadow-2xl">
                
                <!-- Ticket Header -->
                <div class="p-3.5 border-b border-slate-200 dark:border-slate-800/80 bg-slate-50/80 dark:bg-slate-900/90">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 dark:text-slate-100 text-sm tracking-tight">Pesanan Aktif</span>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono font-medium" x-text="totalItemsCount + ' item'"></span>
                        </div>
                        <button @click="clearCart()" x-show="!isCartEmpty" class="text-[11px] text-rose-500 hover:text-rose-600 font-medium transition">
                            Kosongkan
                        </button>
                    </div>

                    <!-- Input Referensi Pesanan / Meja / Pelanggan -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <input type="text" x-model="customerName" placeholder="Nama Pelanggan (Opsional)" 
                               class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-600 focus:border-sky-500 focus:outline-none">
                        
                        <input type="text" x-model="orderRef" placeholder="Ref / Meja / Catatan" 
                               class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-600 focus:border-sky-500 focus:outline-none">
                    </div>
                </div>

                <!-- CART ITEMS LIST -->
                <div class="flex-1 overflow-y-auto p-3 divide-y divide-slate-100 dark:divide-slate-800/60">
                    <template x-for="(item, idx) in cart" :key="idx">
                        <div class="py-2.5 flex items-center justify-between gap-2.5">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate" x-text="item.name"></h4>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5" x-text="formatMoney(item.price)"></div>
                            </div>

                            <!-- Qty Steppers -->
                            <div class="flex items-center space-x-1 bg-slate-100 dark:bg-slate-950 rounded-lg p-0.5 border border-slate-200 dark:border-slate-800">
                                <button @click="updateQty(idx, -1)" class="w-6 h-6 rounded bg-white dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs transition">-</button>
                                <span class="w-7 text-center font-bold text-xs text-slate-900 dark:text-slate-100 font-mono" x-text="item.qty"></span>
                                <button @click="updateQty(idx, 1)" class="w-6 h-6 rounded bg-white dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs transition">+</button>
                            </div>

                            <!-- Line Total -->
                            <div class="w-20 text-right font-black text-xs text-slate-900 dark:text-slate-100 font-mono" x-text="formatMoney(item.price * item.qty)"></div>

                            <!-- Delete Item -->
                            <button @click="removeFromCart(idx)" class="text-slate-400 hover:text-rose-500 text-xs p-1 transition">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <div x-show="isCartEmpty" class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-400 dark:text-slate-500">
                        <i data-lucide="shopping-bag" class="w-10 h-10 mb-2 stroke-1"></i>
                        <p class="text-xs">Keranjang masih kosong. Klik barang di sebelah kiri untuk menambahkan.</p>
                    </div>
                </div>

                <!-- CART FOOTER & CHECKOUT ACTION -->
                <div class="p-3.5 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/80 dark:bg-slate-950/80">
                    <div class="space-y-1.5 text-xs mb-3.5">
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200 font-mono" x-text="formatMoney(subtotal)"></span>
                        </div>
                        <template x-if="discount > 0">
                            <div class="flex justify-between text-rose-500 font-medium">
                                <span>Diskon</span>
                                <span class="font-mono" x-text="'-' + formatMoney(discount)"></span>
                            </div>
                        </template>
                        <template x-if="taxAmount > 0">
                            <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                <span>Pajak (<span x-text="settings.tax_percent"></span>%)</span>
                                <span class="font-mono text-slate-800 dark:text-slate-200" x-text="formatMoney(taxAmount)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between text-sm font-extrabold text-slate-900 dark:text-slate-100 pt-2 border-t border-slate-200 dark:border-slate-800">
                            <span>TOTAL PEMBAYARAN</span>
                            <span class="text-emerald-600 dark:text-emerald-400 text-lg font-black font-mono" x-text="formatMoney(total)"></span>
                        </div>
                    </div>

                    <!-- Big Checkout Button -->
                    <button @click="openPaymentModal()" 
                            :disabled="isCartEmpty"
                            :class="!isCartEmpty ? 'bg-emerald-500 hover:bg-emerald-600 text-white font-black shadow-lg shadow-emerald-500/20' : 'bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-600 cursor-not-allowed'"
                            class="w-full py-3.5 px-4 rounded-xl text-xs uppercase tracking-wider transition flex items-center justify-center gap-2">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        <span>SELESAIKAN PEMBAYARAN (F12)</span>
                    </button>

                    <!-- Kasir Pemroses Watermark -->
                    <div class="mt-2 text-center text-[10px] text-slate-500">
                        Kasir Pemroses: <span class="font-bold text-slate-700 dark:text-slate-300" x-text="currentUser.name"></span>
                    </div>
                </div>
            </aside>
        </div>

        <!-- =================================================================== -->
        <!-- VIEW 2: BACK-OFFICE ADMIN PORTAL                                   -->
        <!-- =================================================================== -->
        <div x-show="currentView === 'backoffice'" class="flex-1 flex overflow-hidden">
            
            <!-- BACK-OFFICE SIDEBAR NAVIGATION -->
            <nav class="w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 p-3 flex flex-col justify-between">
                <div class="space-y-1">
                    <div class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Menu Manajemen Toko
                    </div>

                    <button @click="loadBackofficeTab('dashboard')" 
                            :class="backofficeTab === 'dashboard' ? 'bg-sky-500 text-white dark:text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs transition">
                        <i data-lucide="pie-chart" class="w-4 h-4"></i>
                        <span>Ringkasan & Laporan</span>
                    </button>

                    <button @click="loadBackofficeTab('products')" 
                            :class="backofficeTab === 'products' ? 'bg-sky-500 text-white dark:text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs transition">
                        <i data-lucide="package" class="w-4 h-4"></i>
                        <span>Master Produk & Jasa</span>
                    </button>

                    <button @click="loadBackofficeTab('categories')" 
                            :class="backofficeTab === 'categories' ? 'bg-sky-500 text-white dark:text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs transition">
                        <i data-lucide="tags" class="w-4 h-4"></i>
                        <span>Kelola Kategori</span>
                    </button>

                    <button @click="loadBackofficeTab('stock')" 
                            :class="backofficeTab === 'stock' ? 'bg-sky-500 text-white dark:text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs transition">
                        <i data-lucide="boxes" class="w-4 h-4"></i>
                        <span>Kelola Stok & Opname</span>
                    </button>

                    <button @click="loadBackofficeTab('users')" 
                            :class="backofficeTab === 'users' ? 'bg-sky-500 text-white dark:text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs transition">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>Karyawan & Kasir</span>
                    </button>

                    <button @click="loadBackofficeTab('settings')" 
                            :class="backofficeTab === 'settings' ? 'bg-sky-500 text-white dark:text-slate-950 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs transition">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                        <span>Pengaturan Toko & Struk</span>
                    </button>
                </div>

                <!-- Back to POS button -->
                <button @click="switchView('pos')" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-sky-500 hover:text-white dark:hover:text-slate-950 text-slate-700 dark:text-slate-300 text-xs font-bold transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali ke Layar Kasir</span>
                </button>
            </nav>

            <!-- BACK-OFFICE CONTENT CANVAS -->
            <main class="flex-1 bg-slate-50 dark:bg-slate-950 p-6 overflow-y-auto">
                
                <!-- TAB 1: RINGKASAN & LAPORAN -->
                <div x-show="backofficeTab === 'dashboard'" class="space-y-6 max-w-6xl mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Ringkasan Penjualan & Laba</h2>
                            <p class="text-xs text-slate-500">Rekap transaksi harian, perkiraan laba kotor, dan audit per kasir.</p>
                        </div>
                        <input type="date" x-model="reportDate" @change="loadReports()" 
                               class="px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-lg text-xs font-medium text-slate-800 dark:text-slate-200 shadow-sm">
                    </div>

                    <!-- Metric Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Omset Harian</span>
                            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1" x-text="formatMoney(reportsData.summary?.total_turnover)"></div>
                            <span class="text-[11px] text-slate-500 mt-1 block" x-text="(reportsData.summary?.total_transactions || 0) + ' Transaksi Selesai'"></span>
                        </div>

                        <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Estimasi Laba Kotor</span>
                            <div class="text-2xl font-black text-sky-600 dark:text-sky-400 font-mono mt-1" x-text="formatMoney(reportsData.summary?.gross_profit)"></div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Omset - Harga Modal Pokok</span>
                        </div>

                        <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Uang Fisik Kasir (Tunai)</span>
                            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 font-mono mt-1" x-text="formatMoney(reportsData.summary?.total_cash)"></div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Uang masuk di laci kasir</span>
                        </div>

                        <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Non-Tunai (QRIS & Bank)</span>
                            <div class="text-2xl font-black text-purple-600 dark:text-purple-400 font-mono mt-1" x-text="formatMoney(parseFloat(reportsData.summary?.total_qris || 0) + parseFloat(reportsData.summary?.total_transfer || 0))"></div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Masuk ke rekening toko</span>
                        </div>
                    </div>

                    <!-- Audit per Kasir & 10 Produk Terlaris -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 mb-3">Audit Transaksi per Kasir Pemroses</h3>
                            <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                <template x-for="c in reportsData.by_cashier || []" :key="c.cashier_name">
                                    <div class="py-2.5 flex justify-between items-center">
                                        <div>
                                            <span class="font-bold text-slate-800 dark:text-slate-100" x-text="c.cashier_name"></span>
                                            <span class="text-slate-400 text-[11px] block" x-text="c.total_transactions + ' transaksi diproses'"></span>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-black text-sm text-sky-600 dark:text-sky-400 font-mono" x-text="formatMoney(c.total_amount)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 mb-3">10 Produk & Jasa Paling Laris</h3>
                            <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                <template x-for="p in reportsData.top_products || []" :key="p.product_name">
                                    <div class="py-2 flex justify-between items-center">
                                        <span class="text-slate-700 dark:text-slate-300 font-medium" x-text="p.product_name"></span>
                                        <div class="text-right font-mono">
                                            <span class="text-slate-400" x-text="p.total_qty + ' terjual • '"></span>
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="formatMoney(p.total_sales)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: MASTER PRODUK & JASA -->
                <div x-show="backofficeTab === 'products'" class="space-y-4 max-w-6xl mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Master Produk & Layanan</h2>
                            <p class="text-xs text-slate-500">Kelola katalog barang fisik berstok dan jasa/layanan.</p>
                        </div>
                        <button @click="openProductModal()" class="flex items-center gap-1.5 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-bold shadow transition">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Tambah Produk / Jasa</span>
                        </button>
                    </div>

                    <!-- Products Table -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="p-3.5">SKU / Kode</th>
                                    <th class="p-3.5">Nama Produk</th>
                                    <th class="p-3.5">Kategori</th>
                                    <th class="p-3.5 text-right">Harga Modal</th>
                                    <th class="p-3.5 text-right">Harga Jual</th>
                                    <th class="p-3.5 text-center">Margin</th>
                                    <th class="p-3.5 text-center">Stok</th>
                                    <th class="p-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <template x-for="p in products" :key="p.id">
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                                        <td class="p-3.5 font-mono text-slate-400" x-text="p.code || '-'"></td>
                                        <td class="p-3.5 font-bold text-slate-800 dark:text-slate-100" x-text="p.name"></td>
                                        <td class="p-3.5">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase"
                                                  :class="p.type === 'service' ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400' : 'bg-sky-500/10 text-sky-600 dark:text-sky-400'"
                                                  x-text="p.category_name || (p.type === 'service' ? 'Layanan' : 'Barang')"></span>
                                        </td>
                                        <td class="p-3.5 text-right font-mono text-slate-500" x-text="formatMoney(p.cost_price)"></td>
                                        <td class="p-3.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="formatMoney(p.sell_price)"></td>
                                        <td class="p-3.5 text-center font-mono">
                                            <span class="text-[11px] font-semibold text-sky-600 dark:text-sky-400" 
                                                  x-text="p.sell_price > 0 ? Math.round(((p.sell_price - (p.cost_price || 0)) / p.sell_price) * 100) + '%' : '0%'"></span>
                                        </td>
                                        <td class="p-3.5 text-center">
                                            <template x-if="p.type === 'goods'">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold"
                                                      :class="p.stock > p.min_stock_alert ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : (p.stock > 0 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400')"
                                                      x-text="p.stock"></span>
                                            </template>
                                            <template x-if="p.type === 'service'">
                                                <span class="text-slate-400 font-mono text-[10px]">Unlimited</span>
                                            </template>
                                        </td>
                                        <td class="p-3.5 text-right space-x-1">
                                            <button @click="openProductModal(p)" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded text-xs transition">Edit</button>
                                            <button @click="deleteProduct(p.id)" class="px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded text-xs transition">Hapus</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 3: KELOLA KATEGORI -->
                <div x-show="backofficeTab === 'categories'" class="space-y-4 max-w-4xl mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Kelola Kategori</h2>
                            <p class="text-xs text-slate-500">Sesuaikan kategori dengan bidang usaha Anda (Retail, Kuliner, Jasa, dsb).</p>
                        </div>
                        <button @click="openCategoryModal()" class="flex items-center gap-1.5 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-bold shadow transition">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Tambah Kategori</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <template x-for="c in categories" :key="c.id">
                            <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold">
                                        <i data-lucide="tag" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100" x-text="c.name"></h3>
                                        <span class="text-[11px] text-slate-400" x-text="'Urutan #' + c.sort_order"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button @click="openCategoryModal(c)" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-medium transition">Edit</button>
                                    <button @click="deleteCategory(c.id)" class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-lg text-xs font-medium transition">Hapus</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- TAB 4: KELOLA STOK & INVENTORI -->
                <div x-show="backofficeTab === 'stock'" class="space-y-4 max-w-6xl mx-auto">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Kelola Stok & Inventori</h2>
                        <p class="text-xs text-slate-500">Penambahan stok saat kulakan, stok opname fisik, dan audit mutasi barang.</p>
                    </div>

                    <!-- Goods Stock List -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="p-3.5">SKU / Barcode</th>
                                    <th class="p-3.5">Nama Barang</th>
                                    <th class="p-3.5">Kategori</th>
                                    <th class="p-3.5 text-center">Sisa Stok</th>
                                    <th class="p-3.5 text-center">Status</th>
                                    <th class="p-3.5 text-right">Penyesuaian Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <template x-for="p in products.filter(item => item.type === 'goods')" :key="p.id">
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                                        <td class="p-3.5 font-mono text-slate-400" x-text="p.code || '-'"></td>
                                        <td class="p-3.5 font-bold text-slate-800 dark:text-slate-100" x-text="p.name"></td>
                                        <td class="p-3.5 text-slate-500" x-text="p.category_name"></td>
                                        <td class="p-3.5 text-center font-mono font-bold text-sm" x-text="p.stock"></td>
                                        <td class="p-3.5 text-center">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold"
                                                  :class="p.stock > p.min_stock_alert ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : (p.stock > 0 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400')"
                                                  x-text="p.stock > p.min_stock_alert ? 'Stok Aman' : (p.stock > 0 ? 'Menipis' : 'Habis')"></span>
                                        </td>
                                        <td class="p-3.5 text-right">
                                            <button @click="openStockAdjustModal(p)" class="px-3 py-1.5 bg-sky-500/10 hover:bg-sky-500/20 text-sky-600 dark:text-sky-400 border border-sky-500/20 rounded-lg text-xs font-semibold transition">
                                                + Atur Stok (Masuk/Opname)
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Riwayat Mutasi Terakhir -->
                    <div class="mt-6">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Riwayat Mutasi Stok Terakhir (Audit Log)</h3>
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase tracking-wider font-semibold">
                                    <tr>
                                        <th class="p-3">Waktu</th>
                                        <th class="p-3">Barang</th>
                                        <th class="p-3 text-center">Tipe</th>
                                        <th class="p-3 text-center">Perubahan</th>
                                        <th class="p-3 text-center">Stok Akhir</th>
                                        <th class="p-3">Catatan / Alasan</th>
                                        <th class="p-3">Petugas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    <template x-for="m in stockMutations.slice(0, 15)" :key="m.id">
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                                            <td class="p-3 text-slate-400 font-mono text-[11px]" x-text="m.created_at"></td>
                                            <td class="p-3 font-semibold text-slate-800 dark:text-slate-100" x-text="m.product_name"></td>
                                            <td class="p-3 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-mono uppercase font-bold"
                                                      :class="m.type === 'in' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : (m.type === 'sale' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400')"
                                                      x-text="m.type"></span>
                                            </td>
                                            <td class="p-3 text-center font-mono font-bold" :class="m.type === 'in' ? 'text-emerald-500' : 'text-slate-700 dark:text-slate-300'" x-text="(m.type === 'in' ? '+' : '-') + m.qty"></td>
                                            <td class="p-3 text-center font-mono font-bold text-slate-900 dark:text-slate-100" x-text="m.current_stock"></td>
                                            <td class="p-3 text-slate-500" x-text="m.notes || '-'"></td>
                                            <td class="p-3 text-slate-400" x-text="m.user_name || '-'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: KARYAWAN & KASIR -->
                <div x-show="backofficeTab === 'users'" class="space-y-4 max-w-4xl mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Manajemen Akun Karyawan</h2>
                            <p class="text-xs text-slate-500">Atur akun admin dan kasir beserta PIN cepat login shift.</p>
                        </div>
                        <button @click="openUserModal()" class="flex items-center gap-1.5 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-bold shadow transition">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Tambah Karyawan</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="u in users" :key="u.id">
                            <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center font-black text-sm">
                                        <span x-text="u.name.charAt(0)"></span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100" x-text="u.name"></h3>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono uppercase font-bold"
                                                  :class="u.role === 'admin' ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' : 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20'"
                                                  x-text="u.role"></span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            Username: <span class="font-mono text-slate-600 dark:text-slate-300" x-text="u.username"></span> • 
                                            PIN: <span class="font-mono text-slate-600 dark:text-slate-300">••••</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="openUserModal(u)" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-medium transition">Edit</button>
                                    <button @click="deleteUser(u.id)" class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-lg text-xs font-medium transition">Hapus</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- TAB 6: PENGATURAN TOKO -->
                <div x-show="backofficeTab === 'settings'" class="space-y-4 max-w-2xl mx-auto">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Pengaturan Toko & Struk</h2>
                        <p class="text-xs text-slate-500">Konfigurasi profil usaha dan tata letak cetak struk thermal.</p>
                    </div>

                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Toko / Usaha:</label>
                            <input type="text" x-model="settings.store_name" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 font-semibold focus:outline-none focus:border-sky-500">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Slogan / Keterangan Bisnis:</label>
                            <input type="text" x-model="settings.store_subtitle" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Telepon / WhatsApp:</label>
                                <input type="text" x-model="settings.store_phone" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pajak / PB1 (%):</label>
                                <input type="number" x-model.number="settings.tax_percent" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap Toko:</label>
                            <textarea rows="2" x-model="settings.store_address" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500"></textarea>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Footer Struk:</label>
                            <input type="text" x-model="settings.receipt_footer" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Ukuran Kertas Thermal:</label>
                                <select x-model="settings.paper_size" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500">
                                    <option value="58mm">58mm (Kecil / Mobile Bluetooth)</option>
                                    <option value="80mm">80mm (Standar POS Kasir)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tema Default:</label>
                                <select x-model="settings.theme_mode" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500">
                                    <option value="dark">Dark Theme (Gelap Modern)</option>
                                    <option value="light">Light Theme (Terang Minimalis)</option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button @click="saveSettings()" class="w-full py-3 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl text-xs shadow-lg shadow-sky-500/20 transition">
                                Simpan Pengaturan Toko
                            </button>
                        </div>
                    </div>
                </div>

            </main>
        </div>

    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 1: QUICK PIN LOCK / SHIFT SWITCHER (KEYPAD)                       -->
    <!-- ======================================================================= -->
    <div x-show="activeModal === 'lock'" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50" style="display: none;">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-sm w-full p-6 shadow-2xl text-center" @click.away="activeModal = null">
            
            <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-500 flex items-center justify-center mx-auto mb-3">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            
            <h3 class="font-extrabold text-slate-900 dark:text-slate-100 text-base">Ganti Kasir / Kunci Layar</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-4">Masukkan PIN 4-digit karyawan Anda untuk berganti kasir pemroses.</p>

            <!-- PIN Dot Indicators -->
            <div class="flex justify-center items-center gap-3 mb-4 h-8">
                <template x-for="i in 4" :key="i">
                    <div class="w-3.5 h-3.5 rounded-full transition-all duration-150"
                         :class="pinInput.length >= i ? 'bg-sky-500 scale-110 shadow-sm shadow-sky-500/50' : 'bg-slate-200 dark:bg-slate-800 border border-slate-300 dark:border-slate-700'"></div>
                </template>
            </div>

            <!-- Error message if any -->
            <div x-show="pinError" class="text-xs font-semibold text-rose-500 mb-3" x-text="pinError"></div>

            <!-- Numeric Keypad Grid -->
            <div class="grid grid-cols-3 gap-2.5 max-w-[240px] mx-auto mb-4 font-mono">
                <template x-for="num in [1,2,3,4,5,6,7,8,9]" :key="num">
                    <button @click="pressPin(num)" class="h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-lg font-bold text-slate-800 dark:text-slate-100 transition active:scale-95 shadow-sm" x-text="num"></button>
                </template>
                <button @click="clearPin()" class="h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 transition">C</button>
                <button @click="pressPin(0)" class="h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-lg font-bold text-slate-800 dark:text-slate-100 transition active:scale-95 shadow-sm">0</button>
                <button @click="backspacePin()" class="h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center transition">
                    <i data-lucide="delete" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="flex gap-2">
                <button @click="submitPin()" class="flex-1 py-3 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl text-xs shadow-lg shadow-sky-500/20 transition">
                    Masuk Shift Kasir
                </button>
                <button @click="activeModal = null" class="px-4 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 2: CHECKOUT & MULTI-PAYMENT                                       -->
    <!-- ======================================================================= -->
    <div x-show="activeModal === 'payment'" class="fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-md w-full p-5 shadow-2xl overflow-hidden" @click.away="activeModal = null">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-slate-100 text-base">Pembayaran Transaksi</h3>
                    <p class="text-xs text-slate-500">Kasir Pemroses: <span class="font-bold text-sky-600 dark:text-sky-400" x-text="currentUser.name"></span></p>
                </div>
                <button @click="activeModal = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">✕</button>
            </div>

            <!-- Total Amount Card -->
            <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 text-center mb-4">
                <span class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Total Tagihan:</span>
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-0.5" x-text="formatMoney(total)"></div>
            </div>

            <!-- Payment Methods -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button @click="paymentMethod = 'cash'" 
                        :class="paymentMethod === 'cash' ? 'border-sky-500 bg-sky-500/10 text-sky-600 dark:text-sky-400 font-bold' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-400'"
                        class="p-3 border rounded-xl text-xs flex flex-col items-center justify-center gap-1.5 transition">
                    <i data-lucide="banknote" class="w-5 h-5"></i>
                    <span>Tunai</span>
                </button>
                <button @click="paymentMethod = 'qris'" 
                        :class="paymentMethod === 'qris' ? 'border-sky-500 bg-sky-500/10 text-sky-600 dark:text-sky-400 font-bold' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-400'"
                        class="p-3 border rounded-xl text-xs flex flex-col items-center justify-center gap-1.5 transition">
                    <i data-lucide="qr-code" class="w-5 h-5"></i>
                    <span>QRIS</span>
                </button>
                <button @click="paymentMethod = 'transfer'" 
                        :class="paymentMethod === 'transfer' ? 'border-sky-500 bg-sky-500/10 text-sky-600 dark:text-sky-400 font-bold' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-400'"
                        class="p-3 border rounded-xl text-xs flex flex-col items-center justify-center gap-1.5 transition">
                    <i data-lucide="arrow-right-left" class="w-5 h-5"></i>
                    <span>Transfer</span>
                </button>
            </div>

            <!-- Cash Input Section -->
            <div x-show="paymentMethod === 'cash'" class="mb-4">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Uang Diterima:</label>
                <input type="number" x-model.number="cashTendered" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xl font-mono font-bold text-slate-900 dark:text-slate-100 focus:border-sky-500 focus:outline-none mb-2">
                
                <!-- Quick Money Buttons -->
                <div class="grid grid-cols-4 gap-1.5 mb-3">
                    <button @click="setCashTendered('exact')" class="px-2 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-xs font-semibold text-slate-800 dark:text-slate-200">Uang Pas</button>
                    <button @click="setCashTendered(20000)" class="px-2 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-xs font-semibold text-slate-800 dark:text-slate-200">20.000</button>
                    <button @click="setCashTendered(50000)" class="px-2 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-xs font-semibold text-slate-800 dark:text-slate-200">50.000</button>
                    <button @click="setCashTendered(100000)" class="px-2 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-xs font-semibold text-slate-800 dark:text-slate-200">100.000</button>
                </div>

                <!-- Kembalian Display -->
                <div class="flex justify-between items-center p-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold">
                    <span class="text-slate-500">Kembalian:</span>
                    <span class="text-base font-black text-emerald-600 dark:text-emerald-400 font-mono" x-text="formatMoney(changeAmount)"></span>
                </div>
            </div>

            <!-- Dynamic QRIS Preview -->
            <div x-show="paymentMethod === 'qris'" class="mb-4 text-center p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl">
                <p class="text-xs text-slate-700 dark:text-slate-300 font-medium mb-3">Pindai QRIS untuk Pembayaran</p>
                <div class="inline-block bg-white p-3 rounded-xl shadow-inner border border-slate-200">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=00020101021126570011ID.NODERAPAY.WWW01189360091100213049020208123456785204541153033605802ID5914NODERA%20POS6007JAKARTA61051234062070703A016304ABCD" 
                         class="w-36 h-36 rounded" alt="QRIS">
                </div>
                <p class="text-[10px] text-slate-400 mt-2">BCA • Mandiri • BRI • BNI • GoPay • OVO • Dana • ShopeePay</p>
            </div>

            <!-- Submit Transaction Button -->
            <button @click="processCheckout()" 
                    :disabled="paymentMethod === 'cash' && cashTendered < total"
                    :class="(paymentMethod !== 'cash' || cashTendered >= total) ? 'bg-sky-500 hover:bg-sky-600 text-white font-bold shadow-lg shadow-sky-500/20' : 'bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-600 cursor-not-allowed'"
                    class="w-full py-3.5 rounded-xl text-xs uppercase tracking-wider transition">
                KONFIRMASI & CETAK STRUK
            </button>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 3: PRATINJAU STRUK THERMAL (ESC/POS)                              -->
    <!-- ======================================================================= -->
    <div x-show="activeModal === 'receipt'" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-sm w-full p-5 shadow-2xl flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2.5 mb-3">
                <h3 class="font-bold text-slate-900 dark:text-slate-100 text-sm">Pratinjau Struk Pembelian</h3>
                <button @click="activeModal = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">✕</button>
            </div>

            <!-- Printable Receipt Area -->
            <div class="flex-1 overflow-y-auto bg-white p-4 border border-slate-200 text-black font-mono text-xs rounded-xl space-y-1.5 shadow-inner" id="thermal-receipt-area">
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
                    <template x-if="lastTransaction?.customer_name">
                        <div class="flex justify-between"><span>Pelanggan:</span><span x-text="lastTransaction?.customer_name"></span></div>
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
                <div class="text-center text-[10px] text-gray-500 pt-1" x-text="settings.receipt_footer"></div>
            </div>

            <!-- Print Actions -->
            <div class="mt-3.5 flex gap-2">
                <button @click="printThermal()" class="flex-1 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs shadow transition flex items-center justify-center gap-1.5">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak Struk Thermal</span>
                </button>
                <button @click="activeModal = null" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-xs transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 4: FORM PRODUK & JASA (CRUD)                                      -->
    <!-- ======================================================================= -->
    <div x-show="activeModal === 'product_form'" class="fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-5 shadow-2xl" @click.away="activeModal = null">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                <h3 class="font-bold text-slate-900 dark:text-slate-100 text-base" x-text="productForm.id ? 'Edit Produk / Layanan' : 'Tambah Produk / Layanan Baru'"></h3>
                <button @click="activeModal = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">✕</button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Produk / Layanan:</label>
                    <input type="text" x-model="productForm.name" placeholder="Contoh: Kopi Susu / Jasa Cuci Motor" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500 font-medium">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori:</label>
                        <select x-model="productForm.category_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500">
                            <option value="">-- Pilih Kategori --</option>
                            <template x-for="c in categories" :key="c.id">
                                <option :value="c.id" x-text="c.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Item:</label>
                        <select x-model="productForm.type" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500 font-semibold">
                            <option value="goods">Barang Fisik (Ada Stok)</option>
                            <option value="service">Jasa / Layanan (Tanpa Stok)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">SKU / Kode Barcode:</label>
                        <input type="text" x-model="productForm.code" placeholder="Scan barcode..." 
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 font-mono focus:outline-none focus:border-sky-500">
                    </div>

                    <div x-show="productForm.type === 'goods'">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Stok Awal:</label>
                        <input type="number" x-model.number="productForm.stock" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 font-mono font-bold focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Modal / Beli (Rp):</label>
                        <input type="number" x-model.number="productForm.cost_price" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 font-mono focus:outline-none focus:border-sky-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Jual (Rp):</label>
                        <input type="number" x-model.number="productForm.sell_price" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 font-mono font-bold text-emerald-600 dark:text-emerald-400 focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <!-- Margin calculation preview -->
                <div class="p-3 bg-slate-100 dark:bg-slate-950 rounded-xl flex items-center justify-between text-xs">
                    <span class="text-slate-500">Estimasi Margin Keuntungan:</span>
                    <span class="font-mono font-bold text-sky-600 dark:text-sky-400" x-text="productMarginPercent + '% per unit'"></span>
                </div>

                <div class="pt-2 flex gap-2">
                    <button @click="saveProduct()" class="flex-1 py-3 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl text-xs transition">
                        Simpan Produk
                    </button>
                    <button @click="activeModal = null" class="px-4 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 5: FORM KATEGORI (CRUD)                                           -->
    <!-- ======================================================================= -->
    <div x-show="activeModal === 'category_form'" class="fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-sm w-full p-5 shadow-2xl" @click.away="activeModal = null">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                <h3 class="font-bold text-slate-900 dark:text-slate-100 text-base" x-text="categoryForm.id ? 'Edit Kategori' : 'Tambah Kategori Baru'"></h3>
                <button @click="activeModal = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">✕</button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Kategori:</label>
                    <input type="text" x-model="categoryForm.name" placeholder="Contoh: Sembako / Minuman / Treatment" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Urutan Tampil (Sort Order):</label>
                    <input type="number" x-model.number="categoryForm.sort_order" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 font-mono focus:outline-none focus:border-sky-500">
                </div>

                <div class="pt-2 flex gap-2">
                    <button @click="saveCategory()" class="flex-1 py-3 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl text-xs transition">
                        Simpan Kategori
                    </button>
                    <button @click="activeModal = null" class="px-4 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 6: FORM PENYESUAIAN STOK (KULAKAN / OPNAME)                      -->
    <!-- ======================================================================= -->
    <div x-show="activeModal === 'stock_adjust'" class="fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-sm w-full p-5 shadow-2xl" @click.away="activeModal = null">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-slate-100 text-base">Penyesuaian Stok Barang</h3>
                    <p class="text-xs text-sky-600 dark:text-sky-400 font-semibold" x-text="stockForm.product_name"></p>
                </div>
                <button @click="activeModal = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">✕</button>
            </div>

            <div class="space-y-3 text-xs">
                <div class="p-3 bg-slate-50 dark:bg-slate-950 rounded-xl flex justify-between items-center">
                    <span class="text-slate-500">Stok Saat Ini:</span>
                    <span class="font-mono font-bold text-base text-slate-900 dark:text-slate-100" x-text="stockForm.current_stock + ' unit'"></span>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Mutasi:</label>
                    <select x-model="stockForm.type" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 font-semibold focus:outline-none focus:border-sky-500">
                        <option value="in">Masuk Stok (Kulakan / Pembelian Baru)</option>
                        <option value="out">Keluar Stok (Barang Rusak / Hilang / Kadaluwarsa)</option>
                        <option value="adjustment">Stok Opname (Setel Total Stok Fisik Nyata)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah (Qty):</label>
                    <input type="number" x-model.number="stockForm.qty" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 font-mono font-bold text-base focus:outline-none focus:border-sky-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan / Alasan Mutasi:</label>
                    <input type="text" x-model="stockForm.notes" placeholder="Contoh: Kulakan dari Supplier / Rusak di gudang" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500">
                </div>

                <div class="pt-2 flex gap-2">
                    <button @click="saveStockAdjustment()" class="flex-1 py-3 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl text-xs transition">
                        Perbarui Stok
                    </button>
                    <button @click="activeModal = null" class="px-4 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 7: FORM AKUN KARYAWAN & KASIR                                     -->
    <!-- ======================================================================= -->
    <div x-show="activeModal === 'user_form'" class="fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-sm w-full p-5 shadow-2xl" @click.away="activeModal = null">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                <h3 class="font-bold text-slate-900 dark:text-slate-100 text-base" x-text="userForm.id ? 'Edit Akun Karyawan' : 'Tambah Akun Karyawan'"></h3>
                <button @click="activeModal = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">✕</button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap:</label>
                    <input type="text" x-model="userForm.name" placeholder="Contoh: Siti Rahma" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500 font-semibold">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Username Login:</label>
                    <input type="text" x-model="userForm.username" placeholder="Contoh: siti" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 font-mono focus:outline-none focus:border-sky-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Peran / Role:</label>
                        <select x-model="userForm.role" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500 font-semibold">
                            <option value="cashier">Kasir (Layar POS)</option>
                            <option value="admin">Admin / Pemilik Toko</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">PIN 4-Digit:</label>
                        <input type="text" x-model="userForm.pin" maxlength="4" placeholder="1234" 
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 font-mono font-bold text-center focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp / HP:</label>
                    <input type="text" x-model="userForm.phone" placeholder="0812xxxx" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-sky-500">
                </div>

                <div class="pt-2 flex gap-2">
                    <button @click="saveUser()" class="flex-1 py-3 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl text-xs transition">
                        Simpan Akun
                    </button>
                    <button @click="activeModal = null" class="px-4 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
