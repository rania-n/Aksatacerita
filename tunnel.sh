#!/bin/bash
# Jalanin Vite dev server + ngrok bareng, biar dapet link publik buat testing di HP.
# Link-nya acak tiap dijalankan -- kalau mau link tetap, klaim domain statis gratis
# di https://dashboard.ngrok.com/domains lalu jalankan:
#   ngrok http --domain=nama-domainmu.ngrok-free.app 5173

cd "$(dirname "$0")"
PORT=5173

# Install dependensi kalau belum ada
if [ ! -d node_modules/vite ]; then
    echo "Install dependensi..."
    if command -v pnpm >/dev/null && [ -f pnpm-lock.yaml ]; then pnpm install; else npm install; fi
fi

# Nyalain Vite (pakai config lokal) kalau port belum dipakai
if ! curl -s -o /dev/null "http://127.0.0.1:$PORT"; then
    echo "Menyalakan Vite..."
    nohup npx vite --config vite.local.config.ts > /tmp/vite.log 2>&1 &
    for i in {1..20}; do
        curl -s -o /dev/null "http://127.0.0.1:$PORT" && break
        sleep 1
    done
    if ! curl -s -o /dev/null "http://127.0.0.1:$PORT"; then
        echo "Vite gagal jalan. Log (/tmp/vite.log):"
        tail -n 20 /tmp/vite.log
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
