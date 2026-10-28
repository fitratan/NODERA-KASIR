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

        /* Soft blur backdrop for popups - fokus ke popup tanpa menggelapkan layar */
        .popup-backdrop-soft {
            background-color: rgba(15, 23, 42, 0.14) !important;
            backdrop-filter: blur(3px) !important;
            -webkit-backdrop-filter: blur(3px) !important;
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

        /* Smart Mobile Landscape Guard (Hanya aktif di HP kecil saat di Terminal Kasir) */
        @media screen and (orientation: portrait) and (max-width: 640px) {
            #landscape-guard-overlay.guard-active { display: flex !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased overflow-hidden select-none" x-data="posApp()">

    <!-- ORIENTATION GUARD (Hanya muncul jika HP dipegang tegak saat di terminal kasir) -->
    <div id="landscape-guard-overlay" 
         x-show="currentView === 'pos'"
         :class="{ 'guard-active': currentView === 'pos' }"
         class="fixed inset-0 bg-slate-900 z-50 hidden flex-col items-center justify-center p-6 text-center text-white"
         style="display: none;">
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
            
            <!-- Dashboard Header (Topnav Proper for Tablet & Mobile) -->
            <header class="bg-white border-b border-slate-200/80 px-4 sm:px-6 py-3 flex items-center justify-between shadow-sm sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <template x-if="settings.store_logo">
                        <img :src="settings.store_logo" class="w-10 h-10 rounded-2xl object-contain bg-white p-0.5 border border-slate-200 shadow-sm">
                    </template>
                    <template x-if="!settings.store_logo">
                        <div class="w-10 h-10 rounded-2xl bg-teal-600 text-white flex items-center justify-center font-black text-base shadow-md shadow-teal-600/20">
                            P
                        </div>
                    </template>
                    <div>
                        <h1 class="font-extrabold text-slate-900 text-sm sm:text-base leading-tight" x-text="settings.store_name"></h1>
                        <p class="text-[11px] text-slate-500 font-medium truncate max-w-[160px] sm:max-w-xs" x-text="settings.store_type || 'Point of Sale'"></p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <!-- Quick Action: Panel Admin (Non-Hitam) -->
                    <a href="admin/" 
                       class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition shadow-xs active:scale-95 shrink-0"
                       title="Buka Panel Admin / Manajemen Toko">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-teal-600"></i>
                        <span>Admin</span>
                    </a>

                    <!-- Lock / PIN Screen Button -->
                    <button type="button" 
                            @click="openLockScreen()" 
                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 flex items-center justify-center transition active:scale-95 shadow-xs shrink-0" 
                            title="Kunci Layar Kasir (Lock)">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-600"></i>
                    </button>

                    <!-- Cashier Profile Pill -->
                    <div class="flex items-center gap-2 pl-1 sm:pl-2 sm:border-l sm:border-slate-200 shrink-0">
                        <div class="text-right hidden md:block">
                            <div class="text-xs font-extrabold text-slate-800 leading-tight" x-text="currentUser.name"></div>
                            <div class="text-[9px] text-teal-600 font-semibold uppercase" x-text="currentUser.role || 'Kasir'"></div>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-teal-600 text-white flex items-center justify-center font-black text-xs shadow-md shadow-teal-600/20 shrink-0">
                            <span x-text="currentUser.name ? currentUser.name.charAt(0) : 'K'"></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content Container (W-Full Matching Topnav & Bottom Nav) -->
            <main class="w-full px-4 sm:px-6 py-4 pb-28">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 sm:gap-4 items-start">
                    
                    <!-- KOLOM KIRI (7/12): OPERASIONAL, STATISTIK & GRAFIK PENJUALAN -->
                    <div class="lg:col-span-7 space-y-3 sm:space-y-3.5">
                        
                        <!-- 1. HERO CTA CARD: "MULAI MENJUAL" -->
                        <div @click="navigate('pos')" 
                             class="bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-700 hover:to-teal-600 rounded-2xl p-4 sm:p-5 text-white shadow-md shadow-teal-600/15 cursor-pointer transition transform active:scale-[0.99] flex items-center justify-between group">
                            <div class="space-y-1">
                                <h2 class="text-xl sm:text-2xl font-black tracking-tight leading-tight">Mulai Menjual</h2>
                                <p class="text-teal-100 text-[11px] sm:text-xs">Buka terminal kasir untuk melayani transaksi pelanggan.</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-teal-700 text-white flex items-center justify-center shadow-inner group-hover:scale-105 transition">
                                    <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                                </div>
                                <i data-lucide="arrow-right" class="w-4 h-4 text-white/70 group-hover:translate-x-1 transition"></i>
                            </div>
                        </div>

                        <!-- 2. QUICK METRIC STAT CARDS -->
                        <div class="grid grid-cols-3 gap-2.5 sm:gap-3">
                            <!-- Total Produk -->
                            <div @click="navigate('products')" 
                                 class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs text-center cursor-pointer hover:border-teal-500 hover:shadow-sm transition transform active:scale-[0.98] group">
                                <div class="text-xl sm:text-2xl font-black text-slate-800" x-text="products.length"></div>
                                <div class="text-[11px] text-slate-500 font-bold mt-0.5">Produk Aktif</div>
                            </div>

                            <!-- Transaksi Hari Ini -->
                            <div @click="navigate('history')" 
                                 class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs text-center cursor-pointer hover:border-teal-500 hover:shadow-sm transition transform active:scale-[0.98] group">
                                <div class="text-xl sm:text-2xl font-black text-slate-800" x-text="dashboardData.summary?.total_transactions || 0"></div>
                                <div class="text-[11px] text-slate-500 font-bold mt-0.5">Transaksi Hari Ini</div>
                            </div>

                            <!-- Kategori -->
                            <div @click="navigate('categories')" 
                                 class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs text-center cursor-pointer hover:border-teal-500 hover:shadow-sm transition transform active:scale-[0.98] group">
                                <div class="text-xl sm:text-2xl font-black text-slate-800" x-text="categories.length"></div>
                                <div class="text-[11px] text-slate-500 font-bold mt-0.5">Kategori</div>
                            </div>
                        </div>

                        <!-- 3. GRAFIK VISUAL TREN PENJUALAN 7 HARI -->
                        <div class="bg-white p-4 sm:p-4.5 rounded-2xl border border-slate-200/90 shadow-xs space-y-2.5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-extrabold text-xs text-slate-900 tracking-tight">Grafik Tren Penjualan 7 Hari</h3>
                                    <p class="text-[10px] text-slate-400">Total Omzet: <span class="font-bold text-slate-700" x-text="formatMoney(totalSevenDaysSales)"></span></p>
                                </div>
                                <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 font-bold">7 Hari Terakhir</span>
                            </div>

                            <!-- Bar Chart Visual Interaktif -->
                            <div class="h-32 flex items-end justify-between gap-1.5 sm:gap-3 pt-6 pb-1 border-b border-slate-100">
                                <template x-for="d in dashboardData.seven_days || []" :key="d.date">
                                    <div class="flex-1 flex flex-col items-center h-full justify-end group relative cursor-pointer">
                                        <!-- Tooltip Hover Nominal & Tanggal -->
                                        <div class="absolute -top-7 opacity-0 group-hover:opacity-100 pointer-events-none transition-all duration-200 z-20 whitespace-nowrap bg-slate-900 text-white text-[9px] px-2 py-0.5 rounded-md shadow-md">
                                            <span x-text="d.day + ': ' + formatMoney(d.amount)"></span>
                                        </div>

                                        <!-- Bar Batang Interaktif -->
                                        <div class="w-full max-w-[32px] bg-slate-100 rounded-t-lg relative flex items-end justify-center overflow-hidden h-full">
                                            <div class="w-full rounded-t-lg transition-all duration-500 ease-out group-hover:brightness-110" 
                                                 :class="d.amount > 0 ? 'bg-gradient-to-t from-teal-600 to-teal-400' : 'bg-slate-200'"
                                                 :style="'height: ' + (d.amount > 0 ? Math.max(12, Math.round((d.amount / maxSevenDaysSales) * 100)) : 4) + '%'">
                                            </div>
                                        </div>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase mt-1.5 group-hover:text-teal-600 transition" x-text="d.day"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                    <!-- KOLOM KANAN (5/12): FINANSIAL & OPERASIONAL HARIAN -->
                    <div class="lg:col-span-5 space-y-3 sm:space-y-3.5">
                        
                        <!-- 4. RINGKASAN FINANSIAL (PENDAPATAN, HPP, BEBAN, LABA BERSIH, MARGIN) -->
                        <div class="bg-white p-4 sm:p-4.5 rounded-2xl border border-slate-200/90 shadow-xs space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                <div @click="navigate('history')" class="cursor-pointer group">
                                    <div class="flex items-center gap-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider group-hover:text-teal-600 transition">
                                        <span>Pendapatan Hari Ini</span>
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-0.5 transition"></i>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5 group-hover:text-teal-700 transition" x-text="formatMoney(dashboardData.summary?.total_turnover)"></div>
                                </div>
                                <button @click="openExpenseModal()" 
                                        class="px-2.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-[11px] transition shadow-xs flex items-center gap-1 active:scale-95 shrink-0"
                                        title="Catat Kas Keluar">
                                    <i data-lucide="minus-circle" class="w-3.5 h-3.5 text-white"></i>
                                    <span>+ Kas Keluar</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <span class="text-slate-500 block text-[10px] font-bold">Modal (HPP):</span>
                                    <span class="font-extrabold text-slate-800 text-xs sm:text-sm mt-0.5 block truncate" x-text="formatMoney(dashboardData.summary?.total_cost)"></span>
                                </div>
                                <div @click="openExpenseModal()" class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 hover:border-rose-300 hover:bg-slate-100 cursor-pointer transition active:scale-95 group">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500 block text-[10px] font-bold group-hover:text-rose-600 transition">Beban Kas:</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md bg-rose-50 text-rose-700 border border-rose-200">+ Catat</span>
                                    </div>
                                    <span class="font-extrabold text-rose-600 text-xs sm:text-sm mt-0.5 block truncate" x-text="formatMoney(dashboardData.summary?.total_expenses)"></span>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <span class="text-slate-500 block text-[10px] font-bold">Laba Bersih:</span>
                                    <span class="font-black text-teal-600 text-xs sm:text-sm mt-0.5 block truncate" x-text="formatMoney(dashboardData.summary?.net_profit)"></span>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <span class="text-slate-500 block text-[10px] font-bold">Margin Laba:</span>
                                    <div class="mt-0.5">
                                        <span class="text-xs sm:text-sm font-black text-emerald-600" x-text="(dashboardData.summary?.margin_percent || 0) + '%'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. STATUS OPEN BILL QUICK ACCESS (JIKA ADA BILL TERSIMPAN) -->
                        <div @click="navigate('open_bills')" 
                             class="p-3.5 bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between cursor-pointer transition active:scale-[0.99] shadow-xs">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold shadow-xs">
                                    <i data-lucide="receipt" class="w-4.5 h-4.5 text-amber-600"></i>
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900 text-xs">Open Bill (Tagihan Tertunda)</div>
                                    <div class="text-[10px] text-slate-500" x-text="openBills.length + ' tagihan aktif tersimpan'"></div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold shadow-xs flex items-center gap-1">
                                <span>Buka</span>
                                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-amber-600"></i>
                            </span>
                        </div>

                    </div>
                </div>
            </main>
        </div>

        <!-- =================================================================== -->
        <!-- VIEW 2: TERMINAL KASIR (LAYANAN TRANSAKSI PENJUALAN)                -->
        <!-- =================================================================== -->
        <div x-show="currentView === 'pos'" class="flex-1 flex flex-col overflow-hidden">
            
            <!-- TOPNAV TERMINAL KASIR (Tanpa Logo & Nama Toko, Tombol Non-Hitam) -->
            <header class="bg-white border-b border-slate-200 px-3 sm:px-4 py-2.5 flex items-center justify-between shadow-xs z-20 shrink-0">
                <!-- Left: Tombol Kembali (Non-Hitam) & Status Layanan Kasir -->
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <button @click="navigate('dashboard')" 
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 border border-slate-200 flex items-center justify-center transition active:scale-95 shadow-xs shrink-0"
                            title="Kembali ke Dashboard">
                        <i data-lucide="arrow-left" class="w-5 h-5 text-slate-700"></i>
                    </button>

                    <div class="h-6 w-px bg-slate-200"></div>

                    <div>
                        <span class="font-extrabold text-slate-900 text-sm sm:text-base tracking-tight">Layanan Kasir</span>
                    </div>
                </div>

                <!-- Center / Right: Quick Actions di Kasir (Semua Non-Hitam & Tidak Menyusut) -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <!-- Quick: Open Bill Button -->
                    <button @click="navigate('open_bills')" 
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition active:scale-95 shadow-sm relative shrink-0"
                            title="Daftar Tagihan Belum Lunas (Open Bill)">
                        <i data-lucide="receipt" class="w-3.5 h-3.5 text-white"></i>
                        <span class="hidden md:inline">Open Bill</span>
                        <template x-if="openBills.length > 0">
                            <span class="px-1.5 py-0.2 rounded-full bg-rose-600 text-white text-[9px] font-black shadow-xs" x-text="openBills.length"></span>
                        </template>
                    </button>

                    <!-- Quick: Riwayat Transaksi -->
                    <button @click="navigate('history')" 
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold transition active:scale-95 shadow-sm shrink-0"
                            title="Riwayat Transaksi & Cetak Struk Hari Ini">
                        <i data-lucide="history" class="w-3.5 h-3.5 text-white"></i>
                        <span class="hidden md:inline">Riwayat</span>
                    </button>

                    <!-- Quick: Kas Keluar -->
                    <button @click="openExpenseModal()" 
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition active:scale-95 shadow-sm shrink-0"
                            title="Catat Kas Keluar Toko">
                        <i data-lucide="minus-circle" class="w-3.5 h-3.5 text-white"></i>
                        <span class="hidden md:inline">Kas Keluar</span>
                    </button>

                    <div class="h-5 w-px bg-slate-200 hidden sm:block shrink-0"></div>

                    <!-- Panel Admin Link -->
                    <a href="admin/" 
                       class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition shadow-xs active:scale-95 shrink-0"
                       title="Buka Panel Admin">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-teal-600"></i>
                        <span class="hidden lg:inline">Admin</span>
                    </a>

                    <!-- Lock / PIN Screen Button -->
                    <button type="button" 
                            @click="openLockScreen()" 
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 flex items-center justify-center transition active:scale-95 shadow-xs shrink-0" 
                            title="Kunci Layar Kasir (Lock)">
                        <i data-lucide="lock" class="w-3.5 h-3.5 text-slate-600"></i>
                    </button>

                    <!-- Kasir Profile Pill -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-teal-600 text-white flex items-center justify-center font-black text-xs shadow-sm shrink-0">
                            <span x-text="currentUser.name ? currentUser.name.charAt(0) : 'K'"></span>
                        </div>
                        <div class="hidden lg:block text-left shrink-0">
                            <div class="text-xs font-extrabold text-slate-800 leading-tight" x-text="currentUser.name"></div>
                            <div class="text-[9px] text-teal-600 font-semibold uppercase">Kasir</div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- SPLIT SCREEN: LEFT AREA (60%) + RIGHT AREA (40%) -->
            <div class="flex-1 flex overflow-hidden">
                
                <!-- LEFT AREA: 65% KATALOG PRODUK -->
                <main class="w-7/12 flex flex-col bg-slate-100/60 p-3 overflow-hidden border-r border-slate-200">
                
                <!-- Search & Category Dropdown Toolbar -->
                <div class="mb-3 flex items-center gap-2 sm:gap-2.5">
                    <!-- Custom Popover Category Dropdown -->
                    <div class="relative w-40 sm:w-52 shrink-0" x-data="{ open: false }">
                        <button type="button" 
                                @click="open = !open; $nextTick(() => refreshIcons())" 
                                @click.outside="open = false"
                                class="w-full h-9 sm:h-10 px-3 bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 focus:border-teal-500 rounded-xl text-xs font-bold text-slate-800 flex items-center justify-between shadow-xs transition active:scale-[0.99]">
                            <div class="flex items-center gap-1.5 truncate">
                                <i data-lucide="layers" class="w-3.5 h-3.5 text-teal-600 shrink-0"></i>
                                <span class="truncate" x-text="selectedCategory ? (categories.find(c => c.id == selectedCategory)?.name || 'Kategori') : 'Semua Kategori'"></span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0 ml-1" :class="open ? 'rotate-180 text-teal-600' : ''"></i>
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             class="absolute left-0 top-full mt-1.5 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-1.5 max-h-60 overflow-y-auto space-y-0.5"
                             style="display: none;">
                            <!-- Option: Semua Kategori -->
                            <button type="button" 
                                    @click="selectedCategory = ''; open = false; refreshIcons()" 
                                    class="w-full px-2.5 py-1.5 rounded-xl text-xs font-bold text-left transition flex items-center justify-between group"
                                    :class="!selectedCategory ? 'bg-teal-50 text-teal-800 font-black' : 'text-slate-700 hover:bg-slate-100'">
                                <div class="flex items-center gap-2 truncate">
                                    <i data-lucide="layout-grid" class="w-3.5 h-3.5 shrink-0" :class="!selectedCategory ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600'"></i>
                                    <span class="truncate">Semua Kategori</span>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span class="px-1.5 py-0.2 rounded-full text-[9px] font-semibold bg-slate-100 text-slate-600 border border-slate-200" x-text="products.length"></span>
                                    <template x-if="!selectedCategory">
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-teal-600"></i>
                                    </template>
                                </div>
                            </button>

                            <!-- Dynamic Category Options -->
                            <template x-for="c in categories" :key="c.id">
                                <button type="button" 
                                        @click="selectedCategory = c.id; open = false; refreshIcons()" 
                                        class="w-full px-2.5 py-1.5 rounded-xl text-xs font-bold text-left transition flex items-center justify-between group"
                                        :class="selectedCategory == c.id ? 'bg-teal-50 text-teal-800 font-black' : 'text-slate-700 hover:bg-slate-100'">
                                    <div class="flex items-center gap-2 truncate">
                                        <i data-lucide="folder" class="w-3.5 h-3.5 shrink-0" :class="selectedCategory == c.id ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600'"></i>
                                        <span class="truncate" x-text="c.name"></span>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        <span class="px-1.5 py-0.2 rounded-full text-[9px] font-semibold bg-slate-100 text-slate-600 border border-slate-200" x-text="getCategoryCount(c.id)"></span>
                                        <template x-if="selectedCategory == c.id">
                                            <i data-lucide="check" class="w-3.5 h-3.5 text-teal-600"></i>
                                        </template>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Search Input Produk -->
                    <div class="relative flex-1">
                        <input type="text" 
                               x-model="searchQuery" 
                               placeholder="Cari produk berdasarkan nama atau barcode (F2)..." 
                               class="w-full h-9 sm:h-10 pl-9 pr-8 bg-white border border-slate-200 hover:border-slate-300 focus:border-teal-500 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 shadow-xs transition">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 sm:top-3 pointer-events-none"></i>
                        <button type="button" 
                                x-show="searchQuery" 
                                @click="searchQuery = ''" 
                                class="absolute right-2.5 top-2.5 sm:top-3 text-slate-400 hover:text-slate-600 transition"
                                title="Reset pencarian">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                <!-- BENTO PRODUCT GRID (Presisi Frame 012) -->
                <div class="flex-1 overflow-y-auto pr-1">
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2.5">
                        <template x-for="p in filteredProducts" :key="p.id">
                            <div @click="handleProductClick(p)" 
                                 :class="p.stock <= 0 ? 'opacity-65 bg-slate-50/70 border-dashed border-slate-300' : 'bg-white border-slate-200/80 hover:border-teal-500 hover:shadow-md cursor-pointer'"
                                 class="rounded-2xl border p-3 transition flex flex-col justify-between active:scale-[0.98] group shadow-sm text-center">
                                
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

                                <!-- Subtitle / Soft Pill Badge -->
                                <div class="mt-1">
                                    <template x-if="p.stock <= 0">
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200 text-[9px] font-bold">Stok Habis (0)</span>
                                    </template>
                                    <template x-if="p.stock > 0 && p.has_variants == 1">
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-teal-50 text-teal-700 border border-teal-200 text-[9px] font-bold">
                                            <span x-text="p.variants.length"></span> Varian
                                        </span>
                                    </template>
                                    <template x-if="p.stock > 0 && p.has_variants != 1 && p.is_scale == 1">
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200 text-[9px] font-medium" x-text="'Stok: ' + p.stock + ' ' + p.unit"></span>
                                    </template>
                                    <template x-if="p.stock > 0 && p.has_variants != 1 && p.is_scale != 1 && p.stock >= 900">
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200 text-[9px] font-medium">Stok Bebas</span>
                                    </template>
                                    <template x-if="p.stock > 0 && p.has_variants != 1 && p.is_scale != 1 && p.stock < 900">
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200 text-[9px] font-medium" x-text="'Stok: ' + p.stock"></span>
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
                        <i data-lucide="shopping-bag" class="w-4 h-4 text-teal-600"></i>
                        <h2 class="font-black text-slate-800 text-sm">Keranjang</h2>
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 font-bold tracking-tight" x-text="totalItemsCount"></span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="openSaveBillModal()" 
                                x-show="!isCartEmpty" 
                                class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 active:scale-95 text-white rounded-xl text-xs font-black shadow-xs transition flex items-center gap-1.5" 
                                title="Simpan Tagihan / Meja">
                            <i data-lucide="receipt" class="w-3.5 h-3.5 text-white"></i>
                            <span>Simpan Bill</span>
                        </button>
                        <button type="button" 
                                @click="clearCart()" 
                                x-show="!isCartEmpty" 
                                class="w-8 h-8 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white flex items-center justify-center transition shadow-xs shrink-0" 
                                title="Kosongkan Keranjang">
                            <i data-lucide="trash-2" class="w-4 h-4 text-white"></i>
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
                                    <span class="text-[9px] px-2 py-0.5 rounded-full font-black uppercase shrink-0 text-white shadow-xs"
                                          :class="{
                                              'bg-amber-500': c.tier === 'vip',
                                              'bg-indigo-600': c.tier === 'wholesale',
                                              'bg-teal-600': c.tier === 'regular' || !c.tier
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

                        <button @click="clearSelectedMember()" class="text-[10px] px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold transition shrink-0 shadow-xs">
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

                            <!-- Qty Stepper with Direct Manual Input (Bisa Ketik 250 Tanpa Klik 250 Kali) -->
                            <div class="flex items-center space-x-1 bg-slate-100 rounded-xl p-0.5 border border-slate-200">
                                <button type="button" 
                                        @click="updateCartQty(idx, -1)" 
                                        class="w-7 h-7 rounded-lg bg-white hover:bg-slate-200 text-slate-700 flex items-center justify-center font-black text-xs transition active:scale-95 shadow-2xs"
                                        title="Kurangi Qty">
                                    <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                                </button>
                                <input type="number" 
                                       step="any" 
                                       min="0.01" 
                                       x-model.number="item.qty" 
                                       @change="validateCartItemQty(idx)" 
                                       @focus="$event.target.select()" 
                                       title="Klik untuk langsung ketik jumlah pcs/qty"
                                       class="w-14 sm:w-16 h-7 text-center font-black text-slate-800 bg-white border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-none shadow-2xs">
                                <button type="button" 
                                        @click="updateCartQty(idx, 1)" 
                                        class="w-7 h-7 rounded-lg bg-white hover:bg-slate-200 text-slate-700 flex items-center justify-center font-black text-xs transition active:scale-95 shadow-2xs"
                                        title="Tambah Qty">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                </button>
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
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-800 tracking-tight" x-text="formatMoney(subtotal)"></span>
                        </div>

                        <!-- Toggle On/Off Faktur Pajak (PPN) -->
                        <div class="flex items-center justify-between py-1.5 px-2 bg-white rounded-xl border border-slate-200 shadow-2xs">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700">
                                <i data-lucide="file-text" class="w-3.5 h-3.5 text-teal-600"></i>
                                <span>Faktur Pajak (PPN <span x-text="settings.tax_percent || 11"></span>%)</span>
                            </div>
                            <button type="button" 
                                    @click="useTaxInvoice = !useTaxInvoice; recalculatePaymentAmounts()"
                                    class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                    :class="useTaxInvoice ? 'bg-teal-600' : 'bg-slate-300'"
                                    :title="useTaxInvoice ? 'Faktur Pajak Aktif' : 'Faktur Pajak Non-Aktif'">
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
                                      :class="useTaxInvoice ? 'translate-x-4' : 'translate-x-0'"></span>
                            </button>
                        </div>

                        <template x-if="useTaxInvoice && taxAmount > 0">
                            <div class="flex justify-between text-teal-700 font-semibold">
                                <span>PPN (<span x-text="settings.tax_percent || 11"></span>%)</span>
                                <span class="font-bold tracking-tight" x-text="formatMoney(taxAmount)"></span>
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
        </div>

        <!-- =================================================================== -->
        <!-- VIEW 3: RIWAYAT TRANSAKSI & FILTER TANGGAL KALENDER                -->
        <!-- =================================================================== -->
        <div x-show="currentView === 'history'" class="flex-1 flex flex-col overflow-y-auto pb-28">
            <header class="bg-white border-b border-slate-200 px-3 sm:px-6 py-3 sm:py-3.5 flex items-center justify-between shadow-xs sticky top-0 z-30">
                <div class="flex items-center gap-2.5 min-w-0">
                    <button @click="navigate('dashboard')" 
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 flex items-center justify-center transition active:scale-95 shadow-xs shrink-0" 
                            title="Kembali ke Dashboard">
                        <i data-lucide="arrow-left" class="w-5 h-5 text-slate-700"></i>
                    </button>
                    <h2 class="font-extrabold text-slate-900 text-base leading-tight truncate">Riwayat Transaksi</h2>
                </div>

                <!-- Right: Filter Tanggal (By Icon Kalender Saja) & Refresh -->
                <div class="flex items-center gap-2 shrink-0">
                    <!-- Tombol Filter Tanggal dengan Ikon Kalender Saja -->
                    <button type="button" 
                            @click="openDateFilterModal()" 
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 flex items-center justify-center transition active:scale-95 shadow-xs relative shrink-0" 
                            title="Filter Tanggal Transaksi (Kalender)">
                        <i data-lucide="calendar" class="w-4.5 h-4.5 text-teal-600"></i>
                        <span x-show="historyFilterDate && historyFilterDate !== todayDate" 
                              class="w-2.5 h-2.5 rounded-full bg-amber-500 absolute -top-1 -right-1 ring-2 ring-white"></span>
                    </button>

                    <!-- Tombol Muat Ulang -->
                    <button type="button" 
                            @click="loadHistory(historyFilterDate)" 
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 flex items-center justify-center transition active:scale-95 shadow-xs shrink-0" 
                            title="Muat Ulang Riwayat">
                        <i data-lucide="rotate-cw" class="w-4 h-4 text-slate-600" :class="{ 'animate-spin': isLoadingHistory }"></i>
                    </button>
                </div>
            </header>

            <main class="w-full px-4 sm:px-6 py-4 pb-28 space-y-4">
                <!-- Search Bar Riwayat: Lebar Penuh (w-full h-11 sm:h-12 rounded-2xl) Seragam dengan Open Bill -->
                <div class="w-full">
                    <div class="relative w-full">
                        <input type="text" 
                               x-model="historySearchQuery" 
                               placeholder="Cari no. invoice, nama pelanggan, produk, atau metode bayar..." 
                               class="w-full h-11 sm:h-12 pl-11 pr-10 bg-white border border-slate-200 hover:border-slate-300 focus:border-teal-500 rounded-2xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 shadow-xs transition">
                        <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-3.5 top-3 sm:top-3.5 pointer-events-none"></i>
                        <button type="button" 
                                x-show="historySearchQuery" 
                                @click="historySearchQuery = ''" 
                                class="absolute right-3.5 top-3 sm:top-3.5 text-slate-400 hover:text-slate-600 transition"
                                title="Reset pencarian">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Mini Stats Bar (Dihitung Sesuai Tanggal yang Dipilih) -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Total Transaksi:</span>
                        <span class="font-black text-slate-800 text-base mt-0.5 block" x-text="displaySummary?.total_transactions || 0"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Total Omzet:</span>
                        <span class="font-black text-teal-600 text-base mt-0.5 block" x-text="formatMoney(displaySummary?.total_turnover || 0)"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Tunai (Laci):</span>
                        <span class="font-extrabold text-slate-700 text-sm mt-0.5 block" x-text="formatMoney(displaySummary?.total_cash || 0)"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Non-Tunai / QRIS:</span>
                        <span class="font-extrabold text-sky-700 text-sm mt-0.5 block" x-text="formatMoney(displaySummary?.total_qris || 0)"></span>
                    </div>
                </div>

                <!-- Header Daftar Transaksi & Counter Status -->
                <div class="flex items-center justify-between text-xs font-bold text-slate-600 pt-1 px-1">
                    <span class="uppercase tracking-wider text-[11px] text-slate-400 font-bold">Daftar Transaksi Selesai:</span>
                    <span class="text-[11px] text-slate-500 font-semibold">
                        Menampilkan <span class="font-extrabold text-teal-600" x-text="displayTransactions.length"></span> transaksi
                    </span>
                </div>

                <!-- Transaction List Cards -->
                <div class="space-y-2.5">
                    <template x-for="tx in displayTransactions" :key="tx.id">
                        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-teal-300 transition">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center font-bold shrink-0 mt-0.5">
                                    <i data-lucide="receipt" class="w-5 h-5 text-teal-600"></i>
                                </div>
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-extrabold text-slate-900 text-xs sm:text-sm" x-text="tx.invoice_no"></span>
                                        <span class="text-[9px] px-2 py-0.5 rounded-md font-bold uppercase tracking-wider border"
                                              :class="{
                                                  'bg-teal-50 text-teal-700 border-teal-200': tx.payment_method === 'cash',
                                                  'bg-sky-50 text-sky-700 border-sky-200': tx.payment_method === 'qris' || tx.payment_method === 'transfer',
                                                  'bg-rose-50 text-rose-700 border-rose-200': tx.payment_method === 'debt',
                                                  'bg-indigo-50 text-indigo-700 border-indigo-200': tx.payment_method === 'split',
                                                  'bg-amber-50 text-amber-700 border-amber-200': tx.payment_method === 'edc'
                                              }"
                                              x-text="tx.payment_method">
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 flex items-center gap-2">
                                        <span class="font-bold text-slate-700" x-text="tx.customer_name || 'Pelanggan Umum'"></span>
                                        <span>•</span>
                                        <span x-text="tx.created_at"></span>
                                        <span>•</span>
                                        <span class="text-slate-400" x-text="(tx.items?.length || 0) + ' item'"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-3 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                <div class="sm:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-semibold block sm:hidden">Total Belanja:</span>
                                    <span class="font-black text-slate-900 text-sm sm:text-base tracking-tight" x-text="formatMoney(tx.total)"></span>
                                </div>
                                <button @click="reprintReceipt(tx)" 
                                        class="px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 active:scale-95 shadow-md shadow-teal-600/20">
                                    <i data-lucide="printer" class="w-3.5 h-3.5 text-white"></i>
                                    <span>Cetak Struk</span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="displayTransactions.length === 0" 
                         class="h-56 bg-white rounded-3xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs space-y-2">
                        <i data-lucide="search-x" class="w-8 h-8 stroke-1 text-slate-300" x-show="historySearchQuery"></i>
                        <i data-lucide="inbox" class="w-8 h-8 stroke-1 text-slate-300" x-show="!historySearchQuery"></i>
                        <p class="font-semibold text-slate-600" x-text="historySearchQuery ? 'Transaksi tidak ditemukan' : 'Tidak ada transaksi ditemukan'"></p>
                        <p class="text-[11px] text-slate-400" x-text="historySearchQuery ? 'Tidak ada transaksi yang cocok dengan kata kunci pencarian.' : ('Tidak ada penjualan yang tercatat pada tanggal ' + formatDisplayDate(historyFilterDate) + '.')"></p>
                        <button type="button"
                                x-show="historySearchQuery" 
                                @click="historySearchQuery = ''" 
                                class="mt-2 px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-xl text-xs transition">
                            Reset Pencarian
                        </button>
                        <button type="button"
                                x-show="!historySearchQuery && historyFilterDate !== todayDate" 
                                @click="loadHistory(todayDate)" 
                                class="mt-2 px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs transition">
                            Lihat Transaksi Hari Ini
                        </button>
                    </div>
                </div>
            </main>
        </div>

        <!-- =================================================================== -->
        <!-- VIEW 4: OPEN BILLS (TAGIHAN TERSIMPAN - BENTO GRID RAPI)            -->
        <!-- =================================================================== -->
        <div x-show="currentView === 'open_bills'" class="flex-1 flex flex-col overflow-y-auto pb-28">
            <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3.5 flex items-center justify-between shadow-xs sticky top-0 z-20">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="navigate('dashboard')" 
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 flex items-center justify-center transition active:scale-95 shadow-xs shrink-0" 
                            title="Kembali ke Dashboard">
                        <i data-lucide="arrow-left" class="w-5 h-5 text-slate-700"></i>
                    </button>
                    <h2 class="font-extrabold text-slate-900 text-base leading-tight truncate">Daftar Tagihan (Open Bill)</h2>
                </div>
            </header>

            <main class="w-full px-4 sm:px-6 py-4 pb-28 space-y-4">
                <!-- Search Bar: Ukuran Penuh Senada Topnav (w-full) Tanpa Info Double -->
                <div class="w-full" x-show="openBills.length > 0">
                    <div class="relative w-full">
                        <input type="text" 
                               x-model="openBillSearchQuery" 
                               placeholder="Cari nama meja atau no. bill tagihan..." 
                               class="w-full h-11 sm:h-12 pl-11 pr-10 bg-white border border-slate-200 hover:border-slate-300 focus:border-amber-500 rounded-2xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 shadow-xs transition">
                        <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-3.5 top-3 sm:top-3.5 pointer-events-none"></i>
                        <button type="button" 
                                x-show="openBillSearchQuery" 
                                @click="openBillSearchQuery = ''" 
                                class="absolute right-3.5 top-3 sm:top-3.5 text-slate-400 hover:text-slate-600 transition"
                                title="Reset pencarian">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Bento Grid Open Bills: Responsif 1-4 Kolom Rapi (Tidak Bertumpuk Satu Baris) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <template x-for="bill in filteredOpenBills" :key="bill.id">
                        <div class="bg-white rounded-2xl border border-slate-200 hover:border-amber-400 hover:shadow-md transition flex flex-col justify-between p-4 shadow-xs group">
                            
                            <!-- Top: Meja Name & Bill No Badge -->
                            <div class="space-y-3">
                                <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-2.5">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                                            <i data-lucide="receipt" class="w-4.5 h-4.5 text-amber-600"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base leading-tight truncate" x-text="bill.bill_name"></h3>
                                            <span class="text-[10px] text-slate-400 block mt-0.5" x-text="bill.created_at"></span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200 text-[9px] font-bold uppercase tracking-wider shrink-0" x-text="bill.bill_no"></span>
                                </div>

                                <!-- Middle: Item Summary Box -->
                                <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-100 space-y-1">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="font-bold text-slate-700 flex items-center gap-1">
                                            <i data-lucide="shopping-bag" class="w-3 h-3 text-slate-400"></i>
                                            <span x-text="getBillItemCount(bill) + ' Item'"></span>
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-semibold truncate" x-text="'Kasir: ' + bill.cashier_name"></span>
                                    </div>
                                    <div class="text-xs text-slate-600 line-clamp-2 leading-relaxed font-medium" x-text="getBillItemList(bill)"></div>
                                </div>

                                <!-- Total Bill in Large Bold Teal -->
                                <div class="flex items-baseline justify-between pt-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tagihan:</span>
                                    <span class="font-black text-teal-600 text-base sm:text-lg tracking-tight" x-text="formatMoney(bill.total)"></span>
                                </div>
                            </div>

                            <!-- Bottom: Action Buttons Side by Side (50% Hapus, 50% Proses) -->
                            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 mt-3">
                                <button type="button" 
                                        @click="deleteOpenBill(bill)" 
                                        class="py-2.5 px-3 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-1.5 transition shadow-xs"
                                        title="Hapus Tagihan Open Bill">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-white"></i>
                                    <span>Hapus</span>
                                </button>
                                <button type="button" 
                                        @click="loadOpenBill(bill)" 
                                        class="py-2.5 px-3 bg-teal-600 hover:bg-teal-700 active:scale-95 text-white font-extrabold rounded-xl text-xs flex items-center justify-center gap-1.5 transition shadow-md shadow-teal-600/20"
                                        title="Proses Tagihan ke Kasir">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-white"></i>
                                    <span>Proses</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State jika pencarian tidak menemukan hasil -->
                <div x-show="openBills.length > 0 && filteredOpenBills.length === 0" 
                     class="h-64 bg-white rounded-3xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs space-y-2.5 mt-2">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <i data-lucide="search-x" class="w-6 h-6"></i>
                    </div>
                    <p class="font-bold text-slate-600 text-sm">Tagihan tidak ditemukan</p>
                    <p class="text-[11px] text-slate-400">Tidak ada tagihan yang cocok dengan kata kunci pencarian.</p>
                    <button type="button" 
                            @click="openBillSearchQuery = ''" 
                            class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-xl text-xs transition">
                        Reset Pencarian
                    </button>
                </div>

                <!-- Empty State jika memang belum ada tagihan -->
                <div x-show="openBills.length === 0" class="h-64 bg-white rounded-3xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs space-y-2.5 mt-2">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <i data-lucide="receipt" class="w-6 h-6"></i>
                    </div>
                    <p class="font-bold text-slate-600 text-sm">Tidak ada tagihan tertunda</p>
                    <p class="text-[11px] text-slate-400">Semua tagihan Open Bill telah diproses atau dibersihkan.</p>
                </div>
            </main>
        </div>

        <!-- =================================================================== -->
        <!-- VIEW 5: KATALOG & STOK PRODUK                                       -->
        <!-- =================================================================== -->
        <div x-show="currentView === 'products'" class="flex-1 flex flex-col overflow-y-auto pb-28">
            <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3.5 flex items-center justify-between shadow-xs sticky top-0 z-20">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="navigate('dashboard')" 
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 flex items-center justify-center transition active:scale-95 shadow-xs shrink-0" 
                            title="Kembali ke Dashboard">
                        <i data-lucide="arrow-left" class="w-5 h-5 text-slate-700"></i>
                    </button>
                    <h2 class="font-extrabold text-slate-900 text-base leading-tight truncate">Katalog Produk</h2>
                </div>

                <!-- Right Action: Tombol Kategori (Tidak Menyusut) -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" 
                            @click="navigate('categories')" 
                            class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs active:scale-95 shrink-0"
                            title="Buka Daftar Kategori">
                        <i data-lucide="folder" class="w-4 h-4 text-amber-600"></i>
                        <span>Kategori</span>
                    </button>
                </div>
            </header>

            <main class="w-full px-4 sm:px-6 py-4 pb-28 space-y-4">
                <!-- Filter Bar: Dropdown Kategori + Search Bar Ukuran Penuh -->
                <div class="flex flex-col sm:flex-row items-center gap-2.5">
                    <!-- Custom Popover Dropdown Filter Kategori -->
                    <div class="w-full sm:w-60 shrink-0 relative" x-data="{ open: false }">
                        <button type="button" 
                                @click="open = !open; $nextTick(() => refreshIcons())" 
                                @click.outside="open = false"
                                class="w-full h-11 sm:h-12 px-3.5 bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 focus:border-teal-500 rounded-2xl text-xs sm:text-sm font-bold text-slate-700 flex items-center justify-between shadow-xs transition active:scale-[0.99]">
                            <div class="flex items-center gap-2 truncate">
                                <i data-lucide="filter" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                <span class="truncate" x-text="productFilterCategory ? (getCategoryName(productFilterCategory) + ' (' + getCategoryCount(productFilterCategory) + ')') : 'Semua Kategori'"></span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0 ml-1.5" :class="open ? 'rotate-180 text-teal-600' : ''"></i>
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             class="absolute left-0 top-full mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-1.5 max-h-64 overflow-y-auto space-y-0.5"
                             style="display: none;">
                            <!-- Option: Semua Kategori -->
                            <button type="button" 
                                    @click="productFilterCategory = ''; open = false; refreshIcons()" 
                                    class="w-full px-3 py-2 rounded-xl text-xs font-bold text-left transition flex items-center justify-between group"
                                    :class="!productFilterCategory ? 'bg-teal-50 text-teal-800 font-black' : 'text-slate-700 hover:bg-slate-100'">
                                <div class="flex items-center gap-2 truncate">
                                    <i data-lucide="layout-grid" class="w-3.5 h-3.5 shrink-0" :class="!productFilterCategory ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600'"></i>
                                    <span class="truncate">Semua Kategori</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200" x-text="products.length"></span>
                                    <template x-if="!productFilterCategory">
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-teal-600"></i>
                                    </template>
                                </div>
                            </button>

                            <!-- Dynamic Category Options -->
                            <template x-for="c in categories" :key="c.id">
                                <button type="button" 
                                        @click="productFilterCategory = String(c.id); open = false; refreshIcons()" 
                                        class="w-full px-3 py-2 rounded-xl text-xs font-bold text-left transition flex items-center justify-between group"
                                        :class="productFilterCategory == String(c.id) ? 'bg-teal-50 text-teal-800 font-black' : 'text-slate-700 hover:bg-slate-100'">
                                    <div class="flex items-center gap-2 truncate">
                                        <i data-lucide="folder" class="w-3.5 h-3.5 shrink-0" :class="productFilterCategory == String(c.id) ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-600'"></i>
                                        <span class="truncate" x-text="c.name"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200" x-text="getCategoryCount(c.id)"></span>
                                        <template x-if="productFilterCategory == String(c.id)">
                                            <i data-lucide="check" class="w-3.5 h-3.5 text-teal-600"></i>
                                        </template>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full flex-1">
                        <input type="text" 
                               x-model="productSearchQuery" 
                               placeholder="Cari nama produk, kode SKU, barcode, atau kategori..." 
                               class="w-full h-11 sm:h-12 pl-11 pr-10 bg-white border border-slate-200 hover:border-slate-300 focus:border-teal-500 rounded-2xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 shadow-xs transition">
                        <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-3.5 top-3 sm:top-3.5 pointer-events-none"></i>
                        <button type="button" 
                                x-show="productSearchQuery" 
                                @click="productSearchQuery = ''" 
                                class="absolute right-3.5 top-3 sm:top-3.5 text-slate-400 hover:text-slate-600 transition"
                                title="Reset pencarian">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Active Filter Indicator Banner -->
                <div x-show="productFilterCategory" class="flex items-center justify-between bg-teal-50 border border-teal-200 px-3.5 py-2 rounded-xl text-xs text-teal-800 font-medium">
                    <div class="flex items-center gap-2">
                        <i data-lucide="filter" class="w-4 h-4 text-teal-600"></i>
                        <span>Menampilkan kategori: <strong class="font-extrabold" x-text="getCategoryName(productFilterCategory)"></strong> (<span x-text="displayedCatalogProducts.length"></span> produk)</span>
                    </div>
                    <button type="button" 
                            @click="productFilterCategory = ''" 
                            class="px-2 py-0.5 bg-white border border-teal-300 hover:bg-teal-100 rounded-lg text-[11px] font-bold text-teal-700 transition">
                        Tampilkan Semua
                    </button>
                </div>

                <!-- Bento Grid Produk -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
                    <template x-for="p in displayedCatalogProducts" :key="p.id">
                        <div :class="p.stock <= 0 ? 'opacity-65 bg-slate-50/70 border-dashed border-slate-300' : 'bg-white border-slate-200/90 hover:border-teal-500 hover:shadow-md'"
                             class="rounded-2xl border transition flex flex-col justify-between p-3.5 shadow-xs group">
                            <!-- Product Image / Icon -->
                            <div>
                                <div class="w-full h-28 rounded-xl bg-slate-50 border border-slate-100 overflow-hidden flex items-center justify-center relative">
                                    <template x-if="p.image_url">
                                        <img :src="p.image_url" :alt="p.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    </template>
                                    <template x-if="!p.image_url">
                                        <i data-lucide="package" class="w-9 h-9 text-slate-300"></i>
                                    </template>
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-white/95 backdrop-blur-xs text-slate-700 text-[9px] font-bold tracking-tight border border-slate-200/80 shadow-xs truncate max-w-[85%]" 
                                          x-text="p.category_name || 'Umum'">
                                    </span>
                                </div>

                                <!-- Product Info -->
                                <div class="mt-2.5 space-y-1">
                                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm leading-snug line-clamp-2 group-hover:text-teal-700 transition" x-text="p.name"></h4>
                                    <div class="text-[10px] text-slate-400 font-medium" x-text="'Kode: ' + (p.code || '-')"></div>
                                    <div class="text-sm font-black text-teal-600 tracking-tight" x-text="formatMoney(p.sell_price) + (p.unit ? ' / ' + p.unit : '')"></div>
                                    
                                    <!-- Soft Stock Badge (Bukan Warna Solid Menjorok) -->
                                    <div class="pt-1">
                                        <template x-if="p.stock <= 0">
                                            <span class="inline-block px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold">Stok Habis (0)</span>
                                        </template>
                                        <template x-if="p.stock > 0 && p.stock < 10">
                                            <span class="inline-block px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold" x-text="'Sisa: ' + p.stock + ' ' + (p.unit || '')"></span>
                                        </template>
                                        <template x-if="p.stock >= 10">
                                            <span class="inline-block px-2 py-0.5 rounded-md bg-teal-50 text-teal-700 border border-teal-200 text-[10px] font-bold" x-text="'Stok: ' + p.stock + ' ' + (p.unit || '')"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State jika pencarian tidak menemukan produk -->
                <div x-show="products.length > 0 && displayedCatalogProducts.length === 0" 
                     class="h-64 bg-white rounded-3xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs space-y-2.5 mt-2">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <i data-lucide="search-x" class="w-6 h-6"></i>
                    </div>
                    <p class="font-bold text-slate-600 text-sm">Produk tidak ditemukan</p>
                    <p class="text-[11px] text-slate-400">Tidak ada produk yang cocok dengan kata kunci atau filter kategori yang dipilih.</p>
                    <button type="button" 
                            @click="productSearchQuery = ''; productFilterCategory = ''" 
                            class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-xl text-xs transition">
                        Reset Filter
                    </button>
                </div>

                <!-- Empty State jika belum ada produk sama sekali -->
                <div x-show="products.length === 0" class="h-64 bg-white rounded-3xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs space-y-2.5 mt-2">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <i data-lucide="package" class="w-6 h-6"></i>
                    </div>
                    <p class="font-bold text-slate-600 text-sm">Belum ada produk aktif</p>
                    <p class="text-[11px] text-slate-400">Tambahkan produk baru melalui panel admin untuk mulai berjualan.</p>
                </div>
            </main>
        </div>

        <!-- =================================================================== -->
        <!-- VIEW 6: DAFTAR KATEGORI PRODUK                                      -->
        <!-- =================================================================== -->
        <div x-show="currentView === 'categories'" class="flex-1 flex flex-col overflow-y-auto pb-28">
            <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3.5 flex items-center justify-between shadow-xs sticky top-0 z-20">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="navigate('dashboard')" 
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 flex items-center justify-center transition active:scale-95 shadow-xs shrink-0" 
                            title="Kembali ke Dashboard">
                        <i data-lucide="arrow-left" class="w-5 h-5 text-slate-700"></i>
                    </button>
                    <h2 class="font-extrabold text-slate-900 text-base leading-tight truncate">Kategori Produk</h2>
                </div>

                <!-- Right Action: Tombol Semua Produk (Tidak Menyusut) -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" 
                            @click="productFilterCategory = ''; navigate('products')" 
                            class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs active:scale-95 shrink-0"
                            title="Lihat Seluruh Produk">
                        <i data-lucide="package" class="w-4 h-4 text-teal-600"></i>
                        <span>Semua Produk</span>
                    </button>
                </div>
            </header>

            <main class="w-full px-4 sm:px-6 py-4 pb-28 space-y-4">
                <!-- Search Bar Ukuran Penuh -->
                <div class="w-full">
                    <div class="relative w-full">
                        <input type="text" 
                               x-model="categorySearchQuery" 
                               placeholder="Cari nama kategori produk..." 
                               class="w-full h-11 sm:h-12 pl-11 pr-10 bg-white border border-slate-200 hover:border-slate-300 focus:border-amber-500 rounded-2xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 shadow-xs transition">
                        <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-3.5 top-3 sm:top-3.5 pointer-events-none"></i>
                        <button type="button" 
                                x-show="categorySearchQuery" 
                                @click="categorySearchQuery = ''" 
                                class="absolute right-3.5 top-3 sm:top-3.5 text-slate-400 hover:text-slate-600 transition"
                                title="Reset pencarian">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Bento Grid Kategori -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="cat in displayedCategories" :key="cat.id">
                        <div @click="openCategoryProducts(cat.id)" 
                             class="bg-white rounded-2xl border border-slate-200 hover:border-amber-500 hover:shadow-md transition p-4 sm:p-5 flex flex-col justify-between shadow-xs cursor-pointer group active:scale-[0.99]">
                            <!-- Top: Icon & Product Count Badge -->
                            <div>
                                <div class="flex items-center justify-between">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center group-hover:scale-105 group-hover:bg-amber-600 group-hover:text-white transition shadow-xs">
                                        <i data-lucide="folder" class="w-6 h-6"></i>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-semibold transition" 
                                          x-text="getCategoryCount(cat.id) + ' Produk'">
                                    </span>
                                </div>

                                <!-- Middle: Category Title & Description -->
                                <div class="mt-3.5 space-y-1">
                                    <h3 class="font-extrabold text-slate-900 text-base sm:text-lg leading-tight group-hover:text-amber-700 transition" x-text="cat.name"></h3>
                                    <p class="text-[11px] text-slate-400 font-medium">Klik untuk melihat seluruh produk dalam kategori ini</p>
                                </div>
                            </div>

                            <!-- Bottom: Action link -->
                            <div class="flex items-center justify-between pt-3 border-t border-slate-100 mt-4 text-xs font-bold text-teal-600 group-hover:text-amber-600 transition">
                                <span>Buka Produk</span>
                                <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition"></i>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State jika pencarian tidak menemukan kategori -->
                <div x-show="categories.length > 0 && displayedCategories.length === 0" 
                     class="h-64 bg-white rounded-3xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs space-y-2.5 mt-2">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <i data-lucide="search-x" class="w-6 h-6"></i>
                    </div>
                    <p class="font-bold text-slate-600 text-sm">Kategori tidak ditemukan</p>
                    <p class="text-[11px] text-slate-400">Tidak ada kategori yang cocok dengan kata kunci pencarian.</p>
                    <button type="button" 
                            @click="categorySearchQuery = ''" 
                            class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-xl text-xs transition">
                        Reset Pencarian
                    </button>
                </div>

                <!-- Empty State jika belum ada kategori sama sekali -->
                <div x-show="categories.length === 0" class="h-64 bg-white rounded-3xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs space-y-2.5 mt-2">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <i data-lucide="folder" class="w-6 h-6"></i>
                    </div>
                    <p class="font-bold text-slate-600 text-sm">Belum ada kategori produk</p>
                    <p class="text-[11px] text-slate-400">Kelompokkan produk Anda dengan menambahkan kategori di panel admin.</p>
                </div>
            </main>
        </div>

        <!-- =================================================================== -->
        <!-- BOTTOM NAVIGATION BAR (Docked & Proper for Tablet / Mobile)         -->
        <!-- =================================================================== -->
        <nav class="fixed bottom-0 inset-x-0 w-full bg-white/95 backdrop-blur-md border-t border-slate-200/90 px-3 sm:px-8 py-2 flex items-center justify-around z-30 shadow-lg"
             x-show="currentView !== 'pos'">
            
            <!-- Tab 1: Dashboard Kasir -->
            <button @click="navigate('dashboard')" 
                    :class="currentView === 'dashboard' ? 'text-teal-600 font-black' : 'text-slate-400 hover:text-slate-600 font-semibold'"
                    class="flex flex-col items-center gap-0.5 text-[11px] px-3 py-1 rounded-xl transition relative">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
                <span>Dashboard</span>
                <span x-show="currentView === 'dashboard'" class="w-1.5 h-1.5 rounded-full bg-teal-600 absolute -bottom-1"></span>
            </button>

            <!-- Tab 2: Produk (Katalog & Stok) -->
            <button @click="productFilterCategory = ''; navigate('products')" 
                    :class="currentView === 'products' ? 'text-teal-600 font-black' : 'text-slate-400 hover:text-slate-600 font-semibold'"
                    class="flex flex-col items-center gap-0.5 text-[11px] px-3 py-1 rounded-xl transition relative">
                <i data-lucide="package" class="w-5 h-5"></i>
                <span>Produk</span>
                <span x-show="currentView === 'products'" class="w-1.5 h-1.5 rounded-full bg-teal-600 absolute -bottom-1"></span>
            </button>

            <!-- Tab 3: Open Bill -->
            <button @click="navigate('open_bills')" 
                    :class="currentView === 'open_bills' ? 'text-teal-600 font-black' : 'text-slate-400 hover:text-slate-600 font-semibold'"
                    class="flex flex-col items-center gap-0.5 text-[11px] px-3 py-1 rounded-xl transition relative">
                <i data-lucide="receipt" class="w-5 h-5"></i>
                <span>Open Bill</span>
                <template x-if="openBills.length > 0">
                    <span class="absolute top-0 right-1 px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[9px] font-black" x-text="openBills.length"></span>
                </template>
                <span x-show="currentView === 'open_bills'" class="w-1.5 h-1.5 rounded-full bg-teal-600 absolute -bottom-1"></span>
            </button>

            <!-- Tab 4: Riwayat Transaksi -->
            <button @click="navigate('history')" 
                    :class="currentView === 'history' ? 'text-teal-600 font-black' : 'text-slate-400 hover:text-slate-600 font-semibold'"
                    class="flex flex-col items-center gap-0.5 text-[11px] px-3 py-1 rounded-xl transition relative">
                <i data-lucide="history" class="w-5 h-5"></i>
                <span>Riwayat</span>
                <span x-show="currentView === 'history'" class="w-1.5 h-1.5 rounded-full bg-teal-600 absolute -bottom-1"></span>
            </button>
        </nav>

    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 1: VOLUME VS NOMINAL DIALOG (Presisi Frame 005 & 006 Video)       -->
    <!-- ======================================================================= -->
    <div x-show="scaleModal.open" class="fixed inset-0 popup-backdrop-soft flex items-center justify-center p-4 z-50" style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl border-2 border-slate-200 text-center space-y-4" @click.away="scaleModal.open = false">
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
    <div x-show="expenseModal.open" class="fixed inset-0 popup-backdrop-soft flex items-center justify-center p-4 z-50" style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl border-2 border-slate-200 text-xs space-y-3" @click.away="expenseModal.open = false">
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
                <div class="space-y-1" x-data="{ open: false }">
                    <label class="block text-slate-600 font-semibold mb-1">Kategori Beban:</label>
                    <div class="relative">
                        <button type="button" 
                                @click="open = !open; $nextTick(() => refreshIcons())" 
                                @click.outside="open = false"
                                class="w-full px-3 py-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 flex items-center justify-between shadow-xs transition active:scale-[0.99]">
                            <div class="flex items-center gap-2 truncate">
                                <i data-lucide="tag" class="w-3.5 h-3.5 text-rose-500 shrink-0"></i>
                                <span class="truncate" x-text="expenseModal.form.category || 'Pilih Kategori Beban'"></span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0 ml-1.5" :class="open ? 'rotate-180 text-rose-500' : ''"></i>
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-1.5 max-h-48 overflow-y-auto space-y-0.5"
                             style="display: none;">
                            <template x-for="ec in expenseCategories" :key="ec.id">
                                <button type="button" 
                                        @click="expenseModal.form.category = ec.name; open = false; refreshIcons()" 
                                        class="w-full px-2.5 py-1.5 rounded-xl text-xs font-bold text-left transition flex items-center justify-between group"
                                        :class="expenseModal.form.category === ec.name ? 'bg-rose-50 text-rose-800 font-black' : 'text-slate-700 hover:bg-slate-100'">
                                    <div class="flex items-center gap-2 truncate">
                                        <i data-lucide="tag" class="w-3.5 h-3.5 shrink-0" :class="expenseModal.form.category === ec.name ? 'text-rose-600' : 'text-slate-400 group-hover:text-slate-600'"></i>
                                        <span class="truncate" x-text="ec.name"></span>
                                    </div>
                                    <template x-if="expenseModal.form.category === ec.name">
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-rose-600"></i>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </div>
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
    <div x-show="variantModal.open" class="fixed inset-0 popup-backdrop-soft flex items-center justify-center p-4 z-50" style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl border-2 border-slate-200 space-y-3" @click.away="variantModal.open = false">
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
                            class="w-full p-3 rounded-2xl border border-slate-200 hover:border-teal-500 hover:bg-slate-100 flex items-center justify-between text-left transition group">
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
    <div x-show="paymentModal" class="fixed inset-0 popup-backdrop-soft flex items-center justify-center p-4 z-50" style="display: none;">
        <div class="bg-white rounded-3xl max-w-4xl w-full p-5 shadow-2xl border-2 border-slate-200 flex flex-col max-h-[92vh] overflow-hidden" @click.away="paymentModal = false">
            
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
                                        <span class="text-[8px] px-1.5 py-0.2 rounded-full font-black uppercase shrink-0 text-white shadow-xs"
                                              :class="{
                                                  'bg-amber-500': c.tier === 'vip',
                                                  'bg-indigo-600': c.tier === 'wholesale',
                                                  'bg-teal-600': c.tier === 'regular' || !c.tier
                                              }"
                                              x-text="c.tier || 'reguler'">
                                        </span>
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
                        <!-- Toggle On/Off Faktur Pajak (PPN) di Modal Pembayaran -->
                        <div class="flex items-center justify-between py-1.5 px-2 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700">
                                <i data-lucide="file-text" class="w-3.5 h-3.5 text-teal-600"></i>
                                <span>Faktur Pajak (PPN <span x-text="settings.tax_percent || 11"></span>%)</span>
                            </div>
                            <button type="button" 
                                    @click="useTaxInvoice = !useTaxInvoice; recalculatePaymentAmounts()"
                                    class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                    :class="useTaxInvoice ? 'bg-teal-600' : 'bg-slate-300'"
                                    :title="useTaxInvoice ? 'Faktur Pajak Aktif' : 'Faktur Pajak Non-Aktif'">
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
                                      :class="useTaxInvoice ? 'translate-x-4' : 'translate-x-0'"></span>
                            </button>
                        </div>

                        <template x-if="useTaxInvoice && taxAmount > 0">
                            <div class="flex justify-between text-teal-700 font-semibold">
                                <span>PPN (<span x-text="settings.tax_percent || 11"></span>%)</span>
                                <span class="font-bold tracking-tight" x-text="formatMoney(taxAmount)"></span>
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
                        <!-- Dropdown Pilihan Metode Pembayaran -->
                        <div class="mb-3.5" x-data="{ open: false }">
                            <label class="block text-slate-700 font-extrabold text-xs mb-1.5">Pilih Metode Pembayaran:</label>
                            <div class="relative">
                                <button type="button" 
                                        @click="open = !open; $nextTick(() => refreshIcons())" 
                                        @click.outside="open = false"
                                        class="w-full px-3.5 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl text-xs font-black text-slate-900 flex items-center justify-between shadow-xs transition active:scale-[0.99]">
                                    <div class="flex items-center gap-2.5 truncate">
                                        <template x-if="paymentMethod === 'cash'">
                                            <i data-lucide="banknote" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                                        </template>
                                        <template x-if="paymentMethod === 'qris'">
                                            <i data-lucide="qr-code" class="w-4 h-4 text-sky-600 shrink-0"></i>
                                        </template>
                                        <template x-if="paymentMethod === 'bank'">
                                            <i data-lucide="landmark" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                        </template>
                                        <template x-if="paymentMethod === 'debt'">
                                            <i data-lucide="book-user" class="w-4 h-4 text-amber-600 shrink-0"></i>
                                        </template>
                                        <template x-if="paymentMethod === 'split'">
                                            <i data-lucide="layers" class="w-4 h-4 text-purple-600 shrink-0"></i>
                                        </template>
                                        <span class="truncate" x-text="getPaymentMethodLabel(paymentMethod)"></span>
                                    </div>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0 ml-1.5" :class="open ? 'rotate-180 text-teal-600' : ''"></i>
                                </button>

                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                     class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 p-1.5 space-y-0.5"
                                     style="display: none;">
                                    <!-- 1. Tunai -->
                                    <button type="button" 
                                            @click="paymentMethod = 'cash'; open = false; recalculatePaymentAmounts(); refreshIcons()"
                                            class="w-full px-3 py-2 rounded-xl text-left transition flex items-center justify-between group"
                                            :class="paymentMethod === 'cash' ? 'bg-emerald-50 text-emerald-900 font-black' : 'text-slate-700 hover:bg-slate-100'">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                                <i data-lucide="banknote" class="w-4 h-4"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold leading-tight">Tunai (Cash)</div>
                                                <div class="text-[10px] text-slate-400 font-normal">Pembayaran uang tunai langsung</div>
                                            </div>
                                        </div>
                                        <template x-if="paymentMethod === 'cash'">
                                            <i data-lucide="check" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                                        </template>
                                    </button>

                                    <!-- 2. QRIS -->
                                    <button type="button" 
                                            @click="paymentMethod = 'qris'; open = false; recalculatePaymentAmounts(); refreshIcons()"
                                            class="w-full px-3 py-2 rounded-xl text-left transition flex items-center justify-between group"
                                            :class="paymentMethod === 'qris' ? 'bg-sky-50 text-sky-900 font-black' : 'text-slate-700 hover:bg-slate-100'">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                                                <i data-lucide="qr-code" class="w-4 h-4"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold leading-tight">QRIS Instan</div>
                                                <div class="text-[10px] text-slate-400 font-normal">BCA, GoPay, OVO, ShopeePay, DANA</div>
                                            </div>
                                        </div>
                                        <template x-if="paymentMethod === 'qris'">
                                            <i data-lucide="check" class="w-4 h-4 text-sky-600 shrink-0"></i>
                                        </template>
                                    </button>

                                    <!-- 3. Transfer Bank -->
                                    <button type="button" 
                                            @click="paymentMethod = 'bank'; open = false; recalculatePaymentAmounts(); refreshIcons()"
                                            class="w-full px-3 py-2 rounded-xl text-left transition flex items-center justify-between group"
                                            :class="paymentMethod === 'bank' ? 'bg-teal-50 text-teal-900 font-black' : 'text-slate-700 hover:bg-slate-100'">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center shrink-0">
                                                <i data-lucide="landmark" class="w-4 h-4"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold leading-tight">Transfer Bank</div>
                                                <div class="text-[10px] text-slate-400 font-normal">Transfer ke rekening toko</div>
                                            </div>
                                        </div>
                                        <template x-if="paymentMethod === 'bank'">
                                            <i data-lucide="check" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                        </template>
                                    </button>

                                    <!-- 4. Kasbon (Hutang) -->
                                    <button type="button" 
                                            @click="paymentMethod = 'debt'; open = false; recalculatePaymentAmounts(); refreshIcons()"
                                            class="w-full px-3 py-2 rounded-xl text-left transition flex items-center justify-between group"
                                            :class="paymentMethod === 'debt' ? 'bg-amber-50 text-amber-900 font-black' : 'text-slate-700 hover:bg-slate-100'">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                                <i data-lucide="book-user" class="w-4 h-4"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold leading-tight">Kasbon (Tempo / Hutang)</div>
                                                <div class="text-[10px] text-slate-400 font-normal">Khusus member terdaftar</div>
                                            </div>
                                        </div>
                                        <template x-if="paymentMethod === 'debt'">
                                            <i data-lucide="check" class="w-4 h-4 text-amber-600 shrink-0"></i>
                                        </template>
                                    </button>

                                    <!-- 5. Split Bill -->
                                    <button type="button" 
                                            @click="paymentMethod = 'split'; open = false; recalculatePaymentAmounts(); refreshIcons()"
                                            class="w-full px-3 py-2 rounded-xl text-left transition flex items-center justify-between group"
                                            :class="paymentMethod === 'split' ? 'bg-purple-50 text-purple-900 font-black' : 'text-slate-700 hover:bg-slate-100'">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                                                <i data-lucide="layers" class="w-4 h-4"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold leading-tight">Split Bill (Campuran)</div>
                                                <div class="text-[10px] text-slate-400 font-normal">Kombinasi tunai dan non-tunai</div>
                                            </div>
                                        </div>
                                        <template x-if="paymentMethod === 'split'">
                                            <i data-lucide="check" class="w-4 h-4 text-purple-600 shrink-0"></i>
                                        </template>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 1. VIEW TUNAI (Cash) -->
                        <div x-show="paymentMethod === 'cash'" class="space-y-2.5">
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3">
                                <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Uang Diterima (Rp)</span>
                                <div class="text-2xl font-black text-slate-900 tracking-tight mt-0.5" x-text="formatMoney(cashTendered)"></div>
                            </div>

                            <div class="rounded-2xl p-3 border"
                                 :class="cashTendered >= total ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider" x-text="cashTendered >= total ? 'Kembalian' : 'Kekurangan'"></span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-black text-white"
                                          :class="cashTendered >= total ? 'bg-emerald-600' : 'bg-rose-600'"
                                          x-text="cashTendered >= total ? 'Lunas' : 'Belum Cukup'"></span>
                                </div>
                                <div class="text-xl font-black tracking-tight mt-1" 
                                     x-text="cashTendered >= total ? formatMoney(changeAmount) : formatMoney(total - cashTendered)"></div>
                            </div>
                        </div>

                        <!-- 2. VIEW QRIS INSTAN -->
                        <div x-show="paymentMethod === 'qris'" class="text-center p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2 text-xs">
                            <div class="bg-white p-2 rounded-xl border border-slate-200 inline-block shadow-xs">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=00020101021126570011ID.NODERAPAY.WWW01189360091100213049020208123456785204541153033605802ID5914NODERA%20POS6007JAKARTA61051234062070703A016304ABCD" 
                                     class="w-32 h-32 mx-auto rounded-lg" alt="QRIS">
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-500 font-semibold">Total Tagihan QRIS</div>
                                <div class="text-lg font-black text-slate-900 mt-0.5" x-text="formatMoney(total)"></div>
                                <p class="text-[10px] text-slate-500 mt-1">Scan via BCA, Mandiri, BRI, BNI, GoPay, OVO, ShopeePay, atau DANA</p>
                            </div>
                        </div>

                        <!-- 3. VIEW TRANSFER BANK -->
                        <div x-show="paymentMethod === 'bank'" class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2.5 text-xs">
                            <div>
                                <span class="text-[10px] text-slate-500 font-bold block uppercase tracking-wider">Rekening Tujuan Toko</span>
                                <div class="font-extrabold text-slate-900 text-sm mt-0.5" x-text="settings.bank_name || 'Bank BCA'"></div>
                                <div class="text-xl font-black text-teal-700 tracking-wider mt-0.5" x-text="settings.bank_account_no || '1234-5678-90'"></div>
                                <div class="text-[11px] text-slate-600 font-medium mt-0.5" x-text="'a.n ' + (settings.bank_account_holder || settings.store_name || 'Toko POS Pro')"></div>
                            </div>
                            <button @click="copyBankNo()" class="w-full py-2 bg-white hover:bg-slate-100 text-slate-800 font-bold rounded-xl border border-slate-300 text-xs flex items-center justify-center gap-1.5 transition shadow-xs">
                                <i data-lucide="copy" class="w-3.5 h-3.5 text-slate-600"></i>
                                <span>Salin No. Rekening</span>
                            </button>
                        </div>

                        <!-- 4. VIEW KASBON (TEMPO) -->
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
                                                <span class="text-[8px] px-1.5 py-0.2 rounded-full font-black uppercase shrink-0 text-white shadow-xs"
                                              :class="{
                                                  'bg-amber-500': c.tier === 'vip',
                                                  'bg-indigo-600': c.tier === 'wholesale',
                                                  'bg-teal-600': c.tier === 'regular' || !c.tier
                                              }"
                                              x-text="c.tier || 'reguler'">
                                        </span>
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

                        <!-- 5. VIEW SPLIT (CAMPURAN) -->
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

                    <!-- Tombol Konfirmasi & Cetak Struk -->
                    <button @click="processCheckout()" 
                            :disabled="(paymentMethod === 'cash' && cashTendered < total) || (paymentMethod === 'debt' && !selectedCustomerId) || (paymentMethod === 'split' && (splitCash + splitNonCash) < total)"
                            :class="((paymentMethod !== 'cash' || cashTendered >= total) && (paymentMethod !== 'debt' || selectedCustomerId) && (paymentMethod !== 'split' || (splitCash + splitNonCash) >= total)) ? 'bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-600/20' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                            class="w-full py-3.5 rounded-2xl text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Konfirmasi & Cetak Struk</span>
                    </button>
                </div>

                <!-- KOLOM 3 (KANAN - 4/12): NUMPAD & KONTROL OPERASIONAL -->
                <div class="md:col-span-4 flex flex-col justify-between">
                    
                    <!-- Numpad & Quick Cash (Untuk Tunai & Split) -->
                    <div x-show="paymentMethod === 'cash'">
                        <!-- Quick Money Pills -->
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

                        <!-- Tombol Uang Pas -->
                        <button @click="setQuickCash('exact')" class="w-full py-2.5 rounded-xl border border-teal-600 text-teal-600 hover:bg-teal-50 font-bold text-xs transition">
                            Uang Pas
                        </button>
                    </div>

                    <!-- Ringkasan Operasional untuk Metode Non-Tunai -->
                    <div x-show="paymentMethod !== 'cash'" class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-3 text-xs h-full flex flex-col justify-between">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                <span class="font-extrabold text-slate-800 text-xs">Verifikasi Transaksi</span>
                                <span class="text-[9px] px-2.5 py-0.5 rounded-full font-black uppercase tracking-wider text-white bg-teal-600" x-text="paymentMethod"></span>
                            </div>
                            <div class="space-y-2 text-xs text-slate-600">
                                <div class="flex justify-between">
                                    <span>Kasir Bertugas:</span>
                                    <span class="font-bold text-slate-800" x-text="currentUser.name"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Total Ditagihkan:</span>
                                    <span class="font-black text-slate-900 text-sm" x-text="formatMoney(total)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Status Tagihan:</span>
                                    <span class="font-bold text-emerald-700">Siap Konfirmasi</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-3 rounded-xl border border-slate-200 text-[11px] space-y-1">
                            <span class="font-bold text-slate-800 block">Petunjuk Kasir:</span>
                            <p class="text-slate-500 leading-relaxed text-[10px]">
                                Pastikan dana telah terkonfirmasi masuk atau nota tempo telah disetujui pelanggan sebelum menyelesaikan transaksi.
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 4: STRUK THERMAL 58MM (Presisi Sempurna Frame 015 Video TikTok)   -->
    <!-- ======================================================================= -->
    <div x-show="receiptModal" class="fixed inset-0 popup-backdrop-soft flex items-center justify-center p-4 z-50" style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl border-2 border-slate-200 flex flex-col max-h-[92vh]">
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

    <!-- ======================================================================= -->
    <!-- MODAL 5: PIN LOCK SCREEN & SWITCH USER                                 -->
    <!-- ======================================================================= -->
    <div x-show="isLocked" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 popup-backdrop-soft flex items-center justify-center p-4 z-50" 
         style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 sm:p-6 shadow-2xl flex flex-col items-center border border-slate-200 relative">
            
            <!-- Tombol Batal jika sudah ada user aktif -->
            <button x-show="currentUser && currentUser.id" 
                    @click="isLocked = false; pinInput = ''; pinError = '';" 
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition"
                    title="Batal Kunci">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>

            <!-- Header & Lock Icon (Solid) -->
            <div class="w-12 h-12 rounded-2xl bg-teal-600 text-white flex items-center justify-center mb-2.5 shadow-md shadow-teal-600/20">
                <i data-lucide="lock" class="w-6 h-6 text-white"></i>
            </div>
            
            <h2 class="text-base font-black text-slate-800 tracking-tight" x-text="settings.store_name || 'Kasir POS'"></h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Masukkan 4-digit PIN Pengguna</p>

            <!-- User Selector Tabs -->
            <div class="w-full flex gap-1.5 p-1 bg-slate-100 rounded-2xl my-3.5 border border-slate-200">
                <template x-for="u in authUsers" :key="u.id">
                    <button type="button" 
                            @click="selectAuthUser(u)"
                            class="flex-1 py-1.5 px-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5"
                            :class="selectedAuthUser && selectedAuthUser.id === u.id ? 'bg-white text-teal-700 shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-700'">
                        <i :data-lucide="u.role === 'admin' ? 'shield-check' : 'user'" class="w-3.5 h-3.5"></i>
                        <span class="truncate" x-text="u.name"></span>
                    </button>
                </template>
            </div>

            <!-- PIN Display Dots -->
            <div class="flex items-center justify-center gap-3.5 my-2">
                <template x-for="i in 4" :key="i">
                    <div class="w-4 h-4 rounded-full border-2 transition-all duration-200"
                         :class="pinInput.length >= i ? 'bg-teal-600 border-teal-600 scale-110 shadow-xs' : 'border-slate-300 bg-slate-50'">
                    </div>
                </template>
            </div>

            <!-- Error Notification -->
            <div class="h-6 flex items-center justify-center mb-1.5">
                <span x-show="pinError" 
                      x-text="pinError" 
                      class="text-xs font-bold text-rose-600 animate-pulse"></span>
            </div>

            <!-- Numeric Keypad Grid -->
            <div class="grid grid-cols-3 gap-2 w-full">
                <template x-for="num in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="num">
                    <button type="button" 
                            @click="pressPin(num)"
                            class="py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-800 font-black text-lg transition border border-slate-200/60 shadow-xs">
                        <span x-text="num"></span>
                    </button>
                </template>
                <button type="button" 
                        @click="clearPin()"
                        class="py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-black text-xs transition shadow-xs uppercase tracking-wider">
                    Hapus
                </button>
                <button type="button" 
                        @click="pressPin(0)"
                        class="py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-800 font-black text-lg transition border border-slate-200/60 shadow-xs">
                    0
                </button>
                <button type="button" 
                        @click="backspacePin()"
                        class="py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 font-bold transition border border-slate-200/60 shadow-xs flex items-center justify-center">
                    <i data-lucide="delete" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Hint text -->
            <div class="mt-3.5 text-center">
                <span class="text-[11px] text-slate-400 font-medium">Bisa juga menggunakan tombol keyboard fisik</span>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- FLOATING TOAST NOTIFICATION (Sistem Notifikasi Modern Pengganti Alert)   -->
    <!-- ======================================================================= -->
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
         class="fixed top-5 inset-x-0 flex justify-center z-50 pointer-events-none px-4"
         style="display: none;">
        <div class="pointer-events-auto px-4 py-2.5 rounded-2xl shadow-xl flex items-center gap-2.5 max-w-md border text-xs font-bold"
             :class="{
                 'bg-emerald-600 text-white border-emerald-500 shadow-emerald-600/30': toast.type === 'success',
                 'bg-rose-600 text-white border-rose-500 shadow-rose-600/30': toast.type === 'error',
                 'bg-sky-600 text-white border-sky-500 shadow-sky-600/30': toast.type === 'info',
                 'bg-amber-600 text-white border-amber-500 shadow-amber-600/30': toast.type === 'warning'
             }">
            <template x-if="toast.type === 'success'"><i data-lucide="check-circle" class="w-4 h-4 shrink-0 text-white"></i></template>
            <template x-if="toast.type === 'error'"><i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-white"></i></template>
            <template x-if="toast.type === 'info'"><i data-lucide="info" class="w-4 h-4 shrink-0 text-white"></i></template>
            <template x-if="toast.type === 'warning'"><i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 text-white"></i></template>
            <span x-text="toast.message" class="leading-snug"></span>
            <button type="button" @click="toast.show = false" class="ml-2 text-white/80 hover:text-white p-0.5">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL CONFIRM DIALOG (Pengganti Bawaan Browser Confirm - Soft Blur Fokus) -->
    <!-- ======================================================================= -->
    <div x-show="confirmDialog.open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 popup-backdrop-soft flex items-center justify-center p-4 z-[120]"
         style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border-2 border-slate-200 flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center mb-3 shadow-md shadow-rose-600/20">
                <i data-lucide="help-circle" class="w-6 h-6 text-white"></i>
            </div>
            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base leading-snug" x-text="confirmDialog.title"></h3>
            <p class="text-xs text-slate-500 mt-1 mb-5" x-text="confirmDialog.message"></p>
            <div class="grid grid-cols-2 gap-2.5 w-full">
                <button type="button" 
                        @click="confirmDialog.open = false" 
                        class="py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs transition active:scale-95 shadow-xs">
                    Batal
                </button>
                <button type="button" 
                        @click="handleConfirm()" 
                        :class="confirmDialog.confirmClass" 
                        class="py-2.5 rounded-xl text-white font-extrabold text-xs shadow-md transition active:scale-95"
                        x-text="confirmDialog.confirmText">
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL ALERT POP-UP STOK KOSONG / MELEBIHI BATAS                         -->
    <!-- ======================================================================= -->
    <div x-show="stockAlertModal.open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 popup-backdrop-soft flex items-center justify-center p-4 z-[130]"
         style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border-2 border-slate-200 flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mb-3.5 shadow-sm">
                <i data-lucide="package-x" class="w-7 h-7 text-rose-600"></i>
            </div>
            <h3 class="font-black text-slate-900 text-base leading-snug" 
                x-text="stockAlertModal.isExceeded ? 'Batas Stok Tercapai' : 'Stok Produk Habis'"></h3>
            <p class="text-xs text-slate-500 mt-2 mb-5 leading-relaxed">
                <template x-if="!stockAlertModal.isExceeded">
                    <span>Produk <strong class="text-slate-800" x-text="stockAlertModal.productName"></strong> sudah habis (<strong class="text-rose-600 font-extrabold" x-text="'Stok: 0 ' + stockAlertModal.unit"></strong>) dan tidak dapat ditambahkan ke keranjang penjualan.</span>
                </template>
                <template x-if="stockAlertModal.isExceeded">
                    <span>Jumlah yang diminta melebihi sisa persediaan. Maksimal stok tersedia untuk <strong class="text-slate-800" x-text="stockAlertModal.productName"></strong> adalah <strong class="text-teal-700 font-extrabold" x-text="stockAlertModal.productStock + ' ' + stockAlertModal.unit"></strong>.</span>
                </template>
            </p>
            <button type="button" 
                    @click="stockAlertModal.open = false" 
                    class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition active:scale-95 shadow-md">
                Mengerti & Tutup
            </button>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL SIMPAN OPEN BILL (Pengganti Bawaan Browser Prompt - Soft Blur Fokus)-->
    <!-- ======================================================================= -->
    <div x-show="saveBillModal.open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 popup-backdrop-soft flex items-center justify-center p-4 z-50"
         style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 sm:p-6 shadow-2xl border-2 border-slate-200 flex flex-col space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-600 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="receipt" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Simpan Open Bill</h3>
                        <p class="text-[10px] text-slate-400">Simpan transaksi tertunda untuk diproses nanti</p>
                    </div>
                </div>
                <button type="button" @click="saveBillModal.open = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Ringkasan Item di Keranjang -->
            <div class="p-3 bg-slate-100 rounded-2xl border border-slate-200 flex items-center justify-between text-xs">
                <span class="text-slate-600 font-bold" x-text="totalItemsCount + ' item di keranjang'"></span>
                <span class="font-black text-teal-600 text-sm" x-text="formatMoney(total)"></span>
            </div>

            <!-- Form Input Nama Bill / Meja -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Nama Meja / Tagihan</label>
                <input type="text" 
                       x-model="saveBillModal.billName"
                       @keydown.enter.prevent="submitSaveBill()"
                       placeholder="Contoh: Meja 03 atau Mas Budi" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-xs font-bold text-slate-800 outline-none transition">
            </div>

            <!-- Preset Cepat Meja / Order -->
            <div class="space-y-1">
                <span class="block text-[10px] font-bold text-slate-500">Pilihan Cepat:</span>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="preset in saveBillModal.quickPresets" :key="preset">
                        <button type="button" 
                                @click="saveBillModal.billName = preset"
                                :class="saveBillModal.billName === preset ? 'bg-amber-600 text-white font-black shadow-xs' : 'bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold'"
                                class="px-2.5 py-1 rounded-lg text-[10px] transition active:scale-95"
                                x-text="preset">
                        </button>
                    </template>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button type="button" 
                        @click="saveBillModal.open = false" 
                        class="py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs transition">
                    Batal
                </button>
                <button type="button" 
                        @click="submitSaveBill()" 
                        class="py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 active:scale-95 text-white font-extrabold text-xs shadow-md shadow-amber-600/20 transition flex items-center justify-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Simpan Bill</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 8: FILTER TANGGAL TRANSAKSI (POP UP KALENDER BLUR HALUS)          -->
    <!-- ======================================================================= -->
    <div x-show="dateFilterModal.open" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 popup-backdrop-soft flex items-center justify-center p-4 z-50" 
         style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 sm:p-6 shadow-2xl border-2 border-slate-200 flex flex-col space-y-4"
             @click.away="dateFilterModal.open = false">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="calendar" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Filter Tanggal</h3>
                        <p class="text-[10px] text-slate-400">Pilih rentang tanggal riwayat transaksi</p>
                    </div>
                </div>
                <button type="button" @click="dateFilterModal.open = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Preset Pilihan Cepat -->
            <div class="space-y-1">
                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Pilihan Cepat:</span>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" 
                            @click="setDatePreset('today')"
                            :class="dateFilterModal.selectedDate === todayDate ? 'bg-teal-600 text-white font-black shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold'"
                            class="py-2.5 px-3 rounded-xl text-xs transition active:scale-95 flex items-center justify-center gap-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                        <span>Hari Ini</span>
                    </button>
                    <button type="button" 
                            @click="setDatePreset('yesterday')"
                            :class="isYesterday(dateFilterModal.selectedDate) ? 'bg-teal-600 text-white font-black shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold'"
                            class="py-2.5 px-3 rounded-xl text-xs transition active:scale-95 flex items-center justify-center gap-1.5">
                        <i data-lucide="calendar-days" class="w-3.5 h-3.5"></i>
                        <span>Kemarin</span>
                    </button>
                </div>
            </div>

            <!-- Input Tanggal Spesifik -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Pilih Tanggal Spesifik</label>
                <input type="date" 
                       x-model="dateFilterModal.selectedDate"
                       @keydown.enter.prevent="applyDateFilter()"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-xs font-bold text-slate-800 outline-none transition bg-slate-50">
            </div>

            <!-- Tombol Aksi -->
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                <button type="button" 
                        @click="dateFilterModal.open = false" 
                        class="py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs transition">
                    Batal
                </button>
                <button type="button" 
                        @click="applyDateFilter()" 
                        class="py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 active:scale-95 text-white font-extrabold text-xs shadow-md shadow-teal-600/20 transition flex items-center justify-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Terapkan</span>
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
