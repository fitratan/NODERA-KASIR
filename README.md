# NODERA KASIR POS

<p align="center">
  <img src="assets/logo.png" alt="NODERA KASIR POS" width="180"/>
</p>

<p align="center">
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Platform-Android%20%7C%20Windows%2010%2B%20%7C%20Linux-0052CC.svg?style=for-the-badge&logo=android" alt="Platform"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Engine-Embedded%20PHP%208.2%20Micro--Server-777BB4.svg?style=for-the-badge&logo=php" alt="Engine"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Database-SQLite%203%20Zero--Setup-003B57.svg?style=for-the-badge&logo=sqlite" alt="Database"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Version-v1.0.5%20Official-00B074.svg?style=for-the-badge" alt="Version"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Security-Obfuscated%20%2B%20Cloud%20HWID-E02424.svg?style=for-the-badge" alt="Security"></a>
</p>

<p align="center">
  <a href="https://github.com/fitratan/NODERA-KASIR"><img src="https://komarev.com/ghpvc/?username=fitratan-nodera-kasir&label=Repository%20Views&color=0052cc&style=for-the-badge" alt="Repository Views"></a>
  <a href="https://github.com/fitratan/NODERA-KASIR/releases"><img src="https://img.shields.io/badge/Release%20APK-Available%20v1.0.5-38BDF8.svg?style=for-the-badge&logo=android" alt="Release APK"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Support-Multi--Business%20POS-8B5CF6.svg?style=for-the-badge" alt="Multi-Business"></a>
</p>

<p align="center">
  <b>Language / Bahasa / Langue :</b><br>
  <a href="#-bahasa-indonesia"><b>🇮🇩 Bahasa Indonesia</b></a> •
  <a href="#-english"><b>🇬🇧 English</b></a> (<a href="README.en.md">Full Page</a>) •
  <a href="#-français"><b>🇫🇷 Français</b></a> (<a href="README.fr.md">Page Complète</a>)
</p>

---

# 🇮🇩 Bahasa Indonesia

## 📌 Tentang NODERA KASIR POS

**NODERA KASIR POS** adalah generasi baru sistem kasir (*Point of Sale*) multi-usaha yang dikembangkan secara profesional oleh **NODERA Team**. Dirancang dengan filosofi **100% Offline-First**, sistem ini mampu beroperasi secara mandiri tanpa ketergantungan internet maupun server luar, namun tetap menyediakan opsi sinkronisasi cloud lisensi terpusat dan konektivitas multi-perangkat jaringan lokal.

Aplikasi ini sangat ideal untuk berbagai skala bisnis:
- **Jasa Cuci Kendaraan**: Cuci Motor, Cuci Mobil, Cuci Karpet & Helm dengan nomor antrean.
- **Barbershop & Salon**: Layanan pangkas rambut, treatment, dan komisi kapster/stylist otomatis.
- **Laundry & Servis**: Manajemen antrean nota, status pengerjaan, dan pengambilan barang.
- **Kedai Kopi & Cafe**: Resto cepat saji, cetak tiket dapur (kitchen bar), dan open bill (hold transaksi).
- **Toko Kelontong & Retail**: Penjualan barcode scan camera/laser, level harga grosir bertingkat, dan stok opname.

---

## 📊 Perbandingan: Kasir Konvensional vs NODERA KASIR POS

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
| **Visibility Status Bar** | Tidak menyembunyikan status bar sistem Android agar staf kasir selalu dapat memantau sisa daya baterai dan koneksi printer. |
| **Animasi & Transisi** | Logo floating pulse, loading indicator horizontal Sky Blue (`#38BDF8`), dan fade-out 350ms interpolator. |
| **Multi-Bahasa Terintegrasi** | **5 Bahasa Penuh dengan 100% Parity (2.373 keys)**: **Bahasa Indonesia (ID)**, **English (EN)**, **Français (FR)**, **Español (ES)**, dan **Tagalog / Filipino (TL)**. |

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
| **Pembayaran Non-Tunai** | Dynamic QRIS (NODERA PAY Gateway), Transfer Bank, Kartu Debit, dan Kasbon / Hutang Pelanggan. |
| **Printer Thermal 58mm & 80mm** | Layout struk presisi untuk kertas 58mm (karakter 32 kolom) dan 80mm (karakter 48 kolom). |
| **Direct Android RawBT Print** | Cetak otomatis tanpa dialog browser menggunakan native intent `android.intent.action.SEND` teroptimasi. |
| **Kustomisasi Header & Footer** | Tambahkan nama toko, alamat, nomor WhatsApp, nomor antrean, dan catatan kaki struk secara fleksibel. |

### 5. Keamanan, Anti-Tamper & Proteksi Lisensi
| Fitur | Spesifikasi / Rincian Teknis |
| :--- | :--- |
| **Obfuscated Production Code** | Master source code diproteksi menggunakan script kompilasi khusus (XOR + Deflate) sebelum dibungkus ke file APK / binary release. |
| **BCrypt Hash Security** | Seluruh PIN kasir dan kata sandi back-office dienkripsi dengan algoritma standar keamanan `bcrypt`. |
| **Clock Rollback Guard** | Memeriksa integritas waktu sistem untuk mencegah eksploitasi perpanjangan masa trial dengan memundurkan jam HP/PC. |
| **Hardware ID (HWID) Locking** | Binding lisensi ke identitas unik perangkat (`NDR-POS-...`) yang diverifikasi aman ke Cloud Panel NODERA. |

---

## 💻 Panduan Instalasi & Eksekusi

| Platform | Metode Instalasi | File Executable / Paket | Langkah Penggunaan |
| :--- | :--- | :--- | :--- |
| **Android Tablet / HP** | Sideload APK Resmi | `dist/Nodera-Kasir-POS.apk` | 1. Unduh dan instal APK ke perangkat Android.<br>2. Buka aplikasi, server lokal otomatis menyala.<br>3. Langsung mulai transaksi kasir offline. |
| **Windows Desktop (PC Kasir)** | Native Windows Launcher | `dist/NoderaKasir.exe` | 1. Ekstrak paket desktop ke folder lokal.<br>2. Jalankan `NoderaKasir.exe` (langsung aktif di system tray).<br>3. Buka kasir via WebView2 internal atau browser. |
| **Client Jaringan Lokal (WiFi/LAN)** | Akses Browser Web | Web Browser (Chrome/Firefox/Edge) | 1. Hubungkan HP/Tablet ke WiFi yang sama dengan PC server.<br>2. Buka URL: `http://<IP-PC-SERVER>:8088`. |

---

## 🔑 Kredensial Login Bawaan

| Panel Pengguna | Alamat URL Akses | Username / Akun | Kata Sandi / PIN Default | Level Hak Akses |
| :--- | :--- | :--- | :--- | :--- |
| **Back-Office Admin** | `http://localhost:8088/admin` | `admin` (atau `admin@nodera.id`) | Password: `admin`<br>PIN: `888800` | Akses penuh laporan omset, master data produk, manajemen staf, & lisensi |
| **Aplikasi Kasir POS** | `http://localhost:8088/` | Kasir Utama | PIN: `123456` | Transaksi penjualan, input antrean, cetak struk kasir, & tutup shift |
| **Supervisor Kasir** | `http://localhost:8088/` | Owner / Supervisor | PIN: `888800` | Pembatalan transaksi (void), hapus tagihan terbuka, & retur penjualan |

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
| **1. Obfuscate Source Code** | `php scripts/build_obfuscator.php` | Melakukan stripping komentar, minifikasi logika PHP, enkripsi biner, dan memindahkan aset ke folder `public/` dan `app/` |
| **2. Pack Runtime Assets** | `cd app && zip -qr ../android/app/src/main/assets/kasir.zip . && cd ..` | Membungkus runtime web terobfuskasi ke dalam arsip zip internal Android |
| **3. Kompilasi Release APK** | `export JAVA_HOME=/path/to/jdk17`<br>`export PATH=$JAVA_HOME/bin:$PATH`<br>`cd android && ./gradlew assembleRelease` | Mengompilasi signed production APK menggunakan Gradle & JDK 17 |
| **4. Salin Hasil Biner** | `cp android/app/build/outputs/apk/release/app-release.apk dist/Nodera-Kasir-POS.apk` | Memindahkan biner rilis ke direktori distribusi publik |

---

<br>

# 🇬🇧 English

([Read Dedicated Full Page: README.en.md](README.en.md))

## 📌 About NODERA KASIR POS

**NODERA KASIR POS** is a next-generation multi-business **Point of Sale (POS)** and retail intelligence system engineered by the **NODERA Team**. Designed with a **100% Offline-First** philosophy, the system operates completely standalone without any dependency on an active internet connection or external server, while offering optional cloud-synchronized licensing and multi-device LAN connectivity.

The application is tailored for versatile commercial operations:
- **Vehicle Wash & Detailing**: Motorbike wash, Car wash, Carpet & Helmet laundry with queue ticketing.
- **Barbershop & Beauty Salon**: Haircut services, treatments, and automatic barber/stylist commission tracking.
- **Laundry & Repair Services**: Queue ticket numbers, order progress tracking, and pickup notifications.
- **Coffee Shops, Eateries & Cafes**: Quick-serve food ordering, kitchen bar ticket printing, and open bill tab management.
- **Retail, Minimarkets & Grocery**: High-speed camera/laser barcode scanning, tiered wholesale pricing, and real-time inventory management.

---

## 📊 Comparison: Conventional POS vs NODERA KASIR POS

| Metric & Feature | Standard Commercial POS Apps | NODERA KASIR POS (Standalone Android & Desktop) |
| :--- | :--- | :--- |
| **Android Architecture** | Basic webview wrapper requiring an active PC server running continuously | **100% Standalone APK**: Ships with an *Embedded PHP 8.2 Micro-Server* directly inside the Android device with zero PC dependency |
| **Server Requirements** | Requires external PC running XAMPP/WAMP or a costly monthly cloud VPS | **Zero Server Setup**: Boots instantly on Android (via standalone APK) or Windows (via native `NoderaKasir.exe`) |
| **Internet Dependency** | Requires constant internet connectivity; sales freeze when offline | **100% Offline-First**: Blazing fast local SQLite 3 database with WAL mode; transactions run uninterrupted anywhere |
| **Status Bar Visibility** | Often forced into aggressive fullscreen kiosk, hiding battery & WiFi indicators | **Native Status Bar Always Visible**: Clock, battery percentage, and signal strength remain visible in both Portrait & Landscape |
| **Splash Screen Startup** | Plain white flicker screen (*white-flash flicker*) | **Branded Animated Splash Screen**: Dark theme `#08101E`, floating logo pulse, Sky Blue indicator (`#38BDF8`), and smooth 350ms transition |
| **Screen Responsiveness** | Fixed, rigid orientation; fonts shrink or get clipped when rotated | **Adaptive Dual-Layout**: Automatically adjusts to 2-Column layout (70% product catalog \| 30% active cart) on tablets and landscape screens |
| **Service & Queue Module** | Restricted to physical goods; cannot record vehicle license plates or technicians | **Full Service & Wash Module**: Input license plate numbers, print physical queue tickets (`A-01`, `B-12`), and split employee commissions automatically |
| **Thermal Receipt Print** | Requires repetitive browser print dialog confirmations | **Direct RawBT Android Native & ESC/POS**: Fast one-tap Bluetooth thermal printing (58mm/80mm) without annoying dialog prompts |
| **Security & Protection** | Vulnerable raw source code, plain-text passwords, easily tampered with | **Obfuscated Engine + Anti-Tamper**: Multi-layer XOR + Deflate encryption, BCrypt hashing, clock rollback guard, and cloud HWID license validation |

---

## 🚀 Complete Feature & Functional Matrix (English)

### 1. System Architecture & Performance
- **Runtime Engine**: Embedded PHP 8.2 (JIT enabled, NDK POSIX binary for ARM64, ARMv7, x86_64).
- **Database Engine**: SQLite 3 Zero-Maintenance (WAL mode enabled for high concurrency).
- **Dual-Mode Deployment**: Standalone Android internal micro-server or Desktop PC LAN Server.

### 2. User Interface & Multilingual Support
- **Smart Adaptive Layout**: Seamless switching between portrait cashiering and tablet 2-column landscape POS.
- **Multilingual Localization**: **5 Full Languages with 100% Parity (2,373 keys)**: English (EN), French (FR), Indonesian (ID), Spanish (ES), and Tagalog (TL).
- **Status Bar Intact**: Never hides device battery or connectivity status.

### 3. Installation & Credentials (Quick Reference)
- **Default Admin**: `http://localhost:8088/admin` | Username: `admin` | Password: `admin` | PIN: `888800`
- **Default Cashier**: `http://localhost:8088/` | Account: Main Cashier | PIN: `123456`
- **Default Supervisor**: `http://localhost:8088/` | Account: Owner / Supervisor | PIN: `888800`

---

<br>

# 🇫🇷 Français

([Lire la Page Complète : README.fr.md](README.fr.md))

## 📌 À Propos de NODERA KASIR POS

**NODERA KASIR POS** est un système de caisse enregistreuse (*Point of Sale*) et d'intelligence commerciale multi-activités de nouvelle génération, développé par l'équipe **NODERA**. Conçu selon une philosophie stricte **100% Offline-First**, le système fonctionne de manière totalement autonome sans aucune dépendance à Internet ni à un serveur externe, tout en intégrant une synchronisation cloud pour les licences et une connectivité réseau local (LAN/WiFi).

L'application s'adapte parfaitement à divers secteurs commerciaux :
- **Lavage & Entretien de Véhicules** : Lavage moto, lavage auto, tapis & casques avec gestion des tickets d'attente.
- **Salons de Coiffure & Barbiers** : Forfaits de coupe, soins et calcul automatique des commissions des coiffeurs.
- **Blanchisserie & Ateliers de Réparation** : Suivi des tickets de dépôt, état d'avancement et alertes de retrait.
- **Cafés, Snacks & Restaurants** : Commande rapide au comptoir, impression des bons cuisine et gestion des notes ouvertes (open bill).
- **Épiceries & Commerce de Détail** : Vente par lecture de codes-barres (caméra/laser), tarification de gros par paliers et gestion des stocks en temps réel.

---

## 📊 Tableau Comparatif : Caisses Classiques vs NODERA KASIR POS

| Paramètre & Fonctionnalité | Applications de Caisse Ordinaires | NODERA KASIR POS (Standalone Android & Desktop) |
| :--- | :--- | :--- |
| **Architecture Android** | Simple vue web (webview) nécessitant un PC ou un serveur actif en continu | **APK 100% Autonome** : Intègre un *Micro-Serveur PHP 8.2 Embarqué* directement sur l'appareil Android sans aucun PC requis |
| **Besoins en Serveur PC** | Nécessite l'installation de XAMPP/WAMP ou la location d'un serveur VPS mensuel | **Zéro Configuration Serveur** : Démarre instantanément sur Android (via APK) ou Windows (via le binaire natif `NoderaKasir.exe`) |
| **Dépendance à Internet** | Connexion Internet obligatoire en permanence ; encaissement bloqué lors des pannes | **100% Offline-First** : Base de données locale SQLite 3 haute vitesse (mode WAL) ; encaissement fluide sans forfait Internet |
| **Barre d'État Système** | Mode kiosque plein écran forcé masquant l'état de la batterie et le signal | **Barre d'État Native Toujours Visible** : L'heure, la batterie et le signal réseau restent parfaitement visibles en portrait et paysage |
| **Écran de Démarrage (Splash)** | Écran blanc clignotant désagréable (*white-flash flicker*) | **Écran Branded & Animé** : Fond sombre `#08101E`, logo pulsant, barre de progression bleu ciel (`#38BDF8`) et transition fluide de 350 ms |
| **Adaptabilité d'Affichage** | Disposition figée ; polices réduites ou coupées lors de la rotation de l'écran | **Double Disposition Adaptative** : Bascule automatique en 2 colonnes (70% catalogue produits \| 30% panier actif) sur tablette et écran paysage |
| **Module Services & Véhicules** | Limité aux articles physiques ; impossible d'enregistrer les plaques minéralogiques | **Module Lavage & Services Complet** : Saisie de la plaque d'immatriculation, impression du ticket de file (`A-01`) et répartition des commissions |
| **Impression Thermique** | Multiples fenêtres contextuelles de confirmation du navigateur | **Direct RawBT Android & ESC/POS Natif** : Impression thermique Bluetooth rapide (58mm/80mm) en un clic sans boîte de dialogue |
| **Sécurité & Intégrité** | Code source vulnérable, mots de passe en clair, base facile à corrompre | **Moteur Obfusqué + Anti-Altération** : Chiffrement multi-couches XOR + Deflate, hachage BCrypt, garde anti-recul d'horloge et licence HWID |

---

## 🚀 Matrice Fonctionnelle Complète (Français)

### 1. Architecture & Performance Système
- **Moteur d'Exécution** : PHP 8.2 embarqué (JIT activé, binaire POSIX NDK compilé pour ARM64, ARMv7, x86_64).
- **Moteur de Base de Données** : SQLite 3 sans maintenance (mode *WAL* pour haute concurrence).
- **Modes de Déploiement** : Micro-serveur interne autonome sur smartphone/tablette Android ou Serveur réseau LAN sur PC de bureau.

### 2. Interface Utilisateur & Support Multilingue
- **Disposition Adaptative Intelligente** : Passage automatique entre mode smartphone portrait et mode tablette paysage à 2 colonnes.
- **Localisation Multilingue Complète** : **5 Langues avec Parité Absolue (2 373 clés)** : Français (FR), Anglais (EN), Indonésien (ID), Espagnol (ES) et Tagalog (TL).
- **Barre d'État Système Toujours Visible** : Permet au personnel de vérifier l'état de la batterie et la connexion Bluetooth.

### 3. Installation & Identifiants (Référence Rapide)
- **Administration Back-Office** : `http://localhost:8088/admin` | Identifiant : `admin` | Mot de passe : `admin` | PIN : `888800`
- **Caisse Enregistreuse POS** : `http://localhost:8088/` | Compte : Caissier Principal | PIN : `123456`
- **Superviseur / Gérant** : `http://localhost:8088/` | Compte : Propriétaire / Superviseur | PIN : `888800`

---

## 📁 Struktur Direktori Repositori / Directory Structure / Structure du Répertoire

| Folder / Berkas | Tipe | Fungsi & Deskripsi |
| :--- | :--- | :--- |
| `android/` | Direktori | Proyek native Android Studio / Gradle (Kotlin, PHP Server Manager, Bridge) |
| `kasir-src/` | Direktori | Source code unencrypted master (Lingkungan kerja pengembangan) |
| `app/` | Direktori | Runtime distribusi aplikasi terobfuskasi (Obfuscated PHP & Assets) |
| `assets/` | Direktori | Aset grafis resmi, logo vektor, dan ikon branding proyek |
| `bin/` | Direktori | Biner micro-server runtime Windows (PHP 8.2 portable) |
| `config/` | Direktori | Konfigurasi sistem, lisensi perangkat, dan proteksi HWID |
| `dist/` | Direktori | File biner installer resmi siap pasang (`Nodera-Kasir-POS.apk`, `NoderaKasir.exe`) |
| `launcher-go/` | Direktori | Source code launcher native Windows Go dengan integrasi WebView2 |
| `scripts/` | Direktori | Script otomasi toolchain, packaging, dan pipeline enkripsi |
| `README.md` | Dokumen | Dokumentasi utama multi-bahasa (ID / EN / FR) |
| `README.en.md` | Dokumen | Dedicated English documentation |
| `README.fr.md` | Dokumen | Documentation complète en français |

---

## 📞 Pembelian Lisensi & Bantuan / Licensing & Support / Support & Licences

| Layanan / Service | Informasi Kontak / Contact Details |
| :--- | :--- |
| **Portal Resmi Lisensi / License Portal** | [panel.dgtlnetsolution.com](https://panel.dgtlnetsolution.com) |
| **Dukungan Teknis Telegram / Support** | [@nullneko_exe](https://t.me/nullneko_exe) |
| **WhatsApp Customer Care** | [+62 851-5517-3547](https://wa.me/6285155173547) |
| **Pengembang Resmi / Official Developer** | **NODERA Ecosystem (Digital Network Solution)** |

---

<p align="center">
  <b>NODERA KASIR POS</b><br>
  Smart Point of Sale & Retail Intelligence System<br>
  <i>Copyright &copy; 2026 NODERA Ecosystem. All Rights Reserved.</i>
</p>
