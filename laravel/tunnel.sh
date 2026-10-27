#!/bin/bash
# Jalanin Vite dev server + ngrok bareng, biar dapet link publik buat testing di HP.
# Link-nya acak tiap dijalankan -- kalau mau link tetap, klaim domain statis gratis
# di https://dashboard.ngrok.com/domains lalu jalankan:
#   ngrok http --domain=nama-domainmu.ngrok-free.app 5173

cd "$(dirname "$0")"
PORT=8000

# Pastikan Laravel jalan di port tersebut
if ! curl -s -o /dev/null "http://127.0.0.1:$PORT"; then
    echo "Menyalakan Laravel server (php artisan serve)..."
    nohup php artisan serve --port=$PORT > /tmp/laravel-serve.log 2>&1 &
    for i in {1..20}; do
        curl -s -o /dev/null "http://127.0.0.1:$PORT" && break
        sleep 1
    done
    if ! curl -s -o /dev/null "http://127.0.0.1:$PORT"; then
        echo "Laravel gagal jalan. Log (/tmp/laravel-serve.log):"
        tail -n 20 /tmp/laravel-serve.log
        exit 1
    fi
fi

# Pastikan ngrok mengarah ke port yang benar; restart kalau salah/belum jalan
if ! curl -s http://127.0.0.1:4040/api/tunnels | grep -q "localhost:$PORT"; then
    pkill -f "ngrok http" 2>/dev/null
    sleep 1
    echo "Menyalakan ngrok..."
    nohup ngrok http $PORT --log=stdout > /tmp/ngrok.log 2>&1 &
    for i in {1..10}; do
        curl -s http://127.0.0.1:4040/api/tunnels | grep -q public_url && break
        sleep 1
    done
fi

echo ""
echo "Lokal : http://localhost:$PORT"
echo "Publik: $(curl -s http://127.0.0.1:4040/api/tunnels | grep -o '"public_url":"[^"]*"' | head -1 | cut -d'"' -f4)"
