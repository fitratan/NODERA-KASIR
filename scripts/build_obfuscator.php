<?php
/**
 * ============================================================================
 * NODERA PROPRIETARY BUILDER & SOURCE CODE ENCRYPTOR / OBFUSCATOR v2.5
 * Multi-layer XOR + Dynamic Salt + Deflate + SHA-256 Integrity Verification
 * ============================================================================
 * (c) 2026 NODERA DIGITAL NETWORK. All rights reserved.
 */

// Terminal ANSI Colors
define('CLR_RESET', "\033[0m");
define('CLR_GREEN', "\033[32m");
define('CLR_YELLOW', "\033[33m");
define('CLR_BLUE', "\033[34m");
define('CLR_CYAN', "\033[36m");
define('CLR_RED', "\033[31m");
define('CLR_BOLD', "\033[1m");

$baseDir = dirname(__DIR__);
$srcBackupDir = $baseDir . '/kasir-src';

echo CLR_CYAN . CLR_BOLD . "========================================================\n";
echo "  NODERA POS CODE PROTECTION & ENCRYPTOR BUILDER v2.5  \n";
echo "========================================================\n" . CLR_RESET . "\n";

/**
 * Encrypt a PHP file using Deflate + Dynamic 16-byte XOR + SHA256 integrity check.
 */
function obfuscatePhpFile(string $sourceCode, string $description = ''): string {
    // 1. Compress payload with raw deflate (level 9 maximum compression)
    $compressed = gzdeflate($sourceCode, 9);
    if ($compressed === false) {
        throw new RuntimeException("Gzdeflate compression failed on source payload.");
    }

    // 2. Generate cryptographically secure random 16-byte XOR key
    $key = random_bytes(16);
    $keyLen = strlen($key);

    // 3. Encrypt deflated binary with multi-layer XOR cipher
    $cipher = '';
    $compLen = strlen($compressed);
    for ($i = 0; $i < $compLen; $i++) {
        $cipher .= chr(ord($compressed[$i]) ^ ord($key[$i % $keyLen]));
    }

    // 4. SHA-256 integrity checksum
    $checksum = hash('sha256', $cipher);

    $b64Cipher = base64_encode($cipher);
    $b64Key = base64_encode($key);

    // 5. Generate polymorphic obfuscated variable names
    $vCipher = '$_ndr_' . substr(md5(random_bytes(8)), 0, 8);
    $vKey    = '$_ndr_' . substr(md5(random_bytes(8)), 0, 8);
    $vKLen   = '$_ndr_' . substr(md5(random_bytes(8)), 0, 8);
    $vDec    = '$_ndr_' . substr(md5(random_bytes(8)), 0, 8);
    $vIdx    = '$_ndr_' . substr(md5(random_bytes(8)), 0, 8);
    $vHash   = '$_ndr_' . substr(md5(random_bytes(8)), 0, 8);

    $componentName = !empty($description) ? strtoupper($description) : "PROPRIETARY COMPONENT";

    $banner = "<?php\n"
        . "/**\n"
        . " * ============================================================================\n"
        . " * NODERA DIGITAL NETWORK — {$componentName}\n"
        . " * SECURE RUNTIME BINARY & CLOUD-LOCKED ENGINE\n"
        . " * ----------------------------------------------------------------------------\n"
        . " * (c) 2026 NODERA. All rights reserved.\n"
        . " * This file contains encrypted intellectual property. Any unauthorized\n"
        . " * decompilation, reverse engineering, or modification is strictly prohibited.\n"
        . " * ============================================================================\n"
        . " */\n";

    $stub = $banner
        . "{$vCipher}=base64_decode('{$b64Cipher}');\n"
        . "{$vKey}=base64_decode('{$b64Key}');\n"
        . "{$vHash}='{$checksum}';\n"
        . "if(hash('sha256',{$vCipher})!=={$vHash}){die('NODERA Security: Integrity verification failed (0x8F).');}\n"
        . "{$vKLen}=strlen({$vKey});\n"
        . "{$vDec}='';\n"
        . "for({$vIdx}=0;{$vIdx}<strlen({$vCipher});{$vIdx}++){\n"
        . "    {$vDec}.=chr(ord({$vCipher}[{$vIdx}])^ord({$vKey}[{$vIdx}%{$vKLen}]));\n"
        . "}\n"
        . "return eval('?>'.gzinflate({$vDec}));\n";

    return $stub;
}

// Target proprietary files
$targets = [
    // 1. Security & DRM Engine
    'app/helpers/license.php'            => 'Nodera POS Offline DRM & HWID Security Engine',

    // 2. Database & Session Architecture
    'app/config/database.php'           => 'Nodera POS Database & Migration Engine',
    'app/config/session.php'            => 'Nodera POS Session Security Handler',

    // 3. Database Migration Modules
    'app/database/migrate_v2.php'       => 'Nodera POS Database Migration v2',
    'app/database/migrate_v3.php'       => 'Nodera POS Database Migration v3',
    'app/database/migrate_v4.php'       => 'Nodera POS Database Migration v4',
    'app/database/migrate_v5.php'       => 'Nodera POS Database Migration v5',
    'app/database/migrate_v6.php'       => 'Nodera POS Database Migration v6',

    // 4. Core Backend API
    'public/api.php'                    => 'Nodera POS Core API Backend Engine',

    // 5. PDF & Reporting Engines
    'public/admin/print_report.php'     => 'Nodera POS Report Export Engine',
    'public/admin/print_analytics.php'  => 'Nodera POS Analytics Export Engine',

    // 6. User Interface Controllers
    'public/index.php'                  => 'Nodera POS Cashier Frontend Interface',
    'public/admin/index.php'            => 'Nodera POS Admin Management Dashboard',
];

$action = $argv[1] ?? '--build';

if ($action === '--status') {
    echo CLR_BOLD . "Current Protection Status:\n" . CLR_RESET;
    foreach ($targets as $relPath => $desc) {
        $fullPath = $baseDir . '/' . $relPath;
        if (!file_exists($fullPath)) {
            echo "  [ " . CLR_RED . "MISSING" . CLR_RESET . " ] {$relPath}\n";
            continue;
        }
        $content = file_get_contents($fullPath);
        if (strpos($content, 'SECURE RUNTIME BINARY') !== false && strpos($content, 'gzinflate') !== false) {
            echo "  [ " . CLR_GREEN . "ENCRYPTED" . CLR_RESET . " ] {$relPath} - " . CLR_CYAN . $desc . CLR_RESET . "\n";
        } else {
            echo "  [ " . CLR_YELLOW . "RAW SOURCE" . CLR_RESET . " ] {$relPath} - " . CLR_CYAN . $desc . CLR_RESET . "\n";
        }
    }
    exit(0);
}

if ($action === '--restore') {
    echo CLR_YELLOW . CLR_BOLD . "[!] RESTORING RAW SOURCE FILES FROM: {$srcBackupDir}\n\n" . CLR_RESET;
    if (!is_dir($srcBackupDir)) {
        echo CLR_RED . "[-] Backup directory does not exist! Cannot restore.\n" . CLR_RESET;
        exit(1);
    }
    $restoredCount = 0;
    foreach ($targets as $relPath => $desc) {
        $backupPath = $srcBackupDir . '/' . $relPath;
        $fullPath = $baseDir . '/' . $relPath;
        if (file_exists($backupPath)) {
            copy($backupPath, $fullPath);
            echo "  " . CLR_GREEN . "[✓] Restored:" . CLR_RESET . " {$relPath}\n";
            $restoredCount++;
        } else {
            echo "  " . CLR_RED . "[-] No backup for:" . CLR_RESET . " {$relPath}\n";
        }
    }
    echo "\n" . CLR_GREEN . CLR_BOLD . "Restore completed: {$restoredCount} files restored to pristine state.\n" . CLR_RESET;
    exit(0);
}

// Default: --build
echo CLR_BOLD . "Starting proprietary compilation & encryption...\n\n" . CLR_RESET;
$successCount = 0;

foreach ($targets as $relPath => $desc) {
    $fullPath = $baseDir . '/' . $relPath;
    $backupPath = $srcBackupDir . '/' . $relPath;

    // Determine pristine source code
    if (file_exists($backupPath)) {
        // Source backup already exists; use pristine source
        $sourceCode = file_get_contents($backupPath);
    } elseif (file_exists($fullPath)) {
        // First run: copy current raw file to backup
        $sourceCode = file_get_contents($fullPath);
        $backupSubdir = dirname($backupPath);
        if (!is_dir($backupSubdir)) {
            mkdir($backupSubdir, 0755, true);
        }
        file_put_contents($backupPath, $sourceCode);
    } else {
        echo CLR_RED . "[-] Skipping missing file: {$relPath}\n" . CLR_RESET;
        continue;
    }

    // Ensure pristine backup is saved
    $backupSubdir = dirname($backupPath);
    if (!is_dir($backupSubdir)) {
        mkdir($backupSubdir, 0755, true);
    }
    file_put_contents($backupPath, $sourceCode);

    // Generate protected binary
    echo CLR_BLUE . "[*] Encrypting:" . CLR_RESET . " {$relPath} (" . CLR_CYAN . $desc . CLR_RESET . ")...\n";
    $obfuscated = obfuscatePhpFile($sourceCode, $desc);
    file_put_contents($fullPath, $obfuscated);

    // Verify syntax with php -l
    $lintOut = [];
    $lintCode = 0;
    exec("php -l " . escapeshellarg($fullPath) . " 2>&1", $lintOut, $lintCode);
    if ($lintCode === 0) {
        echo "    " . CLR_GREEN . "[+] Protected & Syntax Verified (PHP Lint OK)\n" . CLR_RESET;
        $successCount++;
    } else {
        echo "    " . CLR_RED . "[!] Lint Error on {$relPath}: " . implode("\n", $lintOut) . "\n" . CLR_RESET;
    }
}

echo "\n" . CLR_GREEN . CLR_BOLD . "========================================================\n";
echo "  COMPLETED: {$successCount} proprietary files compiled & protected!\n";
echo "  Pristine source preserved in: {$srcBackupDir}\n";
echo "========================================================\n" . CLR_RESET;
