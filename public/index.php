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
                    <button @click="openExpenseModal()" class="flex items-center gap-1.5 px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition shadow-sm">
                        <i data-lucide="minus-circle" class="w-4 h-4 text-rose-600"></i>
                        <span>Kas Keluar</span>
                    </button>
                    <a href="admin/" class="hidden sm:flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                        <i data-lucide="shield" class="w-4 h-4 text-teal-600"></i>
                        <span>Panel Admin</span>
                    </a>
                    <button @click="navigate('pos')" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-md shadow-teal-600/20 transition">
                        <i data-lucide="monitor" class="w-4 h-4"></i>
                        <span>Buka Layar Kasir</span>
                    </button>
                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-bold text-slate-800" x-text="currentUser.name"></div>
                        <div class="text-[10px] text-teal-600 font-semibold uppercase">Kasir Aktif</div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-xs border border-teal-200">
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
                            <button @click="openExpenseModal()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition border border-rose-200 flex items-center gap-1">
                                <i data-lucide="minus-circle" class="w-3.5 h-3.5"></i>
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
                            <span class="font-bold text-emerald-600" x-text="(dashboardData.summary?.margin_percent || 0) + '%'"></span>
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
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-teal-50 text-teal-700 text-[9px] font-bold border border-teal-100">
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
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 font-bold font-mono" x-text="totalItemsCount"></span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="openExpenseModal()" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-semibold transition flex items-center gap-1" title="Catat Pengeluaran Kas Kecil">
                            <i data-lucide="minus-circle" class="w-3.5 h-3.5"></i>
                            <span>Kas Keluar</span>
                        </button>
                        <button @click="saveOpenBill()" x-show="!isCartEmpty" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-xs font-semibold transition" title="Simpan Tagihan / Meja">
                            Simpan Bill
                        </button>
                        <button @click="clearCart()" x-show="!isCartEmpty" class="text-xs text-rose-500 hover:text-rose-600 font-medium">
                            Hapus
                        </button>
                    </div>
                </div>

                <!-- CART ITEMS LIST -->
                <div class="flex-1 overflow-y-auto p-3 divide-y divide-slate-100">
                    <template x-for="(item, idx) in cart" :key="idx">
                        <div class="py-2.5 flex items-center justify-between gap-2 text-xs">
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-slate-800 truncate" x-text="item.name"></div>
                                <template x-if="item.variant_name">
                                    <span class="text-[10px] text-teal-600 font-semibold" x-text="'• ' + item.variant_name"></span>
                                </template>
                                <div class="text-[11px] text-slate-400 font-mono" x-text="formatMoney(item.price) + ' / ' + item.unit"></div>
                            </div>

                            <!-- Qty Stepper or Weight Display -->
                            <div class="flex items-center space-x-1 bg-slate-100 rounded-lg p-0.5 border border-slate-200">
                                <button @click="updateCartQty(idx, -1)" class="w-6 h-6 rounded bg-white hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold transition">-</button>
                                <span class="px-1.5 text-center font-bold text-slate-800 font-mono" x-text="formatQty(item.qty, item.unit)"></span>
                                <button @click="updateCartQty(idx, 1)" class="w-6 h-6 rounded bg-white hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold transition">+</button>
                            </div>

                            <!-- Line Subtotal -->
                            <div class="w-16 text-right font-black text-slate-900 font-mono" x-text="formatMoney(item.price * item.qty)"></div>

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
                <div class="p-3.5 border-t border-slate-200 bg-slate-50/80 space-y-2">
                    <div class="space-y-1 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-800 font-mono" x-text="formatMoney(subtotal)"></span>
                        </div>
                        <template x-if="settings.tax_enabled && taxAmount > 0">
                            <div class="flex justify-between text-slate-500">
                                <span>PPN (<span x-text="settings.tax_percent"></span>%)</span>
                                <span class="font-mono text-slate-800" x-text="formatMoney(taxAmount)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between text-sm font-extrabold text-slate-900 pt-1.5 border-t border-slate-200">
                            <span>Total</span>
                            <span class="text-teal-600 text-lg font-black font-mono" x-text="formatMoney(total)"></span>
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
                            <div class="font-black text-teal-600 text-sm font-mono mt-1" x-text="formatMoney(bill.total)"></div>
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
                       class="w-full text-center text-2xl font-black font-mono py-2 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:border-teal-500">
                <span class="text-xs text-slate-400 block mt-1" x-text="'Satuan: ' + scaleModal.product?.unit"></span>
            </div>

            <!-- Mode 2: Input Nominal Uang (Presisi Frame 006) -->
            <div x-show="scaleModal.mode === 'nominal'">
                <input type="number" step="1000" x-model.number="scaleModal.nominalVal" 
                       class="w-full text-center text-2xl font-black font-mono py-2 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:border-teal-500">
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
                <button @click="expenseModal.open = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Kategori Beban:</label>
                    <select x-model="expenseModal.form.category" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        <option value="Operasional Toko">Operasional Toko (Kresek, Sabun, Galon)</option>
                        <option value="Listrik & Internet">Listrik, Air & Internet</option>
                        <option value="Bahan Baku">Bahan Baku / Belanja Harian</option>
                        <option value="Gaji / Uang Makan">Uang Makan / Gaji Harian</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Nominal Uang Keluar (Rp):</label>
                    <input type="number" x-model.number="expenseModal.form.amount" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono font-bold text-rose-600 text-base">
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
                <button @click="variantModal.open = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <div class="space-y-2 max-h-60 overflow-y-auto">
                <template x-for="v in variantModal.product?.variants || []" :key="v.id">
                    <button @click="confirmVariantItem(v)" 
                            class="w-full p-3 rounded-2xl border border-slate-200 hover:border-teal-500 hover:bg-teal-50/50 flex items-center justify-between text-left transition group">
                        <div>
                            <div class="font-bold text-slate-800 text-xs group-hover:text-teal-700" x-text="v.name"></div>
                            <span class="text-[10px] text-slate-400" x-text="'Stok: ' + v.stock"></span>
                        </div>
                        <span class="font-black text-xs text-teal-600 font-mono" x-text="formatMoney(v.sell_price)"></span>
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
            
            <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-4 overflow-y-auto">
                
                <!-- KOLOM 1 (KIRI - 4/12): RINGKASAN PESANAN -->
                <div class="md:col-span-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 flex flex-col justify-between">
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-sm mb-3">Pesanan</h4>
                        
                        <!-- Table Mini Item -->
                        <div class="space-y-2 text-xs divide-y divide-slate-200/60 max-h-48 overflow-y-auto pr-1">
                            <template x-for="item in cart" :key="item.product_id + (item.variant_name || '')">
                                <div class="pt-1.5 flex justify-between items-center">
                                    <div class="truncate pr-2">
                                        <div class="font-bold text-slate-800 truncate" x-text="item.name"></div>
                                        <span class="text-[10px] text-slate-400" x-text="formatQty(item.qty, item.unit)"></span>
                                    </div>
                                    <span class="font-bold font-mono text-slate-700" x-text="formatMoney(item.price * item.qty)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Total Section -->
                    <div class="border-t border-slate-200 pt-3 space-y-1 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span class="font-mono font-semibold" x-text="formatMoney(subtotal)"></span>
                        </div>
                        <template x-if="settings.tax_enabled && taxAmount > 0">
                            <div class="flex justify-between text-slate-500">
                                <span>PPN (<span x-text="settings.tax_percent"></span>%)</span>
                                <span class="font-mono" x-text="formatMoney(taxAmount)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between items-baseline pt-1">
                            <span class="font-bold text-slate-700">Total:</span>
                            <span class="text-xl font-black text-emerald-600 font-mono" x-text="formatMoney(total)"></span>
                        </div>
                    </div>
                </div>

                <!-- KOLOM 2 (TENGAH - 4/12): METODE BAYAR & JUMLAH BAYAR -->
                <div class="md:col-span-4 flex flex-col justify-between space-y-3">
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-sm mb-3">Pembayaran</h4>
                        
                        <!-- Tabs Tunai, QRIS, Kasbon, Split -->
                        <div class="grid grid-cols-4 gap-1.5 mb-3">
                            <button @click="paymentMethod = 'cash'" 
                                    :class="paymentMethod === 'cash' ? 'bg-teal-600 text-white font-bold shadow' : 'bg-slate-100 text-slate-600'"
                                    class="py-2 rounded-xl text-xs transition">
                                Tunai
                            </button>
                            <button @click="paymentMethod = 'qris'" 
                                    :class="paymentMethod === 'qris' ? 'bg-teal-600 text-white font-bold shadow' : 'bg-slate-100 text-slate-600'"
                                    class="py-2 rounded-xl text-xs transition">
                                QRIS
                            </button>
                            <button @click="paymentMethod = 'debt'" 
                                    :class="paymentMethod === 'debt' ? 'bg-rose-600 text-white font-bold shadow' : 'bg-slate-100 text-slate-600'"
                                    class="py-2 rounded-xl text-xs transition">
                                Kasbon
                            </button>
                            <button @click="paymentMethod = 'split'" 
                                    :class="paymentMethod === 'split' ? 'bg-indigo-600 text-white font-bold shadow' : 'bg-slate-100 text-slate-600'"
                                    class="py-2 rounded-xl text-xs transition">
                                Split
                            </button>
                        </div>

                        <!-- Card Input Jumlah Bayar (Sesuai Frame 014) -->
                        <div x-show="paymentMethod === 'cash'" class="bg-slate-50 border border-slate-200 rounded-2xl p-3 mb-3">
                            <span class="text-[10px] text-slate-400 font-semibold block">Jumlah Bayar</span>
                            <div class="text-2xl font-black text-slate-900 font-mono mt-0.5" x-text="formatMoney(cashTendered)"></div>
                        </div>

                        <!-- Card Kembalian (Sesuai Frame 014 - Warna Hijau) -->
                        <div x-show="paymentMethod === 'cash'" class="bg-teal-50 border border-teal-100 rounded-2xl p-3">
                            <span class="text-[10px] text-teal-700 font-semibold block">Kembalian</span>
                            <div class="text-xl font-black text-teal-700 font-mono mt-0.5" x-text="formatMoney(changeAmount)"></div>
                        </div>

                        <!-- Kasbon Member Selector -->
                        <div x-show="paymentMethod === 'debt'" class="bg-rose-50 border border-rose-200 rounded-2xl p-3 mb-3 space-y-2 text-xs">
                            <span class="text-[10px] text-rose-700 font-bold block uppercase tracking-wider">Pilih Pelanggan / Member:</span>
                            <select x-model="selectedCustomerId" class="w-full px-2.5 py-1.5 bg-white border border-rose-200 rounded-xl font-bold text-slate-800">
                                <option value="">-- Pilih Pelanggan --</option>
                                <template x-for="c in customers" :key="c.id">
                                    <option :value="c.id" x-text="c.name + ' (Hutang: ' + formatMoney(c.total_debt) + ')'"></option>
                                </template>
                            </select>
                            <span class="text-[10px] text-slate-500 block">Transaksi ini akan otomatis dicatat ke Buku Kasbon pelanggan.</span>
                        </div>

                        <!-- Split Payment Details -->
                        <div x-show="paymentMethod === 'split'" class="bg-indigo-50 border border-indigo-200 rounded-2xl p-3 mb-3 space-y-2 text-xs">
                            <span class="text-[10px] text-indigo-700 font-bold block uppercase tracking-wider">Split Payment (Campuran):</span>
                            <div>
                                <label class="text-[10px] text-slate-600 font-semibold">Tunai (Rp):</label>
                                <input type="number" x-model.number="splitCash" class="w-full px-2 py-1 bg-white border border-indigo-200 rounded-lg font-mono font-bold">
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-600 font-semibold">Non-Tunai / QRIS (Rp):</label>
                                <input type="number" x-model.number="splitNonCash" class="w-full px-2 py-1 bg-white border border-indigo-200 rounded-lg font-mono font-bold">
                            </div>
                            <div class="text-[10px] font-bold" :class="(splitCash + splitNonCash >= total) ? 'text-emerald-600' : 'text-rose-500'" x-text="'Total Bayar: ' + formatMoney(splitCash + splitNonCash) + ' / ' + formatMoney(total)"></div>
                        </div>

                        <!-- QRIS Dynamic Preview -->
                        <div x-show="paymentMethod === 'qris'" class="text-center p-3 bg-slate-50 rounded-2xl border border-slate-200">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=00020101021126570011ID.NODERAPAY.WWW01189360091100213049020208123456785204541153033605802ID5914NODERA%20POS6007JAKARTA61051234062070703A016304ABCD" 
                                 class="w-32 h-32 mx-auto rounded-lg shadow-sm" alt="QRIS">
                            <span class="text-[10px] text-slate-400 mt-2 block">Pindai QRIS untuk bayar otomatis</span>
                        </div>
                    </div>

                    <!-- Tombol Konfirmasi & Cetak Struk (Sesuai Frame 014) -->
                    <button @click="processCheckout()" 
                            :disabled="paymentMethod === 'cash' && cashTendered < total"
                            :class="(paymentMethod !== 'cash' || cashTendered >= total) ? 'bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-600/20' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                            class="w-full py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider transition">
                        Konfirmasi & Cetak Struk
                    </button>
                </div>

                <!-- KOLOM 3 (KANAN - 4/12): NUMPAD & TOMBOL NOMINAL CEPAT -->
                <div class="md:col-span-4 flex flex-col justify-between">
                    
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
                    <div class="grid grid-cols-3 gap-2 font-mono text-base font-bold mb-2">
                        <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="n">
                            <button @click="pressNumpad(n)" class="h-11 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 transition active:scale-95 shadow-sm" x-text="n"></button>
                        </template>
                        <button @click="clearNumpad()" class="h-11 rounded-xl bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition">C</button>
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
                <button @click="receiptModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
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
                    <template x-if="lastTransaction?.tax > 0">
                        <div class="flex justify-between"><span>PPN 11%</span><span x-text="formatMoney(lastTransaction?.tax)"></span></div>
                    </template>
                    <div class="flex justify-between font-extrabold text-sm pt-1 border-t border-gray-300">
                        <span>Total</span><span x-text="formatMoney(lastTransaction?.total)"></span>
                    </div>
                    <div class="flex justify-between text-[11px]"><span>Tunai</span><span x-text="formatMoney(lastTransaction?.amount_paid)"></span></div>
                    <div class="flex justify-between text-[11px]"><span>Kembalian</span><span x-text="formatMoney(lastTransaction?.change_amount)"></span></div>
                </div>

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
