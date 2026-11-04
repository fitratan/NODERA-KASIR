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
  <a href="README.md"><b>🇮🇩 Bahasa Indonesia</b></a> •
  <a href="README.en.md"><b>🇬🇧 English</b></a> •
  <a href="README.fr.md"><b>🇫🇷 Français</b></a>
</p>

---

## 📌 About NODERA KASIR POS

**NODERA KASIR POS** is a next-generation multi-business **Point of Sale (POS)** and retail intelligence system professionally engineered by the **NODERA Team**. Designed with a **100% Offline-First** philosophy, the system operates completely standalone without any dependency on an active internet connection or external server, while offering optional cloud-synchronized licensing and multi-device LAN connectivity.

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

## 🚀 Complete Feature & Functional Matrix

### 1. System Architecture & Performance
| Feature | Technical Specification |
| :--- | :--- |
| **Runtime Engine** | Embedded PHP 8.2 (JIT enabled, compiled NDK POSIX binary for ARM64, ARMv7, x86_64) |
| **Database Engine** | SQLite 3 Zero-Maintenance (Single-file database with Write-Ahead Logging `WAL` mode for high concurrency) |
| **Deployment Modes** | **Mode 1**: Standalone Android internal micro-server on smartphone/tablet.<br>**Mode 2**: Desktop PC LAN Server for multi-cashier cashier network. |
| **Cold Launch Optimization** | Zero-flicker startup configured with native Android dark background `#08101E`. |

### 2. User Interface & Experience (UI/UX)
| Feature | Technical Specification |
| :--- | :--- |
| **Smart Orientation** | Auto-adapts to smartphone layout (Portrait for handheld cashiering) and Tablet POS (Landscape 2-column interactive layout). |
| **System Status Bar** | Keeps the system status bar visible at all times so staff can monitor device battery levels and Bluetooth printer connectivity. |
| **Visual Polish** | Floating branded logo, Sky Blue progress indicator, and smooth 350ms interpolator fade transitions. |
| **Full Multilingual Localization** | **5 Full Languages with 100% Parity (2,373 keys)**: **Bahasa Indonesia (ID)**, **English (EN)**, **French (FR)**, **Spanish (ES)**, and **Tagalog / Filipino (TL)**. |

### 3. Cashier, Service & Vehicle Wash Management
| Feature | Technical Specification |
| :--- | :--- |
| **Rapid Sales Register** | Real-time search for items/services, large category filter buttons, barcode scanner camera, and custom item entry. |
| **License Plate Tracking** | Specialized for car/bike wash & repair shops: record customer vehicle plate numbers directly in the checkout cart. |
| **Vehicle Queue Tickets** | Generates formatted queue numbers (e.g. `A-01`, `B-12`) for windshield placement or dashboard display. |
| **Staff Commission Split** | Automatically calculates and allocates flat-rate or percentage commissions per staff/technician for every transaction. |
| **Tiered Wholesale Pricing** | Multi-tier wholesale pricing templates based on purchase volume (e.g., buying >= 3 discounts unit price). |
| **Open Bill / Tab Hold** | Save temporary pending orders and resume checkout whenever the customer is ready to pay. |

### 4. Checkout Payments & Thermal Receipt Printing
| Feature | Technical Specification |
| :--- | :--- |
| **Cash Checkout** | Quick tender buttons (`Exact Cash`, `Rp 20.000`, `Rp 50.000`, `Rp 100.000`, etc.) and instant change calculation. |
| **Non-Cash Checkout** | Dynamic QRIS integration (Nodera Pay Gateway), Bank Transfer, Debit Card, and Customer Credit/Debt Ledger. |
| **Thermal Printers (58mm & 80mm)** | Precise ESC/POS formatting for 58mm (32 characters per line) and 80mm (48 characters per line) receipt rolls. |
| **Direct Android RawBT Print** | Native Android Bluetooth print intent via `android.intent.action.SEND` for zero-dialog printing. |
| **Custom Header & Footer** | Configure store name, address, WhatsApp number, queue ticket number, and footer notes effortlessly. |

### 5. Security, Anti-Tamper & License Guard
| Feature | Technical Specification |
| :--- | :--- |
| **Obfuscated Production Code** | Proprietary compiler strips comments, minifies logic, and packages encrypted payloads into the APK / desktop binary. |
| **BCrypt Password Hashing** | All cashier PINs and administrator back-office credentials are protected with industry-standard `bcrypt` hashing. |
| **Clock Rollback Guard** | Verifies time monotonicity to prevent tampering with system clocks to artificially extend trial durations. |
| **Hardware ID (HWID) Locking** | Cryptographic binding to unique device hardware identities (`NDR-POS-...`) verified via Cloud Panel. |

---

## 💻 Installation & Execution Guide

| Platform | Installation Method | Executable / Package | Getting Started |
| :--- | :--- | :--- | :--- |
| **Android Tablet / Smartphone** | Official APK Sideload | `dist/Nodera-Kasir-POS.apk` | 1. Download and install APK on your Android device.<br>2. Open app; internal micro-server starts automatically.<br>3. Start processing offline transactions instantly. |
| **Windows Desktop (Cashier PC)** | Native Windows Launcher | `dist/NoderaKasir.exe` | 1. Extract package to your local directory.<br>2. Run `NoderaKasir.exe` (boots server & system tray).<br>3. Access POS through internal WebView2 or web browser. |
| **Local Network Clients (WiFi/LAN)** | Standard Web Browser | Modern Browser (Chrome/Edge/Safari) | 1. Connect smartphone/tablet to the same WiFi network as the host PC.<br>2. Open browser URL: `http://<HOST-PC-IP>:8088`. |

---

## 🔑 Default Login Credentials

| User Interface | Access URL | Username / Account | Default Password / PIN | Access Level |
| :--- | :--- | :--- | :--- | :--- |
| **Admin Back-Office** | `http://localhost:8088/admin` | `admin` (or `admin@nodera.id`) | Password: `admin`<br>PIN: `888800` | Full administrative control: analytics, products, staff, store settings, & licensing |
| **Cashier Register** | `http://localhost:8088/` | Main Cashier | PIN: `123456` | Sales transactions, queue tickets, thermal printing, & shift closing |
| **Supervisor Override** | `http://localhost:8088/` | Owner / Supervisor | PIN: `888800` | Void transactions, clear open bills, apply special discounts, & refund management |

> ⚠️ **Security Advisory**: For the security of your business records, please immediately change default administrative passwords and cashier PINs in the **User Settings** menu upon first installation.

---

## 🌐 Network Ports & Service Endpoints

| Service Component | Port / Path URL | Protocol | Description |
| :--- | :--- | :--- | :--- |
| **Local Web Server** | `8088` | HTTP | Default port for embedded PHP 8.2 micro-server |
| **POS Cashier Interface** | `http://127.0.0.1:8088/` | HTTP GET/POST | Primary cashier register and sales screen |
| **Admin Back-Office** | `http://127.0.0.1:8088/admin` | HTTP GET/POST | Management dashboard for catalog, stock, and reports |
| **REST API Engine** | `http://127.0.0.1:8088/api.php` | JSON REST API | Local endpoints for transactions, printing, & synchronization |
| **Cloud Licensing API** | `https://panel.dgtlnetsolution.com` | HTTPS | Commercial license verification and HWID activation |

---

## 📱 Minimum System Specifications

| Component | Minimum Android Requirements | Minimum Windows PC Requirements |
| :--- | :--- | :--- |
| **Operating System** | Android 7.0 (Nougat) up to Android 14+ | Windows 10 / Windows 11 (64-Bit) |
| **CPU Architecture** | ARM64 (AArch64), ARMv7 (32-Bit), or x86_64 | Dual Core 1.8 GHz+ (Intel Core i3 / AMD equivalent) |
| **RAM Memory** | Minimum 2 GB (3 GB+ recommended for heavy inventory) | Minimum 2 GB RAM (4 GB+ recommended) |
| **Free Storage Space** | Minimum 150 MB free disk space | Minimum 250 MB free disk space |
| **Printer Support** | Bluetooth Thermal 58mm / 80mm via RawBT or WebBT | USB Thermal / Bluetooth / Virtual POS Printer |

---

## 🛠️ Build & Obfuscation Pipeline (For Developers)

| Step | CLI Command | Technical Description |
| :--- | :--- | :--- |
| **1. Obfuscate Source Code** | `php scripts/build_obfuscator.php` | Strips comments, minifies PHP logic, encrypts with XOR + Deflate, and moves output to `public/` and `app/` |
| **2. Pack Android Runtime** | `cd app && zip -qr ../android/app/src/main/assets/kasir.zip . && cd ..` | Archives obfuscated web runtime into Android internal assets bundle |
| **3. Compile Release APK** | `export JAVA_HOME=/path/to/jdk17`<br>`export PATH=$JAVA_HOME/bin:$PATH`<br>`cd android && ./gradlew assembleRelease` | Compiles signed release production APK using Gradle & JDK 17 |
| **4. Deploy Output Binaries** | `cp android/app/build/outputs/apk/release/app-release.apk dist/Nodera-Kasir-POS.apk` | Copies final signed binary to public distribution folder |

---

## 📁 Repository Directory Structure

| Directory / File | Type | Description |
| :--- | :--- | :--- |
| `android/` | Directory | Native Android Studio / Gradle project (Kotlin, Embedded PHP Server Manager, Bridge) |
| `kasir-src/` | Directory | Master unencrypted source code (Primary development workspace) |
| `app/` | Directory | Distributed obfuscated application runtime (PHP core logic & language files) |
| `assets/` | Directory | Official graphics assets, vector logos, and project branding |
| `bin/` | Directory | Windows portable PHP 8.2 micro-server runtime binaries |
| `config/` | Directory | System configuration, hardware licensing, and HWID protection |
| `dist/` | Directory | Official signed installer binaries (`Nodera-Kasir-POS.apk`, `NoderaKasir.exe`) |
| `launcher-go/` | Directory | Native Go Windows launcher source code with WebView2 integration |
| `scripts/` | Directory | Build automation, obfuscator pipeline, and packaging toolchain |
| `README.md` | Document | Primary documentation file (Indonesian / English / French navigation) |
| `README.en.md` | Document | Full English documentation |
| `README.fr.md` | Document | Full French documentation |

---

## 📞 License Purchases & Support Inquiries

Official commercial licenses for **NODERA KASIR POS** can be activated and managed centrally:

| Channel | Contact Information & Links |
| :--- | :--- |
| **Official License Portal** | [panel.dgtlnetsolution.com](https://panel.dgtlnetsolution.com) |
| **Telegram Technical Support** | [@nullneko_exe](https://t.me/nullneko_exe) |
| **WhatsApp Customer Care** | [+62 851-5517-3547](https://wa.me/6285155173547) |
| **Official Developer** | **NODERA Ecosystem (Digital Network Solution)** |

---

<p align="center">
  <b>NODERA KASIR POS</b><br>
  Smart Point of Sale & Retail Intelligence System<br>
  <i>Copyright &copy; 2026 NODERA Ecosystem. All Rights Reserved.</i>
</p>
