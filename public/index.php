<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>POS Offline Pro - Sistem Kasir Universal UMKM</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='24' fill='%230d9488'/><text x='50%' y='68%' font-size='60' font-weight='900' font-family='sans-serif' fill='white' text-anchor='middle'>P</text></svg>">
    
    <!-- Offline First CSS & Tailwind Standalone Engine -->
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
    <script src="assets/js/tailwindcss.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        teal: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        },
                        slate: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js & Lucide Icons -->
    <script src="assets/js/alpine.min.js" defer></script>
    <script src="assets/js/lucide.min.js"></script>
    <script src="assets/js/pos-app.js"></script>

    <style>
        /* Warm Clean Color Palette Inspired by Video */
        :root {
            --pos-teal: #0d9488;
            --pos-teal-hover: #0f766e;
            --pos-bg: #f8fafc;
        }

        /* Direct Solid Fallbacks for Teal & Gradient */
        .bg-teal-600 { background-color: #0d9488 !important; }
        .bg-teal-700 { background-color: #0f766e !important; }
        .bg-teal-50 { background-color: #f0fdfa !important; }
        .hover\:bg-teal-700:hover { background-color: #0f766e !important; }
        .text-teal-600 { color: #0d9488 !important; }
        .text-teal-700 { color: #0f766e !important; }
        .border-teal-500 { border-color: #14b8a6 !important; }
        .border-teal-600 { border-color: #0d9488 !important; }
        .border-teal-100 { border-color: #ccfbf1 !important; }
        .border-teal-200 { border-color: #99f6e4 !important; }
        .shadow-teal-600\/20 { box-shadow: 0 10px 25px -5px rgba(13, 148, 136, 0.25) !important; }
        .bg-gradient-to-r {
            background-color: #0d9488 !important;
            background-image: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%) !important;
        }

        /* SVG Lucide Fix */
        svg.lucide, i[data-lucide] svg, svg {
            display: inline-block;
            vertical-align: middle;
            stroke-width: 2;
        }
        i[data-lucide] {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Modern Subtle Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Thermal Receipt Print Stylesheet (Presisi Frame 015) */
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

        /* Smart Mobile Landscape Guard (Hanya aktif di HP kecil) */
        @media screen and (orientation: portrait) and (max-width: 640px) {
            #landscape-guard-overlay { display: flex !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased overflow-hidden select-none" x-data="posApp()">

    <!-- ORIENTATION GUARD (Hanya muncul jika HP dipegang tegak saat di terminal kasir) -->
    <div id="landscape-guard-overlay" 
         x-show="currentView === 'pos'"
         class="fixed inset-0 bg-slate-900 z-50 hidden flex-col items-center justify-center p-6 text-center text-white">
        <div class="w-16 h-16 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center mb-4 animate-pulse">
            <i data-lucide="smartphone" class="w-8 h-8 transform rotate-90"></i>
        </div>
        <h2 class="text-xl font-bold tracking-tight mb-2">Putar Layar ke Mode Landscape</h2>
        <p class="text-slate-300 text-xs max-w-xs mb-6">Terminal Kasir dirancang mendatar (Landscape) persis seperti di video untuk kecepatan transaksi kasir.</p>
        <button @click="navigate('dashboard')" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold shadow-lg transition">
            Kembali ke Dashboard
        </button>
    </div>

    <!-- MAIN APP CANVAS -->
    <div class="flex flex-col h-screen w-screen overflow-hidden bg-slate-50">
        
        <!-- =================================================================== -->
        <!-- VIEW 1: DASHBOARD UTAMA (Presisi Frame 008 Video TikTok)            -->
        <!-- =================================================================== -->
        <div x-show="currentView === 'dashboard'" class="flex-1 flex flex-col overflow-y-auto pb-28">
            
            <!-- Dashboard Header -->
            <header class="bg-white border-b border-slate-200/80 px-6 py-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <template x-if="settings.store_logo">
                        <img :src="settings.store_logo" class="w-9 h-9 rounded-xl object-contain bg-white p-0.5 border border-slate-200">
                    </template>
                    <template x-if="!settings.store_logo">
                        <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center font-black text-sm shadow-md shadow-teal-600/20">
                            P
                        </div>
                    </template>
                    <div>
                        <h1 class="font-extrabold text-slate-900 text-base leading-tight" x-text="settings.store_name"></h1>
                        <p class="text-xs text-slate-500" x-text="settings.store_type"></p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="admin/" class="hidden sm:flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                        <i data-lucide="shield" class="w-4 h-4 text-teal-600"></i>
                        <span>Panel Admin</span>
                    </a>
                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-bold text-slate-800" x-text="currentUser.name"></div>
                        <div class="text-[10px] text-teal-600 font-semibold uppercase">Kasir Aktif</div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center font-black text-xs shadow-sm">
                        <span x-text="currentUser.name.charAt(0)"></span>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content Container -->
            <main class="max-w-5xl w-full mx-auto p-5 space-y-5">
                
                <!-- 1. BIG TEAL CTA CARD: "MULAI MENJUAL" (Sesuai Frame 008) -->
                <div @click="navigate('pos')" 
                     class="bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-700 hover:to-teal-600 rounded-3xl p-6 text-white shadow-xl shadow-teal-600/20 cursor-pointer transition transform active:scale-[0.99] flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-bold tracking-wider uppercase mb-1">
                            Terminal Kasir Siap
                        </span>
                        <h2 class="text-2xl font-black tracking-tight">Mulai Menjual</h2>
                        <p class="text-teal-100 text-xs">Buka layar kasir landscape untuk melayani transaksi pelanggan.</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-white backdrop-blur-sm shadow-inner">
                        <i data-lucide="shopping-cart" class="w-7 h-7"></i>
                    </div>
                </div>

                <!-- 2. QUICK METRIC STAT CARDS -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                        <div class="text-[11px] font-semibold text-slate-400">Total Produk</div>
                        <div class="text-2xl font-black text-slate-800 mt-1" x-text="products.length"></div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                        <div class="text-[11px] font-semibold text-slate-400">Transaksi Hari Ini</div>
                        <div class="text-2xl font-black text-slate-800 mt-1" x-text="dashboardData.summary?.total_transactions || 0"></div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                        <div class="text-[11px] font-semibold text-slate-400">Kategori</div>
                        <div class="text-2xl font-black text-slate-800 mt-1" x-text="categories.length"></div>
                    </div>
                </div>

                <!-- 3. FINANCIAL SUMMARY (PENDAPATAN, HPP, LABA KOTOR, MARGIN) -->
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pendapatan Hari Ini</span>
                            <div class="text-3xl font-black text-slate-900 mt-0.5" x-text="formatMoney(dashboardData.summary?.total_turnover)"></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="openExpenseModal()" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition shadow-sm flex items-center gap-1.5">
                                <i data-lucide="minus-circle" class="w-3.5 h-3.5 text-white"></i>
                                <span>+ Catat Kas Keluar</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Modal (HPP):</span>
                            <span class="font-bold text-slate-700" x-text="formatMoney(dashboardData.summary?.total_cost)"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Kas Keluar (Beban):</span>
                            <span class="font-bold text-rose-600" x-text="formatMoney(dashboardData.summary?.total_expenses)"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Laba Bersih Toko:</span>
                            <span class="font-black text-teal-600" x-text="formatMoney(dashboardData.summary?.net_profit)"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Margin:</span>
                            <span class="inline-block mt-0.5 px-2.5 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-black shadow-sm" x-text="(dashboardData.summary?.margin_percent || 0) + '%'"></span>
                        </div>
                    </div>
                </div>

                <!-- 4. TREN PENJUALAN 7 HARI (GRAFIK BATANG SEDERHANA) -->
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 mb-4">Tren 7 Hari Terakhir</h3>
                    <div class="h-32 flex items-end justify-between gap-2 pt-4">
                        <template x-for="d in dashboardData.seven_days || []" :key="d.date">
                            <div class="flex-1 flex flex-col items-center gap-2">
                                <div class="w-full bg-slate-100 rounded-t-lg relative flex items-end justify-center overflow-hidden" style="height: 80px;">
                                    <div class="w-full bg-teal-500 rounded-t-lg transition-all duration-300" 
                                         :style="'height: ' + (d.amount > 0 ? Math.min(100, Math.max(15, (d.amount / 100000) * 80)) : 4) + 'px'"></div>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase" x-text="d.day"></span>
                            </div>
                        </template>
                    </div>
                </div>

            </main>
        </div>

        <!-- =================================================================== -->
        <!-- VIEW 2: TERMINAL KASIR (LANDSCAPE - Presisi Frame 005, 007, 012)     -->
        <!-- =================================================================== -->
        <div x-show="currentView === 'pos'" class="flex-1 flex overflow-hidden">
            
            <!-- LEFT AREA: 65% KATALOG PRODUK -->
            <main class="w-7/12 flex flex-col bg-slate-100/60 p-3 overflow-hidden border-r border-slate-200">
                
                <!-- Category Pills & Search -->
                <div class="mb-3 flex items-center gap-2">
                    <!-- Search Input -->
                    <div class="relative w-44">
                        <input type="text" x-model="searchQuery" placeholder="Cari..." 
                               class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-teal-500 shadow-sm">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2"></i>
                    </div>

                    <!-- Category Pills -->
                    <div class="flex-1 flex items-center gap-1.5 overflow-x-auto py-0.5">
                        <button @click="selectedCategory = null" 
                                :class="selectedCategory === null ? 'bg-teal-600 text-white font-bold shadow-sm' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200'"
                                class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                            Semua
                        </button>
                        <template x-for="c in categories" :key="c.id">
                            <button @click="selectedCategory = c.id" 
                                    :class="selectedCategory === c.id ? 'bg-teal-600 text-white font-bold shadow-sm' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200'"
                                    class="px-2.5 py-1 rounded-xl text-xs whitespace-nowrap transition flex items-center gap-1.5 shrink-0">
                                <template x-if="c.image_url">
                                    <img :src="c.image_url" class="w-4 h-4 rounded-full object-cover">
                                </template>
                                <span x-text="c.name"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- BENTO PRODUCT GRID (Presisi Frame 012) -->
                <div class="flex-1 overflow-y-auto pr-1">
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2.5">
                        <template x-for="p in filteredProducts" :key="p.id">
                            <div @click="handleProductClick(p)" 
                                 class="rounded-2xl border border-slate-200/80 bg-white p-3 hover:border-teal-500 hover:shadow-md cursor-pointer transition flex flex-col justify-between active:scale-[0.98] group shadow-sm text-center">
                                
                                <!-- Product Image Thumbnail -->
                                <div class="w-full h-20 rounded-xl bg-slate-100 mb-2 overflow-hidden flex items-center justify-center">
                                    <template x-if="p.image_url">
                                        <img :src="p.image_url" :alt="p.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    </template>
                                    <template x-if="!p.image_url">
                                        <i data-lucide="package" class="w-8 h-8 text-slate-300"></i>
                                    </template>
                                </div>

                                <!-- Product Title -->
                                <h3 class="font-bold text-slate-800 text-xs leading-snug truncate" x-text="p.name"></h3>

                                <!-- Price in Teal -->
                                <div class="text-xs font-black text-teal-600 mt-1" 
                                     x-text="formatMoney(p.sell_price) + (p.unit === 'kg' ? ' / kg' : '')"></div>

                                <!-- Subtitle / Badge -->
                                <div class="mt-1">
                                    <template x-if="p.has_variants == 1">
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-teal-600 text-white text-[9px] font-extrabold shadow-sm">
                                            <span x-text="p.variants.length"></span> VARIAN
                                        </span>
                                    </template>
                                    <template x-if="p.has_variants != 1 && p.is_scale == 1">
                                        <span class="text-[10px] text-slate-400 font-medium" x-text="'Stok: ' + p.stock + ' ' + p.unit"></span>
                                    </template>
                                    <template x-if="p.has_variants != 1 && p.is_scale != 1 && p.stock >= 900">
                                        <span class="text-[10px] text-slate-400 font-medium">Stok Tanpa Batas</span>
                                    </template>
                                    <template x-if="p.has_variants != 1 && p.is_scale != 1 && p.stock < 900">
                                        <span class="text-[10px] text-slate-400 font-medium" x-text="'Stok: ' + p.stock"></span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </main>

            <!-- RIGHT AREA: 35% KERANJANG BELANJA (Presisi Frame 012) -->
            <aside class="w-5/12 flex flex-col bg-white border-l border-slate-200 shadow-xl overflow-hidden">
                
                <!-- Cart Header -->
                <div class="p-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <button @click="navigate('dashboard')" class="p-1 rounded-lg hover:bg-slate-200 text-slate-500" title="Kembali ke Dashboard">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        </button>
                        <h2 class="font-bold text-slate-800 text-sm">Keranjang</h2>
                        <span class="text-[11px] px-2.5 py-0.5 rounded-full bg-slate-800 text-white font-black tracking-tight" x-text="totalItemsCount"></span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="saveOpenBill()" x-show="!isCartEmpty" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold shadow-sm transition" title="Simpan Tagihan / Meja">
                            Simpan Bill
                        </button>
                        <button @click="clearCart()" x-show="!isCartEmpty" class="text-xs text-rose-500 hover:text-rose-600 font-medium">
                            Hapus
                        </button>
                    </div>
                </div>

                <!-- MEMBER SELECTION STRIP (Presisi & Ergonomis) -->
                <div class="px-3 py-2 bg-slate-100/80 border-b border-slate-200 text-xs">
                    <!-- State 1: Pelanggan Umum / Input Pencarian Cepat -->
                    <div x-show="!selectedCustomer" class="relative">
                        <div class="relative flex items-center">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 pointer-events-none"></i>
                            <input type="text" 
                                   x-ref="cartMemberSearchInput"
                                   x-model="memberSearchQuery" 
                                   @focus="showMemberSearchResults = true"
                                   @input="showMemberSearchResults = true"
                                   @click.stop
                                   placeholder="Pelanggan: Umum (Cari Member / F2)..." 
                                   class="w-full pl-8 pr-7 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-teal-500 transition shadow-sm placeholder:text-slate-400">
                            <button x-show="memberSearchQuery" @click="memberSearchQuery = ''" class="absolute right-2 text-slate-400 hover:text-slate-600 text-xs">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>

                        <!-- Live Search Dropdown -->
                        <div x-show="showMemberSearchResults && filteredMembersList.length > 0" 
                             @click.away="showMemberSearchResults = false"
                             class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-2xl shadow-xl max-h-48 overflow-y-auto z-50 divide-y divide-slate-100 p-1">
                            <template x-for="c in filteredMembersList" :key="c.id">
                                <button type="button" 
                                        @click="selectMember(c)" 
                                        class="w-full p-2 hover:bg-teal-50 rounded-xl text-left transition flex items-center justify-between group">
                                    <div class="truncate pr-2">
                                        <div class="font-bold text-slate-800 text-xs group-hover:text-teal-700 truncate" x-text="c.name"></div>
                                        <div class="text-[10px] text-slate-400 flex items-center gap-1.5">
                                            <span x-text="c.phone || 'Tanpa No HP'"></span>
                                            <span class="text-slate-300">•</span>
                                            <span class="font-semibold text-teal-600" x-text="(c.points || 0) + ' Poin'"></span>
                                        </div>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full font-bold uppercase shrink-0"
                                          :class="{
                                              'bg-amber-100 text-amber-800': c.tier === 'vip',
                                              'bg-indigo-100 text-indigo-800': c.tier === 'wholesale',
                                              'bg-teal-100 text-teal-800': c.tier === 'regular' || !c.tier
                                          }"
                                          x-text="c.tier || 'reguler'">
                                    </span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- State 2: Member Terpilih -->
                    <div x-show="selectedCustomer" class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded-xl border border-teal-200 shadow-sm">
                        <div class="flex items-center gap-2 truncate pr-2">
                            <div class="w-6 h-6 rounded-lg bg-teal-600 text-white flex items-center justify-center shrink-0">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                            </div>
                            <div class="truncate">
                                <div class="font-extrabold text-slate-900 text-xs truncate flex items-center gap-1.5">
                                    <span class="truncate" x-text="selectedCustomer?.name"></span>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded-full font-black uppercase tracking-wider shrink-0"
                                          :class="{
                                              'bg-amber-500 text-white': selectedCustomer?.tier === 'vip',
                                              'bg-indigo-600 text-white': selectedCustomer?.tier === 'wholesale',
                                              'bg-teal-600 text-white': selectedCustomer?.tier === 'regular' || !selectedCustomer?.tier
                                          }"
                                          x-text="selectedCustomer?.tier || 'reguler'">
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-500 flex items-center gap-1.5">
                                    <span x-text="selectedCustomer?.phone || '-'"></span>
                                    <span>•</span>
                                    <span class="font-semibold text-teal-700" x-text="'Saldo: ' + (selectedCustomer?.points || 0) + ' Poin'"></span>
                                </div>
                            </div>
                        </div>

                        <button @click="clearSelectedMember()" class="text-[10px] px-2 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 font-bold transition shrink-0">
                            Batal
                        </button>
                    </div>
                </div>

                <!-- CART ITEMS LIST -->
                <div class="flex-1 overflow-y-auto p-3 divide-y divide-slate-100">
                    <template x-for="(item, idx) in cart" :key="idx">
                        <div class="py-2.5 flex items-center justify-between gap-2 text-xs">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-slate-800 truncate" x-text="item.name"></span>
                                    <template x-if="item.is_wholesale">
                                        <span class="px-1.5 py-0.2 bg-indigo-600 text-white text-[9px] font-black rounded uppercase shrink-0">Grosir</span>
                                    </template>
                                </div>
                                <template x-if="item.variant_name">
                                    <span class="text-[10px] text-teal-600 font-semibold" x-text="'• ' + item.variant_name"></span>
                                </template>
                                <div class="text-[11px] text-slate-400 font-medium tracking-tight" x-text="formatMoney(item.price) + ' / ' + item.unit"></div>
                            </div>

                            <!-- Qty Stepper or Weight Display -->
                            <div class="flex items-center space-x-1 bg-slate-100 rounded-lg p-0.5 border border-slate-200">
                                <button @click="updateCartQty(idx, -1)" class="w-6 h-6 rounded bg-white hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold transition">-</button>
                                <span class="px-1.5 text-center font-black text-slate-800 tracking-tight" x-text="formatQty(item.qty, item.unit)"></span>
                                <button @click="updateCartQty(idx, 1)" class="w-6 h-6 rounded bg-white hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold transition">+</button>
                            </div>

                            <!-- Line Subtotal -->
                            <div class="w-20 text-right font-black text-slate-900 tracking-tight" x-text="formatMoney(item.price * item.qty)"></div>

                            <button @click="removeFromCart(idx)" class="text-slate-300 hover:text-rose-500 p-1">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </template>

                    <div x-show="isCartEmpty" class="h-full flex flex-col items-center justify-center text-slate-400 text-xs">
                        <i data-lucide="shopping-bag" class="w-8 h-8 mb-2 stroke-1"></i>
                        <p>Keranjang masih kosong</p>
                    </div>
                </div>

                <!-- CART FOOTER & CHECKOUT BUTTON -->
                <div class="p-3.5 border-t border-slate-200 bg-slate-50/80 space-y-2.5">
                    <div class="space-y-1 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-800 tracking-tight" x-text="formatMoney(subtotal)"></span>
                        </div>
                        <template x-if="settings.tax_enabled && taxAmount > 0">
                            <div class="flex justify-between text-slate-500">
                                <span>PPN (<span x-text="settings.tax_percent"></span>%)</span>
                                <span class="font-bold text-slate-800 tracking-tight" x-text="formatMoney(taxAmount)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between text-sm font-extrabold text-slate-900 pt-1.5 border-t border-slate-200">
                            <span>Total</span>
                            <span class="text-teal-600 text-xl font-black tracking-tight" x-text="formatMoney(total)"></span>
                        </div>
                    </div>

                    <!-- Big Teal Checkout Button (Presisi Frame 012) -->
                    <button @click="openPayment()" 
                            :disabled="isCartEmpty"
                            :class="!isCartEmpty ? 'bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-600/20' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                            class="w-full py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-2">
                        <span>Lanjutkan ke Pembayaran (F12)</span>
                    </button>
                </div>
            </aside>
        </div>

        <!-- =================================================================== -->
        <!-- VIEW 3: PENGATURAN TOKO (Presisi Frame 009 & 010 Video TikTok)      -->
        <!-- =================================================================== -->
        <div x-show="currentView === 'settings'" class="flex-1 flex flex-col overflow-y-auto pb-28">
            <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <button @click="navigate('dashboard')" class="p-1 rounded-lg hover:bg-slate-100 text-slate-600">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </button>
                    <h2 class="font-extrabold text-slate-900 text-base">Pengaturan Sistem</h2>
                </div>
            </header>

            <main class="max-w-xl w-full mx-auto p-5 space-y-4">
                
                <!-- FEATURE TOGGLES (Presisi Frame 009 & 010) -->
                <div class="bg-white rounded-3xl border border-slate-200 p-4 shadow-sm divide-y divide-slate-100 text-xs">
                    
                    <!-- 1. Fitur Barcode -->
                    <div class="py-3 flex items-center justify-between">
                        <div class="pr-4">
                            <h4 class="font-bold text-slate-800 text-sm">Fitur Barcode</h4>
                            <p class="text-slate-400 text-[11px]">Tampilkan mode scan barcode saat transaksi dan kolom barcode di produk</p>
                        </div>
                        <input type="checkbox" x-model="settings.feature_barcode" :true-value="1" :false-value="0" class="w-5 h-5 text-teal-600 rounded">
                    </div>

                    <!-- 2. Fitur Varian Produk -->
                    <div class="py-3 flex items-center justify-between">
                        <div class="pr-4">
                            <h4 class="font-bold text-slate-800 text-sm">Fitur Varian Produk</h4>
                            <p class="text-slate-400 text-[11px]">Kelola varian pada produk, mis. ukuran S/M/L dengan harga dan stok masing-masing</p>
                        </div>
                        <input type="checkbox" x-model="settings.feature_variants" :true-value="1" :false-value="0" class="w-5 h-5 text-teal-600 rounded">
                    </div>

                    <!-- 3. Fitur Open Bill -->
                    <div class="py-3 flex items-center justify-between">
                        <div class="pr-4">
                            <h4 class="font-bold text-slate-800 text-sm">Fitur Open Bill</h4>
                            <p class="text-slate-400 text-[11px]">Tampilkan fitur bill yang disimpan dan dibayar nanti (kasbon/resto)</p>
                        </div>
                        <input type="checkbox" x-model="settings.feature_open_bill" :true-value="1" :false-value="0" class="w-5 h-5 text-teal-600 rounded">
                    </div>

                    <!-- 4. Fitur Pelanggan -->
                    <div class="py-3 flex items-center justify-between">
                        <div class="pr-4">
                            <h4 class="font-bold text-slate-800 text-sm">Fitur Pelanggan</h4>
                            <p class="text-slate-400 text-[11px]">Tampilkan pemilihan pelanggan saat transaksi dan laporan pelanggan</p>
                        </div>
                        <input type="checkbox" x-model="settings.feature_customer" :true-value="1" :false-value="0" class="w-5 h-5 text-teal-600 rounded">
                    </div>

                    <!-- 5. Cetak Struk Otomatis -->
                    <div class="py-3 flex items-center justify-between">
                        <div class="pr-4">
                            <h4 class="font-bold text-slate-800 text-sm">Cetak Struk Otomatis</h4>
                            <p class="text-slate-400 text-[11px]">Cetak struk otomatis setiap pembayaran berhasil (butuh printer terhubung)</p>
                        </div>
                        <input type="checkbox" x-model="settings.auto_print" :true-value="1" :false-value="0" class="w-5 h-5 text-teal-600 rounded">
                    </div>

                    <!-- 6. Pajak PPN -->
                    <div class="py-3 flex items-center justify-between">
                        <div class="pr-4">
                            <h4 class="font-bold text-slate-800 text-sm">Pajak PPN</h4>
                            <p class="text-slate-400 text-[11px]">Hitung PPN otomatis pada setiap transaksi (mis. PPN 11%)</p>
                        </div>
                        <input type="checkbox" x-model="settings.tax_enabled" :true-value="1" :false-value="0" class="w-5 h-5 text-teal-600 rounded">
                    </div>

                    <!-- 7. Persentase PPN -->
                    <div class="py-3 flex items-center justify-between" x-show="settings.tax_enabled">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Persentase PPN (%)</h4>
                            <p class="text-slate-400 text-[11px]">Persentase pajak yang ditambahkan ke subtotal</p>
                        </div>
                        <input type="number" x-model.number="settings.tax_percent" class="w-20 px-2 py-1 bg-slate-50 border border-slate-300 rounded-lg text-center font-bold">
                    </div>
                </div>

                <!-- PROFILE TOKO -->
                <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-sm space-y-3 text-xs">
                    <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider mb-2">Profil Usaha & Struk</h3>
                    <div>
                        <label class="block text-slate-500 font-semibold mb-1">Nama Toko:</label>
                        <input type="text" x-model="settings.store_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block text-slate-500 font-semibold mb-1">Jenis Toko / Usaha:</label>
                        <input type="text" x-model="settings.store_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-slate-500 font-semibold mb-1">Alamat Toko:</label>
                        <input type="text" x-model="settings.store_address" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-slate-500 font-semibold mb-1">Nomor Telepon:</label>
                        <input type="text" x-model="settings.store_phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-slate-500 font-semibold mb-1">Catatan Footer Struk:</label>
                        <input type="text" x-model="settings.receipt_footer" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <button @click="saveSettings()" class="w-full py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow transition">
                        Simpan Semua Pengaturan
                    </button>
                </div>

            </main>
        </div>

        <!-- =================================================================== -->
        <!-- VIEW 4: OPEN BILLS (TAGIHAN TERSIMPAN)                              -->
        <!-- =================================================================== -->
        <div x-show="currentView === 'open_bills'" class="flex-1 flex flex-col overflow-y-auto pb-28">
            <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <button @click="navigate('dashboard')" class="p-1 rounded-lg hover:bg-slate-100 text-slate-600">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </button>
                    <h2 class="font-extrabold text-slate-900 text-base">Daftar Tagihan Belum Lunas (Open Bill)</h2>
                </div>
            </header>

            <main class="max-w-2xl w-full mx-auto p-5 space-y-3">
                <template x-for="bill in openBills" :key="bill.id">
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 flex items-center justify-between shadow-sm">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm" x-text="bill.bill_name"></h3>
                            <span class="text-xs text-slate-400" x-text="bill.created_at + ' • Kasir: ' + bill.cashier_name"></span>
                            <div class="font-black text-teal-600 text-base tracking-tight mt-1" x-text="formatMoney(bill.total)"></div>
                        </div>
                        <button @click="loadOpenBill(bill)" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs transition">
                            Buka & Lunasi
                        </button>
                    </div>
                </template>
                <div x-show="openBills.length === 0" class="h-48 flex flex-col items-center justify-center text-slate-400 text-xs">
                    <p>Tidak ada tagihan yang tersimpan.</p>
                </div>
            </main>
        </div>

        <!-- =================================================================== -->
        <!-- 4 ICONIC BOTTOM NAVIGATION TABS (Presisi Frame 010 Video TikTok)     -->
        <!-- =================================================================== -->
        <nav class="fixed bottom-0 inset-x-0 sm:bottom-4 sm:inset-x-auto sm:left-1/2 sm:-translate-x-1/2 sm:w-full sm:max-w-md bg-white border border-slate-200/90 sm:rounded-3xl px-6 py-2.5 flex items-center justify-around z-20 shadow-2xl"
             x-show="currentView !== 'pos'">
            
            <button @click="navigate('dashboard')" 
                    :class="currentView === 'dashboard' ? 'text-teal-600 font-bold' : 'text-slate-400 hover:text-slate-600'"
                    class="flex flex-col items-center gap-1 text-[11px] transition">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
                <span>Dashboard</span>
            </button>

            <button @click="navigate('pos')" 
                    class="flex flex-col items-center gap-1 text-[11px] text-teal-600 font-bold transition">
                <div class="w-11 h-11 -mt-6 rounded-full bg-teal-600 text-white flex items-center justify-center shadow-lg shadow-teal-600/30 ring-4 ring-white">
                    <i data-lucide="shopping-cart" class="w-5 h-5 text-white"></i>
                </div>
                <span>Kasir</span>
            </button>

            <button @click="navigate('open_bills')" 
                    :class="currentView === 'open_bills' ? 'text-teal-600 font-bold' : 'text-slate-400 hover:text-slate-600'"
                    class="flex flex-col items-center gap-1 text-[11px] transition relative">
                <i data-lucide="receipt" class="w-5 h-5"></i>
                <span>Open Bill</span>
                <template x-if="openBills.length > 0">
                    <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center" x-text="openBills.length"></span>
                </template>
            </button>

            <button @click="navigate('settings')" 
                    :class="currentView === 'settings' ? 'text-teal-600 font-bold' : 'text-slate-400 hover:text-slate-600'"
                    class="flex flex-col items-center gap-1 text-[11px] transition">
                <i data-lucide="settings" class="w-5 h-5"></i>
                <span>Pengaturan</span>
            </button>
        </nav>

    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 1: VOLUME VS NOMINAL DIALOG (Presisi Frame 005 & 006 Video)       -->
    <!-- ======================================================================= -->
    <div x-show="scaleModal.open" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50" style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl text-center space-y-4" @click.away="scaleModal.open = false">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base" x-text="scaleModal.product?.name"></h3>
                <p class="text-xs text-slate-400" x-text="'Stok tersedia: ' + scaleModal.product?.stock + ' ' + scaleModal.product?.unit"></p>
            </div>

            <!-- Two Tabs: Volume & Nominal -->
            <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1 rounded-2xl">
                <button @click="scaleModal.mode = 'volume'" 
                        :class="scaleModal.mode === 'volume' ? 'bg-teal-600 text-white font-bold shadow' : 'text-slate-600 font-medium'"
                        class="py-2 rounded-xl text-xs transition">
                    Volume
                </button>
                <button @click="scaleModal.mode = 'nominal'" 
                        :class="scaleModal.mode === 'nominal' ? 'bg-teal-600 text-white font-bold shadow' : 'text-slate-600 font-medium'"
                        class="py-2 rounded-xl text-xs transition">
                    Nominal
                </button>
            </div>

            <!-- Mode 1: Input Volume -->
            <div x-show="scaleModal.mode === 'volume'">
                <input type="number" step="0.1" x-model.number="scaleModal.volumeVal" 
                       class="w-full text-center text-2xl font-black tracking-tight py-2 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:border-teal-500">
                <span class="text-xs text-slate-400 block mt-1" x-text="'Satuan: ' + scaleModal.product?.unit"></span>
            </div>

            <!-- Mode 2: Input Nominal Uang (Presisi Frame 006) -->
            <div x-show="scaleModal.mode === 'nominal'">
                <input type="number" step="1000" x-model.number="scaleModal.nominalVal" 
                       class="w-full text-center text-2xl font-black tracking-tight py-2 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:border-teal-500">
                <span class="text-xs text-slate-400 block mt-1">Masukkan nominal rupiah yang dibeli</span>
            </div>

            <!-- Calculation Output (Presisi Frame 006: 0,4 kg x Rp 50.000 = Rp 20.000) -->
            <div class="p-3 bg-teal-50/70 border border-teal-100 rounded-2xl text-xs font-semibold text-teal-800">
                <span x-text="scaleModal.calculatedQty + ' ' + scaleModal.product?.unit + ' x ' + formatMoney(scaleModal.product?.sell_price) + ' = ' + formatMoney(scaleModal.calculatedTotal)"></span>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-2 pt-1">
                <button @click="scaleModal.open = false" class="py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button @click="confirmScaleItem()" class="py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs shadow-md transition">
                    Simpan
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 1B: CATAT KAS KELUAR / PENGELUARAN KASIR                          -->
    <!-- ======================================================================= -->
    <div x-show="expenseModal.open" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50" style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl text-xs space-y-3" @click.away="expenseModal.open = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2 text-rose-600">
                    <i data-lucide="minus-circle" class="w-4 h-4"></i>
                    <span>Catat Kas Keluar Toko</span>
                </h3>
                <button @click="expenseModal.open = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Kategori Beban:</label>
                    <select x-model="expenseModal.form.category" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                        <template x-for="ec in expenseCategories" :key="ec.id">
                            <option :value="ec.name" x-text="ec.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Nominal Uang Keluar (Rp):</label>
                    <input type="number" x-model.number="expenseModal.form.amount" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold tracking-tight text-rose-600 text-base">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Keterangan Catatan:</label>
                    <input type="text" x-model="expenseModal.form.notes" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl" placeholder="misal: Beli es batu & kantong kresek">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button @click="expenseModal.open = false" class="py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button @click="saveExpense()" class="py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs shadow-md transition">
                    Simpan Kas Keluar
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 2: PILIH VARIAN PRODUK (Presisi Frame 007 Video TikTok)          -->
    <!-- ======================================================================= -->
    <div x-show="variantModal.open" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50" style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl space-y-3" @click.away="variantModal.open = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base" x-text="variantModal.product?.name"></h3>
                    <p class="text-xs text-slate-400">Pilih varian untuk ditambahkan ke keranjang</p>
                </div>
                <button @click="variantModal.open = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="space-y-2 max-h-60 overflow-y-auto">
                <template x-for="v in variantModal.product?.variants || []" :key="v.id">
                    <button @click="confirmVariantItem(v)" 
                            class="w-full p-3 rounded-2xl border border-slate-200 hover:border-teal-500 hover:bg-teal-50/50 flex items-center justify-between text-left transition group">
                        <div>
                            <div class="font-bold text-slate-800 text-xs group-hover:text-teal-700" x-text="v.name"></div>
                            <span class="text-[10px] text-slate-400" x-text="'Stok: ' + v.stock"></span>
                        </div>
                        <span class="font-black text-xs text-teal-600 tracking-tight" x-text="formatMoney(v.sell_price)"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 3: SCREEN PEMBAYARAN 3-KOLOM (Presisi Sempurna Frame 014 Video)   -->
    <!-- ======================================================================= -->
    <div x-show="paymentModal" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50" style="display: none;">
        <div class="bg-white rounded-3xl max-w-4xl w-full p-5 shadow-2xl flex flex-col max-h-[92vh] overflow-hidden" @click.away="paymentModal = false">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-teal-600 text-white flex items-center justify-center shadow-md shadow-teal-600/20">
                        <i data-lucide="credit-card" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Pembayaran Transaksi</h3>
                        <p class="text-[11px] text-slate-400">Pilih metode bayar, periksa tagihan, dan cetak struk.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <button type="button" 
                                @click="showModalMemberSearch = !showModalMemberSearch" 
                                class="text-xs px-3 py-1.5 rounded-xl font-bold border flex items-center gap-1.5 hover:bg-slate-100 transition shadow-sm"
                                :class="selectedCustomer ? 'bg-teal-50 border-teal-200 text-teal-800' : 'bg-slate-100 border-slate-200 text-slate-700'">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-teal-600"></i>
                            <span x-text="selectedCustomer ? selectedCustomer.name + ' (' + (selectedCustomer.tier ? selectedCustomer.tier.toUpperCase() : 'REGULER') + ')' : 'Pelanggan Umum'"></span>
                            <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
                        </button>

                        <!-- Dropdown Popover Ganti Member di Modal -->
                        <div x-show="showModalMemberSearch" 
                             @click.away="showModalMemberSearch = false" 
                             class="absolute right-0 mt-1.5 w-64 bg-white border border-slate-200 rounded-2xl shadow-2xl p-2.5 z-50 space-y-2 text-xs" style="display: none;">
                            <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                                <span class="font-bold text-slate-800 text-[11px]">Ganti Member / Pelanggan</span>
                                <template x-if="selectedCustomer">
                                    <button @click="clearSelectedMember(); showModalMemberSearch = false;" class="text-[10px] text-rose-600 font-bold hover:underline">
                                        Batal / Umum
                                    </button>
                                </template>
                            </div>
                            <div class="relative flex items-center">
                                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 pointer-events-none"></i>
                                <input type="text" 
                                       x-model="memberSearchQuery" 
                                       placeholder="Cari nama / no. HP..." 
                                       class="w-full pl-8 pr-2 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div class="max-h-36 overflow-y-auto divide-y divide-slate-100">
                                <template x-for="c in filteredMembersList" :key="c.id">
                                    <button type="button" 
                                            @click="selectMember(c); showModalMemberSearch = false;" 
                                            class="w-full p-1.5 hover:bg-teal-50 rounded-lg text-left transition flex items-center justify-between group">
                                        <div class="truncate pr-1">
                                            <div class="font-bold text-slate-800 text-[11px] group-hover:text-teal-700 truncate" x-text="c.name"></div>
                                            <span class="text-[10px] text-slate-400" x-text="c.phone || '-'"></span>
                                        </div>
                                        <span class="text-[8px] px-1.5 py-0.2 rounded-full font-bold uppercase shrink-0 bg-slate-100 text-slate-600" x-text="c.tier || 'reguler'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <button @click="paymentModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-4 overflow-y-auto">
                
                <!-- KOLOM 1 (KIRI - 4/12): RINGKASAN PESANAN & POIN -->
                <div class="md:col-span-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 flex flex-col justify-between">
                    <div class="space-y-3">
                        <!-- Member Summary Pill if selected -->
                        <div x-show="selectedCustomer" class="bg-teal-50/80 border border-teal-200 rounded-xl p-2.5 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5">
                                        <span x-text="selectedCustomer?.name"></span>
                                        <span class="text-[9px] px-1.5 py-0.2 rounded-full font-black uppercase tracking-wider"
                                              :class="{
                                                  'bg-amber-500 text-white': selectedCustomer?.tier === 'vip',
                                                  'bg-indigo-600 text-white': selectedCustomer?.tier === 'wholesale',
                                                  'bg-teal-600 text-white': selectedCustomer?.tier === 'regular' || !selectedCustomer?.tier
                                              }"
                                              x-text="selectedCustomer?.tier || 'reguler'">
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                        <span x-text="selectedCustomer?.phone || '-'"></span>
                                        <span>•</span>
                                        <span class="font-semibold text-teal-700" x-text="'Saldo: ' + (selectedCustomer?.points || 0) + ' Poin'"></span>
                                    </div>
                                </div>
                                <button @click="clearSelectedMember()" class="text-[10px] text-rose-600 hover:text-rose-700 font-bold hover:underline">
                                    Batal
                                </button>
                            </div>

                            <!-- Loyalty Points Redemption Checkbox -->
                            <template x-if="selectedCustomer && selectedCustomer.points > 0">
                                <label class="flex items-center gap-2 cursor-pointer select-none bg-emerald-50 border border-emerald-200 rounded-xl px-2.5 py-1.5 mt-1">
                                    <input type="checkbox" x-model="usePoints" class="rounded border-emerald-400 text-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5">
                                    <span class="text-[11px] font-bold text-emerald-900" 
                                          x-text="usePoints ? ('Gunakan ' + pointsUsed + ' Poin (Hemat ' + formatMoney(pointsDiscount) + ')') : ('Tukar ' + potentialPointsUsed + ' Poin (Hemat ' + formatMoney(potentialPointsDiscount) + ')')">
                                    </span>
                                </label>
                            </template>
                        </div>

                        <!-- Mini Table Item -->
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-xs mb-1.5 flex items-center justify-between">
                                <span>Pesanan</span>
                                <span class="text-[10px] text-slate-400 font-semibold" x-text="totalItemsCount + ' item'"></span>
                            </h4>
                            
                            <div class="space-y-1.5 text-xs divide-y divide-slate-200/60 max-h-36 overflow-y-auto pr-1">
                                <template x-for="item in cart" :key="item.product_id + (item.variant_name || '')">
                                    <div class="pt-1 flex justify-between items-center">
                                        <div class="truncate pr-2">
                                            <div class="font-bold text-slate-800 truncate text-[11px]" x-text="item.name"></div>
                                            <div class="flex items-center gap-1 text-[10px] text-slate-400">
                                                <span x-text="formatQty(item.qty, item.unit)"></span>
                                                <template x-if="item.is_wholesale">
                                                    <span class="px-1 py-0.2 bg-indigo-600 text-white rounded font-bold text-[8px] uppercase">Grosir</span>
                                                </template>
                                            </div>
                                        </div>
                                        <span class="font-bold tracking-tight text-slate-700 text-[11px]" x-text="formatMoney(item.price * item.qty)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Total Section -->
                    <div class="border-t border-slate-200 pt-2.5 space-y-1 text-xs mt-2">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-800 tracking-tight" x-text="formatMoney(subtotal)"></span>
                        </div>
                        <template x-if="memberDiscountAmount > 0">
                            <div class="flex justify-between text-amber-700 font-semibold">
                                <span>Diskon VIP (<span x-text="selectedCustomer?.discount_percent"></span>%)</span>
                                <span class="font-bold tracking-tight" x-text="'- ' + formatMoney(memberDiscountAmount)"></span>
                            </div>
                        </template>
                        <template x-if="usePoints && pointsDiscount > 0">
                            <div class="flex justify-between text-emerald-700 font-semibold">
                                <span>Diskon Poin (<span x-text="pointsUsed"></span> Poin)</span>
                                <span class="font-bold tracking-tight" x-text="'- ' + formatMoney(pointsDiscount)"></span>
                            </div>
                        </template>
                        <template x-if="settings.tax_enabled && taxAmount > 0">
                            <div class="flex justify-between text-slate-500">
                                <span>PPN (<span x-text="settings.tax_percent"></span>%)</span>
                                <span class="font-bold text-slate-800 tracking-tight" x-text="formatMoney(taxAmount)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between items-baseline pt-1.5 border-t border-slate-200">
                            <span class="font-bold text-slate-700">Total Tagihan:</span>
                            <span class="text-2xl font-black text-teal-600 tracking-tight" x-text="formatMoney(total)"></span>
                        </div>
                    </div>
                </div>

                <!-- KOLOM 2 (TENGAH - 4/12): METODE BAYAR & JUMLAH BAYAR -->
                <div class="md:col-span-4 flex flex-col justify-between space-y-3">
                    <div>
                        <!-- Tabs 4 Metode: Tunai, Digital, Kasbon, Split -->
                        <div class="grid grid-cols-4 gap-1.5 mb-3">
                            <button @click="paymentMethod = 'cash'" 
                                    :class="paymentMethod === 'cash' ? 'bg-teal-600 text-white font-black shadow-md shadow-teal-600/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold'"
                                    class="py-2.5 rounded-xl text-xs flex flex-col items-center justify-center gap-1 transition">
                                <i data-lucide="banknote" class="w-4 h-4"></i>
                                <span>Tunai</span>
                            </button>
                            <button @click="paymentMethod = 'digital'" 
                                    :class="paymentMethod === 'digital' ? 'bg-sky-600 text-white font-black shadow-md shadow-sky-600/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold'"
                                    class="py-2.5 rounded-xl text-xs flex flex-col items-center justify-center gap-1 transition">
                                <i data-lucide="smartphone" class="w-4 h-4"></i>
                                <span>Digital</span>
                            </button>
                            <button @click="paymentMethod = 'debt'" 
                                    :class="paymentMethod === 'debt' ? 'bg-rose-600 text-white font-black shadow-md shadow-rose-600/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold'"
                                    class="py-2.5 rounded-xl text-xs flex flex-col items-center justify-center gap-1 transition">
                                <i data-lucide="notebook-pen" class="w-4 h-4"></i>
                                <span>Kasbon</span>
                            </button>
                            <button @click="paymentMethod = 'split'" 
                                    :class="paymentMethod === 'split' ? 'bg-indigo-600 text-white font-black shadow-md shadow-indigo-600/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold'"
                                    class="py-2.5 rounded-xl text-xs flex flex-col items-center justify-center gap-1 transition">
                                <i data-lucide="git-fork" class="w-4 h-4"></i>
                                <span>Split</span>
                            </button>
                        </div>

                        <!-- 1. VIEW TUNAI (Cash) -->
                        <div x-show="paymentMethod === 'cash'" class="space-y-2.5">
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Uang Diterima (Rp)</span>
                                <div class="text-2xl font-black text-slate-900 tracking-tight mt-0.5" x-text="formatMoney(cashTendered)"></div>
                            </div>

                            <div class="rounded-2xl p-3 border"
                                 :class="cashTendered >= total ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800'">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold uppercase tracking-wider" x-text="cashTendered >= total ? 'Kembalian' : 'Kekurangan'"></span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-black text-white"
                                          :class="cashTendered >= total ? 'bg-emerald-600' : 'bg-rose-600'"
                                          x-text="cashTendered >= total ? 'Lunas' : 'Belum Cukup'"></span>
                                </div>
                                <div class="text-xl font-black tracking-tight mt-1" 
                                     x-text="cashTendered >= total ? formatMoney(changeAmount) : formatMoney(total - cashTendered)"></div>
                            </div>
                        </div>

                        <!-- 2. VIEW DIGITAL (QRIS & TRANSFER BANK) -->
                        <div x-show="paymentMethod === 'digital'" class="space-y-2.5 text-xs">
                            <!-- Sub-toggle Pills -->
                            <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100 rounded-xl">
                                <button @click="digitalSubMethod = 'qris'" 
                                        :class="digitalSubMethod === 'qris' ? 'bg-white text-teal-700 font-black shadow-sm' : 'text-slate-600 font-semibold'"
                                        class="py-1.5 rounded-lg text-xs transition">
                                    QRIS Instan
                                </button>
                                <button @click="digitalSubMethod = 'bank'" 
                                        :class="digitalSubMethod === 'bank' ? 'bg-white text-teal-700 font-black shadow-sm' : 'text-slate-600 font-semibold'"
                                        class="py-1.5 rounded-lg text-xs transition">
                                    Transfer Bank
                                </button>
                            </div>

                            <!-- QRIS Sub-view -->
                            <div x-show="digitalSubMethod === 'qris'" class="text-center p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=00020101021126570011ID.NODERAPAY.WWW01189360091100213049020208123456785204541153033605802ID5914NODERA%20POS6007JAKARTA61051234062070703A016304ABCD" 
                                     class="w-32 h-32 mx-auto rounded-xl shadow-sm border border-slate-200 bg-white p-1" alt="QRIS">
                                <div>
                                    <span class="text-sm font-black text-slate-800" x-text="formatMoney(total)"></span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">Mendukung BCA, Mandiri, BRI, GoPay, OVO, ShopeePay, DANA</span>
                                </div>
                            </div>

                            <!-- Transfer Bank Sub-view -->
                            <div x-show="digitalSubMethod === 'bank'" class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2.5">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-semibold block uppercase">Rekening Tujuan Toko</span>
                                    <div class="font-extrabold text-slate-900 text-sm mt-0.5" x-text="settings.bank_name || 'Bank BCA'"></div>
                                    <div class="text-lg font-black text-teal-700 tracking-wider mt-0.5" x-text="settings.bank_account_no || '1234-5678-90'"></div>
                                    <div class="text-[11px] text-slate-500 font-medium" x-text="'a.n ' + (settings.bank_account_holder || settings.store_name || 'Toko POS Pro')"></div>
                                </div>
                                <button @click="copyBankNo()" class="w-full py-2 bg-teal-50 hover:bg-teal-100 text-teal-800 font-bold rounded-xl border border-teal-200 text-xs flex items-center justify-center gap-1.5 transition">
                                    <i data-lucide="copy" class="w-3.5 h-3.5 text-teal-700"></i>
                                    <span>Salin No. Rekening</span>
                                </button>
                            </div>
                        </div>

                        <!-- 3. VIEW KASBON (TEMPO) -->
                        <div x-show="paymentMethod === 'debt'" class="space-y-2.5 text-xs">
                            <!-- Warning & Interactive Search if non-member -->
                            <div x-show="!selectedCustomerId" class="bg-rose-50 border border-rose-200 rounded-2xl p-3.5 space-y-2.5">
                                <div class="flex items-center gap-1.5 text-rose-700 font-bold">
                                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                                    <span>Pilih Member untuk Kasbon</span>
                                </div>
                                <p class="text-[11px] text-rose-600 leading-relaxed">
                                    Fasilitas Kasbon (Tempo) wajib memilih member terdaftar. Cari nama member di bawah ini:
                                </p>
                                
                                <div class="relative">
                                    <div class="relative flex items-center">
                                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 pointer-events-none"></i>
                                        <input type="text" 
                                               x-model="kasbonMemberSearchQuery" 
                                               @focus="showKasbonSearchResults = true"
                                               @input="showKasbonSearchResults = true"
                                               @click.stop
                                               placeholder="Ketik nama member / no. HP..." 
                                               class="w-full pl-8 pr-7 py-2 bg-white border border-rose-300 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-rose-500 shadow-sm">
                                        <button x-show="kasbonMemberSearchQuery" @click="kasbonMemberSearchQuery = ''" class="absolute right-2 text-slate-400 hover:text-slate-600 text-xs">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>

                                    <!-- Live Dropdown Results in Kasbon Tab -->
                                    <div x-show="showKasbonSearchResults && filteredKasbonMembersList.length > 0" 
                                         @click.away="showKasbonSearchResults = false"
                                         class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-40 overflow-y-auto z-50 divide-y divide-slate-100 p-1">
                                        <template x-for="c in filteredKasbonMembersList" :key="c.id">
                                            <button type="button" 
                                                    @click="selectMember(c); showKasbonSearchResults = false; kasbonMemberSearchQuery = '';" 
                                                    class="w-full p-2 hover:bg-rose-50 rounded-lg text-left transition flex items-center justify-between group">
                                                <div class="truncate pr-1">
                                                    <div class="font-bold text-slate-800 text-xs group-hover:text-rose-700 truncate" x-text="c.name"></div>
                                                    <div class="text-[10px] text-slate-400 flex items-center gap-1.5">
                                                        <span x-text="c.phone || 'Tanpa No HP'"></span>
                                                        <span class="text-slate-300">•</span>
                                                        <span class="text-rose-600 font-semibold" x-text="'Hutang: ' + formatMoney(c.total_debt)"></span>
                                                    </div>
                                                </div>
                                                <span class="text-[8px] px-1.5 py-0.2 rounded-full font-bold uppercase shrink-0 bg-slate-100 text-slate-600" x-text="c.tier || 'reguler'"></span>
                                            </button>
                                        </template>
                                    </div>
                                    <div x-show="showKasbonSearchResults && kasbonMemberSearchQuery && filteredKasbonMembersList.length === 0" 
                                         class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg p-2 text-center text-xs text-rose-500 z-50">
                                        Member tidak ditemukan
                                    </div>
                                </div>
                            </div>

                            <!-- Member Kasbon Details if selected -->
                            <div x-show="selectedCustomerId" class="bg-rose-50/70 border border-rose-200 rounded-2xl p-3.5 space-y-2">
                                <div class="flex items-center justify-between border-b border-rose-200/60 pb-2">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-slate-900 text-xs block" x-text="selectedCustomer?.name"></span>
                                            <button @click="clearSelectedMember()" class="text-[10px] text-rose-600 hover:text-rose-700 font-bold hover:underline">
                                                (Ganti)
                                            </button>
                                        </div>
                                        <span class="text-[10px] text-slate-500" x-text="selectedCustomer?.phone || 'Tanpa No HP'"></span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black text-white"
                                          :class="(parseFloat(selectedCustomer?.total_debt || 0) + total <= (parseFloat(selectedCustomer?.debt_limit) || 500000)) ? 'bg-emerald-600' : 'bg-amber-500'"
                                          x-text="(parseFloat(selectedCustomer?.total_debt || 0) + total <= (parseFloat(selectedCustomer?.debt_limit) || 500000)) ? 'Plafon Aman' : 'Melebihi Limit'">
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-[11px]">
                                    <div>
                                        <span class="text-slate-500 block text-[10px]">Plafon Limit:</span>
                                        <span class="font-bold text-slate-800" x-text="formatMoney(selectedCustomer?.debt_limit || 500000)"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 block text-[10px]">Hutang Berjalan:</span>
                                        <span class="font-bold text-rose-600" x-text="formatMoney(selectedCustomer?.total_debt || 0)"></span>
                                    </div>
                                    <div class="col-span-2 pt-1 border-t border-rose-200/60 flex justify-between items-center">
                                        <span class="text-slate-600 font-semibold">Total Setelah Ini:</span>
                                        <span class="font-black text-rose-700 text-xs" x-text="formatMoney((parseFloat(selectedCustomer?.total_debt) || 0) + total)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. VIEW SPLIT (CAMPURAN) -->
                        <div x-show="paymentMethod === 'split'" class="bg-indigo-50/70 border border-indigo-200 rounded-2xl p-3.5 space-y-2.5 text-xs">
                            <span class="text-[10px] text-indigo-700 font-bold block uppercase tracking-wider">Kombinasi Pembayaran:</span>
                            <div>
                                <label class="text-[10px] text-slate-600 font-semibold block mb-0.5">Uang Tunai (Rp):</label>
                                <input type="number" x-model.number="splitCash" class="w-full px-2.5 py-1.5 bg-white border border-indigo-200 rounded-xl font-bold tracking-tight text-slate-900">
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-600 font-semibold block mb-0.5">Non-Tunai / QRIS (Rp):</label>
                                <input type="number" x-model.number="splitNonCash" class="w-full px-2.5 py-1.5 bg-white border border-indigo-200 rounded-xl font-bold tracking-tight text-slate-900">
                            </div>
                            <div class="flex justify-between items-center pt-1 border-t border-indigo-200/80">
                                <span class="text-[11px] font-semibold text-slate-600">Total Diterima:</span>
                                <span class="font-black text-xs" :class="(splitCash + splitNonCash >= total) ? 'text-emerald-600' : 'text-rose-600'" 
                                      x-text="formatMoney(splitCash + splitNonCash) + ' / ' + formatMoney(total)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Konfirmasi & Cetak Struk (Sesuai Frame 014) -->
                    <button @click="processCheckout()" 
                            :disabled="(paymentMethod === 'cash' && cashTendered < total) || (paymentMethod === 'debt' && !selectedCustomerId) || (paymentMethod === 'split' && (splitCash + splitNonCash) < total)"
                            :class="((paymentMethod !== 'cash' || cashTendered >= total) && (paymentMethod !== 'debt' || selectedCustomerId) && (paymentMethod !== 'split' || (splitCash + splitNonCash) >= total)) ? 'bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-600/20' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                            class="w-full py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Konfirmasi & Cetak Struk</span>
                    </button>
                </div>

                <!-- KOLOM 3 (KANAN - 4/12): NUMPAD & TOMBOL NOMINAL CEPAT -->
                <div class="md:col-span-4 flex flex-col justify-between">
                    
                    <div x-show="paymentMethod === 'cash'">
                        <!-- Quick Money Pills (Presisi Frame 014: 100rb, 50rb, 20rb, 10rb, 5rb) -->
                        <div class="grid grid-cols-3 gap-1.5 mb-2 text-xs font-bold">
                            <button @click="setQuickCash(100000)" class="py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700">100rb</button>
                            <button @click="setQuickCash(50000)" class="py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700">50rb</button>
                            <button @click="setQuickCash(20000)" class="py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700">20rb</button>
                        </div>
                        <div class="grid grid-cols-2 gap-1.5 mb-3 text-xs font-bold">
                            <button @click="setQuickCash(10000)" class="py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700">10rb</button>
                            <button @click="setQuickCash(5000)" class="py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700">5rb</button>
                        </div>

                        <!-- Numpad Grid (1-9, C, 0, Backspace) -->
                        <div class="grid grid-cols-3 gap-2 text-lg font-black tracking-tight mb-2">
                            <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="n">
                                <button @click="pressNumpad(n)" class="h-11 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 transition active:scale-95 shadow-sm" x-text="n"></button>
                            </template>
                            <button @click="clearNumpad()" class="h-11 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black shadow-sm transition">C</button>
                            <button @click="pressNumpad(0)" class="h-11 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 transition active:scale-95 shadow-sm">0</button>
                            <button @click="backspaceNumpad()" class="h-11 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                                <i data-lucide="delete" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <!-- Tombol Uang Pas (Presisi Frame 014) -->
                        <button @click="setQuickCash('exact')" class="w-full py-2.5 rounded-xl border border-teal-600 text-teal-600 hover:bg-teal-50 font-bold text-xs transition">
                            Uang Pas
                        </button>
                    </div>

                    <!-- Shortcut Guide for Non-Cash Methods -->
                    <div x-show="paymentMethod !== 'cash'" class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-3 text-xs h-full flex flex-col justify-center">
                        <div class="text-center space-y-1">
                            <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 mx-auto flex items-center justify-center font-bold">
                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                            </div>
                            <span class="font-bold text-slate-800 block text-sm">Pembayaran Non-Tunai</span>
                            <p class="text-[11px] text-slate-400">Pastikan dana telah berhasil diterima sebelum menekan tombol konfirmasi struk.</p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-200 space-y-1.5 text-[11px]">
                            <div class="flex justify-between text-slate-600">
                                <span>Status Verifikasi:</span>
                                <span class="font-bold text-teal-600">Otomatis / Siap</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Pencatatan Finansial:</span>
                                <span class="font-bold text-slate-800">Buku Kas & Riwayat</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 4: STRUK THERMAL 58MM (Presisi Sempurna Frame 015 Video TikTok)   -->
    <!-- ======================================================================= -->
    <div x-show="receiptModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50" style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl flex flex-col max-h-[92vh]">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-3">
                <h3 class="font-bold text-slate-900 text-sm">Transaksi Selesai</h3>
                <button @click="receiptModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Struk Thermal Kertas Putih (Sesuai Frame 015) -->
            <div class="flex-1 overflow-y-auto bg-white p-4 border border-slate-200 text-black font-mono text-xs rounded-xl space-y-1.5 shadow-inner" id="thermal-receipt-area">
                <div class="text-center space-y-0.5">
                    <template x-if="settings.store_logo && settings.print_logo_receipt == 1">
                        <div class="flex justify-center mb-1">
                            <img :src="settings.store_logo" class="max-h-12 max-w-[120px] object-contain filter grayscale contrast-200">
                        </div>
                    </template>
                    <div class="font-extrabold text-sm" x-text="settings.store_name"></div>
                    <div class="text-[10px] text-gray-600" x-text="settings.store_type"></div>
                    <div class="text-[10px] text-gray-600" x-text="settings.store_address"></div>
                    <div class="text-[10px] text-gray-600" x-text="settings.store_phone"></div>
                </div>
                
                <div class="border-t border-dashed border-gray-400 my-2"></div>
                
                <div class="text-[10px] text-center">
                    <div class="font-bold" x-text="lastTransaction?.invoice_no"></div>
                    <div x-text="lastTransaction?.created_at"></div>
                </div>

                <template x-if="lastTransaction?.customer_name && lastTransaction?.customer_name !== 'Pelanggan Umum'">
                    <div class="text-[10px] text-gray-700 flex justify-between border-t border-dashed border-gray-300 pt-1 mt-1">
                        <span>Pelanggan:</span>
                        <span class="font-bold" x-text="lastTransaction?.customer_name + ' (' + (lastTransaction?.customer_tier ? lastTransaction?.customer_tier.toUpperCase() : 'MEMBER') + ')'"></span>
                    </div>
                </template>
                
                <div class="border-t border-dashed border-gray-400 my-2"></div>

                <!-- Items -->
                <div class="space-y-1 text-[11px]">
                    <template x-for="item in lastTransaction?.items || []" :key="item.product_id + (item.variant_name || '')">
                        <div>
                            <div class="font-bold" x-text="item.name + (item.variant_name ? ' (' + item.variant_name + ')' : '')"></div>
                            <div class="flex justify-between text-gray-700">
                                <span x-text="formatMoney(item.price) + (item.unit === 'kg' ? '/kg' : '')"></span>
                                <span class="font-bold" x-text="formatQty(item.qty, item.unit)"></span>
                                <span class="font-bold" x-text="formatMoney(item.price * item.qty)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

                <!-- Totals -->
                <div class="space-y-0.5 text-xs">
                    <div class="flex justify-between"><span>Subtotal</span><span x-text="formatMoney(lastTransaction?.subtotal)"></span></div>
                    <template x-if="lastTransaction?.points_discount > 0">
                        <div class="flex justify-between text-gray-700">
                            <span>Diskon Poin (<span x-text="lastTransaction?.points_used"></span> Poin)</span>
                            <span class="font-bold" x-text="'- ' + formatMoney(lastTransaction?.points_discount)"></span>
                        </div>
                    </template>
                    <template x-if="lastTransaction?.tax > 0">
                        <div class="flex justify-between"><span>PPN 11%</span><span x-text="formatMoney(lastTransaction?.tax)"></span></div>
                    </template>
                    <div class="flex justify-between font-extrabold text-sm pt-1 border-t border-gray-300">
                        <span>Total</span><span x-text="formatMoney(lastTransaction?.total)"></span>
                    </div>
                    <div class="flex justify-between text-[11px]"><span>Metode</span><span class="uppercase font-bold" x-text="lastTransaction?.payment_method || 'Tunai'"></span></div>
                    <div class="flex justify-between text-[11px]"><span>Bayar</span><span x-text="formatMoney(lastTransaction?.amount_paid)"></span></div>
                    <div class="flex justify-between text-[11px]"><span>Kembalian</span><span x-text="formatMoney(lastTransaction?.change_amount)"></span></div>
                </div>

                <template x-if="lastTransaction?.points_earned > 0">
                    <div class="border-t border-dashed border-gray-400 my-1 pt-1 text-[10px] text-center font-bold text-gray-800">
                        Bonus Poin: +<span x-text="lastTransaction?.points_earned"></span> Poin Loyalty
                    </div>
                </template>

                <div class="border-t border-dashed border-gray-400 my-2"></div>
                
                <div class="text-center text-[10px] text-gray-600 pt-1" x-text="settings.receipt_footer"></div>
            </div>

            <div class="mt-3.5 flex gap-2">
                <button @click="printReceipt()" class="flex-1 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs shadow transition flex items-center justify-center gap-1.5">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak Struk Thermal</span>
                </button>
                <button @click="receiptModal = false" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        // Pastikan Lucide Icons selalu dirender saat DOM siap
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
        window.addEventListener('load', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
