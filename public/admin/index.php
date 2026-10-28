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
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 mx-auto flex items-center justify-center border border-teal-200">
                <i data-lucide="lock" class="w-7 h-7"></i>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Portal Back-Office Owner</h2>
                <p class="text-xs text-slate-500 mt-1">Masukkan PIN Otorisasi Admin untuk mengelola data rahasia toko.</p>
            </div>
            <div>
                <input type="password" maxlength="6" x-model="authPin" @keyup.enter="verifyPin()" 
                       class="w-full text-center text-3xl font-mono tracking-widest px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-teal-500 focus:outline-none"
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

    <!-- MAIN SIDEBAR -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between border-r border-slate-800 shadow-xl shrink-0 z-20">
        <div>
            <!-- Store Branding Header -->
            <div class="p-5 border-b border-slate-800/80 flex items-center gap-3">
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

            <!-- Navigation Links -->
            <nav class="p-3 space-y-1 text-xs">
                <button @click="currentTab = 'dashboard'; refreshIcons();" 
                        :class="currentTab === 'dashboard' ? 'bg-teal-600 text-white font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard Keuangan</span>
                </button>

                <button @click="currentTab = 'products'; loadProducts(); refreshIcons();" 
                        :class="currentTab === 'products' ? 'bg-teal-600 text-white font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition">
                    <i data-lucide="package" class="w-4 h-4"></i>
                    <span>Master Produk</span>
                </button>

                <button @click="currentTab = 'categories'; loadCategories(); refreshIcons();" 
                        :class="currentTab === 'categories' ? 'bg-teal-600 text-white font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition">
                    <i data-lucide="tags" class="w-4 h-4"></i>
                    <span>Master Kategori (Foto)</span>
                </button>

                <button @click="currentTab = 'customers'; loadCustomers(); refreshIcons();" 
                        :class="currentTab === 'customers' ? 'bg-teal-600 text-white font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                    <span>Buku Kasbon / Piutang</span>
                </button>

                <button @click="currentTab = 'expenses'; loadExpenses(); refreshIcons();" 
                        :class="currentTab === 'expenses' ? 'bg-teal-600 text-white font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                    <span>Arus Kas & Pengeluaran</span>
                </button>

                <button @click="currentTab = 'employees'; loadEmployees(); refreshIcons();" 
                        :class="currentTab === 'employees' ? 'bg-teal-600 text-white font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Karyawan & Komisi</span>
                </button>

                <button @click="currentTab = 'settings'; refreshIcons();" 
                        :class="currentTab === 'settings' ? 'bg-teal-600 text-white font-bold' : 'hover:bg-slate-800 text-slate-400 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                    <span>Integrasi API & Logo</span>
                </button>
            </nav>
        </div>

        <!-- Quick Switch to Cashier Front-End -->
        <div class="p-4 border-t border-slate-800/80 space-y-2">
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
                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
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
                <!-- Date Filter & Quick Metrics -->
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
                            <span class="px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 text-[10px] font-bold" x-text="(reportData.summary?.margin_percent || 0) + '% Margin'"></span>
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
                                        <span class="w-6 h-6 rounded-full bg-slate-100 font-black text-[10px] text-slate-600 flex items-center justify-center" x-text="idx + 1"></span>
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
                                    <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-600 font-extrabold text-xs border border-rose-200"
                                          x-text="'Sisa: ' + ls.stock + ' ' + ls.unit"></span>
                                </div>
                            </template>
                            <div x-show="!reportData.low_stock || reportData.low_stock.length === 0" class="py-6 text-center text-emerald-600 font-semibold">
                                ✓ Semua stok barang masih dalam kondisi aman.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Komisi Karyawan Hari Ini -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i data-lucide="award" class="w-4 h-4 text-teal-600"></i>
                        <span>Rekap Penjualan per Staf / Karyawan (Komisi)</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <template x-for="ec in reportData.employee_commissions || []" :key="ec.employee_name">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
                                <span class="font-extrabold text-slate-900 text-sm block" x-text="ec.employee_name"></span>
                                <div class="mt-2 text-xs flex justify-between text-slate-600">
                                    <span>Total Transaksi:</span>
                                    <span class="font-bold" x-text="ec.total_orders"></span>
                                </div>
                                <div class="text-xs flex justify-between text-slate-600 mt-1">
                                    <span>Total Penjualan:</span>
                                    <span class="font-black text-teal-600" x-text="formatMoney(ec.total_revenue)"></span>
                                </div>
                            </div>
                        </template>
                        <div x-show="!reportData.employee_commissions || reportData.employee_commissions.length === 0" class="col-span-3 py-4 text-center text-slate-400 text-xs">
                            Belum ada transaksi dengan staf yang dipilih.
                        </div>
                    </div>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 2: MASTER PRODUK                                            -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'products'" class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-500">Kelola katalog barang, harga modal HPP, satuan, dan varian produk.</div>
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
                                            <img :src="p.image_url || 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=100'" class="w-10 h-10 rounded-xl object-cover bg-slate-100">
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
            <!-- TAB 3: MASTER KATEGORI DENGAN FOTO                              -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'categories'" class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-500">Kelola kategori visual dengan foto/gambar untuk tampilan kasir cepat.</div>
                    <button @click="openCategoryModal()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition shadow">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Kategori Baru</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <template x-for="cat in categories" :key="cat.id">
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex flex-col justify-between hover:border-teal-500 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100 shrink-0">
                                    <template x-if="cat.image_url">
                                        <img :src="cat.image_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!cat.image_url">
                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                            <i data-lucide="tag" class="w-6 h-6"></i>
                                        </div>
                                    </template>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm" x-text="cat.name"></h4>
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
            <!-- TAB 4: BUKU KASBON & PELANGGAN                                  -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'customers'" class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-500">Buku piutang kasbon langganan dan pengingat tagihan WhatsApp.</div>
                    <button @click="openCustomerModal()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition shadow">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Tambah Pelanggan Baru</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Debt Summary & List -->
                    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i data-lucide="book-open" class="w-4 h-4 text-rose-500"></i>
                            <span>Daftar Kasbon Berjalan</span>
                        </h3>
                        <div class="divide-y divide-slate-100 text-xs">
                            <template x-for="d in debts" :key="d.id">
                                <div class="py-3 flex items-center justify-between">
                                    <div>
                                        <span class="font-extrabold text-slate-900 text-sm block" x-text="d.customer_name"></span>
                                        <span class="text-slate-400 text-[11px]" x-text="d.notes + ' • ' + d.created_at"></span>
                                        <div class="text-[11px] text-rose-600 font-bold mt-0.5" x-text="'Sisa Hutang: ' + formatMoney(d.remaining_debt)"></div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click="openPayDebtModal(d)" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs transition">
                                            Bayar / Cicil
                                        </button>
                                        <button @click="sendWaDebtReminder(d)" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs transition flex items-center gap-1">
                                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                            <span>Tagih via WA</span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <div x-show="debts.length === 0" class="py-6 text-center text-slate-400">
                                Tidak ada catatan kasbon yang belum lunas.
                            </div>
                        </div>
                    </div>

                    <!-- Member Directory -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                        <h3 class="font-bold text-slate-900 text-sm">Member / Pelanggan Tetap</h3>
                        <div class="divide-y divide-slate-100 text-xs">
                            <template x-for="c in customers" :key="c.id">
                                <div class="py-2.5 flex items-center justify-between">
                                    <div>
                                        <span class="font-bold text-slate-800 block" x-text="c.name"></span>
                                        <span class="text-slate-400 text-[10px]" x-text="c.phone || 'Tanpa No HP'"></span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold" :class="c.total_debt > 0 ? 'text-rose-600' : 'text-slate-400'" x-text="formatMoney(c.total_debt)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 5: ARUS KAS & PENGELUARAN                                   -->
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
            <!-- TAB 6: KARYAWAN & PENGGUNA                                      -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'employees'" class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-slate-500">Kelola PIN login staf kasir dan peran hak akses admin.</div>
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
                                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-sm border border-teal-200">
                                        <span x-text="emp.name.charAt(0)"></span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-sm" x-text="emp.name"></h4>
                                        <span class="text-[10px] uppercase font-bold tracking-wider" 
                                              :class="emp.role === 'admin' ? 'text-rose-600' : 'text-teal-600'" 
                                              x-text="emp.role === 'admin' ? 'Pemilik (Admin)' : 'Kasir Toko'"></span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold" 
                                      :class="emp.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400'"
                                      x-text="emp.is_active ? 'Aktif' : 'Nonaktif'"></span>
                            </div>
                            <div class="text-xs text-slate-500 flex justify-between border-t border-slate-100 pt-2">
                                <span>PIN Login:</span>
                                <span class="font-mono font-bold text-slate-800" x-text="emp.pin"></span>
                            </div>
                            <div class="flex justify-end gap-1 pt-1">
                                <button @click="editEmployee(emp)" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-600 text-xs font-semibold flex items-center gap-1">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- =============================================================== -->
            <!-- TAB 7: INTEGRASI API, LOGO & PENGATURAN TOKO                    -->
            <!-- =============================================================== -->
            <div x-show="currentTab === 'settings'" class="space-y-6">
                <!-- 1. Logo & Identitas Toko -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                        <i data-lucide="image" class="w-4 h-4 text-teal-600"></i>
                        <span>Logo Toko & Switch Struk Kasir</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                        <div class="space-y-3">
                            <label class="block text-xs font-bold text-slate-700">Upload Logo Toko (JPG/PNG):</label>
                            <input type="file" @change="handleLogoUpload($event)" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                            <p class="text-[11px] text-slate-400">Logo disimpan langsung ke database SQLite agar tidak hilang saat pindah PC.</p>
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

                <!-- 2. Bot Telegram Owner -->
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

                <!-- 3. Profil Toko & PIN Admin -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-4 text-xs">
                    <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                        <i data-lucide="store" class="w-4 h-4 text-teal-600"></i>
                        <span>Profil Usaha & PIN Keamanan</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Nama Toko:</label>
                            <input type="text" x-model="settings.store_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                        </div>
                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Jenis Usaha:</label>
                            <input type="text" x-model="settings.store_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">PIN Akses Admin Baru:</label>
                            <input type="password" maxlength="6" x-model="settings.admin_pin" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono text-center font-bold">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button @click="saveSettings()" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-xs shadow transition">
                            Simpan Perubahan Pengaturan
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- MODAL: FORM PRODUK (ADD / EDIT) -->
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

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">Harga Modal (HPP):</label>
                        <input type="number" x-model.number="productModal.form.cost_price" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">Harga Jual:</label>
                        <input type="number" x-model.number="productModal.form.sell_price" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono font-bold text-teal-600">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">Jumlah Stok Awal:</label>
                        <input type="number" x-model.number="productModal.form.stock" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">URL Foto Produk:</label>
                        <input type="text" x-model="productModal.form.image_url" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl" placeholder="https://...">
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="productModal.form.is_scale" :true-value="1" :false-value="0" class="w-4 h-4 text-teal-600 rounded">
                        <span>Barang Timbangan (Kg)</span>
                    </label>
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

    <!-- MODAL: FORM KATEGORI DENGAN FOTO -->
    <div x-show="categoryModal.open" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-xs">
            <h3 class="font-extrabold text-slate-900 text-base" x-text="categoryModal.form.id ? 'Edit Kategori' : 'Tambah Kategori Baru'"></h3>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Nama Kategori:</label>
                    <input type="text" x-model="categoryModal.form.name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold" placeholder="misal: Minuman">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">URL Foto Kategori:</label>
                    <input type="text" x-model="categoryModal.form.image_url" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl" placeholder="https://...">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Urutan Tampil (Sort Order):</label>
                    <input type="number" x-model.number="categoryModal.form.sort_order" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono">
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
                    <select x-model="expenseModal.form.category" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        <option value="Operasional Toko">Operasional Toko (Kresek, Galon, Sabun)</option>
                        <option value="Listrik & Internet">Listrik, Air & Internet</option>
                        <option value="Bahan Baku">Bahan Baku / Belanja Harian</option>
                        <option value="Gaji / Uang Makan">Gaji / Uang Makan Karyawan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Nominal (Rp):</label>
                    <input type="number" x-model.number="expenseModal.form.amount" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono font-bold text-rose-600 text-sm">
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

    <!-- MODAL: BAYAR CICILAN KASBON -->
    <div x-show="payDebtModal.open" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-xs">
            <h3 class="font-extrabold text-slate-900 text-base">Pelunasan Kasbon</h3>
            <p class="text-slate-500">Pelanggan: <strong class="text-slate-900" x-text="payDebtModal.data?.customer_name"></strong></p>
            <p class="text-rose-600 font-bold" x-text="'Sisa Hutang: ' + formatMoney(payDebtModal.data?.remaining_debt)"></p>

            <div>
                <label class="block text-slate-600 font-semibold mb-1">Nominal Pembayaran (Rp):</label>
                <input type="number" x-model.number="payDebtModal.amount" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono font-bold text-teal-600 text-sm">
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

    <script>
        function adminApp() {
            return {
                isAuthorized: sessionStorage.getItem('admin_authorized') === 'true',
                authPin: '',
                authError: '',
                currentTab: 'dashboard',
                tabTitles: {
                    dashboard: 'Dashboard Keuangan & Laba Bersih',
                    products: 'Master Produk & Katalog',
                    categories: 'Master Kategori (Foto & Ikon)',
                    customers: 'Buku Kasbon & Pelanggan',
                    expenses: 'Arus Kas Keluar (Petty Cash)',
                    employees: 'Karyawan, PIN Kasir & Komisi',
                    settings: 'Integrasi API & Logo Toko'
                },

                settings: {},
                categories: [],
                products: [],
                customers: [],
                debts: [],
                expenses: [],
                employees: [],
                reportData: {},

                productModal: { open: false, form: {} },
                categoryModal: { open: false, form: {} },
                expenseModal: { open: false, form: { category: 'Operasional Toko', amount: 0, notes: '' } },
                payDebtModal: { open: false, data: null, amount: 0 },

                async init() {
                    await this.loadInitialSettings();
                    if (this.isAuthorized) {
                        await this.loadDashboardReport();
                    }
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
                    if (d.success) {
                        this.reportData = d;
                    }
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

                // Product Actions
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

                // Category Actions
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

                // Customer & Debt Actions
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
                        `Terima kasih banyak ya 🙏`
                    );
                    window.open(`https://wa.me/${cleanPhone}?text=${text}`, '_blank');
                },

                // Expense Actions
                openExpenseModal() {
                    this.expenseModal.form = { category: 'Operasional Toko', amount: 0, notes: '' };
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
                        await this.loadDashboardReport();
                    } else {
                        alert(d.error || 'Gagal menyimpan pengeluaran.');
                    }
                },

                // Logo Upload Handler
                handleLogoUpload(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        this.settings.store_logo = event.target.result;
                    };
                    reader.readAsDataURL(file);
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
                }
            };
        }
    </script>
</body>
</html>
