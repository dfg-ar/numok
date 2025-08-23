#!/usr/bin/env sh

# This entrypoint prepares config/config.php from env variables
# and runs pending database initialization (if requested), then starts Apache.

set -eu

APP_ROOT="/var/www/html"
CONFIG_DIR="$APP_ROOT/config"
CONFIG_FILE="$CONFIG_DIR/config.php"
EXAMPLE_FILE="$CONFIG_DIR/config.example.php"
UPLOADS_DIR="$APP_ROOT/public/assets/uploads"
TRACKING_DIR="$APP_ROOT/public/tracking"

# Create config/config.php from example if missing
if [ ! -f "$CONFIG_FILE" ]; then
  echo "[entrypoint] Generating config.php from example..."
  if [ -f "$EXAMPLE_FILE" ]; then
    cp "$EXAMPLE_FILE" "$CONFIG_FILE"
  else
    echo "[entrypoint] ERROR: config.example.php not found" >&2
    exit 1
  fi
fi

# Ensure writable directories exist and have proper ownership
mkdir -p "$UPLOADS_DIR" "$TRACKING_DIR"
chown -R www-data:www-data "$UPLOADS_DIR" "$TRACKING_DIR" || true

# Ensure APP_URL and DB envs are present (defaults handled by example file)
: "${APP_URL:=http://localhost}"
: "${DB_HOST:=db}"
: "${DB_NAME:=numok_app}"
: "${DB_USER:=numok_user}"
: "${DB_PASS:=change_me_app_2025}"

# Allow optional one-time DB bootstrap via RUN_MIGRATIONS=true
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  echo "[entrypoint] Running initial database migration via mysql client..."
  if command -v mysql >/dev/null 2>&1; then
    # Simple wait for MySQL to be ready - just check if port is open
    echo "[entrypoint] Waiting for MySQL at $DB_HOST:3306..."
    for i in $(seq 1 60); do
      # Use nc or telnet to check if port is open
      if nc -z "$DB_HOST" 3306 2>/dev/null; then
        echo "[entrypoint] MySQL port is open, waiting for initialization..."
        # Give MySQL time to complete its initialization
        sleep 10
        break
      fi
      echo "[entrypoint] MySQL not ready yet ($i)..."
      sleep 2
    done
    
    # Now try to connect with the app user credentials
    echo "[entrypoint] Attempting database operations..."
    
    # Create database if not exists (this may fail if user doesn't have permission, that's OK)
    mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --ssl-mode=DISABLED -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true
    
    # Apply schema only if core table doesn't exist yet
    TABLES_COUNT=$(mysql -N -s -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --ssl-mode=DISABLED -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}' AND table_name='users';" 2>/dev/null) || TABLES_COUNT=0
    
    if [ "${TABLES_COUNT}" = "0" ]; then
      echo "[entrypoint] Applying schema from database/deploy.sql..."
      # Temporarily disable foreign key checks
      mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --ssl-mode=DISABLED "$DB_NAME" -e "SET FOREIGN_KEY_CHECKS=0;" 2>/dev/null || true
      mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --ssl-mode=DISABLED "$DB_NAME" < "$APP_ROOT/database/deploy.sql" 2>/dev/null || true
      # Re-enable foreign key checks
      mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --ssl-mode=DISABLED "$DB_NAME" -e "SET FOREIGN_KEY_CHECKS=1;" 2>/dev/null || true
      echo "[entrypoint] Schema import attempted."
    else
      echo "[entrypoint] Schema already present; skipping deploy.sql"
    fi
  else
    echo "[entrypoint] mysql client is not installed; skipping migrations"
  fi
fi

exec "$@"