# Deployment Guide

Complete guide for deploying the Osclass Classifieds platform.

## Quick Start with Docker

### Prerequisites
- Docker 20.10+
- Docker Compose 1.29+
- 2GB RAM minimum
- 10GB disk space

### Installation Steps

1. **Clone the repository**
```bash
git clone [your-repo-url]
cd osclass-app
```

2. **Configure environment**
```bash
cp config/config.sample.php config/config.php
# Edit config/config.php with your settings
```

3. **Start with Docker Compose**
```bash
docker-compose up -d
```

4. **Access the application**
- **App**: http://localhost:8080
- **PHPMyAdmin**: http://localhost:8081
- **Database**: localhost:3306

5. **Run database setup**
```bash
docker-compose exec app php setup/install.php
```

## Manual Installation

### Requirements
- PHP 8.2+
- MySQL 8.0+ / MariaDB 10.6+
- Nginx or Apache
- Composer
- GD library for image processing

### Step-by-Step

1. **Install PHP dependencies**
```bash
composer install --no-dev
```

2. **Configure web server**

**Nginx Example:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/osclass-app/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?url=$uri&$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

**Apache Example (.htaccess):**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

3. **Set permissions**
```bash
chmod -R 755 public/
chmod -R 777 cache/ logs/ public/uploads/
```

4. **Create database**
```bash
mysql -u root -p
CREATE DATABASE osclass_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'osclass_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON osclass_db.* TO 'osclass_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

5. **Import database schema**
```bash
mysql -u osclass_user -p osclass_db < database/schema.sql
```

6. **Configure application**
Edit `config/config.php` with your settings:
- Database credentials
- Base URL
- Email settings
- Payment gateway keys

7. **Restart web server**
```bash
# Nginx
sudo systemctl restart nginx php8.2-fpm

# Apache
sudo systemctl restart apache2
```

## Production Deployment

### Security Checklist

- [ ] Change all default passwords
- [ ] Set `ENVIRONMENT` to `production`
- [ ] Disable error display in PHP
- [ ] Enable HTTPS/SSL
- [ ] Configure firewall rules
- [ ] Set up regular backups
- [ ] Enable rate limiting
- [ ] Configure CSRF protection
- [ ] Set secure session settings
- [ ] Remove setup files after installation

### SSL Configuration

**Using Let's Encrypt:**
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### Performance Optimization

1. **Enable OPcache**
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
```

2. **Configure MySQL**
```ini
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
max_connections = 100
query_cache_size = 16M
```

3. **Use CDN for static assets**
- Upload images to CDN
- Update asset URLs
- Configure CDN caching

4. **Enable Gzip compression**
Already configured in nginx.conf

### Monitoring

**Log files locations:**
- PHP errors: `/var/www/osclass-app/logs/php_errors.log`
- Nginx access: `/var/log/nginx/access.log`
- Nginx errors: `/var/log/nginx/error.log`
- MySQL: `/var/log/mysql/error.log`

**Set up monitoring:**
```bash
# Install monitoring tools
sudo apt install htop iotop nethogs

# Monitor logs
tail -f logs/php_errors.log
```

## Backup & Restore

### Automated Backup Script
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/osclass"

# Backup database
mysqldump -u osclass_user -p osclass_db > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/osclass-app/public/uploads

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete
```

### Restore
```bash
# Restore database
mysql -u osclass_user -p osclass_db < backup.sql

# Restore files
tar -xzf files_backup.tar.gz -C /var/www/osclass-app/public/
```

## Scaling

### Horizontal Scaling

1. **Load Balancer Setup**
```nginx
upstream osclass_backend {
    server 10.0.0.1:80;
    server 10.0.0.2:80;
    server 10.0.0.3:80;
}

server {
    listen 80;
    server_name yourdomain.com;
    
    location / {
        proxy_pass http://osclass_backend;
    }
}
```

2. **Shared Storage**
- Use NFS or S3 for uploads
- Centralized cache (Redis/Memcached)
- Single database server or master-slave replication

### Database Replication

**Master-Slave Configuration:**
```sql
-- On master
CHANGE MASTER TO MASTER_HOST='master-ip', 
  MASTER_USER='replication_user',
  MASTER_PASSWORD='password';
START SLAVE;
```

## Troubleshooting

### Common Issues

**500 Internal Server Error:**
- Check PHP error logs
- Verify file permissions
- Check .htaccess syntax

**Database Connection Failed:**
- Verify credentials in config.php
- Check MySQL is running
- Test connection manually

**Images not uploading:**
- Check upload directory permissions
- Verify PHP upload settings
- Check disk space

**Slow performance:**
- Enable OPcache
- Optimize database queries
- Add indexes to tables
- Use CDN for assets

## Support

For issues and support:
- GitHub Issues: [your-repo]/issues
- Documentation: [your-docs-url]
- Community Forum: [your-forum-url]

## Updates

To update the application:
```bash
git pull origin main
composer install --no-dev
php update/migrate.php
sudo systemctl restart php8.2-fpm nginx
```

## License

This project is licensed under the MIT License.

