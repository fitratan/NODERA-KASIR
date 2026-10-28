<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back-Office Admin - POS Offline Pro</title>
    
    <!-- Offline First Assets -->
    <link rel="stylesheet" href="../assets/css/tailwind.min.css">
    <script src="../assets/js/tailwindcss.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        teal: {
                            50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 300: '#5eead4',
                            400: '#2dd4bf', 500: '#14b8a6', 600: '#0d9488', 700: '#0f766e',
                            800: '#115e59', 900: '#134e4a'
                        },
                        slate: {
                            50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1',
                            400: '#94a3b8', 500: '#64748b', 600: '#475569', 700: '#334155',
                            800: '#1e293b', 900: '#0f172a'
                        }
                    }
                }
            }
        }
    </script>
    
    <script src="../assets/js/alpine.min.js" defer></script>
    <script src="../assets/js/lucide.min.js"></script>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='24' fill='%230d9488'/><text x='50%' y='68%' font-size='60' font-weight='900' font-family='sans-serif' fill='white' text-anchor='middle'>A</text></svg>">

    <style>
        :root { --pos-teal: #0d9488; }
        .bg-teal-600 { background-color: #0d9488 !important; }
        .bg-teal-700 { background-color: #0f766e !important; }
        .bg-teal-50 { background-color: #f0fdfa !important; }
        .text-teal-600 { color: #0d9488 !important; }
        .text-teal-700 { color: #0f766e !important; }
        .border-teal-500 { border-color: #14b8a6 !important; }
        .border-teal-600 { border-color: #0d9488 !important; }
        svg.lucide, i[data-lucide] svg, svg { display: inline-block; vertical-align: middle; stroke-width: 2; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex h-screen overflow-hidden select-none" x-data="adminApp()">

    <!-- PIN SECURITY MODAL OVERLAY -->
    <div x-show="!isAuthorized" class="fixed inset-0 bg-slate-900/90 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl space-y-4">
            <div class="w-14 h-14 rounded-2xl bg-teal-600 text-white mx-auto flex items-center justify-center shadow-md shadow-teal-600/20">
                <i data-lucide="lock" class="w-7 h-7"></i>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Portal Back-Office Owner</h2>
                <p class="text-xs text-slate-500 mt-1">Masukkan PIN Otorisasi Admin untuk mengelola data rahasia toko.</p>
            </div>
            <div>
                <input type="password" maxlength="6" x-model="authPin" @keyup.enter="verifyPin()" 
                       class="w-full text-center text-3xl font-bold tracking-widest px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-teal-500 focus:outline-none"
                       placeholder="••••" autofocus>
                <p x-show="authError" class="text-rose-500 text-xs font-semibold mt-2" x-text="authError"></p>
            </div>
            <div class="flex gap-2">
                <button @click="verifyPin()" class="flex-1 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs transition shadow-md shadow-teal-600/20">
                    Buka Panel Admin
                </button>
                <a href="../" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition flex items-center justify-center">
                    Ke Kasir
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN SIDEBAR WITH ACCORDION DROPDOWNS -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between border-r border-slate-800 shadow-xl shrink-0 z-20 overflow-hidden">
        <div class="flex-1 flex flex-col overflow-y-auto">
            
            <!-- Store Branding Header -->
            <div class="p-5 border-b border-slate-800/80 flex items-center gap-3 shrink-0">
                <template x-if="settings.store_logo">
                    <img :src="settings.store_logo" class="w-9 h-9 rounded-xl object-contain bg-white p-1">
                </template>
                <template x-if="!settings.store_logo">
                    <div class="w-9 h-9 rounded-xl bg-teal-500 text-white flex items-center justify-center font-black text-sm">
                        A
                    </div>
                </template>
                <div class="overflow-hidden">
                    <h1 class="font-extrabold text-white text-sm truncate" x-text="settings.store_name || 'POS Offline Pro'"></h1>
                    <span class="text-[10px] text-teal-400 font-semibold tracking-wider uppercase">Back-Office Admin</span>
                </div>
            </div>

            <!-- Grouped Accordion Navigation -->
            <nav class="p-3 space-y-1.5 text-xs">
                
                <!-- 1. DASHBOARD KEUNANGAN (Single Link) -->
                <button @click="setTab('dashboard')" 
                        :class="currentTab === 'dashboard' ? 'bg-teal-600 text-white font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard Keuangan</span>
                </button>

                <!-- 2. DROPDOWN: MASTER DATA / KATALOG -->
                <div class="space-y-1">
                    <button @click="toggleMenu('catalog')" 
                            class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                        <div class="flex items-center gap-3 font-semibold">
                            <i data-lucide="package" class="w-4 h-4 text-teal-400"></i>
                            <span>Master Data</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200" :class="menuDropdowns.catalog ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="menuDropdowns.catalog" class="pl-7 pr-1 space-y-0.5 border-l border-slate-800 ml-4 py-1">
                        <button @click="setTab('categories')" 
                                :class="currentTab === 'categories' ? 'text-teal-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/40'"
                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg transition text-left">
                            <i data-lucide="tags" class="w-3.5 h-3.5"></i>
                            <span>Kategori Produk</span>
                        </button>
                        <button @click="setTab('products')" 
                                :class="currentTab === 'products' ? 'text-teal-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/40'"
                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg transition text-left">
                            <i data-lucide="boxes" class="w-3.5 h-3.5"></i>
                            <span>Daftar Produk & Varian</span>
                        </button>
                    </div>
                </div>

                <!-- 3. DROPDOWN: PELANGGAN & HUTANG -->
                <div class="space-y-1">
                    <button @click="toggleMenu('customers')" 
                            class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                        <div class="flex items-center gap-3 font-semibold">
                            <i data-lucide="users" class="w-4 h-4 text-sky-400"></i>
                            <span>Pelanggan & Hutang</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200" :class="menuDropdowns.customers ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="menuDropdowns.customers" class="pl-7 pr-1 space-y-0.5 border-l border-slate-800 ml-4 py-1">
                        <button @click="setTab('customers')" 
                                :class="currentTab === 'customers' ? 'text-teal-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/40'"
                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg transition text-left">
                            <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                            <span>Data Pelanggan (Member)</span>
                        </button>
                        <button @click="setTab('debts')" 
                                :class="currentTab === 'debts' ? 'text-teal-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/40'"
                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg transition text-left">
                            <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                            <span>Buku Kasbon (Piutang)</span>
                        </button>
                    </div>
                </div>

                <!-- 4. DROPDOWN: ARUS KAS & SHIFT -->
                <div class="space-y-1">
                    <button @click="toggleMenu('finance')" 
                            class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                        <div class="flex items-center gap-3 font-semibold">
                            <i data-lucide="wallet" class="w-4 h-4 text-emerald-400"></i>
                            <span>Arus Kas & Pengeluaran</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200" :class="menuDropdowns.finance ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="menuDropdowns.finance" class="pl-7 pr-1 space-y-0.5 border-l border-slate-800 ml-4 py-1">
                        <button @click="setTab('expenses')" 
                                :class="currentTab === 'expenses' ? 'text-teal-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/40'"
                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg transition text-left">
                            <i data-lucide="minus-circle" class="w-3.5 h-3.5"></i>
                            <span>Kas Keluar (Petty Cash)</span>
                        </button>
                        <button @click="setTab('expense_categories')" 
                                :class="currentTab === 'expense_categories' ? 'text-teal-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/40'"
                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg transition text-left">
                            <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                            <span>Kategori Pengeluaran</span>
                        </button>
                    </div>
                </div>

                <!-- 5. KARYAWAN & KOMISI (Single Link) -->
                <button @click="setTab('employees')" 
                        :class="currentTab === 'employees' ? 'bg-teal-600 text-white font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition">
                    <i data-lucide="badge-percent" class="w-4 h-4"></i>
                    <span>Karyawan & Komisi</span>
                </button>

                <!-- 6. DROPDOWN: PENGATURAN TOKO & INTEGRASI -->
                <div class="space-y-1">
                    <button @click="toggleMenu('settings')" 
                            class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                        <div class="flex items-center gap-3 font-semibold">
                            <i data-lucide="sliders" class="w-4 h-4 text-amber-400"></i>
                            <span>Pengaturan Sistem</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200" :class="menuDropdowns.settings ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="menuDropdowns.settings" class="pl-7 pr-1 space-y-0.5 border-l border-slate-800 ml-4 py-1">
                        <button @click="setTab('settings_profile')" 
                                :class="currentTab === 'settings_profile' ? 'text-teal-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/40'"
                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg transition text-left">
                            <i data-lucide="store" class="w-3.5 h-3.5"></i>
                            <span>Profil Usaha & Logo</span>
                        </button>
                        <button @click="setTab('settings_notifications')" 
                                :class="currentTab === 'settings_notifications' ? 'text-teal-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/40'"
                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg transition text-left">
                            <i data-lucide="bell" class="w-3.5 h-3.5"></i>
                            <span>Notifikasi Telegram & WA</span>
                        </button>
                        <button @click="setTab('settings_security')" 
                                :class="currentTab === 'settings_security' ? 'text-teal-400 font-bold bg-slate-800/80' : 'text-slate-400 hover:text-white hover:bg-slate-800/40'"
                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg transition text-left">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            <span>PIN Keamanan & Backup</span>
                        </button>
                    </div>
                </div>

            </nav>
        </div>

        <!-- Quick Switch to Cashier Front-End -->
        <div class="p-4 border-t border-slate-800/80 space-y-2 shrink-0">
            <a href="../" class="w-full py-2.5 px-3 bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/30 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                <span>Buka Layar Kasir</span>
            </a>
            <div class="text-[11px] text-slate-500 text-center">
                POS Offline Pro v2.0
            </div>
        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <main class="flex-1 flex flex-col overflow-y-auto bg-slate-50">
        
        <!-- Top Sticky Header -->
        <header class="bg-white border-b border-slate-200/90 px-8 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-black text-slate-900" x-text="tabTitles[currentTab]"></h2>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-emerald-600 text-white text-[11px] font-bold flex items-center gap-1.5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    <span>Database SQLite Aktif</span>
                </span>
                <button @click="sessionStorage.removeItem('admin_authorized'); isAuthorized = false;" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" title="Kunci Panel">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                </button>
            </div>
        </header>

        <!-- DYNAMIC TAB VIEWS -->
        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- =============================================================== -->
            <!-- TAB 1: DASHBOARD EKSEKUTIF                                      -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'dashboard'" class="space-y-6">
                <!-- Metrics -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Omzet Penjualan</span>
                        <div class="text-2xl font-black text-slate-900 mt-1" x-text="formatMoney(reportData.summary?.total_turnover)"></div>
                        <span class="text-[11px] text-slate-400 mt-1 block" x-text="(reportData.summary?.total_transactions || 0) + ' Transaksi tercatat'"></span>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Modal (HPP)</span>
                        <div class="text-2xl font-black text-slate-700 mt-1" x-text="formatMoney(reportData.summary?.total_cost)"></div>
                        <span class="text-[11px] text-slate-400 mt-1 block">Beban pokok penjualan</span>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pengeluaran Toko</span>
                        <div class="text-2xl font-black text-rose-600 mt-1" x-text="formatMoney(reportData.summary?.total_expenses)"></div>
                        <span class="text-[11px] text-slate-400 mt-1 block">Kas keluar operasional</span>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-teal-500/30 bg-gradient-to-br from-white to-teal-50/40 shadow-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-[11px] font-bold text-teal-800 uppercase tracking-wider">Laba Bersih Toko</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-teal-600 text-white text-[10px] font-black shadow-sm" x-text="(reportData.summary?.margin_percent || 0) + '% Margin'"></span>
                        </div>
                        <div class="text-2xl font-black text-teal-600 mt-1" x-text="formatMoney(reportData.summary?.net_profit)"></div>
                        <span class="text-[11px] text-teal-700/80 mt-1 block">Laba kotor dikurangi biaya</span>
                    </div>
                </div>

                <!-- Two Columns: Top 5 & Low Stock Alerts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Top Products -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i data-lucide="flame" class="w-4 h-4 text-amber-500"></i>
                            <span>5 Produk Terlaris</span>
                        </h3>
                        <div class="divide-y divide-slate-100 text-xs">
                            <template x-for="(tp, idx) in reportData.top_products || []" :key="tp.product_name">
                                <div class="py-2.5 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="w-6 h-6 rounded-full bg-slate-800 font-black text-[10px] text-white flex items-center justify-center shadow-sm" x-text="idx + 1"></span>
                                        <span class="font-bold text-slate-800" x-text="tp.product_name"></span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-slate-900" x-text="tp.total_qty + ' terjual'"></span>
                                        <span class="text-slate-400 text-[10px] block" x-text="formatMoney(tp.total_sales)"></span>
                                    </div>
                                </div>
                            </template>
                            <div x-show="!reportData.top_products || reportData.top_products.length === 0" class="py-6 text-center text-slate-400">
                                Belum ada data penjualan hari ini.
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Alerts -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500"></i>
                            <span>Peringatan Stok Menipis</span>
                        </h3>
                        <div class="divide-y divide-slate-100 text-xs">
                            <template x-for="ls in reportData.low_stock || []" :key="ls.id">
                                <div class="py-2.5 flex items-center justify-between">
                                    <div>
                                        <span class="font-bold text-slate-800 block" x-text="ls.name"></span>
                                        <span class="text-slate-400 text-[10px]" x-text="'Batas Minimum: ' + ls.min_stock_alert + ' ' + ls.unit"></span>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full bg-rose-600 text-white font-extrabold text-xs shadow-sm"
                                          x-text="'Sisa: ' + ls.stock + ' ' + ls.unit"></span>
                                </div>
                            </template>
                            <div x-show="!reportData.low_stock || reportData.low_stock.length === 0" class="py-6 text-center text-emerald-600 font-semibold flex items-center justify-center gap-1.5">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span>Semua stok barang masih dalam kondisi aman.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 2: MASTER KATEGORI (UPLOAD FOTO DENGAN PREVIEW)            -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'categories'" class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-500">Kelola kategori visual dengan foto produk langsung dari komputer/HP untuk tampilan kasir cepat.</div>
                    <button @click="openCategoryModal()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition shadow">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Kategori Baru</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="cat in categories" :key="cat.id">
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex flex-col justify-between hover:border-teal-500 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                    <template x-if="cat.image_url">
                                        <img :src="cat.image_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!cat.image_url">
                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                            <i data-lucide="tag" class="w-6 h-6"></i>
                                        </div>
                                    </template>
                                </div>
                                <div class="overflow-hidden">
                                    <h4 class="font-bold text-slate-900 text-sm truncate" x-text="cat.name"></h4>
                                    <span class="text-xs text-slate-400" x-text="(cat.product_count || 0) + ' Produk Terhubung'"></span>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center">
                                <span class="text-[10px] font-semibold text-slate-400 uppercase" x-text="'Urutan: ' + cat.sort_order"></span>
                                <div class="space-x-1">
                                    <button @click="editCategory(cat)" class="p-1 rounded-lg hover:bg-slate-100 text-slate-600">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button @click="deleteCategory(cat.id)" class="p-1 rounded-lg hover:bg-rose-50 text-rose-500">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 3: MASTER PRODUK                                            -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'products'" class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-500">Kelola katalog barang, foto produk, harga modal HPP, satuan, dan varian.</div>
                    <button @click="openProductModal()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition shadow">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Produk Baru</span>
                    </button>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="p-3.5">Foto</th>
                                    <th class="p-3.5">Nama & Kategori</th>
                                    <th class="p-3.5">Satuan</th>
                                    <th class="p-3.5">HPP Modal</th>
                                    <th class="p-3.5">Harga Jual</th>
                                    <th class="p-3.5">Stok</th>
                                    <th class="p-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="p in products" :key="p.id">
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="p-3.5">
                                            <img :src="p.image_url || 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=100'" class="w-10 h-10 rounded-xl object-cover bg-slate-100 border border-slate-200">
                                        </td>
                                        <td class="p-3.5">
                                            <div class="font-bold text-slate-900" x-text="p.name"></div>
                                            <span class="text-[10px] text-teal-600 font-medium" x-text="p.category_name || 'Tanpa Kategori'"></span>
                                        </td>
                                        <td class="p-3.5 font-medium" x-text="p.unit"></td>
                                        <td class="p-3.5 font-bold text-slate-600" x-text="formatMoney(p.cost_price)"></td>
                                        <td class="p-3.5 font-black text-teal-600" x-text="formatMoney(p.sell_price)"></td>
                                        <td class="p-3.5 font-bold" :class="p.stock <= p.min_stock_alert ? 'text-rose-600' : 'text-slate-800'" x-text="p.stock + ' ' + p.unit"></td>
                                        <td class="p-3.5 text-right space-x-1">
                                            <button @click="editProduct(p)" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-600" title="Edit">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>
                                            <button @click="deleteProduct(p.id)" class="p-1.5 rounded-lg hover:bg-rose-50 text-rose-500" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 4: DATA PELANGGAN (MEMBER)                                 -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'customers'" class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-500">Kelola data pelanggan langganan tetap, level tiering harga, poin reward, dan limit plafon kasbon.</div>
                    <button @click="openCustomerModal()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition shadow">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Tambah Pelanggan Baru</span>
                    </button>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                    <div class="divide-y divide-slate-100 text-xs">
                        <template x-for="c in customers" :key="c.id">
                            <div class="py-3.5 flex items-center justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-extrabold text-slate-900 text-sm truncate" x-text="c.name"></span>
                                        <template x-if="c.tier === 'wholesale'">
                                            <span class="px-2 py-0.5 rounded-full bg-indigo-600 text-white text-[10px] font-black uppercase tracking-tight shadow-sm">Grosir</span>
                                        </template>
                                        <template x-if="c.tier === 'vip'">
                                            <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-black uppercase tracking-tight shadow-sm" x-text="'VIP ' + (c.discount_percent || 0) + '%'"></span>
                                        </template>
                                        <template x-if="!c.tier || c.tier === 'regular'">
                                            <span class="px-2 py-0.5 rounded-full bg-slate-700 text-white text-[10px] font-black uppercase tracking-tight shadow-sm">Reguler</span>
                                        </template>
                                        <span class="px-2 py-0.5 rounded-full bg-teal-50 text-teal-800 font-bold border border-teal-200 text-[10px]" x-text="(c.points || 0) + ' Poin'"></span>
                                    </div>
                                    <span class="text-slate-400 text-[11px] block mt-0.5" x-text="(c.phone || 'Tanpa No HP') + ' • ' + (c.address || 'Tanpa Alamat')"></span>
                                    <span class="text-slate-500 text-[11px] block mt-0.5" x-text="'Limit Plafon: ' + formatMoney(c.debt_limit)"></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <span class="text-[10px] text-slate-400 block uppercase font-bold">Total Kasbon:</span>
                                        <span class="font-black text-sm" :class="c.total_debt > 0 ? 'text-rose-600' : 'text-slate-700'" x-text="formatMoney(c.total_debt)"></span>
                                    </div>
                                    <button @click="openCustomerModal(c)" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="Edit Data Member">
                                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 5: BUKU KASBON / PIUTANG                                    -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'debts'" class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-500">Daftar transaksi kasbon yang belum lunas dan pengingat tagihan WhatsApp.</div>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                    <div class="divide-y divide-slate-100 text-xs">
                        <template x-for="d in debts" :key="d.id">
                            <div class="py-3.5 flex items-center justify-between">
                                <div>
                                    <span class="font-extrabold text-slate-900 text-sm block" x-text="d.customer_name"></span>
                                    <span class="text-slate-400 text-[11px]" x-text="(d.notes || 'Kasbon Belanja') + ' • ' + d.created_at"></span>
                                    <div class="text-xs text-rose-600 font-black mt-1" x-text="'Sisa Hutang: ' + formatMoney(d.remaining_debt)"></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="openPayDebtModal(d)" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs transition shadow">
                                        Bayar / Cicil
                                    </button>
                                    <button @click="sendWaDebtReminder(d)" class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow">
                                        <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                        <span>Tagih via WA</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div x-show="debts.length === 0" class="py-10 text-center text-slate-400">
                            Semua kasbon pelanggan telah lunas.
                        </div>
                    </div>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 6: ARUS KAS KELUAR (PETTY CASH)                            -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'expenses'" class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-500">Catat kas kecil keluar harian toko agar hitungan laba bersih selalu akurat.</div>
                    <button @click="openExpenseModal()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition shadow">
                        <i data-lucide="minus-circle" class="w-4 h-4"></i>
                        <span>+ Catat Kas Keluar</span>
                    </button>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase">
                            <tr>
                                <th class="p-3.5">Waktu</th>
                                <th class="p-3.5">Kategori Beban</th>
                                <th class="p-3.5">Keterangan</th>
                                <th class="p-3.5">Petugas Kasir</th>
                                <th class="p-3.5 text-right">Nominal Keluar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="exp in expenses" :key="exp.id">
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3.5 text-slate-400" x-text="exp.created_at"></td>
                                    <td class="p-3.5 font-bold text-slate-700" x-text="exp.category"></td>
                                    <td class="p-3.5" x-text="exp.notes"></td>
                                    <td class="p-3.5 text-slate-500" x-text="exp.cashier_name"></td>
                                    <td class="p-3.5 text-right font-black text-rose-600" x-text="formatMoney(exp.amount)"></td>
                                </tr>
                            </template>
                            <tr x-show="expenses.length === 0">
                                <td colspan="5" class="p-8 text-center text-slate-400">Belum ada catatan kas keluar hari ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 6B: KATEGORI PENGELUARAN (EXPENSE CATEGORIES)                -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'expense_categories'" class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-500">Kelola pos kategori biaya operasional agar pembukuan pengeluaran toko rapi.</div>
                    <button @click="openExpenseCategoryModal()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition shadow">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>+ Tambah Kategori Biaya</span>
                    </button>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="p-3.5">Nama Pos Pengeluaran</th>
                                <th class="p-3.5 text-center">Banyak Transaksi</th>
                                <th class="p-3.5 text-right">Total Akumulasi Biaya</th>
                                <th class="p-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="cat in expenseCategories" :key="cat.id">
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-3.5 font-bold text-slate-800 flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                            <i data-lucide="tag" class="w-4 h-4"></i>
                                        </div>
                                        <span x-text="cat.name"></span>
                                    </td>
                                    <td class="p-3.5 text-center font-bold text-slate-600" x-text="(cat.expense_count || 0) + ' kali'"></td>
                                    <td class="p-3.5 text-right font-black text-rose-600 text-sm" x-text="formatMoney(cat.total_spent || 0)"></td>
                                    <td class="p-3.5 text-right space-x-1">
                                        <button @click="openExpenseCategoryModal(cat)" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-600" title="Edit">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        <button @click="deleteExpenseCategory(cat.id)" class="p-1.5 rounded-lg hover:bg-rose-50 text-rose-500" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="expenseCategories.length === 0">
                                <td colspan="4" class="p-8 text-center text-slate-400">Belum ada kategori pengeluaran.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 7: KARYAWAN & KOMISI                                        -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'employees'" class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-500">Kelola PIN login staf kasir dan rekap bagi hasil / komisi jasa.</div>
                    <button @click="openEmployeeModal()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition shadow">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Tambah Karyawan</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <template x-for="emp in employees" :key="emp.id">
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                        <span x-text="emp.name.charAt(0)"></span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-sm" x-text="emp.name"></h4>
                                        <span class="text-[10px] uppercase font-bold tracking-wider" 
                                              :class="emp.role === 'admin' ? 'text-rose-600' : 'text-teal-600'" 
                                              x-text="emp.role === 'admin' ? 'Pemilik (Admin)' : 'Kasir Toko'"></span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold" 
                                      :class="emp.is_active ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-400 text-white'"
                                      x-text="emp.is_active ? 'Aktif' : 'Nonaktif'"></span>
                            </div>
                            <div class="text-xs text-slate-500 flex justify-between border-t border-slate-100 pt-2">
                                <span>PIN Login:</span>
                                <span class="font-bold text-slate-800 tracking-wider" x-text="emp.pin"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 8: PENGATURAN - PROFIL USAHA & LOGO                         -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'settings_profile'" class="space-y-6">
                <!-- Logo & Identitas Toko -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                        <i data-lucide="image" class="w-4 h-4 text-teal-600"></i>
                        <span>Logo Toko & Switch Struk Kasir</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                        <div class="space-y-3">
                            <label class="block text-xs font-bold text-slate-700">Pilih File Logo Toko (JPG/PNG):</label>
                            <label class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold rounded-xl border border-teal-200 cursor-pointer text-xs transition">
                                <i data-lucide="upload" class="w-4 h-4"></i>
                                <input type="file" @change="handleLogoUpload($event)" accept="image/*" class="hidden">
                                <span>Pilih Gambar dari Komputer / HP</span>
                            </label>
                            <template x-if="settings.store_logo">
                                <button @click="settings.store_logo = ''" class="block text-xs text-rose-500 font-semibold hover:underline">Hapus Logo</button>
                            </template>
                            <p class="text-[11px] text-slate-400">Otomatis di-resize dan disimpan langsung ke database SQLite agar tidak hilang saat pindah PC.</p>
                        </div>

                        <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            <div class="w-16 h-16 rounded-xl bg-white border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                                <template x-if="settings.store_logo">
                                    <img :src="settings.store_logo" class="w-full h-full object-contain p-1">
                                </template>
                                <template x-if="!settings.store_logo">
                                    <span class="text-xs text-slate-300 font-bold">No Logo</span>
                                </template>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="chkPrintLogo" x-model="settings.print_logo_receipt" :true-value="1" :false-value="0" class="w-4 h-4 text-teal-600 rounded">
                                    <label for="chkPrintLogo" class="text-xs font-bold text-slate-800 cursor-pointer">Cetak Logo di Struk Thermal</label>
                                </div>
                                <p class="text-[10px] text-slate-400">Nonaktifkan untuk mempercepat cetak printer dan hemat kertas thermal.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profil Toko -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-4 text-xs">
                    <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                        <i data-lucide="store" class="w-4 h-4 text-teal-600"></i>
                        <span>Informasi Identitas Usaha</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Nama Toko / Usaha:</label>
                            <input type="text" x-model="settings.store_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                        </div>
                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Jenis Usaha:</label>
                            <input type="text" x-model="settings.store_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Alamat Toko:</label>
                            <input type="text" x-model="settings.store_address" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">No. Telepon / WhatsApp:</label>
                            <input type="text" x-model="settings.store_phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-slate-600 font-semibold mb-1">Catatan Footer Struk:</label>
                            <input type="text" x-model="settings.receipt_footer" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button @click="saveSettings()" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs shadow transition">
                            Simpan Profil Usaha
                        </button>
                    </div>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 9: PENGATURAN - NOTIFIKASI TELEGRAM & WHATSAPP              -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'settings_notifications'" class="space-y-6">
                <!-- Bot Telegram Owner -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                            <i data-lucide="send" class="w-4 h-4 text-sky-500"></i>
                            <span>Integrasi Bot Telegram Owner (100% Gratis)</span>
                        </h3>
                        <button @click="testTelegram()" class="px-3 py-1.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl transition shadow flex items-center gap-1.5">
                            <i data-lucide="bell" class="w-3.5 h-3.5"></i>
                            <span>Tes Kirim Notifikasi</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Bot Token Telegram (@BotFather):</label>
                            <input type="text" x-model="settings.telegram_bot_token" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl" placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
                        </div>
                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Chat ID Owner Telegram:</label>
                            <input type="text" x-model="settings.telegram_chat_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl" placeholder="987654321">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button @click="saveSettings()" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs shadow transition">
                        Simpan Konfigurasi Notifikasi
                    </button>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 10: PENGATURAN - PIN KEAMANAN & BACKUP                      -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'settings_security'" class="space-y-6">
                <!-- 1. PIN KEAMANAN AKSES ADMIN -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-4 text-xs">
                    <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                        <i data-lucide="shield" class="w-4 h-4 text-teal-600"></i>
                        <span>Keamanan PIN Akses Admin</span>
                    </h3>

                    <div class="max-w-xs space-y-2">
                        <label class="block text-slate-600 font-semibold">PIN Akses Admin Baru (4-6 Digit):</label>
                        <input type="password" maxlength="6" x-model="settings.admin_pin" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-center font-bold text-lg tracking-widest">
                        <p class="text-[11px] text-slate-400">PIN ini digunakan untuk membuka panel Back-Office ini.</p>
                    </div>

                    <div class="pt-2 flex justify-start">
                        <button @click="saveSettings()" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs shadow transition">
                            Simpan PIN Baru
                        </button>
                    </div>
                </div>

                <!-- 2. PENGATURAN LOYALTY POINTS & REKENING BANK -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-5 text-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <i data-lucide="award" class="w-4 h-4 text-amber-500"></i>
                            <h3 class="font-extrabold text-slate-900 text-sm">Poin Loyalitas & Rekening Toko</h3>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-teal-600 text-white text-[10px] font-black uppercase tracking-tight shadow-sm">CRM Toko</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                <div>
                                    <span class="font-bold text-slate-800 block">Sistem Poin Member</span>
                                    <span class="text-[11px] text-slate-400">Berikan poin reward saat pelanggan berbelanja.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="settings.loyalty_points_enabled" :true-value="1" :false-value="0" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                                </label>
                            </div>

                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Rasio Belanja Perolehan (Rp / 1 Poin):</label>
                                <input type="number" x-model.number="settings.point_earn_rate" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                                <span class="text-[10px] text-slate-400 mt-1 block">Contoh: 10000 = Tiap belanja Rp 10.000 dapat 1 Poin.</span>
                            </div>

                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Nilai Tukar Potongan (Rp / 1 Poin):</label>
                                <input type="number" x-model.number="settings.point_redeem_value" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                                <span class="text-[10px] text-slate-400 mt-1 block">Contoh: 100 = 1 Poin memotong harga belanja Rp 100.</span>
                            </div>
                        </div>

                        <div class="space-y-3 border-t md:border-t-0 md:border-l border-slate-100 md:pl-4 pt-3 md:pt-0">
                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Nama Bank Pembayaran Toko:</label>
                                <input type="text" x-model="settings.bank_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold" placeholder="Bank BCA / Mandiri / BRI">
                            </div>
                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Nomor Rekening Bank:</label>
                                <input type="text" x-model="settings.bank_account_no" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold tracking-wider" placeholder="1234-5678-90">
                            </div>
                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Atas Nama Pemilik Rekening:</label>
                                <input type="text" x-model="settings.bank_account_holder" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl" placeholder="Nama Pemilik Toko">
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button @click="saveSettings()" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs shadow transition">
                            Simpan Poin & Rekening
                        </button>
                    </div>
                </div>

                <!-- 3. BACKUP & RESTORE DATABASE (OFFLINE SQLITE ENGINE) -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-5 text-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <i data-lucide="database" class="w-4 h-4 text-teal-600"></i>
                            <h3 class="font-extrabold text-slate-900 text-sm">Manajemen Database & Backup (.sqlite)</h3>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-800 text-white text-[10px] font-black uppercase tracking-tight shadow-sm">SQLite 3 Offline</span>
                    </div>

                    <!-- Metrics Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/70">
                            <span class="text-[10px] text-slate-400 font-semibold uppercase">Ukuran File</span>
                            <div class="text-base font-black text-slate-900 mt-0.5" x-text="dbStats.size || 'Memuat...'"></div>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/70">
                            <span class="text-[10px] text-slate-400 font-semibold uppercase">Total Produk</span>
                            <div class="text-base font-black text-slate-900 mt-0.5" x-text="(dbStats.products_count || 0) + ' Item'"></div>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/70">
                            <span class="text-[10px] text-slate-400 font-semibold uppercase">Transaksi</span>
                            <div class="text-base font-black text-slate-900 mt-0.5" x-text="(dbStats.transactions_count || 0) + ' Nota'"></div>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/70">
                            <span class="text-[10px] text-slate-400 font-semibold uppercase">Pelanggan</span>
                            <div class="text-base font-black text-slate-900 mt-0.5" x-text="(dbStats.customers_count || 0) + ' Kontak'"></div>
                        </div>
                    </div>

                    <!-- Actions: Download & Restore -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <!-- Download Box -->
                        <div class="p-4 bg-teal-50/60 rounded-2xl border border-teal-200 flex flex-col justify-between space-y-3">
                            <div>
                                <h4 class="font-bold text-teal-950 text-xs flex items-center gap-1.5">
                                    <i data-lucide="download" class="w-4 h-4 text-teal-700"></i>
                                    <span>Unduh File Cadangan (Backup)</span>
                                </h4>
                                <p class="text-[11px] text-teal-800 mt-1 leading-relaxed">
                                    Unduh seluruh master produk, riwayat penjualan, buku kasbon, dan pengaturan dalam satu file berkas database SQLite portabel.
                                </p>
                            </div>
                            <a href="../api.php?action=backup_download" 
                               class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs shadow-md shadow-teal-600/20 text-center flex items-center justify-center gap-2 transition">
                                <i data-lucide="download-cloud" class="w-4 h-4"></i>
                                <span>Unduh Database (.sqlite)</span>
                            </a>
                        </div>

                        <!-- Restore Box -->
                        <div class="p-4 bg-amber-50/60 rounded-2xl border border-amber-200 flex flex-col justify-between space-y-3">
                            <div>
                                <h4 class="font-bold text-amber-950 text-xs flex items-center gap-1.5">
                                    <i data-lucide="upload-cloud" class="w-4 h-4 text-amber-700"></i>
                                    <span>Pulihkan Data (Restore)</span>
                                </h4>
                                <p class="text-[11px] text-amber-800 mt-1 leading-relaxed">
                                    Unggah berkas cadangan .sqlite dari komputer atau flashdisk. Sistem otomatis membuat cadangan rollback darurat sebelum memulihkan data.
                                </p>
                            </div>
                            <label class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs shadow-md shadow-amber-600/20 text-center flex items-center justify-center gap-2 cursor-pointer transition">
                                <i data-lucide="folder-up" class="w-4 h-4"></i>
                                <input type="file" @change="restoreDatabase($event)" accept=".sqlite,.db,.sqlite3" class="hidden">
                                <span>Pilih File .sqlite & Pulihkan</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 4. PEMBERSIHAN DATA UJI COBA (DANGEROUS ZONE) -->
                <div class="bg-rose-50/50 rounded-3xl border border-rose-200 p-6 shadow-sm space-y-4 text-xs">
                    <div class="flex items-center justify-between border-b border-rose-200/80 pb-3">
                        <div class="flex items-center gap-2">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                            <h3 class="font-extrabold text-rose-950 text-sm">Zona Pembersihan & Reset Data</h3>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-rose-600 text-white text-[10px] font-black uppercase tracking-tight shadow-sm">Zona Kritis</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-2xl border border-rose-200 flex flex-col justify-between space-y-3">
                            <div>
                                <span class="font-bold text-slate-900 block">Reset Transaksi Uji Coba Saja</span>
                                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                    Hapus seluruh nota transaksi, data kasbon, dan pengeluaran demo saat pertama mencoba. <strong>Katalog produk, kategori, member, dan nama toko TETAP AMAN.</strong>
                                </p>
                            </div>
                            <button @click="openResetConfirmModal('transactions_only')" 
                                    class="w-full py-2.5 bg-rose-100 hover:bg-rose-200 text-rose-800 font-bold rounded-xl border border-rose-300 transition flex items-center justify-center gap-1.5">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                <span>Bersihkan Transaksi (Mulai Jualan)</span>
                            </button>
                        </div>

                        <div class="bg-white p-4 rounded-2xl border border-rose-200 flex flex-col justify-between space-y-3">
                            <div>
                                <span class="font-bold text-rose-700 block">Reset Total Pabrik (Factory Reset)</span>
                                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                    Hapus seluruh data toko termasuk produk, transaksi, pelanggan, dan kembali ke kondisi baru awal terpasang.
                                </p>
                            </div>
                            <button @click="openResetConfirmModal('full_reset')" 
                                    class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md shadow-rose-600/20 transition flex items-center justify-center gap-1.5">
                                <i data-lucide="alert-octagon" class="w-3.5 h-3.5 text-white"></i>
                                <span>Reset Total Pabrik</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- MODAL: FORM PRODUK (DENGAN UPLOAD FOTO & PREVIEW) -->
    <div x-show="productModal.open" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 text-xs max-h-[90vh] overflow-y-auto">
            <h3 class="font-extrabold text-slate-900 text-base" x-text="productModal.form.id ? 'Edit Produk' : 'Tambah Produk Baru'"></h3>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Nama Produk / Jasa:</label>
                    <input type="text" x-model="productModal.form.name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">Kategori:</label>
                        <select x-model="productModal.form.category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                            <option value="">Pilih Kategori</option>
                            <template x-for="c in categories" :key="c.id">
                                <option :value="c.id" x-text="c.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">Satuan:</label>
                        <input type="text" x-model="productModal.form.unit" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl" placeholder="pcs, kg, porsi, jam">
                    </div>
                </div>

                <!-- Foto Produk Upload Box -->
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Foto Produk:</label>
                    <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-2xl border border-slate-200">
                        <div class="w-16 h-16 rounded-xl bg-white border border-slate-200 overflow-hidden flex items-center justify-center shrink-0">
                            <template x-if="productModal.form.image_url">
                                <img :src="productModal.form.image_url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!productModal.form.image_url">
                                <i data-lucide="image" class="w-6 h-6 text-slate-300"></i>
                            </template>
                        </div>
                        <div class="flex-1 space-y-1">
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold rounded-xl border border-teal-200 cursor-pointer text-xs transition">
                                <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                                <input type="file" @change="handleProductPhoto($event)" accept="image/*" class="hidden">
                                <span>Pilih Foto Produk</span>
                            </label>
                            <template x-if="productModal.form.image_url">
                                <button @click="productModal.form.image_url = ''" class="block text-[11px] text-rose-500 font-semibold hover:underline">Hapus Foto</button>
                            </template>
                            <p class="text-[10px] text-slate-400">Otomatis dikompres agar database tetap ringan.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">Harga Modal (HPP):</label>
                        <input type="number" x-model.number="productModal.form.cost_price" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold tracking-tight">
                    </div>
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">Harga Jual:</label>
                        <input type="number" x-model.number="productModal.form.sell_price" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold tracking-tight text-teal-600">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">Jumlah Stok Awal:</label>
                        <input type="number" x-model.number="productModal.form.stock" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold tracking-tight">
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="productModal.form.is_scale" :true-value="1" :false-value="0" class="w-4 h-4 text-teal-600 rounded">
                            <span class="font-bold text-slate-700">Barang Timbangan (Kg)</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button @click="saveProduct()" class="flex-1 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow transition">
                    Simpan Produk
                </button>
                <button @click="productModal.open = false" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: FORM KATEGORI (DENGAN UPLOAD FOTO & PREVIEW) -->
    <div x-show="categoryModal.open" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-xs">
            <h3 class="font-extrabold text-slate-900 text-base" x-text="categoryModal.form.id ? 'Edit Kategori' : 'Tambah Kategori Baru'"></h3>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Nama Kategori:</label>
                    <input type="text" x-model="categoryModal.form.name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold" placeholder="misal: Minuman">
                </div>

                <!-- Foto Kategori Upload Box -->
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Foto Kategori:</label>
                    <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-2xl border border-slate-200">
                        <div class="w-16 h-16 rounded-xl bg-white border border-slate-200 overflow-hidden flex items-center justify-center shrink-0">
                            <template x-if="categoryModal.form.image_url">
                                <img :src="categoryModal.form.image_url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!categoryModal.form.image_url">
                                <i data-lucide="image" class="w-6 h-6 text-slate-300"></i>
                            </template>
                        </div>
                        <div class="flex-1 space-y-1">
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold rounded-xl border border-teal-200 cursor-pointer text-xs transition">
                                <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                                <input type="file" @change="handleCategoryPhoto($event)" accept="image/*" class="hidden">
                                <span>Pilih File Foto</span>
                            </label>
                            <template x-if="categoryModal.form.image_url">
                                <button @click="categoryModal.form.image_url = ''" class="block text-[11px] text-rose-500 font-semibold hover:underline">Hapus Foto</button>
                            </template>
                            <p class="text-[10px] text-slate-400">Otomatis di-resize 300x300 agar database ringan.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Urutan Tampil (Sort Order):</label>
                    <input type="number" x-model.number="categoryModal.form.sort_order" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold tracking-tight">
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button @click="saveCategory()" class="flex-1 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow transition">
                    Simpan Kategori
                </button>
                <button @click="categoryModal.open = false" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: CATAT PENGELUARAN KAS KELUAR -->
    <div x-show="expenseModal.open" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-xs">
            <h3 class="font-extrabold text-slate-900 text-base text-rose-600 flex items-center gap-2">
                <i data-lucide="minus-circle" class="w-5 h-5"></i>
                <span>Catat Kas Keluar Toko</span>
            </h3>

            <div class="space-y-3">
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Kategori Pengeluaran:</label>
                    <select x-model="expenseModal.form.category" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                        <template x-for="ec in expenseCategories" :key="ec.id">
                            <option :value="ec.name" x-text="ec.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Nominal (Rp):</label>
                    <input type="number" x-model.number="expenseModal.form.amount" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold tracking-tight text-rose-600 text-sm">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Keterangan Catatan:</label>
                    <input type="text" x-model="expenseModal.form.notes" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl" placeholder="Beli token listrik 50rb">
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button @click="saveExpense()" class="flex-1 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow transition">
                    Simpan Pengeluaran
                </button>
                <button @click="expenseModal.open = false" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: TAMBAH / EDIT KATEGORI PENGELUARAN -->
    <div x-show="expenseCategoryModal.open" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-xs" @click.away="expenseCategoryModal.open = false">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2 text-teal-600">
                <i data-lucide="tag" class="w-5 h-5"></i>
                <span x-text="expenseCategoryModal.form.id ? 'Edit Kategori Pengeluaran' : 'Tambah Kategori Pengeluaran'"></span>
            </h3>

            <div class="space-y-3">
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Nama Pos / Kategori:</label>
                    <input type="text" x-model="expenseCategoryModal.form.name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold" placeholder="misal: Bahan Baku & Dapur">
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button @click="saveExpenseCategory()" class="flex-1 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow transition">
                    Simpan
                </button>
                <button @click="expenseCategoryModal.open = false" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: BAYAR CICILAN KASBON -->
    <div x-show="payDebtModal.open" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-xs">
            <h3 class="font-extrabold text-slate-900 text-base">Pelunasan Kasbon</h3>
            <p class="text-slate-500">Pelanggan: <strong class="text-slate-900" x-text="payDebtModal.data?.customer_name"></strong></p>
            <p class="text-rose-600 font-bold" x-text="'Sisa Hutang: ' + formatMoney(payDebtModal.data?.remaining_debt)"></p>

            <div>
                <label class="block text-slate-600 font-semibold mb-1">Nominal Pembayaran (Rp):</label>
                <input type="number" x-model.number="payDebtModal.amount" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold tracking-tight text-teal-600 text-sm">
            </div>

            <div class="flex gap-2 pt-2">
                <button @click="submitDebtPayment()" class="flex-1 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow transition">
                    Konfirmasi Pembayaran
                </button>
                <button @click="payDebtModal.open = false" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: FORM PELANGGAN (CRM, TIER & LOYALTY) -->
    <div x-show="customerModal.open" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-xs max-h-[92vh] overflow-y-auto">
            <h3 class="font-extrabold text-slate-900 text-base" x-text="customerModal.form.id ? 'Edit Data Pelanggan' : 'Tambah Pelanggan Baru'"></h3>

            <div class="space-y-3">
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Nama Pelanggan:</label>
                    <input type="text" x-model="customerModal.form.name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold" placeholder="misal: Pak Budi">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Level / Tier Member:</label>
                    <select x-model="customerModal.form.tier" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                        <option value="regular">Reguler (Standar)</option>
                        <option value="vip">VIP (Diskon Persentase Khusus)</option>
                        <option value="wholesale">Grosir / Reseller (Harga Grosir Otomatis)</option>
                    </select>
                </div>
                <div x-show="customerModal.form.tier === 'vip'">
                    <label class="block text-amber-700 font-semibold mb-1">Diskon Khusus VIP (%):</label>
                    <input type="number" x-model.number="customerModal.form.discount_percent" class="w-full px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl font-bold text-amber-900" placeholder="5">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Saldo Poin Loyalitas:</label>
                    <input type="number" x-model.number="customerModal.form.points" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-teal-700">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">No. WhatsApp:</label>
                    <input type="text" x-model="customerModal.form.phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl" placeholder="081234567890">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Alamat / Catatan:</label>
                    <input type="text" x-model="customerModal.form.address" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Limit Plafon Kasbon (Rp):</label>
                    <input type="number" x-model.number="customerModal.form.debt_limit" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold tracking-tight">
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button @click="saveCustomer()" class="flex-1 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow transition">
                    Simpan Pelanggan
                </button>
                <button @click="customerModal.open = false" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: KONFIRMASI PIN RESET DATA -->
    <div x-show="resetModal.open" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-xs text-center">
            <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white mx-auto flex items-center justify-center shadow-md shadow-rose-600/20">
                <i data-lucide="alert-octagon" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-slate-900 text-base" x-text="resetModal.mode === 'full_reset' ? 'Konfirmasi Reset Total Pabrik' : 'Konfirmasi Bersihkan Transaksi'"></h3>
                <p class="text-slate-500 mt-1 leading-relaxed" x-text="resetModal.mode === 'full_reset' ? 'Seluruh data produk, kategori, pelanggan, dan transaksi akan dihapus bersih kembali ke nol.' : 'Data transaksi, kasbon, dan pengeluaran akan dihapus. Master produk dan pelanggan tetap aman.'"></p>
            </div>

            <div>
                <label class="block text-slate-600 font-semibold mb-1 text-left">Masukkan PIN Admin untuk Konfirmasi:</label>
                <input type="password" maxlength="6" x-model="resetModal.pin" @keyup.enter="submitReset()"
                       class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-center font-bold text-lg tracking-widest focus:border-rose-500 focus:outline-none" placeholder="••••">
            </div>

            <div class="flex gap-2 pt-2">
                <button @click="submitReset()" class="flex-1 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow transition">
                    Ya, Jalankan Reset
                </button>
                <button @click="resetModal.open = false" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC ENGINE -->
    <script>
        function adminApp() {
            return {
                isAuthorized: sessionStorage.getItem('admin_authorized') === 'true',
                authPin: '',
                authError: '',
                currentTab: 'dashboard',
                
                // Dropdown States
                menuDropdowns: {
                    catalog: true,
                    customers: false,
                    finance: false,
                    settings: false
                },

                tabTitles: {
                    dashboard: 'Dashboard Keuangan & Laba Bersih',
                    categories: 'Master Kategori Produk',
                    products: 'Master Produk & Varian',
                    customers: 'Data Pelanggan (Member)',
                    debts: 'Buku Kasbon / Piutang Berjalan',
                    expenses: 'Arus Kas Keluar (Petty Cash)',
                    expense_categories: 'Kategori Biaya Operasional Toko',
                    employees: 'Karyawan, PIN Kasir & Komisi',
                    settings_profile: 'Profil Usaha & Logo Toko',
                    settings_notifications: 'Notifikasi Bot Telegram & WhatsApp',
                    settings_security: 'PIN Keamanan & Backup Database'
                },

                settings: {},
                categories: [],
                products: [],
                customers: [],
                debts: [],
                expenses: [],
                expenseCategories: [],
                employees: [],
                reportData: {},
                dbStats: {},

                productModal: { open: false, form: {} },
                categoryModal: { open: false, form: {} },
                customerModal: { open: false, form: {} },
                expenseCategoryModal: { open: false, form: { id: null, name: '' } },
                expenseModal: { open: false, form: { category: 'Operasional Toko', amount: 0, notes: '' } },
                payDebtModal: { open: false, data: null, amount: 0 },
                resetModal: { open: false, mode: 'transactions_only', pin: '' },

                async init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('preview') === '1' || urlParams.get('pin') === '1234') {
                        this.isAuthorized = true;
                        sessionStorage.setItem('admin_authorized', 'true');
                    }
                    await this.loadInitialSettings();
                    await this.loadExpenseCategories();
                    if (this.isAuthorized) {
                        await this.loadDashboardReport();
                    }
                    const initTab = urlParams.get('tab');
                    if (initTab && this.tabTitles[initTab]) {
                        this.setTab(initTab);
                    }
                    this.refreshIcons();
                },

                toggleMenu(menu) {
                    this.menuDropdowns[menu] = !this.menuDropdowns[menu];
                    this.refreshIcons();
                },

                setTab(tab) {
                    this.currentTab = tab;
                    if (tab === 'dashboard') this.loadDashboardReport();
                    else if (tab === 'categories') this.loadCategories();
                    else if (tab === 'products') this.loadProducts();
                    else if (tab === 'customers' || tab === 'debts') this.loadCustomers();
                    else if (tab === 'expenses') this.loadExpenses();
                    else if (tab === 'expense_categories') this.loadExpenseCategories();
                    else if (tab === 'employees') this.loadEmployees();
                    else if (tab === 'settings_security') this.loadDbStats();
                    this.refreshIcons();
                },

                refreshIcons() {
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 80);
                },

                formatMoney(val) {
                    val = parseFloat(val) || 0;
                    return 'Rp ' + Math.round(val).toLocaleString('id-ID');
                },

                // Client-Side Image Auto-Compress via HTML5 Canvas
                compressImage(file, maxWidth = 300, maxHeight = 300, quality = 0.82) {
                    return new Promise((resolve, reject) => {
                        const reader = new FileReader();
                        reader.readAsDataURL(file);
                        reader.onload = (event) => {
                            const img = new Image();
                            img.src = event.target.result;
                            img.onload = () => {
                                const canvas = document.createElement('canvas');
                                let width = img.width;
                                let height = img.height;
                                
                                if (width > height) {
                                    if (width > maxWidth) {
                                        height = Math.round((height * maxWidth) / width);
                                        width = maxWidth;
                                    }
                                } else {
                                    if (height > maxHeight) {
                                        width = Math.round((width * maxHeight) / height);
                                        height = maxHeight;
                                    }
                                }
                                
                                canvas.width = width;
                                canvas.height = height;
                                const ctx = canvas.getContext('2d');
                                ctx.drawImage(img, 0, 0, width, height);
                                resolve(canvas.toDataURL('image/jpeg', quality));
                            };
                            img.onerror = (err) => reject(err);
                        };
                        reader.onerror = (err) => reject(err);
                    });
                },

                async handleCategoryPhoto(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    try {
                        const compressed = await this.compressImage(file, 300, 300, 0.82);
                        this.categoryModal.form.image_url = compressed;
                    } catch (e) {
                        alert('Gagal memproses gambar foto.');
                    }
                },

                async handleProductPhoto(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    try {
                        const compressed = await this.compressImage(file, 300, 300, 0.82);
                        this.productModal.form.image_url = compressed;
                    } catch (e) {
                        alert('Gagal memproses gambar foto.');
                    }
                },

                async handleLogoUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    try {
                        const compressed = await this.compressImage(file, 250, 250, 0.85);
                        this.settings.store_logo = compressed;
                    } catch (e) {
                        alert('Gagal memproses logo.');
                    }
                },

                async verifyPin() {
                    this.authError = '';
                    try {
                        const res = await fetch('../api.php?action=admin_verify_pin', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ pin: this.authPin })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.isAuthorized = true;
                            sessionStorage.setItem('admin_authorized', 'true');
                            await this.loadDashboardReport();
                            this.refreshIcons();
                        } else {
                            this.authError = data.error || 'PIN Salah.';
                        }
                    } catch (e) {
                        this.authError = 'Gagal menghubungi server.';
                    }
                },

                async loadInitialSettings() {
                    const res = await fetch('../api.php?action=init');
                    const d = await res.json();
                    if (d.success) {
                        this.settings = d.settings || {};
                    }
                },

                async loadDashboardReport() {
                    const res = await fetch('../api.php?action=reports_admin');
                    const d = await res.json();
                    if (d.success) this.reportData = d;
                    this.refreshIcons();
                },

                async loadProducts() {
                    const res = await fetch('../api.php?action=products_list');
                    const d = await res.json();
                    if (d.success) this.products = d.data;
                    this.refreshIcons();
                },

                async loadCategories() {
                    const res = await fetch('../api.php?action=categories_list');
                    const d = await res.json();
                    if (d.success) this.categories = d.data;
                    this.refreshIcons();
                },

                async loadCustomers() {
                    const cRes = await fetch('../api.php?action=customers_list');
                    const cData = await cRes.json();
                    if (cData.success) this.customers = cData.data;

                    const dRes = await fetch('../api.php?action=debts_list');
                    const dData = await dRes.json();
                    if (dData.success) this.debts = dData.data;
                    this.refreshIcons();
                },

                async loadExpenses() {
                    const res = await fetch('../api.php?action=expenses_list');
                    const d = await res.json();
                    if (d.success) this.expenses = d.data;
                    this.refreshIcons();
                },

                async loadEmployees() {
                    const res = await fetch('../api.php?action=employees_list');
                    const d = await res.json();
                    if (d.success) this.employees = d.data;
                    this.refreshIcons();
                },

                // Product Handlers
                openProductModal() {
                    this.productModal.form = { id: null, name: '', category_id: '', unit: 'pcs', cost_price: 0, sell_price: 0, stock: 10, min_stock_alert: 5, is_scale: 0, image_url: '' };
                    this.productModal.open = true;
                },
                editProduct(p) {
                    this.productModal.form = JSON.parse(JSON.stringify(p));
                    this.productModal.open = true;
                },
                async saveProduct() {
                    const res = await fetch('../api.php?action=products_save', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.productModal.form)
                    });
                    const d = await res.json();
                    if (d.success) {
                        this.productModal.open = false;
                        await this.loadProducts();
                    } else {
                        alert(d.error || 'Gagal menyimpan produk.');
                    }
                },
                async deleteProduct(id) {
                    if (!confirm('Yakin ingin menghapus produk ini?')) return;
                    await fetch('../api.php?action=products_delete', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    });
                    await this.loadProducts();
                },

                // Category Handlers
                openCategoryModal() {
                    this.categoryModal.form = { id: null, name: '', image_url: '', sort_order: this.categories.length + 1 };
                    this.categoryModal.open = true;
                },
                editCategory(c) {
                    this.categoryModal.form = JSON.parse(JSON.stringify(c));
                    this.categoryModal.open = true;
                },
                async saveCategory() {
                    const res = await fetch('../api.php?action=categories_save', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.categoryModal.form)
                    });
                    const d = await res.json();
                    if (d.success) {
                        this.categoryModal.open = false;
                        await this.loadCategories();
                    } else {
                        alert(d.error || 'Gagal menyimpan kategori.');
                    }
                },
                async deleteCategory(id) {
                    if (!confirm('Yakin ingin menghapus kategori ini?')) return;
                    await fetch('../api.php?action=categories_delete', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    });
                    await this.loadCategories();
                },

                // Customer Handlers
                openCustomerModal(c = null) {
                    if (c) {
                        this.customerModal.form = {
                            id: c.id,
                            name: c.name,
                            phone: c.phone || '',
                            address: c.address || '',
                            debt_limit: c.debt_limit || 500000,
                            tier: c.tier || 'regular',
                            discount_percent: c.discount_percent || 0,
                            points: c.points || 0
                        };
                    } else {
                        this.customerModal.form = {
                            id: null,
                            name: '',
                            phone: '',
                            address: '',
                            debt_limit: 500000,
                            tier: 'regular',
                            discount_percent: 0,
                            points: 0
                        };
                    }
                    this.customerModal.open = true;
                    this.refreshIcons();
                },
                async saveCustomer() {
                    const res = await fetch('../api.php?action=customers_save', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.customerModal.form)
                    });
                    const d = await res.json();
                    if (d.success) {
                        this.customerModal.open = false;
                        await this.loadCustomers();
                    } else {
                        alert(d.error || 'Gagal menyimpan pelanggan.');
                    }
                },

                openPayDebtModal(debt) {
                    this.payDebtModal.data = debt;
                    this.payDebtModal.amount = debt.remaining_debt;
                    this.payDebtModal.open = true;
                },
                async submitDebtPayment() {
                    const res = await fetch('../api.php?action=debt_pay', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ debt_id: this.payDebtModal.data.id, amount: this.payDebtModal.amount })
                    });
                    const d = await res.json();
                    if (d.success) {
                        alert(d.message);
                        this.payDebtModal.open = false;
                        await this.loadCustomers();
                    } else {
                        alert(d.error || 'Gagal memproses pembayaran hutang.');
                    }
                },
                sendWaDebtReminder(debt) {
                    const phone = (debt.customer_phone || '').replace(/[^0-9]/g, '');
                    const cleanPhone = phone.startsWith('0') ? '62' + phone.slice(1) : phone;
                    const text = encodeURIComponent(
                        `Halo ${debt.customer_name},\n` +
                        `Mengingatkan catatan kasbon di ${this.settings.store_name || 'Toko Kami'} sebesar ${this.formatMoney(debt.remaining_debt)} ` +
                        `(transaksi ${debt.created_at}).\n` +
                        `Terima kasih banyak.`
                    );
                    window.open(`https://wa.me/${cleanPhone}?text=${text}`, '_blank');
                },

                // Expense Categories Handlers
                openExpenseCategoryModal(cat = null) {
                    if (cat) {
                        this.expenseCategoryModal.form = JSON.parse(JSON.stringify(cat));
                    } else {
                        this.expenseCategoryModal.form = { id: null, name: '' };
                    }
                    this.expenseCategoryModal.open = true;
                },
                async loadExpenseCategories() {
                    try {
                        const res = await fetch('../api.php?action=expense_categories_list');
                        const d = await res.json();
                        if (d.success) this.expenseCategories = d.data || [];
                    } catch (e) {}
                    this.refreshIcons();
                },
                async saveExpenseCategory() {
                    if (!this.expenseCategoryModal.form.name || !this.expenseCategoryModal.form.name.trim()) {
                        alert('Nama pos kategori pengeluaran wajib diisi.');
                        return;
                    }
                    try {
                        const res = await fetch('../api.php?action=expense_categories_save', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(this.expenseCategoryModal.form)
                        });
                        const d = await res.json();
                        if (d.success) {
                            this.expenseCategoryModal.open = false;
                            await this.loadExpenseCategories();
                        } else {
                            alert(d.error || 'Gagal menyimpan kategori.');
                        }
                    } catch (e) {
                        alert('Gagal menghubungi server.');
                    }
                },
                async deleteExpenseCategory(id) {
                    if (!confirm('Yakin ingin menghapus pos kategori pengeluaran ini?')) return;
                    try {
                        const res = await fetch('../api.php?action=expense_categories_delete', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id })
                        });
                        const d = await res.json();
                        if (d.success) {
                            await this.loadExpenseCategories();
                        } else {
                            alert(d.error || 'Gagal menghapus kategori.');
                        }
                    } catch (e) {
                        alert('Gagal menghubungi server.');
                    }
                },

                openExpenseModal() {
                    const defCat = (this.expenseCategories && this.expenseCategories.length > 0) ? this.expenseCategories[0].name : 'Operasional Toko';
                    this.expenseModal.form = { category: defCat, amount: 0, notes: '' };
                    this.expenseModal.open = true;
                },
                async saveExpense() {
                    const res = await fetch('../api.php?action=expenses_save', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.expenseModal.form)
                    });
                    const d = await res.json();
                    if (d.success) {
                        this.expenseModal.open = false;
                        await this.loadExpenses();
                        await this.loadExpenseCategories();
                        await this.loadDashboardReport();
                    } else {
                        alert(d.error || 'Gagal menyimpan pengeluaran.');
                    }
                },

                async testTelegram() {
                    const res = await fetch('../api.php?action=telegram_test', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            bot_token: this.settings.telegram_bot_token,
                            chat_id: this.settings.telegram_chat_id
                        })
                    });
                    const d = await res.json();
                    alert(d.message || d.error);
                },

                async saveSettings() {
                    const res = await fetch('../api.php?action=settings_update', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(this.settings)
                    });
                    const d = await res.json();
                    if (d.success) {
                        alert('Pengaturan toko berhasil disimpan!');
                    } else {
                        alert(d.error || 'Gagal menyimpan pengaturan.');
                    }
                },

                // Database Management & Factory Reset Handlers
                async loadDbStats() {
                    try {
                        const res = await fetch('../api.php?action=database_stats');
                        const d = await res.json();
                        if (d.success) {
                            this.dbStats = d;
                        }
                    } catch (e) {}
                    this.refreshIcons();
                },

                async restoreDatabase(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    if (!confirm('PERINGATAN: Memulihkan database akan menimpa data saat ini dengan file cadangan .sqlite yang Anda pilih. Cadangan darurat otomatis akan dibuat sebelum ditimpa. Lanjutkan proses pulihkan?')) {
                        event.target.value = '';
                        return;
                    }

                    const formData = new FormData();
                    formData.append('backup_file', file);

                    try {
                        const res = await fetch('../api.php?action=backup_restore', {
                            method: 'POST',
                            body: formData
                        });
                        const d = await res.json();
                        if (d.success) {
                            alert(d.message || 'Database berhasil dipulihkan!');
                            location.reload();
                        } else {
                            alert(d.error || 'Gagal memulihkan database.');
                        }
                    } catch (e) {
                        alert('Gagal menghubungi server kasir.');
                    }
                    event.target.value = '';
                },

                openResetConfirmModal(mode) {
                    this.resetModal.mode = mode;
                    this.resetModal.pin = '';
                    this.resetModal.open = true;
                    this.refreshIcons();
                },

                async submitReset() {
                    if (!this.resetModal.pin) {
                        alert('Silakan masukkan PIN Admin.');
                        return;
                    }

                    try {
                        const res = await fetch('../api.php?action=factory_reset', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pin: this.resetModal.pin,
                                mode: this.resetModal.mode
                            })
                        });
                        const d = await res.json();
                        if (d.success) {
                            alert(d.message);
                            this.resetModal.open = false;
                            location.reload();
                        } else {
                            alert(d.error || 'Gagal menjalankan reset. Periksa kembali PIN Anda.');
                        }
                    } catch (e) {
                        alert('Gagal menghubungi server.');
                    }
                }
            };
        }
    </script>
</body>
</html>
