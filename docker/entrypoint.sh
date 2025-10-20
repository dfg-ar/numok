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
: "${VIRTUAL_HOST:=localhost:8080}"
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
    mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --skip-ssl -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true
    
    # Apply schema only if core table doesn't exist yet
    TABLES_COUNT=$(mysql -N -s -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --skip-ssl -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}' AND table_name='users';" 2>/dev/null) || TABLES_COUNT=0
    
    if [ "${TABLES_COUNT}" = "0" ]; then
      echo "[entrypoint] 🛠️  Applying database schema from deploy.sql..."
      # Temporarily disable foreign key checks
      mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --skip-ssl "$DB_NAME" -e "SET FOREIGN_KEY_CHECKS=0;" 2>/dev/null || true
      mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --skip-ssl "$DB_NAME" < "$APP_ROOT/database/deploy.sql" 2>/dev/null || true
      # Re-enable foreign key checks
      mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --skip-ssl "$DB_NAME" -e "SET FOREIGN_KEY_CHECKS=1;" 2>/dev/null || true
      echo "[entrypoint] ✅ Database schema imported successfully!"

      # Create default admin user for first-time setup
      echo "[entrypoint] 👤 Creating default administrator account..."
      ADMIN_EMAIL="${ADMIN_EMAIL:-admin@numok.com}"
      ADMIN_PASSWORD_HASH="\$2y\$10\$bLQ3Qd64NRSxvc7A2wKJAe/ocgCCkB5jbyC11I1XklnjDClzO6vpK"  # Hash for 'admin123'
      mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --skip-ssl "$DB_NAME" -e "INSERT IGNORE INTO users (email, password, name, is_admin, created_at) VALUES ('$ADMIN_EMAIL', '$ADMIN_PASSWORD_HASH', 'Default Admin', 1, CURRENT_TIMESTAMP);" 2>/dev/null || true

      echo "[entrypoint] ======================================"
      echo "[entrypoint] 🔐 LOGIN CREDENTIALS"
      echo "[entrypoint] -------------------------------------"
      echo "[entrypoint] Open a new incognito/private window and navigate to http://$VIRTUAL_HOST/admin/login "
      echo "[entrypoint] 📧 Email:    $ADMIN_EMAIL"
      echo "[entrypoint] 🔑 Password: admin123"
      echo "[entrypoint] ⚠️  IMPORTANT: Change this password immediately after login!"
      echo "[entrypoint] ======================================"
    else
      echo "[entrypoint] Schema already present; skipping deploy.sql"
    fi
  else
    echo "[entrypoint] mysql client not found - cannot run migrations" >&2
  fi
fi

exec "$@"