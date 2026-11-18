# Panduan Deployment Server Produksi (Production Deployment)

Dokumen ini memuat panduan lengkap penerapan (*deployment*) aplikasi **E-Voting RT/RW** pada lingkungan server produksi (*production environment*) dengan standar keamanan tinggi dan performa optimal.

---

## 1. Spesifikasi Server yang Direkomendasikan

| Komponen | Spesifikasi Minimum (1.000 Pemilih) | Spesifikasi Optimal (> 10.000 Pemilih) |
|---|---|---|
| **Sistem Operasi** | Ubuntu Server 24.04 LTS / Debian 12 | Ubuntu Server 24.04 LTS |
| **CPU / Processor** | 2 vCPU Core | 4 - 8 vCPU Cores |
| **RAM / Memori** | 4 GB | 8 - 16 GB |
| **Penyimpanan** | 20 GB SSD / NVMe | 50+ GB Enterprise NVMe SSD |
| **Web Server** | Nginx 1.26+ | Nginx 1.26+ (Load Balanced) |
| **PHP Engine** | PHP-FPM 8.4 | PHP-FPM 8.4 |
| **Database** | MySQL 8.4 LTS | MySQL 8.4 LTS (Dedicated Cluster) |
| **Sertifikat SSL** | Let's Encrypt / Commercial SSL | Wildcard SSL + Cloudflare DDoS Proxy |

---

## 2. Persiapan Dependensi & Paket Server

Jalankan perintah berikut pada server Linux Ubuntu/Debian:

```bash
# Update repositori sistem
sudo apt update && sudo apt upgrade -y

# Tambahkan repositori PHP Ondrej Surý
sudo apt install -y software-properties-common curl git unzip nginx
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Pasang PHP 8.4 dan ekstensi yang diperlukan
sudo apt install -y php8.4-fpm php8.4-cli php8.4-mysql php8.4-curl \
    php8.4-mbstring php8.4-xml php8.4-bcmath php8.4-zip php8.4-gd \
    php8.4-intl php8.4-opcache

# Pasang Composer versi terbaru
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Pasang Node.js 22 LTS
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

---

## 3. Instalasi Berkas Aplikasi & Hak Akses

```bash
# Direktori web root
cd /var/www
sudo git clone <URL_REPOSITORI> evoting
cd evoting

# Pasang dependensi PHP tanpa paket pengembangan
composer install --no-dev --optimize-autoloader

# Kompilasi frontend asset
npm ci
npm run build

# Atur kepemilikan direktori ke www-data (User Web Server)
sudo chown -R www-data:www-data /var/www/evoting
sudo find /var/www/evoting -type f -exec chmod 644 {} \;
sudo find /var/www/evoting -type d -exec chmod 755 {} \;

# Berikan hak tulis eksklusif untuk storage & cache
sudo chmod -R 775 /var/www/evoting/storage /var/www/evoting/bootstrap/cache
```

---

## 4. Konfigurasi Lingkungan Produksi (.env)

Buat berkas `.env` dan konfigurasikan parameter produksi secara ketat:

```env
APP_NAME="E-Voting RT/RW"
APP_ENV=production
APP_KEY=base64:GENERATE_DENGAN_ARTISAN_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://evoting.desa-harmoni.id

LOG_CHANNEL=daily
LOG_LEVEL=warning

# Konfigurasi Database Produksi MySQL 8.4
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=evoting_prod
DB_USERNAME=evoting_user
DB_PASSWORD=KATA_SANDI_SANGAT_KUAT_DAN_ACAK

# Konfigurasi Sesi Aman (HTTPS Only)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=evoting.desa-harmoni.id
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Cache & Antrean
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Jalankan optimasi Laravel:
```bash
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
php artisan migrate --force
```

---

## 5. Konfigurasi Web Server Nginx

Buat berkas `/etc/nginx/sites-available/evoting`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name evoting.desa-harmoni.id;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name evoting.desa-harmoni.id;
    root /var/www/evoting/public;

    # SSL Certificates (Let's Encrypt Certbot)
    ssl_certificate /etc/letsencrypt/live/evoting.desa-harmoni.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/evoting.desa-harmoni.id/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    index index.php;
    charset utf-8;

    # Anti Directory Traversal & Dotfile Block
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 60;
    }
}
```

Aktifkan konfigurasi dan muat ulang Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/evoting /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 6. Optimasi PHP 8.4 (OPcache & PHP-FPM)

Sunting `/etc/php/8.4/fpm/php.ini`:
```ini
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 12M
max_execution_time = 30
expose_php = Off

; OPcache High Performance
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 0
opcache.validate_timestamps = 0
opcache.save_comments = 1
```

Muat ulang PHP-FPM:
```bash
sudo systemctl restart php8.4-fpm
```

---

## 7. Optimasi MySQL 8.4 LTS

Sunting `/etc/mysql/mysql.conf.d/mysqld.cnf`:
```ini
[mysqld]
# Konfigurasi Buffer Pool InnoDB (Gunakan 60-70% total RAM server)
innodb_buffer_pool_size = 2G
innodb_log_file_size = 512M
innodb_flush_log_at_trx_commit = 1
innodb_flush_method = O_DIRECT

# Strict Mode & Isolasi Transaksi
sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION"
transaction_isolation = REPEATABLE-READ
max_connections = 300
```

Muat ulang MySQL:
```bash
sudo systemctl restart mysql
```

---

## 8. Jadwal Otomatis (Cron Job) & Cadangan Data (Backup)

Tambahkan scheduler Laravel ke crontab server:
```bash
sudo crontab -u www-data -e
```
Isi dengan:
```cron
* * * * * cd /var/www/evoting && php artisan schedule:run >> /dev/null 2>&1
```

### Skrip Cadangan Basis Data Otomatis Harian:
Buat berkas `/usr/local/bin/backup-evoting.sh`:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/evoting"
DATE=$(date +"%Y%m%d_%H%M%S")
mkdir -p $BACKUP_DIR

mysqldump -u evoting_user -pKATA_SANDI evoting_prod | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Simpan cadangan 30 hari terakhir
find $BACKUP_DIR -type f -mtime +30 -name "*.sql.gz" -exec rm {} \;
```
Berikan izin eksekusi: `chmod +x /usr/local/bin/backup-evoting.sh`.
