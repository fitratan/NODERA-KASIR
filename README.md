# NODERA KASIR POS (Desktop & Standalone Android)

<p align="center">
  <img src="https://img.shields.io/badge/Version-v1.0.3-00B074.svg?style=for-the-badge" alt="Version">
  <img src="https://img.shields.io/badge/Platform-Android%20%7C%20Windows%20%7C%20Linux-0052CC.svg?style=for-the-badge&logo=android" alt="Platform">
  <img src="https://img.shields.io/badge/Runtime-Embedded%20PHP%208.2%20Micro--Server-777BB4.svg?style=for-the-badge&logo=php" alt="PHP Runtime">
  <img src="https://img.shields.io/badge/Database-SQLite%203%20Zero--Setup-003B57.svg?style=for-the-badge&logo=sqlite" alt="Database">
  <img src="https://img.shields.io/badge/Status%20Bar-Visible%20%28Battery%20%26%20Signal%29-38BDF8.svg?style=for-the-badge" alt="Status Bar">
  <img src="https://img.shields.io/badge/Security-Obfuscated%20%2B%20Anti--Tamper-E11D48.svg?style=for-the-badge" alt="Security">
</p>

---

## 📌 Ringkasan Sistem

**NODERA KASIR POS** adalah solusi kasir (Point of Sale) modern *offline-first* yang dirancang untuk kecepatan operasional tinggi pada berbagai skala UMKM — mulai dari **Jasa Cuci Kendaraan (Motor / Mobil / Helm)**, **Barbershop & Salon**, **Laundry**, hingga **Kedai Kopi & Toko Retail**.

Aplikasi ini mengusung arsitektur **Dual-Engine**:
1. **100% Standalone Android**: Berjalan mandiri langsung di smartphone atau tablet Android tanpa membutuhkan server PC eksternal berkat runtime *Embedded PHP 8.2 Micro-Server* dan database lokal *SQLite3* berkecepatan tinggi di dalam file APK.
2. **Desktop & Local LAN Server**: Berjalan di PC/Laptop kasir Windows melalui *native launcher* (background zero-console window & system tray), siap diakses oleh banyak perangkat kasir/pelayan lain melalui jaringan WiFi lokal (LAN).

---

## ✨ Fitur Unggulan

### 1. Standalone Embedded Android Runtime (Zero PC Setup)
- File APK memuat micro-server internal lengkap (PHP 8.2 NDK, SQLite3, cURL, mbstring).
- Aplikasi berjalan instan dan 100% *offline-first* tanpa koneksi internet dan tanpa perlu konfigurasi web server manual.

### 2. Tampilan Modern & Status Bar Tetap Terlihat
- **Branded Dark Theme (`#08101E`)**: Tampilan elegan selaras dengan tema aplikasi kasir Nodera.
- **Modern Animated Splash Screen**: Logo badge beranimasi pulse, progress bar ramping Sky Blue, serta transisi *fade-out* 350ms yang mulus menuju WebView tanpa kedipan layar (flicker).
- **Status Bar Selalu Terlihat**: Persentase baterai, indikator sinyal, dan jam sistem Android tetap terlihat jelas baik pada orientasi Portrait maupun Landscape.
- **Layout Landscape & Portrait Cerdas**: Otomatis menyesuaikan tata letak 2-kolom (70% katalog produk | 30% keranjang pesanan) di tablet/layar lebar.

### 3. Modul Khusus UMKM Jasa & Retail
- **Manajemen Antrean & Plat Kendaraan**: Input nomor polisi/plat kendaraan dan cetak tiket antrean (contoh: `A-01`) untuk diselipkan pada kendaraan pelanggan.
- **Komisi Karyawan Otomatis**: Perhitungan otomatis hak upah/bagi hasil teknisi atau staf cuci per transaksi.
- **Penjualan Silang (Cross-Selling)**: Penggabungan jasa servis/cuci dengan penjualan produk retail (kopi, minuman, aksesoris) dalam satu struk transaksi.

### 4. Cetak Struk ESC/POS & RawBT Integration
- Kompatibel dengan printer thermal Bluetooth 58mm & 80mm.
- Terintegrasi langsung dengan Android Native Intent **RawBT** untuk pencetakan struk instan tanpa dialog browser, serta mendukung Web Bluetooth API.

### 5. Multi-Metode Pembayaran
- **Tunai (Cash)**: Tombol instan pecahan uang pas (`Uang Pas`, `Rp 20.000`, `Rp 50.000`, `Rp 100.000`) dengan kalkulasi kembalian otomatis.
- **Dynamic QRIS**: Pembayaran non-tunai via e-wallet (GoPay, OVO, Dana, ShopeePay) dan Mobile Banking.
- **Transfer Bank & Debit**.

### 6. Keamanan & Proteksi Anti-Tamper
- **Proteksi Source Code Produksi**: Seluruh kode PHP dan assets dikompilasi melalui pipeline obfuscator khusus (`scripts/build_obfuscator.php`) sebelum dipaketkan ke dalam APK.
- **Keamanan Akun**: Hashing PIN dan kata sandi menggunakan standar industri `bcrypt`.
- **Clock Rollback Guard**: Proteksi deteksi manipulasi pemunduran tanggal/jam sistem pada perangkat.
- **Hardware ID (HWID) Binding**: Identifikasi unik perangkat (`NDR-POS-...`) dengan integrasi watermark ganda pada SQLite dan cloud licensing.

---

## 🚀 Panduan Instalasi & Penggunaan

### A. Perangkat Android (Smartphone / Tablet)
Unduh file APK rilis resmi terbaru yang tersedia langsung di repositori:
```text
dist/Nodera-Kasir-POS.apk
```
1. Pasang (install) file APK ke perangkat Android Anda.
2. Buka aplikasi **Nodera Kasir POS**.
3. Aplikasi akan menginisialisasi micro-server lokal dan langsung membuka layar kasir.

### B. Komputer Windows (PC Kasir Utama)
Jalankan file executable native:
```text
dist/NoderaKasir.exe
```
- Berjalan otomatis di latar belakang tanpa jendela command prompt/CMD.
- Dilengkapi System Tray Icon di sudut kanan bawah taskbar untuk membuka Panel Admin, POS, atau melihat alamat IP LAN.

### C. Akses Multi-Device via Jaringan WiFi Lokal (LAN)
1. Hubungkan HP/Tablet/Laptop kasir lain ke jaringan WiFi yang sama dengan komputer server.
2. Buka browser di perangkat klien dan akses alamat IP server:
   ```text
   http://192.168.1.xxx:8088
   ```

---

## 🔑 Kredensial Login Bawaan

Saat pertama kali dipasang, sistem menyediakan akun default berikut:

| Panel | Alamat URL / Akses | Kredensial Default |
| :--- | :--- | :--- |
| **Back-Office / Admin** | `http://localhost:8088/admin` | Username: `admin` (atau `admin@nodera.id`)<br>Password: `admin` (atau PIN: `888800`) |
| **Aplikasi Kasir POS** | `http://localhost:8088/` | **Kasir Utama**: PIN `123456`<br>**Supervisor / Owner**: PIN `888800` |

> 🔒 **Catatan Keamanan**: Segera ganti kata sandi admin dan PIN kasir default setelah proses masuk pertama kali melalui menu **Pengaturan Pengguna** di Panel Admin Back-Office.

---

## 🛠️ Panduan Build & Kompilasi (Developer)

### Prasyarat:
- Linux / macOS / Windows WSL2
- PHP CLI >= 8.2 (dengan ekstensi `sqlite3`, `curl`, `mbstring`, `zip`)
- Java JDK 17 (disarankan JDK 17 LTS)
- Android SDK Build Tools (API Level 34)

### 1. Obfuscate & Kemas Runtime Assets
```bash
# Obfuscate source code master ke folder distribusi app/
php scripts/build_obfuscator.php

# Kemas source terobfuskasi ke dalam zip assets Android
cd app && zip -qr ../android/app/src/main/assets/kasir.zip . && cd ..
```

### 2. Kompilasi Release APK Android
```bash
export JAVA_HOME=/path/to/jdk17
export PATH=$JAVA_HOME/bin:$PATH

cd android
./gradlew assembleRelease
```
Hasil kompilasi APK bertanda tangan resmi (signed release) akan otomatis tersimpan di:
```text
android/app/build/outputs/apk/release/app-release.apk
```
Salin ke folder distribusi proyek:
```bash
cp android/app/build/outputs/apk/release/app-release.apk dist/Nodera-Kasir-POS.apk
```

---

## 📁 Struktur Direktori Repositori

```text
├── android/            # Project Android Studio / Gradle (Kotlin, PHP Server Manager, AndroidBridge)
├── kasir-src/          # Master unencrypted source code (Lingkungan Pengembangan / Gitignored)
├── app/                # Production distribution files (Obfuscated PHP & Assets)
├── bin/                # Windows micro-server binary runtime
├── config/             # Konfigurasi sistem, lisensi, dan proteksi perangkat
├── dist/               # File biner rilis siap pakai (Nodera-Kasir-POS.apk)
├── scripts/            # Script otomasi build dan pipeline enkripsi/obfuscation
├── .gitignore          # Konfigurasi proteksi repositori Git
└── README.md           # Dokumentasi resmi proyek
```

---

## 📄 Lisensi & Hak Cipta
Hak Cipta © 2026 **NODERA Ecosystem (DN Solution)**. Seluruh hak cipta dilindungi undang-undang.
