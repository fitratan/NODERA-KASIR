# KASIR STANDALONE DESKTOP & MOBILE by NODERA

<p align="center">
  <img src="https://img.shields.io/badge/Platform-Windows%20%7C%20Linux%20%7C%20Android%20Tablet-0052CC.svg?style=for-the-badge&logo=windows" alt="Platform">
  <img src="https://img.shields.io/badge/Orientation-Landscape%20POS-00B074.svg?style=for-the-badge" alt="Orientation">
  <img src="https://img.shields.io/badge/Database-SQLite%203%20Zero--Setup-777BB4.svg?style=for-the-badge&logo=sqlite" alt="Database">
  <img src="https://img.shields.io/badge/Language-ID%20%7C%20EN%20%7C%20FR-E3B341.svg?style=for-the-badge" alt="Language">
</p>

---

## 📌 Tentang Aplikasi

**KASIR STANDALONE by NODERA** adalah aplikasi Point of Sale (POS) modern berorientasi **Landscape-First** yang dirancang khusus untuk berbagai sektor UMKM di Indonesia dan internasional — mulai dari **Jasa Cuci Kendaraan (Motor / Mobil / Helm)**, **Barbershop & Salon**, **Laundry**, hingga **Kedai Kopi & Warung Retail**.

Aplikasi ini dapat dijalankan secara instan di PC/Laptop kasir menggunakan **Micro-Server Portabel internal** (tanpa perlu install XAMPP atau MySQL eksternal). Perangkat lain di toko (seperti HP atau tablet Android kasir/pelayan) dapat langsung terhubung melalui jaringan WiFi lokal.

---

## ✨ Fitur Unggulan

### 1. Desain Khusus Landscape-First (Tablet & Layar Monitor)
- Antarmuka 2-kolom terpisah (Katalog Produk 70% | Keranjang Kasir 30%).
- Pada HP atau tablet Android, aplikasi secara otomatis mengunci orientasi ke mendatar (*sensorLandscape*).
- Dilengkapi pengaman layar putar (*Smart Rotation Guard*) saat diakses melalui browser ponsel.

### 2. Identifikasi Pemroses Pembayaran & Manajemen Karyawan
- **Pemroses Pembayaran (Kasir)**: Setiap transaksi mencatat nama kasir yang sedang bertugas dan memproses pembayaran, lengkap di laporan dan tercetak di struk kasir.
- **Peran Karyawan Multi-Level**:
  - `Admin / Pemilik`: Akses penuh ke laporan omset, pengaturan toko, dan komisi.
  - `Kasir`: Pemroses pembayaran, input order, dan cetak struk.
  - `Petugas Lapangan / Pencuci / Teknisi`: Diberikan penugasan pengerjaan unit dengan perhitungan komisi/bagi hasil otomatis.
- **Sistem Ganti Kasir Cepat**: Ganti kasir shift bertugas dalam 1 klik tanpa logout.

### 3. Modul Khusus UMKM Cuci Kendaraan (Motor & Mobil)
- **Input Nomor Plat Kendaraan**: Memudahkan antrean kendaraan dan mencegah tertukarnya unit pelanggan.
- **Cetak Tiket Antrean (Queue Ticket)**: Cetak tiket antrean (contoh: `A-01`) saat motor masuk untuk diselipkan di stang kendaraan.
- **Penghitungan Komisi Tukang Cuci Otomatis**: Menghitung hak upah per motor yang dicuci oleh masing-masing karyawan secara harian.
- **Dukungan Penjualan Silang (Cross-Selling)**: Menggabungkan jasa cuci motor dengan pesanan kopi dingin, minuman, atau semir ban dalam 1 struk yang sama.

### 4. Multi-Metode Pembayaran
- **Tunai (Cash)** dengan tombol cepat pecahan uang (`Uang Pas`, `Rp 20.000`, `Rp 50.000`, `Rp 100.000`) dan kalkulasi uang kembalian otomatis.
- **Dynamic QRIS** instan siap scan via GoPay, OVO, Dana, ShopeePay, dan Mobile Banking.
- **Transfer Bank & Debit**.

### 5. Multi-Bahasa (i18n)
- Mendukung 3 bahasa bawaan yang dapat diganti seketika:
  - 🇮🇩 **Bahasa Indonesia**
  - 🇬🇧 **English**
  - 🇫🇷 **Français (Afrique / Côte d'Ivoire)**

### 6. Cetak Struk Thermal Standar ESC/POS
- Pratinjau struk thermal 58mm & 80mm.
- Format struk rapi mencantumkan nama toko, kasir pemroses, petugas pengerjaan, nomor plat motor, rincian biaya, dan ucapan terima kasih.

---

## 🚀 Cara Menjalankan

### A. Di Komputer Windows (Rekomendasi Utama):
Cukup klik ganda file executable native:
```text
NoderaKasir.exe
```
*(atau `Kasir.exe`)*
- **Zero Console Window**: Berjalan otomatis di latar belakang tanpa jendela CMD/terminal hitam.
- Otomatis membuka window aplikasi POS mandiri (*app-mode*).
- Dilengkapi System Tray Icon di sudut kanan bawah taskbar untuk akses cepat ke Panel Admin, POS, dan info IP LAN.

### B. Di Smartphone / Tablet Android:
Telah tersedia file instalasi APK siap pakai di direktori `dist/`:
```text
dist/Nodera-Kasir-POS.apk
```
- Signed release APK (APK Signature Scheme v2/v3).
- Orientasi fleksibel (Sensor Dinamis Portrait & Landscape).
- Dukungan printer thermal Bluetooth (RawBT) dan scanner barcode kamera.

### C. Mengakses dari Perangkat Lain via WiFi Lokal (LAN):
1. Pastikan perangkat (HP/Tablet/Laptop kasir lain) terhubung ke WiFi/jaringan yang sama dengan server.
2. Buka browser dan ketik alamat IP komputer server kasir:
   ```text
   http://192.168.1.xxx:8088
   ```

---

## 🔑 Kredensial Login Default (Pertama Kali)

Saat pertama kali aplikasi dijalankan, sistem otomatis menyediakan akun bawaan berikut:

### 1. Back-Office / Panel Admin (`http://localhost:8088/admin`)
- **Username**: `admin` *(atau `admin@nodera.id`)*
- **Password**: `admin` *(atau PIN Admin: `888800`)*

### 2. Aplikasi Kasir POS (`http://localhost:8088/`)
- **Kasir Utama**: PIN `123456`
- **Owner / Admin**: PIN `888800`

> 💡 **Saran Keamanan**: Setelah berhasil masuk pertama kali, segera perbarui kata sandi dan PIN kasir melalui menu **Pengaturan Pengguna** di Panel Admin Back-Office.

---

## 📱 Wrapper Android APK

Source code wrapper Android tersedia di direktori `android/`.
- Dibangun dengan SDK Android modern, WebView performa tinggi, dan orientasi dinamis sensor.
- Build release APK otomatis: `./gradlew assembleRelease`
- File output rilis: `dist/Nodera-Kasir-POS.apk`

---

## 🔒 Lisensi & Hak Cipta
Hak Cipta © 2026 NODERA (DN Solution). Seluruh hak cipta dilindungi undang-undang.
