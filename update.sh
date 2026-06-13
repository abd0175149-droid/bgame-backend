#!/bin/bash
# ═══════════════════════════════════════════════════════════
#  BGame — Update & Deploy Script
#  الاستخدام: ./update.sh
# ═══════════════════════════════════════════════════════════

set -e

GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
log()  { echo -e "${GREEN}[✓]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  🎮 BGame — Update & Deploy"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# ── 1. سحب آخر تعديلات من GitHub ─────────────────────────
log "Pulling latest code from GitHub..."
git pull origin main

# ── 2. بناء وتشغيل الـ Docker ─────────────────────────────
log "Building & starting containers..."
docker compose up -d --build

# ── 3. انتظار قاعدة البيانات ──────────────────────────────
log "Waiting for database to be ready..."
sleep 8

# ── 4. تشغيل الـ Migrations الجديدة ──────────────────────
log "Running migrations..."
docker compose exec -T app php artisan migrate --force

# ── 5. تحديث الـ Cache ────────────────────────────────────
log "Clearing & caching config..."
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

# ── 6. تنظيف الـ Images القديمة ──────────────────────────
log "Cleaning up old Docker images..."
docker image prune -f

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "  ${GREEN}✅ Update complete!${NC}"
echo "  🌐 https://bgame.grade.sbs"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
