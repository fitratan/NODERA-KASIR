# NODERA KASIR POS

<p align="center">
  <img src="assets/logo.png" alt="NODERA KASIR POS" width="180"/>
</p>

<p align="center">
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Platform-Android%20%7C%20Windows%2010%2B%20%7C%20Linux-0052CC.svg?style=for-the-badge&logo=android" alt="Platform"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Engine-Embedded%20PHP%208.2%20Micro--Server-777BB4.svg?style=for-the-badge&logo=php" alt="Engine"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Database-SQLite%203%20Zero--Setup-003B57.svg?style=for-the-badge&logo=sqlite" alt="Database"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Version-v1.0.3%20Official-00B074.svg?style=for-the-badge" alt="Version"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Security-Obfuscated%20%2B%20Cloud%20HWID-E02424.svg?style=for-the-badge" alt="Security"></a>
</p>

<p align="center">
  <a href="https://github.com/fitratan/NODERA-KASIR"><img src="https://komarev.com/ghpvc/?username=fitratan-nodera-kasir&label=Repository%20Views&color=0052cc&style=for-the-badge" alt="Repository Views"></a>
  <a href="https://github.com/fitratan/NODERA-KASIR/releases"><img src="https://img.shields.io/badge/Release%20APK-Available%20v1.0.3-38BDF8.svg?style=for-the-badge&logo=android" alt="Release APK"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Support-Multi--Business%20POS-8B5CF6.svg?style=for-the-badge" alt="Multi-Business"></a>
</p>

---

## 📌 Tentang NODERA KASIR POS

**NODERA KASIR POS** adalah generasi baru sistem kasir (*Point of Sale*) multi-usaha yang dikembangkan secara profesional oleh **NODERA Team**. Dirancang dengan filosofi **100% Offline-First**, sistem ini mampu beroperasi secara mandiri tanpa ketergantungan internet maupun server luar, namun tetap menyediakan opsi sinkronisasi cloud lisensi terpusat.

Aplikasi ini sangat ideal untuk berbagai skala bisnis:
- **Jasa Cuci Kendaraan**: Cuci Motor, Cuci Mobil, Cuci Karpet & Helm.
- **Barbershop & Salon**: Layanan pangkas rambut, treatment, dan komisi kapster/stylist.
- **Laundry & Servis**: Manajemen antrean nota, status pengerjaan, dan pengambilan.
- **Kedai Kopi & Cafe**: Resto cepat saji, cetak tiket dapur (kitchen bar), dan open bill.
- **Toko Kelontong & Retail**: Penjualan barcode scan, level harga grosir bertingkat, dan stok opname.

---

## 📊 Perbandingan: Kasir Konvensional vs NODERA KASIR POS

Berikut adalah tabel perbandingan mendasar antara aplikasi kasir biasa di pasaran dengan **NODERA KASIR POS**:

| Parameter & Fitur | Aplikasi Kasir Biasa (Umum di Pasaran) | NODERA KASIR POS (Standalone Android & Desktop) |
| :--- | :--- | :--- |
| **Arsitektur Android** | Hanya webview biasa yang membutuhkan laptop/PC aktif sebagai server | **100% Standalone APK**: Memuat *Embedded PHP 8.2 Micro-Server* internal di HP/Tablet Android tanpa butuh PC sama sekali |
| **Kebutuhan Server PC** | Wajib instal XAMPP, Web Server, atau sewa VPS bulanan | **Zero Server Setup**: Berjalan instan di Android (via APK) atau Windows (via native `NoderaKasir.exe`) |
| **Ketergantungan Internet** | Wajib online terus-menerus; transaksi macet saat internet mati | **100% Offline-First**: Database lokal SQLite3 berkecepatan tinggi, transaksi tetap berjalan lancar tanpa kuota internet |
| **Tampilan Status Bar** | Sering dipaksa Fullscreen Kiosk sehingga persentase baterai & sinyal hilang | **Native Status Bar Always Visible**: Baterai, jam, dan sinyal HP tetap terlihat jelas di orientasi Portrait & Landscape |
| **Splash Screen Awal** | Layar putih polos membosankan (*white-flash flicker*) | **Branded Animated Splash Screen**: Background `#08101E`, logo badge pulse, progress bar Sky Blue, dan transisi halus 350ms ke WebView |
| **Responsivitas Layar** | Kaku, sering terpotong atau font mengecil saat diputar | **Adaptive Dual-Layout**: Otomatis 2-Kolom (70% katalog produk \| 30% keranjang transaksi) di tablet/landscape |
| **Modul Jasa & Antrean** | Hanya untuk barang fisik; sulit mencatat plat nomor & teknisi | **Modul Cuci & Servis Lengkap**: Input nomor polisi/plat, cetak tiket antrean cuci (`A-01`), dan bagi komisi staf otomatis |
| **Cetak Struk Thermal** | Memerlukan konfirmasi print dialog browser berkali-kali | **Direct RawBT Android Native & ESC/POS**: Cetak cepat Bluetooth thermal 58mm/80mm instan tanpa dialog |
| **Sistem Keamanan & Data** | Database rentan rusak, kode mentah mudah dibajak | **Obfuscated Engine + Anti-Tamper**: Enkripsi kode produksi, bcrypt hashing, deteksi clock rollback, & cloud HWID license |

---

## 🚀 Fitur Lengkap & Matriks Fungsional

### 1. Arsitektur & Performa Sistem
| Fitur | Spesifikasi / Rincian Teknis |
| :--- | :--- |
| **Engine Runtime** | Embedded PHP 8.2 (JIT enabled, NDK POSIX binary terkompilasi untuk ARM64, ARMv7, x86_64) |
| **Database Engine** | SQLite 3 Zero-Maintenance (File tunggal aman, *WAL mode enabled* untuk konkurensi tinggi) |
| **Dual-Mode Deployment** | **Mode 1**: Standalone Android internal micro-server.<br>**Mode 2**: Desktop PC LAN Server untuk multi-kasir. |
| **Cold Launch Optimization** | Zero-flicker startup dengan konfigurasi tema gelap native Android `#08101E`. |

### 2. Antarmuka Layar & Pengalaman Pengguna (UI/UX)
| Fitur | Spesifikasi / Rincian Teknis |
| :--- | :--- |
| **Orientasi Cerdas** | Otomatis menyesuaikan layar HP (Portrait kasir cepat) dan Tablet POS (Landscape 2-Kolom interaktif). |
| **Visibility Status Bar** | Tidak menyembunyikan status bar sistem Android agar staf kasir selalu dapat memantau sisa daya baterai dan koneksi WiFi/Bluetooth printer. |
| **Animasi & Transisi** | Logo card breathing pulse, loading indicator horizontal Sky Blue (`#38BDF8`), dan fade-out 350ms interpolator. |
| **Multi-Bahasa Terintegrasi** | Mendukung 3 bahasa utama: **Bahasa Indonesia (ID)**, **English (EN)**, dan **Français (FR)**. |

### 3. Modul Kasir, Layanan Jasa & Cuci Kendaraan
| Fitur | Spesifikasi / Rincian Teknis |
| :--- | :--- |
| **Kasir Penjualan Cepat** | Pencarian instan nama produk/jasa, filter kategori tombol besar, barcode scanner camera, dan input manual. |
| **Manajemen Plat Nomor** | Khusus usaha cuci mobil/motor/servis: catat nomor plat kendaraan pelanggan langsung di keranjang kasir. |
| **Tiket Antrean Kendaraan** | Otomatis menerbitkan nomor antrean (misal: `A-01`, `B-12`) untuk diselipkan pada kaca spion atau dashboard kendaraan. |
| **Perhitungan Komisi Staf** | Mengalokasikan persentase atau nominal rupiah komisi untuk staf/teknisi pencuci secara otomatis per transaksi. |
| **Harga Grosir Bertingkat** | Template penetapan harga grosir otomatis berdasarkan kuantiti pembelian (misal: beli >= 3 diskon Rp 2.000). |
| **Open Bill / Pending Order** | Menyimpan pesanan sementara dan melanjutkan pembayaran saat pelanggan selesai bersantap/servis. |

### 4. Pembayaran & Cetak Struk Instan
| Fitur | Spesifikasi / Rincian Teknis |
| :--- | :--- |
| **Pembayaran Tunai (Cash)** | Tombol nominal uang pas cepat (`Uang Pas`, `Rp 20.000`, `Rp 50.000`, `Rp 100.000`) dan perhitungan kembalian instan. |
| **Pembayaran Non-Tunai** | Dynamic QRIS (GoPay, OVO, Dana, ShopeePay, LinkAja, Mobile Banking), Transfer Bank, dan Kartu Debit. |
| **Printer Thermal 58mm & 80mm** | Layout struk presisi untuk kertas 58mm (karakter 32 kolom) dan 80mm (karakter 48 kolom). |
| **Direct Android RawBT Print** | Cetak otomatis tanpa dialog browser menggunakan native intent `android.intent.action.SEND` teroptimasi. |
| **Kustomisasi Header & Footer** | Tambahkan nama toko, alamat, nomor WhatsApp, nomor antrean, dan catatan kaki struk secara fleksibel. |

### 5. Keamanan, Anti-Tamper & Proteksi Lisensi
| Fitur | Spesifikasi / Rincian Teknis |
| :--- | :--- |
| **Obfuscated Production Code** | Master source code diproteksi menggunakan script kompilasi khusus sebelum dibungkus ke file APK / binary release. |
| **BCrypt Hash Security** | Seluruh PIN kasir dan kata sandi back-office dienkripsi dengan algoritma standar keamanan `bcrypt`. |
| **Clock Rollback Guard** | Memeriksa integritas waktu sistem untuk mencegah eksploitasi perpanjangan masa trial dengan memundurkan jam HP/PC. |
| **Hardware ID (HWID) Locking** | Binding lisensi ke identitas unik perangkat (`NDR-POS-...`) yang diverifikasi aman ke Cloud Panel NODERA. |

---

## 💻 Panduan Instalasi & Eksekusi

| Platform | Metode Instalasi | File Executable / Paket | Langkah Penggunaan |
| :--- | :--- | :--- | :--- |
| **Android Tablet / HP** | Sideload APK Resmi | `dist/Nodera-Kasir-POS.apk` | 1. Unduh dan instal APK ke perangkat Android.<br>2. Buka aplikasi, server lokal otomatis menyala.<br>3. Langsung mulai transaksi kasir offline. |
| **Windows Desktop (PC Kasir)** | Native Windows Launcher | `dist/NoderaKasir.exe` | 1. Ekstrak paket desktop ke folder lokal.<br>2. Jalankan `NoderaKasir.exe` (langsung aktif di system tray).<br>3. Buka kasir via browser atau tray icon menu. |
| **Client Jaringan Lokal (WiFi/LAN)** | Akses Browser Web | Web Browser (Chrome/Firefox/Edge) | 1. Hubungkan HP/Tablet ke WiFi yang sama dengan PC server.<br>2. Buka URL: `http://<IP-PC-SERVER>:8088`. |

---

## 🔑 Kredensial Login Bawaan

| Panel Pengguna | Alamat URL Akses | Username / Akun | Kata Sandi / PIN Default | Level Hak Akses |
| :--- | :--- | :--- | :--- | :--- |
| **Back-Office Admin** | `http://localhost:8088/admin` | `admin` (atau `admin@nodera.id`) | Password: `admin`<br>PIN: `888800` | Akses penuh laporan omset, master data produk, manajemen staf, & lisensi |
| **Aplikasi Kasir POS** | `http://localhost:8088/` | Kasir Utama | PIN: `123456` | Transaksi penjualan, input antrean, cetak struk kasir, & tutup shift |
| **Supervisor Kasir** | `http://localhost:8088/` | Owner / Supervisor | PIN: `888800` | Pembatalan transaksi, hapus tagihan terbuka, & void penjualan |

> ⚠️ **Peringatan Keamanan**: Demi keamanan data bisnis Anda, wajib segera mengubah kata sandi administrator dan PIN bawaan di menu **Pengaturan Pengguna** pada Panel Admin setelah instalasi pertama kali.

---

## 🌐 Daftar Port & Endpoint Layanan

| Komponen Layanan | Port / Path URL | Protokol | Keterangan |
| :--- | :--- | :--- | :--- |
| **Web Server Lokal** | `8088` | HTTP | Port standar micro-server internal PHP 8.2 |
| **Aplikasi POS Kasir** | `http://127.0.0.1:8088/` | HTTP GET/POST | Tampilan layar utama penjualan dan antrean |
| **Back-Office Panel** | `http://127.0.0.1:8088/admin` | HTTP GET/POST | Pengaturan master produk, harga grosir, & stok |
| **Endpoint REST API** | `http://127.0.0.1:8088/api.php` | JSON REST API | Komunikasi transaksi, Bluetooth print, & sinkronisasi data |
| **Cloud Licensing API** | `https://panel.dgtlnetsolution.com` | HTTPS | Validasi lisensi komersial dan aktivasi HWID |

---

## 📱 Persyaratan Sistem Minimum

| Komponen Perangkat | Spesifikasi Minimum Android | Spesifikasi Minimum Windows PC |
| :--- | :--- | :--- |
| **Sistem Operasi** | Android 7.0 (Nougat) hingga Android 14+ | Windows 10 / Windows 11 (64-Bit) |
| **Arsitektur CPU** | ARM64 (AArch64), ARMv7 (32-Bit), atau x86_64 | Intel Core i3 / AMD setara (Dual Core 1.8 GHz+) |
| **Memori RAM** | Minimal 2 GB (Disarankan 3 GB+ untuk kenyamanan) | Minimal 2 GB RAM (Disarankan 4 GB+) |
| **Ruang Penyimpanan** | Minimal 150 MB ruang penyimpanan kosong | Minimal 250 MB ruang penyimpanan kosong |
| **Dukungan Printer** | Thermal Bluetooth 58mm / 80mm via RawBT / WebBT | Thermal USB / Bluetooth / Virtual POS Printer |

---

## 🛠️ Panduan Build & Obfuskasi (Untuk Developer)

| Tahap | Perintah Eksekusi CLI | Penjelasan Teknis |
| :--- | :--- | :--- |
| **1. Obfuscate Source Code** | `php scripts/build_obfuscator.php` | Melakukan stripping komentar, minifikasi logika PHP, dan memindahkan aset ke folder `app/` |
| **2. Pack Runtime Assets** | `cd app && zip -qr ../android/app/src/main/assets/kasir.zip . && cd ..` | Membungkus runtime web terobfuskasi ke dalam arsip zip internal Android |
| **3. Kompilasi Release APK** | `export JAVA_HOME=/path/to/jdk17`<br>`export PATH=$JAVA_HOME/bin:$PATH`<br>`cd android && ./gradlew assembleRelease` | Mengompilasi signed production APK menggunakan Gradle & JDK 17 |
| **4. Salin Hasil Biner** | `cp android/app/build/outputs/apk/release/app-release.apk dist/Nodera-Kasir-POS.apk` | Memindahkan biner rilis ke direktori distribusi publik |

---

## 📁 Struktur Direktori Repositori

| Folder / Berkas | Tipe | Fungsi & Deskripsi |
| :--- | :--- | :--- |
| `android/` | Direktori | Proyek native Android Studio / Gradle (Kotlin, PHP Server Manager, Bridge) |
| `kasir-src/` | Direktori | Source code unencrypted master (Lingkungan kerja pengembangan) |
| `app/` | Direktori | Runtime distribusi aplikasi terobfuskasi (Obfuscated PHP & Assets) |
| `assets/` | Direktori | Aset grafis resmi, logo vektor, dan ikon branding proyek |
| `bin/` | Direktori | Biner micro-server runtime Windows |
| `config/` | Direktori | Konfigurasi sistem, lisensi perangkat, dan proteksi HWID |
| `dist/` | Direktori | File biner installer resmi siap pasang (`Nodera-Kasir-POS.apk`) |
| `scripts/` | Direktori | Script otomasi toolchain, packaging, dan pipeline enkripsi |
| `README.md` | Dokumen | Dokumentasi arsitektur dan panduan operasional proyek |

---

## 📞 Pembelian Lisensi & Layanan Bantuan

Lisensi komersial resmi **NODERA KASIR POS** dapat diaktivasi dan dikelola secara terpusat:

| Layanan | Informasi Kontak & Tautan |
| :--- | :--- |
| **Portal Resmi Lisensi** | [panel.dgtlnetsolution.com](https://panel.dgtlnetsolution.com) |
| **Dukungan Teknis Telegram** | [@nullneko_exe](https://t.me/nullneko_exe) |
| **WhatsApp Customer Care** | [085155173547](https://wa.me/6285155173547) |
| **Pengembang Resmi** | **NODERA Ecosystem (Digital Network Solution)** |

---

<p align="center">
  <b>NODERA KASIR POS</b><br>
  Smart Point of Sale & Retail Intelligence System<br>
  <i>Copyright &copy; 2026 NODERA Ecosystem. All Rights Reserved.</i>
</p>
