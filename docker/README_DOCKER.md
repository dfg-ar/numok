# Numok Docker Setup

This directory contains the Docker configuration for running the Numok affiliate program platform in containers.

## 🚀 Quick Start

### Prerequisites
- Docker Desktop (v20.10+) or Docker Engine (v20.10+)
- Docker Compose (v2.0+)

### 1. Environment Setup
```bash
# Navigate to the docker directory
cd docker

# Copy the example environment file
cp .env_example .env

# Edit the environment file with your settings
nano .env  # or use your preferred editor
```

### 2. Start the Stack
```bash
# Build and start all services
docker compose up -d --build

# View logs
docker compose logs -f

# Check service status
docker compose ps
```

### 3. Access the Application
- **Main App**: http://localhost:8080
- **Database**: localhost:33060 (MySQL)
- **Admin Panel**: http://localhost:8080/admin/login

### 4. Default Admin Login
On first startup, a default admin user is automatically created:
- **Email**: `admin@numok.com`
- **Password**: `admin123`

⚠️ **SECURITY WARNING**: Change this password immediately after first login!

## 📁 Project Structure

```
docker/
├── docker-compose.yml      # Main orchestration file
├── Dockerfile             # PHP application container
├── entrypoint.sh          # Container initialization script
├── .env_example          # Environment variables template
├── .env                  # Your environment configuration (create this)
└── README_DOCKER.md      # This file
```

## ⚙️ Configuration

### Environment Variables

Copy `.env_example` to `.env` and customize:

```bash
# Application settings
VIRTUAL_PORT=8080                    # Host port mapping
VIRTUAL_HOST=http://localhost:8080   # Public URL
APP_DEBUG=1                          # Debug mode (0 for production)
RUN_MIGRATIONS=true                  # Auto-run migrations on first boot

# Database settings
DB_NAME=numok_app
DB_USER=numok_user
DB_PASS=change_me_app_2025
DB_ROOT_PASSWORD=change_me_root_2025
```

### Database Configuration
- **Host**: `db` (internal container name)
- **Port**: `3306` (internal) / `33060` (external)
- **Database**: Set via `DB_NAME` environment variable
- **User**: Set via `DB_USER` environment variable
- **Password**: Set via `DB_PASS` environment variable

## 🐳 Services

### App Service (`numok_app`)
- **Image**: Custom PHP 8.2 + Apache
- **Port**: `${VIRTUAL_PORT}:80` (configurable host port)
- **Features**: 
  - PHP 8.2 with PDO MySQL, mbstring extensions
  - Apache with mod_rewrite and mod_headers enabled
  - Composer for dependency management
  - Automatic configuration from environment variables
  - Network testing tools: `ping`, `curl`, `netcat` for debugging
  - Multi-stage build for optimized Composer installation

### Database Service (`numok_db`)
- **Image**: MySQL 8.0
- **Port**: `33060:3306` (host:container)
- **Features**:
  - MySQL 8.0 with native password authentication
  - Persistent data storage
  - Automatic database creation
  - Health checks with automatic restart
  - Log rotation (10MB max, 3 files)

## 🔧 Advanced Features

### Automatic Configuration
The container automatically:
- Generates `config/config.php` from `config.example.php`
- Creates necessary directories with proper permissions
- Runs database migrations on first boot (if `RUN_MIGRATIONS=true`)

### Health Monitoring
- Database health checks every 10 seconds
- Automatic restart on failure
- Log rotation to prevent disk space issues

### Network Security
- Isolated `numok_network` bridge network
- Internal service communication only
- Configurable network security options (commented)

## 📊 Data Persistence

The following data is persisted across container restarts:

- **Database**: `numok_db_data` volume
- **Uploads**: `numok_uploads_data` volume  
- **Tracking**: `numok_tracking_data` volume

All volumes are managed by Docker and stored in the Docker volumes directory.

## 🔧 Common Commands

### Development
```bash
# Start services
docker compose up -d

# View logs
docker compose logs -f app
docker compose logs -f db

# Rebuild and restart
docker compose up -d --build

# Stop services
docker compose down

# Stop and remove volumes (⚠️ WARNING: Data loss)
docker compose down -v
```

### Database Operations
```bash
# Access MySQL shell
docker compose exec db mysql -u numok_user -p numok_app

# Backup database
docker compose exec db mysqldump -u numok_user -p numok_app > backup.sql

# Restore database
docker compose exec -T db mysql -u numok_user -p numok_app < backup.sql
```

### Application Operations
```bash
# Access application container
docker compose exec app bash

# Run Composer commands
docker compose exec app composer install
docker compose exec app composer update

# Check PHP configuration
docker compose exec app php -m

# View generated config
docker compose exec app cat config/config.php
```

### Testing and Debugging Tools
The application container includes network testing tools for debugging:

```bash
# Test network connectivity
docker compose exec app ping google.com

# Test HTTP endpoints
docker compose exec app curl -I http://localhost
docker compose exec app curl -v http://localhost

# Test external services
docker compose exec app curl -I https://api.example.com

# Test database connectivity
docker compose exec app nc -z db 3306
```

## 🚨 Troubleshooting

### Port Conflicts
If port 8080 is already in use:
```bash
# Edit .env file
VIRTUAL_PORT=8081
VIRTUAL_HOST=http://localhost:8081

# Restart services
docker compose down
docker compose up -d
```

### Database Connection Issues
```bash
# Check database logs
docker compose logs db

# Verify database health
docker compose exec db mysqladmin ping -u numok_user -p

# Check health check status
docker compose ps

# Reset database (⚠️ WARNING: Data loss)
docker compose down -v
docker compose up -d
```

### Configuration Issues
```bash
# Check if config was generated
docker compose exec app ls -la config/

# Regenerate config by restarting
docker compose restart app

# Check entrypoint logs
docker compose logs app | grep entrypoint
```

### Permission Issues
```bash
# Fix file permissions
docker compose exec app chown -R www-data:www-data /var/www/html/public

# Check volume ownership
docker compose exec app ls -la /var/www/html/public
```

### Memory Issues
If you encounter memory problems:
```bash
# Uncomment resource limits in docker-compose.yml:
services:
  app:
    deploy:
      resources:
        limits:
          memory: 512M
        reservations:
          memory: 256M
  db:
    deploy:
      resources:
        limits:
          memory: 1G
        reservations:
          memory: 512M
```

## 🔒 Security Notes

- **Change default passwords** in `.env` file
- **Use strong passwords** for database users
- **Limit external access** to database port (33060)
- **Enable HTTPS** in production
- **Regular updates** of base images
- **Network isolation** prevents container-to-container attacks

## 🚀 Production Deployment

For production use:

1. **Set `APP_DEBUG=0`** in `.env`
2. **Use strong, unique passwords**
3. **Configure proper SSL certificates**
4. **Set up regular backups**
5. **Monitor resource usage**
6. **Use production-ready database passwords**
7. **Enable resource limits** in docker-compose.yml
8. **Set `RUN_MIGRATIONS=false`** after initial setup

## 📚 Additional Resources

- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [PHP Docker Official Image](https://hub.docker.com/_/php)
- [MySQL Docker Official Image](https://hub.docker.com/_/mysql)
- [Numok Project Documentation](../readme.md)

## 🤝 Support

If you encounter issues:

1. Check the logs: `docker compose logs`
2. Verify environment variables are set correctly
3. Ensure Docker has sufficient resources
4. Check the [main project README](../readme.md) for additional setup steps
5. Verify health check status: `docker compose ps`

---



