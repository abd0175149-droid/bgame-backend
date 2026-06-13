#!/bin/bash
# ═══════════════════════════════════════════════════════════
#  BGame — First-Time Server Setup Script
#  Run ONCE on your server:  bash deploy.sh
# ═══════════════════════════════════════════════════════════
set -e

REPO_URL="https://github.com/abd0175149-droid/bgame-backend.git"
DEPLOY_DIR="/opt/bgame"
APP_URL="https://bgame.grade.sbs"

GREEN='\033[0;32m'; YELLOW='\033[1;33m'; RED='\033[0;31m'; NC='\033[0m'
log()  { echo -e "${GREEN}[✓]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
err()  { echo -e "${RED}[✗]${NC} $1"; exit 1; }

command -v docker >/dev/null 2>&1 || err "Docker is not installed!"
command -v git    >/dev/null 2>&1 || err "Git is not installed!"

# Clone or Pull
if [ -d "$DEPLOY_DIR/.git" ]; then
    log "Project exists — pulling latest..."
    cd "$DEPLOY_DIR" && git pull origin main
else
    log "Cloning project..."
    git clone "$REPO_URL" "$DEPLOY_DIR"
    cd "$DEPLOY_DIR"
fi

# Setup .env
if [ ! -f .env ]; then
    warn "Creating .env..."
    cp .env.example .env
    DB_PASS=$(openssl rand -hex 16)
    ROOT_PASS=$(openssl rand -hex 20)
    APP_KEY=$(openssl rand -base64 32)
    sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" .env
    sed -i "s|APP_KEY=.*|APP_KEY=base64:$APP_KEY|" .env
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS|" .env
    sed -i "s|DB_ROOT_PASSWORD=.*|DB_ROOT_PASSWORD=$ROOT_PASS|" .env
    log ".env configured with random passwords"
fi

# Build & Start
log "Building Docker containers..."
docker compose build --no-cache

log "Starting containers..."
docker compose up -d --remove-orphans

# Wait for DB
log "Waiting for database..."
for i in {1..20}; do
    docker compose exec -T db mysqladmin ping -h localhost --silent 2>/dev/null && break
    echo -n "." && sleep 3
done
echo ""

# Migrations
log "Running migrations..."
docker compose exec -T app php artisan migrate --force

log "Seeding data..."
docker compose exec -T app php artisan db:seed --force 2>/dev/null || warn "No seeders"

log "Optimizing Laravel..."
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

docker image prune -f

echo ""
echo "═══════════════════════════════════════════"
echo -e "${GREEN}  🎮 BGame is LIVE!${NC}"
echo "═══════════════════════════════════════════"
echo -e "  🌐 https://bgame.grade.sbs"
echo -e "  🗄️  DB Admin: http://localhost:4071"
echo "═══════════════════════════════════════════"
