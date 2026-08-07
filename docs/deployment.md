# EventPro Deployment Guide

Follow these instructions to deploy EventPro in a production or staging environment.

## System Requirements
- PHP >= 8.1
- MySQL >= 5.7 or MariaDB >= 10.3
- Apache or Nginx Web Server
- Composer

## Step-by-Step Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/4xrhd/event-pro.git
   cd event-pro
   ```

2. **Install Dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Configure Database**
   - Create a MySQL database named `event_pro`.
   - Import the schema:
     ```bash
     mysql -u root -p event_pro < database/schema.sql
     ```
   - Update database credentials in `config/database.php`.

4. **Web Server Configuration**
   - Point your virtual host document root to the `/public` directory.
   - Ensure URL rewriting is enabled (mod_rewrite for Apache, try_files for Nginx).

5. **Permissions**
   - Ensure `public/qrcodes/` and `logs/` directories are writable by the web server user.
   ```bash
   chmod -R 775 public/qrcodes logs
   ```
