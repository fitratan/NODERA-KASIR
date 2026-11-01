#!/usr/bin/env bash
# start-server.sh - Launcher Kasir Standalone by NODERA

PORT=8088
HOST="0.0.0.0"
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "=========================================================="
echo "          KASIR STANDALONE by NODERA (Landscape POS)      "
echo "=========================================================="
echo "Menjalankan Micro-Server PHP pada port $PORT..."
echo ""
echo "Akses dari Komputer Ini : http://localhost:$PORT"

# Ambil IP LAN Lokal
LOCAL_IP=$(hostname -I 2>/dev/null | awk '{print $1}')
if [ -n "$LOCAL_IP" ]; then
    echo "Akses dari HP / Tablet  : http://$LOCAL_IP:$PORT"
fi
echo ""
echo "Tekan Ctrl+C untuk menghentikan server."
echo "=========================================================="

# Buka browser otomatis jika desktop environment aktif
if command -v xdg-open &>/dev/null; then
    (sleep 1 && xdg-open "http://localhost:$PORT") &
fi

# Multi-threaded CLI server workers (mencegah freeze saat multi-client LAN)
export PHP_CLI_SERVER_WORKERS=4

exec php -S "$HOST:$PORT" -t "$DIR/public"
