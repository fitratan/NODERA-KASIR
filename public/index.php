<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>KASIR by NODERA - POS Landscape</title>
    
    <!-- Offline First CSS -->
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
    
    <!-- Alpine.js & Lucide Icons -->
    <script src="assets/js/alpine.min.js" defer></script>
    <script src="assets/js/lucide.min.js"></script>
    <script src="assets/js/pos-app.js"></script>

    <style>
        /* Custom scrollbar & layout tweaks */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Thermal Receipt Preview Styling */
        @media print {
            body * { visibility: hidden; }
            #thermal-receipt-area, #thermal-receipt-area * { visibility: visible; }
            #thermal-receipt-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 58mm;
                font-family: 'Courier New', monospace;
                font-size: 11px;
                line-height: 1.2;
                color: #000;
                background: #fff;
                padding: 4px;
            }
        }

        /* Smart Rotation Reminder Overlay on Mobile Portrait */
        @media screen and (orientation: portrait) and (max-width: 768px) {
            #rotate-overlay { display: flex !important; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased overflow-hidden select-none" x-data="posApp()">

    <!-- ROTATE DEVICE OVERLAY (HANYA MUNCUL DI HP JIKA TEGAK/PORTRAIT) -->
    <div id="rotate-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-95 z-50 hidden flex-col items-center justify-center p-6 text-center text-white">
        <div class="animate-bounce mb-6 text-emerald-400">
            <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold mb-2">Putar Layar ke Mode Landscape</h2>
        <p class="text-gray-300 text-sm max-w-xs mb-6">Aplikasi Kasir NODERA dirancang mendatar (landscape) untuk kemudahan transaksi kasir & tablet.</p>
        <button @click="toggleFullscreen()" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-semibold shadow-lg transition">
            Aktifkan Layar Penuh
        </button>
    </div>

    <!-- MAIN LANDSCAPE POS WRAPPER -->
    <div class="flex flex-col h-screen w-screen overflow-hidden">
        
        <!-- TOP NAVIGATION BAR -->
        <header class="bg-white border-b border-gray-200 px-4 py-2.5 flex items-center justify-between shadow-sm z-20">
            <!-- Brand & Store Info -->
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold text-lg shadow">
                    N
                </div>
                <div>
                    <h1 class="font-bold text-gray-800 text-base leading-tight" x-text="settings.store_name || 'KASIR by NODERA'"></h1>
                    <span class="text-xs text-gray-500 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                        <span x-text="settings.store_subtitle || 'Kasir Standalone UMKM'"></span>
                    </span>
                </div>
            </div>

            <!-- Quick Mode Buttons & Cashier Profile -->
            <div class="flex items-center space-x-3">
                <!-- Mode Filter: Cuci Motor / Jasa vs Semua -->
                <div class="bg-gray-100 p-1 rounded-lg flex items-center space-x-1 border border-gray-200">
                    <button @click="serviceOnly = false; selectedCategory = null" 
                            :class="!serviceOnly ? 'bg-white text-emerald-700 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900'"
                            class="px-3 py-1.5 rounded-md text-xs transition">
                        Semua Menu
                    </button>
                    <button @click="serviceOnly = true; selectedCategory = 1" 
                            :class="serviceOnly ? 'bg-emerald-600 text-white shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900'"
                            class="px-3 py-1.5 rounded-md text-xs transition flex items-center gap-1.5">
                        <span>🏍️ Cuci Motor & Jasa</span>
                    </button>
                </div>

                <!-- Pemroses Pembayaran / Kasir Aktif (Click to Switch) -->
                <div class="relative" x-data="{ openCashierMenu: false }">
                    <button @click="openCashierMenu = !openCashierMenu" 
                            class="flex items-center space-x-2 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-900 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                        <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">
                            <span x-text="activeCashier.name ? activeCashier.name.charAt(0) : 'K'"></span>
                        </div>
                        <div class="text-left">
                            <div class="text-[10px] text-emerald-600 uppercase tracking-wider font-semibold">Kasir Pemroses:</div>
                            <div class="font-bold truncate max-w-[110px]" x-text="activeCashier.name || 'Pilih Kasir'"></div>
                        </div>
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <!-- Dropdown Pilih Kasir -->
                    <div x-show="openCashierMenu" @click.away="openCashierMenu = false" 
                         class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-xl border border-gray-100 py-1.5 z-30" style="display: none;">
                        <div class="px-3 py-1 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            Ganti Kasir Bertugas
                        </div>
                        <template x-for="c in cashiers" :key="c.id">
                            <button @click="switchCashier(c); openCashierMenu = false" 
                                    class="w-full text-left px-3 py-2 text-xs flex items-center justify-between hover:bg-emerald-50 transition"
                                    :class="activeCashier.id === c.id ? 'bg-emerald-50 font-bold text-emerald-800' : 'text-gray-700'">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center text-[10px]" x-text="c.name.charAt(0)"></span>
                                    <span x-text="c.name"></span>
                                </div>
                                <span class="text-[10px] uppercase px-1.5 py-0.5 rounded" 
                                      :class="c.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'" 
                                      x-text="c.role"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Multi-Language Switcher (ID / EN / FR) -->
                <div class="flex items-center bg-gray-100 p-1 rounded-lg border border-gray-200 text-xs">
                    <button @click="setLanguage('id')" :class="currentLang === 'id' ? 'bg-white font-bold text-gray-900 shadow-sm' : 'text-gray-500'" class="px-2 py-1 rounded">🇮🇩 ID</button>
                    <button @click="setLanguage('en')" :class="currentLang === 'en' ? 'bg-white font-bold text-gray-900 shadow-sm' : 'text-gray-500'" class="px-2 py-1 rounded">🇬🇧 EN</button>
                    <button @click="setLanguage('fr')" :class="currentLang === 'fr' ? 'bg-white font-bold text-gray-900 shadow-sm' : 'text-gray-500'" class="px-2 py-1 rounded">🇫🇷 FR</button>
                </div>

                <!-- Menu Tombol Manajemen Karyawan & Laporan -->
                <button @click="openEmployeeModal()" class="p-2 text-gray-600 hover:text-emerald-600 hover:bg-gray-100 rounded-lg transition" title="Manajemen Karyawan">
                    👥 <span class="hidden lg:inline text-xs font-semibold ml-1">Karyawan</span>
                </button>
                <button @click="openReportsModal()" class="p-2 text-gray-600 hover:text-emerald-600 hover:bg-gray-100 rounded-lg transition" title="Laporan & Komisi">
                    📊 <span class="hidden lg:inline text-xs font-semibold ml-1">Laporan</span>
                </button>
                <button @click="toggleFullscreen()" class="p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition" title="Layar Penuh">
                    ⛶
                </button>
            </div>
        </header>

        <!-- MAIN DUAL-PANE LANDSCAPE WORKSPACE (70% KATALOG | 30% KERANJANG KASIR) -->
        <div class="flex-1 flex overflow-hidden">
            
            <!-- LEFT PANE: KATALOG PRODUK & JASA (65% - 70%) -->
            <main class="w-7/12 lg:w-8/12 flex flex-col bg-gray-50 border-r border-gray-200 p-3 overflow-hidden">
                
                <!-- Search & Category Tabs -->
                <div class="mb-3 flex items-center gap-2">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <input type="text" x-model="searchQuery" :placeholder="lang.search_placeholder || 'Cari jasa / produk / scan barcode...'" 
                               class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-sm">
                        <span class="absolute left-3 top-2.5 text-gray-400 text-xs">🔍</span>
                        <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 text-xs">✕</button>
                    </div>

                    <!-- Category Pills Horizontal Scroll -->
                    <div class="flex items-center gap-1.5 overflow-x-auto py-0.5 max-w-[55%]">
                        <button @click="selectedCategory = null" 
                                :class="selectedCategory === null ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
                                class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition font-medium">
                            Semua
                        </button>
                        <template x-for="cat in categories" :key="cat.id">
                            <button @click="selectedCategory = cat.id" 
                                    :class="selectedCategory === cat.id ? 'bg-emerald-600 text-white font-semibold' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition">
                                <span x-text="cat.name"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Quick Presets for Motor Wash (Jika Kategori Cuci Aktif) -->
                <div class="mb-2.5 flex items-center gap-1.5 overflow-x-auto text-xs bg-emerald-50 p-2 rounded-lg border border-emerald-100">
                    <span class="text-emerald-800 font-bold whitespace-nowrap">⚡ Cepat Plat:</span>
                    <button @click="vehicleType = 'Matic (Beat/Vario)'; searchQuery = 'Matic Kecil'" class="px-2 py-1 bg-white hover:bg-emerald-100 border border-emerald-200 rounded text-emerald-800">🛵 Matic</button>
                    <button @click="vehicleType = 'Bebek (Supra)'; searchQuery = 'Bebek'" class="px-2 py-1 bg-white hover:bg-emerald-100 border border-emerald-200 rounded text-emerald-800">🏍️ Bebek</button>
                    <button @click="vehicleType = 'Maxi (NMax/PCX)'; searchQuery = 'Maxi'" class="px-2 py-1 bg-white hover:bg-emerald-100 border border-emerald-200 rounded text-emerald-800">🛵 Maxi</button>
                    <button @click="vehicleType = 'Sport/Moge'; searchQuery = 'Sport'" class="px-2 py-1 bg-white hover:bg-emerald-100 border border-emerald-200 rounded text-emerald-800">🏍️ Sport</button>
                    <button @click="vehicleType = 'Mobil'; searchQuery = 'Mobil'" class="px-2 py-1 bg-white hover:bg-emerald-100 border border-emerald-200 rounded text-emerald-800">🚗 Mobil</button>
                    <button @click="searchQuery = ''" class="px-2 py-1 text-gray-500 hover:text-gray-700 ml-auto">Reset</button>
                </div>

                <!-- PRODUCT CARDS GRID (SCROLLABLE) -->
                <div class="flex-1 overflow-y-auto pr-1">
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2.5">
                        <template x-for="p in filteredProducts" :key="p.id">
                            <div @click="addToCart(p)" 
                                 class="bg-white rounded-xl border border-gray-200 p-3 hover:border-emerald-500 hover:shadow-md cursor-pointer transition flex flex-col justify-between active:scale-95 group">
                                <div>
                                    <!-- Badges -->
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded"
                                              :class="p.type === 'service' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-amber-50 text-amber-700 border border-amber-100'"
                                              x-text="p.type === 'service' ? 'Jasa Layanan' : 'Barang'"></span>
                                        <template x-if="p.type === 'goods' && p.stock >= 0">
                                            <span class="text-[10px] text-gray-500 font-medium" x-text="'Stok: ' + p.stock"></span>
                                        </template>
                                        <template x-if="p.type === 'service' && p.default_worker_commission > 0">
                                            <span class="text-[10px] text-emerald-600 font-medium" x-text="'Komisi: ' + formatMoney(p.default_worker_commission)"></span>
                                        </template>
                                    </div>

                                    <!-- Product Name -->
                                    <h3 class="font-bold text-gray-800 text-xs leading-snug line-clamp-2 group-hover:text-emerald-700 transition" x-text="p.name"></h3>
                                </div>

                                <!-- Price Tag -->
                                <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center justify-between">
                                    <span class="font-extrabold text-sm text-emerald-600" x-text="formatMoney(p.price)"></span>
                                    <span class="w-6 h-6 rounded-full bg-gray-100 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center text-xs font-bold text-gray-600 transition">+</span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State -->
                    <div x-show="filteredProducts.length === 0" class="h-48 flex flex-col items-center justify-center text-gray-400">
                        <span class="text-3xl mb-2">🔍</span>
                        <p class="text-xs">Tidak ditemukan produk atau jasa yang cocok.</p>
                    </div>
                </div>
            </main>

            <!-- RIGHT PANE: KERANJANG KASIR & PEMROSES PEMBAYARAN (30% - 35%) -->
            <aside class="w-5/12 lg:w-4/12 flex flex-col bg-white border-l border-gray-200 overflow-hidden shadow-lg">
                
                <!-- Cart Header & Customer / Vehicle Meta -->
                <div class="p-3 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="font-bold text-gray-800 text-sm">🛒 Keranjang Kasir</span>
                            <span class="text-xs text-gray-500" x-text="'(' + cart.length + ' item)'"></span>
                        </div>
                        <button @click="clearCart()" x-show="!isCartEmpty" class="text-xs text-red-500 hover:text-red-700 font-medium">Kosongkan</button>
                    </div>

                    <!-- Input Spesial Cuci Motor: Plat Nomor & Petugas Lapangan -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <!-- Plat Nomor Kendaraan -->
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-0.5">Plat Nomor / Identitas:</label>
                            <input type="text" x-model="vehiclePlate" placeholder="B 1234 ABC" 
                                   class="w-full px-2 py-1.5 bg-white border border-gray-300 rounded text-xs font-mono font-bold uppercase focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <!-- Petugas Cuci / Lapangan (Untuk Komisi) -->
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-0.5">Dikerjakan Oleh:</label>
                            <select x-model="activeWorker.id" @change="activeWorker.name = $event.target.options[$event.target.selectedIndex].text"
                                    class="w-full px-2 py-1.5 bg-white border border-gray-300 rounded text-xs font-medium focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                                <option value="">-- Tanpa Petugas --</option>
                                <template x-for="w in workers" :key="w.id">
                                    <option :value="w.id" x-text="w.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- CART ITEMS LIST (SCROLLABLE) -->
                <div class="flex-1 overflow-y-auto p-3 divide-y divide-gray-100">
                    <template x-for="(item, idx) in cart" :key="idx">
                        <div class="py-2.5 flex items-center justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-xs text-gray-800 truncate" x-text="item.name"></h4>
                                <div class="text-[11px] text-gray-500 flex items-center gap-2 mt-0.5">
                                    <span x-text="formatMoney(item.price)"></span>
                                    <template x-if="item.worker_commission > 0">
                                        <span class="text-emerald-600 font-semibold text-[10px]" x-text="'(Komisi ' + formatMoney(item.worker_commission) + ')'"></span>
                                    </template>
                                </div>
                            </div>

                            <!-- Qty Buttons -->
                            <div class="flex items-center space-x-1.5">
                                <button @click="updateQty(idx, -1)" class="w-6 h-6 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center font-bold text-xs">-</button>
                                <span class="w-7 text-center font-bold text-xs" x-text="item.qty"></span>
                                <button @click="updateQty(idx, 1)" class="w-6 h-6 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center font-bold text-xs">+</button>
                            </div>

                            <!-- Line Total -->
                            <div class="w-20 text-right font-bold text-xs text-gray-800" x-text="formatMoney(item.price * item.qty)"></div>

                            <!-- Delete Item -->
                            <button @click="removeFromCart(idx)" class="text-gray-300 hover:text-red-500 text-xs pl-1">✕</button>
                        </div>
                    </template>

                    <!-- Empty Cart Message -->
                    <div x-show="isCartEmpty" class="h-full flex flex-col items-center justify-center text-center p-6 text-gray-400">
                        <span class="text-4xl mb-2">🛒</span>
                        <p class="text-xs" x-text="lang.empty_cart"></p>
                    </div>
                </div>

                <!-- CART FOOTER & CHECKOUT BUTTONS -->
                <div class="p-3 border-t border-gray-200 bg-gray-50">
                    <!-- Calculations -->
                    <div class="space-y-1 text-xs mb-3">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-semibold text-gray-800" x-text="formatMoney(subtotal)"></span>
                        </div>
                        <template x-if="discount > 0">
                            <div class="flex justify-between text-red-600 font-medium">
                                <span>Diskon</span>
                                <span x-text="'-' + formatMoney(discount)"></span>
                            </div>
                        </template>
                        <template x-if="taxAmount > 0">
                            <div class="flex justify-between text-gray-600">
                                <span>Pajak (<span x-text="settings.tax_percent"></span>%)</span>
                                <span x-text="formatMoney(taxAmount)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between text-sm font-extrabold text-gray-900 pt-1.5 border-t border-gray-200">
                            <span>TOTAL TAGIHAN</span>
                            <span class="text-emerald-700 text-base" x-text="formatMoney(total)"></span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-3 gap-2">
                        <!-- Tombol Tiket Antrean Cuci Motor -->
                        <button @click="createQueueTicket()" 
                                :disabled="!vehiclePlate"
                                :class="vehiclePlate ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                                class="col-span-1 py-3 px-2 rounded-xl font-bold text-xs transition shadow flex flex-col items-center justify-center">
                            <span>🎫 Tiket</span>
                            <span class="text-[10px] font-normal">Antrean</span>
                        </button>

                        <!-- Tombol Bayar & Cetak Struk (Besar) -->
                        <button @click="openPaymentModal()" 
                                :disabled="isCartEmpty"
                                :class="!isCartEmpty ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-200' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                                class="col-span-2 py-3 px-4 rounded-xl font-extrabold text-sm transition shadow-lg flex items-center justify-center gap-2">
                            <span>💳 PROSES BAYAR</span>
                        </button>
                    </div>

                    <!-- Label Kasir Aktif yang Mencatat Transaksi -->
                    <div class="mt-2 text-center text-[10px] text-gray-500">
                        Diproses oleh: <span class="font-bold text-gray-700" x-text="activeCashier.name"></span>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- MODAL 1: PEMBAYARAN & CHECKOUT -->
    <div x-show="activeModal === 'payment'" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl overflow-hidden" @click.away="activeModal = null">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <div>
                    <h3 class="font-extrabold text-gray-900 text-base">Konfirmasi Pembayaran</h3>
                    <p class="text-xs text-gray-500">Pemroses: <span class="font-bold text-emerald-700" x-text="activeCashier.name"></span></p>
                </div>
                <button @click="activeModal = null" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
            </div>

            <!-- Total Amount Header -->
            <div class="bg-emerald-50 rounded-xl p-3.5 text-center mb-4 border border-emerald-100">
                <span class="text-xs text-emerald-800 font-semibold uppercase tracking-wider">Total yang Harus Dibayar:</span>
                <div class="text-2xl font-black text-emerald-700 mt-0.5" x-text="formatMoney(total)"></div>
            </div>

            <!-- Pilih Metode Pembayaran -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button @click="paymentMethod = 'cash'" 
                        :class="paymentMethod === 'cash' ? 'border-2 border-emerald-600 bg-emerald-50 font-bold text-emerald-800' : 'border border-gray-200 text-gray-700'"
                        class="p-2.5 rounded-xl text-xs flex flex-col items-center justify-center gap-1 transition">
                    <span class="text-xl">💵</span>
                    <span>Tunai (Cash)</span>
                </button>
                <button @click="paymentMethod = 'qris'" 
                        :class="paymentMethod === 'qris' ? 'border-2 border-emerald-600 bg-emerald-50 font-bold text-emerald-800' : 'border border-gray-200 text-gray-700'"
                        class="p-2.5 rounded-xl text-xs flex flex-col items-center justify-center gap-1 transition">
                    <span class="text-xl">📱</span>
                    <span>Dynamic QRIS</span>
                </button>
                <button @click="paymentMethod = 'transfer'" 
                        :class="paymentMethod === 'transfer' ? 'border-2 border-emerald-600 bg-emerald-50 font-bold text-emerald-800' : 'border border-gray-200 text-gray-700'"
                        class="p-2.5 rounded-xl text-xs flex flex-col items-center justify-center gap-1 transition">
                    <span class="text-xl">💳</span>
                    <span>Transfer Bank</span>
                </button>
            </div>

            <!-- Bagian Tunai / Cash Tendered -->
            <div x-show="paymentMethod === 'cash'" class="mb-4">
                <label class="block text-xs font-bold text-gray-700 mb-1">Uang Diterima (Rp):</label>
                <input type="number" x-model.number="cashTendered" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-lg font-mono font-bold focus:border-emerald-500 focus:outline-none mb-2">
                
                <!-- Quick Cash Presets -->
                <div class="grid grid-cols-4 gap-1.5 mb-3">
                    <button @click="setCashTendered('exact')" class="px-2 py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-xs font-bold text-gray-700">Uang Pas</button>
                    <button @click="setCashTendered(20000)" class="px-2 py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-xs font-bold text-gray-700">20.000</button>
                    <button @click="setCashTendered(50000)" class="px-2 py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-xs font-bold text-gray-700">50.000</button>
                    <button @click="setCashTendered(100000)" class="px-2 py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-xs font-bold text-gray-700">100.000</button>
                </div>

                <!-- Kembalian -->
                <div class="flex justify-between items-center p-2.5 bg-gray-50 rounded-lg text-xs font-bold">
                    <span class="text-gray-600">Uang Kembalian:</span>
                    <span class="text-base font-extrabold text-emerald-600" x-text="formatMoney(changeAmount)"></span>
                </div>
            </div>

            <!-- Bagian QRIS Dinamis -->
            <div x-show="paymentMethod === 'qris'" class="mb-4 text-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                <div class="text-xs font-bold text-gray-700 mb-2">Scan QRIS untuk Pembayaran Instan</div>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=00020101021126570011ID.NODERAPAY.WWW01189360091100213049020208123456785204541153033605802ID5914KASIR%20NODERA6007JAKARTA61051234062070703A016304ABCD" 
                     class="mx-auto rounded-lg shadow-sm border p-1 bg-white mb-2" alt="QRIS Dinamis">
                <p class="text-[11px] text-gray-500">Mendukung GoPay, OVO, Dana, ShopeePay, BCA, & Mobile Banking.</p>
            </div>

            <!-- Tombol Selesaikan & Cetak -->
            <button @click="processCheckout()" 
                    :disabled="paymentMethod === 'cash' && cashTendered < total"
                    :class="(paymentMethod !== 'cash' || cashTendered >= total) ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                    class="w-full py-3 rounded-xl font-extrabold text-sm shadow-lg transition">
                SELESAIKAN & CETAK STRUK
            </button>
        </div>
    </div>

    <!-- MODAL 2: CETAK STRUK THERMAL RESMI -->
    <div x-show="activeModal === 'receipt'" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between border-b pb-2 mb-3">
                <h3 class="font-bold text-gray-800 text-sm">Pratinjau Struk Kasir</h3>
                <button @click="activeModal = null" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
            </div>

            <!-- Printable Thermal Area -->
            <div class="flex-1 overflow-y-auto bg-gray-50 p-4 border border-dashed border-gray-300 font-mono text-xs text-gray-900 rounded-lg space-y-2" id="thermal-receipt-area">
                <div class="text-center">
                    <div class="font-extrabold text-sm" x-text="settings.store_name"></div>
                    <div class="text-[10px] text-gray-500" x-text="settings.store_address"></div>
                    <div class="text-[10px] text-gray-500" x-text="'Telp: ' + settings.store_phone"></div>
                </div>
                <div class="border-t border-dashed border-gray-400 my-2"></div>
                
                <div class="text-[10px] space-y-0.5">
                    <div class="flex justify-between"><span>No: <b x-text="lastTransaction?.invoice_no"></b></span><span x-text="lastTransaction?.created_at"></span></div>
                    <div class="flex justify-between"><span>Kasir:</span><b x-text="lastTransaction?.cashier_name"></b></div>
                    <template x-if="lastTransaction?.worker_name">
                        <div class="flex justify-between"><span>Petugas:</span><b x-text="lastTransaction?.worker_name"></b></div>
                    </template>
                    <template x-if="lastTransaction?.vehicle_plate">
                        <div class="flex justify-between"><span>Plat Motor:</span><b x-text="lastTransaction?.vehicle_plate"></b></div>
                    </template>
                </div>
                
                <div class="border-t border-dashed border-gray-400 my-2"></div>

                <!-- Items -->
                <div class="space-y-1 text-[11px]">
                    <template x-for="item in lastTransaction?.items || []" :key="item.product_id">
                        <div>
                            <div class="font-medium" x-text="item.name"></div>
                            <div class="flex justify-between text-gray-600">
                                <span x-text="item.qty + ' x ' + formatMoney(item.price)"></span>
                                <span class="font-bold" x-text="formatMoney(item.price * item.qty)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="border-t border-dashed border-gray-400 my-2"></div>

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

                <div class="border-t border-dashed border-gray-400 my-2"></div>
                <div class="text-center text-[10px] text-gray-500" x-text="settings.footer_receipt"></div>
            </div>

            <!-- Print Actions -->
            <div class="mt-3 flex gap-2">
                <button @click="printThermal()" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs shadow flex items-center justify-center gap-1.5">
                    <span>🖨️ Cetak Struk (ESC/POS)</span>
                </button>
                <button @click="activeModal = null" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-lg text-xs">
                    Selesai
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 3: TIKET ANTREAN CUCI MOTOR -->
    <div x-show="activeModal === 'queue_receipt'" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-white rounded-2xl max-w-xs w-full p-5 shadow-2xl text-center">
            <h3 class="font-bold text-gray-800 text-sm mb-2">Tiket Antrean Cuci Motor</h3>
            
            <div class="p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300 font-mono mb-4 text-xs">
                <div class="font-bold text-gray-600 uppercase text-[10px]">Nomor Antrean:</div>
                <div class="text-4xl font-black text-blue-600 my-1" x-text="lastQueueTicket?.ticket_no"></div>
                <div class="font-extrabold text-sm text-gray-800" x-text="lastQueueTicket?.vehicle_plate"></div>
                <div class="text-gray-500 text-[10px]" x-text="lastQueueTicket?.service_name"></div>
                <div class="text-[10px] text-gray-400 mt-2" x-text="lastQueueTicket?.created_at"></div>
            </div>

            <div class="flex gap-2">
                <button @click="printThermal()" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs">
                    🖨️ Cetak Tiket
                </button>
                <button @click="activeModal = null" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-lg text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 4: MANAJEMEN KARYAWAN & KOMISI PETUGAS CUCI -->
    <div x-show="activeModal === 'employee'" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-white rounded-2xl max-w-3xl w-full p-5 shadow-2xl flex flex-col max-h-[90vh]" @click.away="activeModal = null">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <div>
                    <h3 class="font-extrabold text-gray-900 text-base">👥 Manajemen Karyawan & Komisi</h3>
                    <p class="text-xs text-gray-500">Kelola kasir pemroses pembayaran dan petugas cuci motor / teknisi.</p>
                </div>
                <button @click="activeModal = null" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
            </div>

            <div class="flex-1 grid grid-cols-1 md:grid-cols-5 gap-4 overflow-y-auto">
                <!-- Form Tambah / Edit Karyawan (2 Kolom) -->
                <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <h4 class="font-bold text-xs text-gray-800 uppercase mb-3" x-text="employeeForm.id ? 'Edit Karyawan' : 'Tambah Karyawan Baru'"></h4>
                    <div class="space-y-2.5 text-xs">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Nama Karyawan:</label>
                            <input type="text" x-model="employeeForm.name" placeholder="Contoh: Rian Hidayat" class="w-full px-3 py-1.5 border rounded-lg focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Peran / Jabatan:</label>
                            <select x-model="employeeForm.role" class="w-full px-3 py-1.5 border rounded-lg focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                                <option value="cashier">Kasir Pemroses Pembayaran</option>
                                <option value="worker">Petugas Cuci / Lapangan (Dapat Komisi)</option>
                                <option value="admin">Admin / Pemilik</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Nomor WhatsApp / HP:</label>
                            <input type="text" x-model="employeeForm.phone" placeholder="0812xxxx" class="w-full px-3 py-1.5 border rounded-lg focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">PIN Cepat (4 Angka):</label>
                            <input type="text" x-model="employeeForm.pin" placeholder="1234" maxlength="4" class="w-full px-3 py-1.5 border rounded-lg focus:ring-1 focus:ring-emerald-500 focus:outline-none font-mono">
                        </div>
                        <div x-show="employeeForm.role === 'worker'">
                            <label class="block font-semibold text-gray-700 mb-1">Komisi Default per Layanan (Rp):</label>
                            <input type="number" x-model.number="employeeForm.commission_rate" class="w-full px-3 py-1.5 border rounded-lg focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button @click="saveEmployee()" class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition">Simpan Karyawan</button>
                            <button @click="resetEmployeeForm()" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">Reset</button>
                        </div>
                    </div>
                </div>

                <!-- Daftar Karyawan & Rekap Komisi Hari Ini (3 Kolom) -->
                <div class="md:col-span-3 space-y-2">
                    <h4 class="font-bold text-xs text-gray-800 uppercase mb-2">Daftar Karyawan Aktif</h4>
                    <template x-for="emp in employees" :key="emp.id">
                        <div class="p-3 bg-white border border-gray-200 rounded-xl hover:border-emerald-300 transition flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs text-gray-800" x-text="emp.name"></span>
                                    <span class="text-[10px] px-2 py-0.5 rounded font-semibold uppercase"
                                          :class="emp.role === 'admin' ? 'bg-purple-100 text-purple-700' : (emp.role === 'cashier' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')"
                                          x-text="emp.role"></span>
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5">
                                    <template x-if="emp.role === 'worker'">
                                        <span>Hari ini: <b class="text-emerald-600" x-text="emp.today_jobs || 0"></b> motor | Komisi: <b class="text-emerald-600" x-text="formatMoney(emp.today_commission)"></b></span>
                                    </template>
                                    <template x-if="emp.role === 'cashier' || emp.role === 'admin'">
                                        <span>Memproses: <b class="text-blue-600" x-text="emp.today_cashier_transactions || 0"></b> transaksi (<b class="text-blue-600" x-text="formatMoney(emp.today_cashier_total)"></b>)</span>
                                    </template>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button @click="editEmployee(emp)" class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded">Edit</button>
                                <button @click="deleteEmployee(emp.id)" class="px-2 py-1 text-xs bg-red-50 hover:bg-red-100 text-red-600 rounded">Hapus</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 5: LAPORAN PENJUALAN & PEMBAGIAN KOMISI -->
    <div x-show="activeModal === 'reports'" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-40" style="display: none;">
        <div class="bg-white rounded-2xl max-w-3xl w-full p-5 shadow-2xl flex flex-col max-h-[90vh]" @click.away="activeModal = null">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <div>
                    <h3 class="font-extrabold text-gray-900 text-base">📊 Laporan Kasir & Rekap Komisi</h3>
                    <p class="text-xs text-gray-500">Transparansi omset per kasir dan komisi tukang cuci.</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="date" x-model="reportDate" @change="openReportsModal()" class="px-2.5 py-1 text-xs border rounded-lg">
                    <button @click="activeModal = null" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto space-y-4">
                <!-- Ringkasan Kartu -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                        <div class="text-[10px] text-emerald-800 font-bold uppercase">Total Omset Harian</div>
                        <div class="text-xl font-extrabold text-emerald-700 mt-1" x-text="formatMoney(reportsData.summary?.total_turnover)"></div>
                        <div class="text-[10px] text-emerald-600" x-text="(reportsData.summary?.total_transactions || 0) + ' Transaksi'"></div>
                    </div>
                    <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl">
                        <div class="text-[10px] text-blue-800 font-bold uppercase">Uang Tunai (Cash)</div>
                        <div class="text-xl font-extrabold text-blue-700 mt-1" x-text="formatMoney(reportsData.summary?.total_cash)"></div>
                        <div class="text-[10px] text-blue-600">Fisik di Laci Kasir</div>
                    </div>
                    <div class="p-3 bg-purple-50 border border-purple-100 rounded-xl">
                        <div class="text-[10px] text-purple-800 font-bold uppercase">Non-Tunai (QRIS / Bank)</div>
                        <div class="text-xl font-extrabold text-purple-700 mt-1" x-text="formatMoney(parseFloat(reportsData.summary?.total_qris || 0) + parseFloat(reportsData.summary?.total_transfer || 0))"></div>
                        <div class="text-[10px] text-purple-600">Masuk Rekening Langsung</div>
                    </div>
                </div>

                <!-- Rekap per Kasir (Siapa Pemroses Pembayarannya) -->
                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200">
                    <h4 class="font-bold text-xs text-gray-800 uppercase mb-2">👤 Rekap Omset per Kasir Pemroses:</h4>
                    <div class="divide-y divide-gray-200 text-xs">
                        <template x-for="c in reportsData.by_cashier || []" :key="c.cashier_name">
                            <div class="py-2 flex justify-between items-center">
                                <span class="font-medium text-gray-800" x-text="c.cashier_name"></span>
                                <div class="text-right">
                                    <span class="font-bold text-gray-900" x-text="formatMoney(c.turnover)"></span>
                                    <span class="text-gray-500 text-[10px]" x-text="' (' + c.total_trx + ' trx)'"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Rekap per Petugas Cuci (Bagi Hasil / Komisi) -->
                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200">
                    <h4 class="font-bold text-xs text-gray-800 uppercase mb-2">🏍️ Rekap Komisi Pencuci Motor / Lapangan:</h4>
                    <div class="divide-y divide-gray-200 text-xs">
                        <template x-for="w in reportsData.by_worker || []" :key="w.worker_name">
                            <div class="py-2 flex justify-between items-center">
                                <div>
                                    <span class="font-medium text-gray-800" x-text="w.worker_name"></span>
                                    <span class="text-gray-500 text-[10px]" x-text="' (' + w.total_jobs + ' kendaraan dicuci)'"></span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-emerald-600" x-text="formatMoney(w.total_commission)"></span>
                                    <span class="text-[10px] text-gray-400 block">Hak Komisi</span>
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
