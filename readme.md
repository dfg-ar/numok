# numok: Open Source Affiliate Program Platform

An open source affiliate program that connects to Stripe to track payments.

Installation Guide
==================

Requirements
------------

-   PHP 8.1 or higher
-   MySQL 5.7 or higher
-   Apache/Nginx web server
-   Composer
-   SSL certificate (required for Stripe integration)
-   Stripe account

Step-by-Step Installation
-------------------------

### 1\. Prepare Your Server

bash

Copy

`# Install required PHP extensions
php -v  # Verify PHP version
php -m  # Check for required extensions:
        # - PDO
        # - PDO_MySQL
        # - json
        # - mbstring`

### 2\. Get the Code

bash

Copy

`# Clone the repository
git clone https://github.com/numok/numok.git
cd numok

# Install dependencies
composer install`

### 3\. Database Setup

bash

Copy

`# Create a new MySQL database
mysql -u root -p
CREATE DATABASE numok CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`

### 4\. Configuration

1.  Copy the example configuration file:

    bash

    Copy

    `cp config/config.example.php config/config.php`

2.  Edit `config/config.php` with your settings:

    php

    Copy

    `return [
        'db' => [
            'host' => 'localhost',
            'database' => 'numok',
            'username' => 'your_db_user',
            'password' => 'your_db_password'
        ],
        'app' => [
            'url' => 'https://your-domain.com'
        ]
    ];`

### 5\. Database Migration

bash

Copy

`# Import the database structure
mysql -u your_db_user -p numok < database/numok-0-1.sql`

### 6\. Web Server Configuration

#### Apache

Ensure mod_rewrite is enabled and `.htaccess` is working:

apache

Copy

`<VirtualHost *:80>
    DocumentRoot /path/to/numok/public
    <Directory /path/to/numok/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>`

#### Nginx

nginx

Copy

`server {
    listen  80;
    server_name your-domain.com;
    root /path/to/numok/public;

    location / {
        try_files  $uri  $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}`

### 7\. File Permissions

bash

Copy

`# Set proper permissions
chmod -R 755 public/
chmod -R 755 src/
chmod -R 777 public/tracking/`

### 8\. Create Admin Account

Since Numok separates admins (users table) from partners (partners table), you'll need to create the first admin account directly in the database:

sql

Copy

`-- Insert the first admin user
INSERT INTO users (
    email,
    password,
    name,
    is_admin,
    created_at
) VALUES (
    'admin@yourdomain.com',
    -- This creates a password hash for 'admin123'
    '$2y$10$YW3BVlwXSWBIyQ1wl55m1eEDaXXqn9D9Q0qNwPAmtZAVJc1HgmsIO',
    'Admin User',
    1,
    CURRENT_TIMESTAMP
);`
After running this SQL:
1\.  Access `/admin/login`
2\.  Login with:
    -   Email: `admin@yourdomain.com`
    -   Password: `admin123`
3\.  **Important**: Immediately go to your profile settings and change your password

### 9\. Stripe Integration

1.  Log in to your admin account
2.  Go to Settings
3.  Enter your Stripe credentials:
    -   Secret Key
    -   Webhook Secret
4.  Configure your webhook endpoint in Stripe's dashboard:
    -   URL: `https://your-domain.com/webhook/stripe`
    -   Events to send:
        -   `checkout.session.completed`
        -   `payment_intent.succeeded`
        -   `invoice.paid`

Security Checklist
------------------

-   [ ]  Use HTTPS only
-   [ ]  Set secure file permissions
-   [ ]  Change default database credentials
-   [ ]  Enable error reporting only in development
-   [ ]  Configure PHP settings properly
-   [ ]  Set up SSL certificate
-   [ ]  Configure server firewall

Troubleshooting
---------------

### Common Issues

1.  **500 Internal Server Error**
    -   Check PHP error logs
    -   Verify file permissions
    -   Confirm .htaccess is working
2.  **Database Connection Failed**
    -   Verify database credentials
    -   Check MySQL server is running
    -   Confirm PHP PDO extension is installed
3.  **Webhook Errors**
    -   Verify SSL certificate is valid
    -   Check Stripe webhook secret
    -   Confirm webhook URL is accessible

Support
-------

-   GitHub Issues: Report bugs and feature requests
-   Documentation: [Link to documentation]
-   Community Forum: [Link to forum]

Contributing
------------

See <CONTRIBUTING.md> for details on:

-   Code of Conduct
-   Development setup
-   Testing
-   Pull request process

License
-------

This project is licensed under the MIT License - see the <LICENSE> file for details.