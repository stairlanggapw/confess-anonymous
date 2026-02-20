# 💬 Confess Anonymous

Aplikasi web untuk berbagi cerita/rahasia secara anonim dengan fitur moderasi admin yang lengkap dan aman.

## 🎯 Deskripsi Singkat

Confess Anonymous adalah platform yang memungkinkan pengguna untuk mengirimkan cerita atau rahasia mereka secara anonim. Setiap confess yang dikirim akan melewati proses moderasi oleh admin sebelum ditampilkan di halaman publik, memastikan konten yang aman dan berkualitas.

---

## ✨ Fitur Utama

### 👤 User Features
- ✅ **Register** - Daftar akun dengan username & password (di-hash dengan BCRYPT)
- ✅ **Login** - Login dengan session-based authentication
- ✅ **Kirim Confess** - Kirim cerita anonim (status: pending approval)
- ✅ **View Approved Confess** - Lihat confess yang sudah di-approve (100% anonim)
- ✅ **Filter Kata Kasar** - Otomatis menyensor kata-kata tidak pantas
- ✅ **Logout** - Keluar dari aplikasi

### 🛡️ Admin Features
- ✅ **Admin Login** - Login khusus admin dengan role verification
- ✅ **Dashboard** - Melihat statistik (pending, approved, rejected, total users)
- ✅ **Moderasi** - Review confess yang pending
- ✅ **Approve Confess** - Setujui pesan untuk tampil di publik
- ✅ **Reject Confess** - Tolak pesan yang tidak sesuai
- ✅ **Blokir User** - Blokir user dari mengirim confess lagi
- ✅ **delete users messagesdelete users messages**

### 🌍 Public Features
- ✅ **View All Approved Confess** - Melihat semua confess yang sudah disetujui
- ✅ **100% Anonymous** - Username tidak terlihat, hanya pesan & tanggal
- ✅ **Latest First** - Confess ditampilkan dari terbaru ke terlama

---

## 🛠️ Tech Stack

- **Backend**: PHP 7.0+
- **Database**: MySQL 5.7+
- **Web Server**: Apache/Nginx
- **Frontend**: HTML5, CSS3, JavaScript
- **Authentication**: Session-based + Password BCRYPT
- **Security**: Prepared Statements, XSS Protection, SQL Injection Prevention

---

## 📋 Requirements

### Minimum Requirements
- PHP 7.0 atau lebih baru
- MySQL 5.7 atau MariaDB equivalent
- Web Server (Apache/Nginx)
- Modern Web Browser

### Recommended
- PHP 8.0+
- MySQL 8.0+
- Apache 2.4+
- 100MB disk space
- Stable internet connection

---

## 🚀 Instalasi Lokal (Development)

### Metode 1: Menggunakan XAMPP (Recommended)

1. **Install XAMPP**
   - Download dari https://www.apachefriends.org/
   - Install ke C:\xampp (Windows) atau Applications/XAMPP (Mac)

2. **Start XAMPP Services**
   - Buka XAMPP Control Panel
   - Klik "Start" Apache
   - Klik "Start" MySQL
   - Tunggu sampai "Running" (hijau)

3. **Copy Project Files**
   - Copy semua file project ke: `C:\xampp\htdocs\confess-anonymous\`
   - Struktur folder harus benar:
     ```
     C:\xampp\htdocs\confess-anonymous\
     ├── config.php
     ├── index.php
     ├── home.php
     ├── login.php
     ├── register.php
     ├── confess.php
     ├── logout.php
     ├── database.sql
     └── admin/
         ├── login.php
         ├── dashboard.php
         └── logout.php
     ```

4. **Setup Database**
   - Buka browser: `http://localhost/phpmyadmin`
   - Login: root / (kosong)
   - Klik tab "SQL"
   - Copy & paste isi file `database.sql`
   - Klik "Go"
   - Tunggu sampai: "Query successful" ✅

5. **Update config.php (jika perlu)**
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'confess_anonymous');
   ```

6. **Akses Aplikasi**
   - Buka browser: `http://localhost/confess-anonymous/`
   - Aplikasi ready to use! 🎉

### Metode 2: PHP Built-in Server

```bash
cd /path/to/confess-anonymous
php -S localhost:8000
```

Akses: `http://localhost:8000`

---

## 📊 Database Schema

### Tabel: users
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('user', 'admin') DEFAULT 'user',
    is_blocked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Tabel: confessions
```sql
CREATE TABLE confessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    approved_by INT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX (status),
    INDEX (created_at)
);
```

---

## 🔐 Default Credentials

### Admin Account
```
Username: stefanus
Password: aing3001
```

⚠️ **IMPORTANT**: Ubah password admin setelah login pertama untuk production!

---

## 📁 File Structure

```
confess-anonymous/
├── config.php              # Database config & security functions
├── index.php               # Landing page (redirect)
├── home.php                # Public homepage
├── login.php               # User login
├── register.php            # User registration
├── confess.php             # Send confession (user only)
├── logout.php              # User logout
├── database.sql            # Database setup SQL
├── admin/
│   ├── login.php           # Admin login
│   ├── dashboard.php       # Admin moderation dashboard
│   └── logout.php          # Admin logout
└── README.md               # This file
```

---

## 🔒 Security Features

- ✅ **Password Hashing**: BCRYPT algorithm
- ✅ **SQL Injection Protection**: Prepared statements
- ✅ **XSS Protection**: Input sanitization & htmlspecialchars()
- ✅ **Session Management**: Secure session handling
- ✅ **Role-Based Access Control**: User vs Admin differentiation
- ✅ **User Blocking**: Admin dapat blokir user
- ✅ **Profanity Filter**: Automatic bad word censoring

---

## 🚀 Publishing ke Production

### Step 1: Pilih Hosting Provider

**Recommended Hosting:**
- **Shared Hosting**: Hostinger, Niagahoster, IDHostinger
- **Cloud**: DigitalOcean, AWS, Google Cloud, Azure
- **VPS**: Linode, Vultr, DigitalOcean Apps

**Requirements:**
- PHP 7.4+ support
- MySQL 5.7+ support
- Unlimited databases
- SSH access (recommended)

### Step 2: Upload Files ke Hosting

**Metode A: FTP (Paling Mudah)**

1. Download FTP Client: FileZilla (https://filezilla-project.org/)
2. Buka hosting control panel (cPanel/Plesk)
3. Cari "FTP Accounts"
4. Create FTP account
5. Buka FileZilla:
   - Host: ftp.yourdomain.com
   - Username: ftp username
   - Password: ftp password
   - Port: 21
6. Upload semua file ke folder `public_html/confess-anonymous/`

**Metode B: SSH/Git (Lebih Professional)**

```bash
# SSH ke server
ssh user@yourdomain.com

# Clone repository (jika ada di Git)
git clone https://github.com/username/confess-anonymous.git public_html/confess-anonymous

# Atau upload via SCP
scp -r /local/path/confess-anonymous user@yourdomain.com:/public_html/
```

### Step 3: Setup Database di Server

1. **Akses phpMyAdmin hosting**
   - Buka: `yourdomain.com/phpmyadmin`
   - Login dengan credentials hosting

2. **Create Database**
   - Klik "New"
   - Database name: `confess_anonymous`
   - Charset: `utf8mb4_unicode_ci`
   - Klik "Create"

3. **Import SQL**
   - Select database `confess_anonymous`
   - Klik tab "Import"
   - Upload file `database.sql`
   - Klik "Go"

### Step 4: Update config.php

Edit file `config.php` dengan credentials hosting:

```php
<?php
define('DB_HOST', 'localhost');                    // Biasanya 'localhost'
define('DB_USER', 'your_hosting_db_user');         // Database user dari hosting
define('DB_PASS', 'your_hosting_db_password');     // Database password
define('DB_NAME', 'confess_anonymous');            // Database name
?>
```

### Step 5: Setup SSL/HTTPS

**Sangat penting untuk production!**

1. Hosting biasanya menyediakan SSL gratis (Let's Encrypt)
2. Akses cPanel → SSL/TLS
3. Install certificate
4. Force HTTPS:

Edit `.htaccess` di root folder:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### Step 6: Setup Domain

1. Beli domain dari: Niagahoster, IDN, DomainMe, dll
2. Point domain ke hosting:
   - Buka domain registrar
   - Update nameserver ke hosting nameserver
   - Atau setup DNS records (A, CNAME)
3. Update domain di hosting control panel
4. Akses aplikasi: `https://yourdomain.com/confess-anonymous/`

### Step 7: Security Hardening

**Untuk Production:**

1. **Ubah Default Admin Password**
   ```sql
   UPDATE users SET password = PASSWORD('your_new_strong_password') WHERE username = 'stefanus';
   ```

2. **Disable Directory Listing**
   - Edit `.htaccess`:
   ```apache
   Options -Indexes
   ```

3. **Protect Sensitive Files**
   ```apache
   <FilesMatch "^(config|database)\.php$">
       Deny from all
   </FilesMatch>
   ```

4. **Set File Permissions**
   ```bash
   chmod 644 *.php
   chmod 755 admin/
   chmod 644 admin/*.php
   ```

5. **Setup Backup**
   - Hosting cPanel biasanya ada auto-backup
   - Setup backup schedule

---

## 🔧 Maintenance

### Regular Tasks

1. **Check Logs** - Monitor error logs
2. **Update Password** - Change default credentials
3. **Backup Database** - Weekly backups
4. **Monitor Disk Usage** - Check storage
5. **Review Confess** - Moderate pending confess
6. **Block Spam** - Block users posting spam

### Database Backup

**Via phpMyAdmin:**
1. Select database `confess_anonymous`
2. Klik "Export"
3. Format: SQL
4. Klik "Go"

**Via Command Line:**
```bash
mysqldump -u root -p confess_anonymous > backup.sql
```

### Database Restore

```bash
mysql -u root -p confess_anonymous < backup.sql
```

---

## 🐛 Troubleshooting

### Error: "Connection failed"
- ✓ Check MySQL service running
- ✓ Check DB credentials di config.php
- ✓ Check database name correct

### Error: "Table doesn't exist"
- ✓ Re-import database.sql
- ✓ Check database name di config.php

### Error: "Access Denied" saat login
- ✓ Check username/password di database
- ✓ Check password di-hash dengan benar

### Confess tidak tersimpan
- ✓ Check user sudah login
- ✓ Check error message di browser console (F12)
- ✓ Verify file confess.php tidak ada error

---

## 📞 Support & FAQ

### Bagaimana cara reset password admin?

Via phpMyAdmin:
```sql
UPDATE users SET password = PASSWORD('newpassword') WHERE username = 'stefanus';
```

### Bagaimana cara menambah admin user baru?

Via phpMyAdmin atau:
```sql
INSERT INTO users (username, password, role) VALUES ('newadmin', PASSWORD('password123'), 'admin');
```

### Bagaimana cara delete semua confess?

```sql
TRUNCATE TABLE confessions;
```

### Bagaimana cara block/unblock user?

```sql
-- Block user
UPDATE users SET is_blocked = TRUE WHERE username = 'username';

-- Unblock user
UPDATE users SET is_blocked = FALSE WHERE username = 'username';
```

---

## 📜 License

Open source - Bebas digunakan untuk keperluan pribadi maupun komersial.

---

## 🎉 Selamat!

Website Confess Anonymous Anda sudah siap deploy!

Untuk bantuan lebih lanjut, contact: support@yourdomain.com

---

**Version**: 1.0  
**Last Updated**: February 2026  
**Status**: Production Ready ✅# 💬 Confess Anonymous

Aplikasi web untuk berbagi cerita/rahasia secara anonim dengan fitur moderasi admin yang lengkap dan aman.

## 🎯 Deskripsi Singkat

Confess Anonymous adalah platform yang memungkinkan pengguna untuk mengirimkan cerita atau rahasia mereka secara anonim. Setiap confess yang dikirim akan melewati proses moderasi oleh admin sebelum ditampilkan di halaman publik, memastikan konten yang aman dan berkualitas.

---

## ✨ Fitur Utama

### 👤 User Features
- ✅ **Register** - Daftar akun dengan username & password (di-hash dengan BCRYPT)
- ✅ **Login** - Login dengan session-based authentication
- ✅ **Kirim Confess** - Kirim cerita anonim (status: pending approval)
- ✅ **View Approved Confess** - Lihat confess yang sudah di-approve (100% anonim)
- ✅ **Filter Kata Kasar** - Otomatis menyensor kata-kata tidak pantas
- ✅ **Logout** - Keluar dari aplikasi

### 🛡️ Admin Features
- ✅ **Admin Login** - Login khusus admin dengan role verification
- ✅ **Dashboard** - Melihat statistik (pending, approved, rejected, total users)
- ✅ **Moderasi** - Review confess yang pending
- ✅ **Approve Confess** - Setujui pesan untuk tampil di publik
- ✅ **Reject Confess** - Tolak pesan yang tidak sesuai
- ✅ **Blokir User** - Blokir user dari mengirim confess lagi

### 🌍 Public Features
- ✅ **View All Approved Confess** - Melihat semua confess yang sudah disetujui
- ✅ **100% Anonymous** - Username tidak terlihat, hanya pesan & tanggal
- ✅ **Latest First** - Confess ditampilkan dari terbaru ke terlama

---

## 🛠️ Tech Stack

- **Backend**: PHP 7.0+
- **Database**: MySQL 5.7+
- **Web Server**: Apache/Nginx
- **Frontend**: HTML5, CSS3, JavaScript
- **Authentication**: Session-based + Password BCRYPT
- **Security**: Prepared Statements, XSS Protection, SQL Injection Prevention

---

## 📋 Requirements

### Minimum Requirements
- PHP 7.0 atau lebih baru
- MySQL 5.7 atau MariaDB equivalent
- Web Server (Apache/Nginx)
- Modern Web Browser

### Recommended
- PHP 8.0+
- MySQL 8.0+
- Apache 2.4+
- 100MB disk space
- Stable internet connection

---

## 🚀 Instalasi Lokal (Development)

### Metode 1: Menggunakan XAMPP (Recommended)

1. **Install XAMPP**
   - Download dari https://www.apachefriends.org/
   - Install ke C:\xampp (Windows) atau Applications/XAMPP (Mac)

2. **Start XAMPP Services**
   - Buka XAMPP Control Panel
   - Klik "Start" Apache
   - Klik "Start" MySQL
   - Tunggu sampai "Running" (hijau)

3. **Copy Project Files**
   - Copy semua file project ke: `C:\xampp\htdocs\confess-anonymous\`
   - Struktur folder harus benar:
     ```
     C:\xampp\htdocs\confess-anonymous\
     ├── config.php
     ├── index.php
     ├── home.php
     ├── login.php
     ├── register.php
     ├── confess.php
     ├── logout.php
     ├── database.sql
     └── admin/
         ├── login.php
         ├── dashboard.php
         └── logout.php
     ```

4. **Setup Database**
   - Buka browser: `http://localhost/phpmyadmin`
   - Login: root / (kosong)
   - Klik tab "SQL"
   - Copy & paste isi file `database.sql`
   - Klik "Go"
   - Tunggu sampai: "Query successful" ✅

5. **Update config.php (jika perlu)**
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'confess_anonymous');
   ```

6. **Akses Aplikasi**
   - Buka browser: `http://localhost/confess-anonymous/`
   - Aplikasi ready to use! 🎉

### Metode 2: PHP Built-in Server

```bash
cd /path/to/confess-anonymous
php -S localhost:8000
```

Akses: `http://localhost:8000`

---

## 📊 Database Schema

### Tabel: users
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('user', 'admin') DEFAULT 'user',
    is_blocked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Tabel: confessions
```sql
CREATE TABLE confessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    approved_by INT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX (status),
    INDEX (created_at)
);
```

---

## 🔐 Default Credentials

### Admin Account
```
Username: stefanus
Password: aing3001
```

⚠️ **IMPORTANT**: Ubah password admin setelah login pertama untuk production!

---

## 📁 File Structure

```
confess-anonymous/
├── config.php              # Database config & security functions
├── index.php               # Landing page (redirect)
├── home.php                # Public homepage
├── login.php               # User login
├── register.php            # User registration
├── confess.php             # Send confession (user only)
├── logout.php              # User logout
├── database.sql            # Database setup SQL
├── admin/
│   ├── login.php           # Admin login
│   ├── dashboard.php       # Admin moderation dashboard
│   └── logout.php          # Admin logout
└── README.md               # This file
```

---

## 🔒 Security Features

- ✅ **Password Hashing**: BCRYPT algorithm
- ✅ **SQL Injection Protection**: Prepared statements
- ✅ **XSS Protection**: Input sanitization & htmlspecialchars()
- ✅ **Session Management**: Secure session handling
- ✅ **Role-Based Access Control**: User vs Admin differentiation
- ✅ **User Blocking**: Admin dapat blokir user
- ✅ **Profanity Filter**: Automatic bad word censoring

---

## 🚀 Publishing ke Production

### Step 1: Pilih Hosting Provider

**Recommended Hosting:**
- **Shared Hosting**: Hostinger, Niagahoster, IDHostinger
- **Cloud**: DigitalOcean, AWS, Google Cloud, Azure
- **VPS**: Linode, Vultr, DigitalOcean Apps

**Requirements:**
- PHP 7.4+ support
- MySQL 5.7+ support
- Unlimited databases
- SSH access (recommended)

### Step 2: Upload Files ke Hosting

**Metode A: FTP (Paling Mudah)**

1. Download FTP Client: FileZilla (https://filezilla-project.org/)
2. Buka hosting control panel (cPanel/Plesk)
3. Cari "FTP Accounts"
4. Create FTP account
5. Buka FileZilla:
   - Host: ftp.yourdomain.com
   - Username: ftp username
   - Password: ftp password
   - Port: 21
6. Upload semua file ke folder `public_html/confess-anonymous/`

**Metode B: SSH/Git (Lebih Professional)**

```bash
# SSH ke server
ssh user@yourdomain.com

# Clone repository (jika ada di Git)
git clone https://github.com/username/confess-anonymous.git public_html/confess-anonymous

# Atau upload via SCP
scp -r /local/path/confess-anonymous user@yourdomain.com:/public_html/
```

### Step 3: Setup Database di Server

1. **Akses phpMyAdmin hosting**
   - Buka: `yourdomain.com/phpmyadmin`
   - Login dengan credentials hosting

2. **Create Database**
   - Klik "New"
   - Database name: `confess_anonymous`
   - Charset: `utf8mb4_unicode_ci`
   - Klik "Create"

3. **Import SQL**
   - Select database `confess_anonymous`
   - Klik tab "Import"
   - Upload file `database.sql`
   - Klik "Go"

### Step 4: Update config.php

Edit file `config.php` dengan credentials hosting:

```php
<?php
define('DB_HOST', 'localhost');                    // Biasanya 'localhost'
define('DB_USER', 'your_hosting_db_user');         // Database user dari hosting
define('DB_PASS', 'your_hosting_db_password');     // Database password
define('DB_NAME', 'confess_anonymous');            // Database name
?>
```

### Step 5: Setup SSL/HTTPS

**Sangat penting untuk production!**

1. Hosting biasanya menyediakan SSL gratis (Let's Encrypt)
2. Akses cPanel → SSL/TLS
3. Install certificate
4. Force HTTPS:

Edit `.htaccess` di root folder:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### Step 6: Setup Domain

1. Beli domain dari: Niagahoster, IDN, DomainMe, dll
2. Point domain ke hosting:
   - Buka domain registrar
   - Update nameserver ke hosting nameserver
   - Atau setup DNS records (A, CNAME)
3. Update domain di hosting control panel
4. Akses aplikasi: `https://yourdomain.com/confess-anonymous/`

### Step 7: Security Hardening

**Untuk Production:**

1. **Ubah Default Admin Password**
   ```sql
   UPDATE users SET password = PASSWORD('your_new_strong_password') WHERE username = 'stefanus';
   ```

2. **Disable Directory Listing**
   - Edit `.htaccess`:
   ```apache
   Options -Indexes
   ```

3. **Protect Sensitive Files**
   ```apache
   <FilesMatch "^(config|database)\.php$">
       Deny from all
   </FilesMatch>
   ```

4. **Set File Permissions**
   ```bash
   chmod 644 *.php
   chmod 755 admin/
   chmod 644 admin/*.php
   ```

5. **Setup Backup**
   - Hosting cPanel biasanya ada auto-backup
   - Setup backup schedule

---

## 🔧 Maintenance

### Regular Tasks

1. **Check Logs** - Monitor error logs
2. **Update Password** - Change default credentials
3. **Backup Database** - Weekly backups
4. **Monitor Disk Usage** - Check storage
5. **Review Confess** - Moderate pending confess
6. **Block Spam** - Block users posting spam

### Database Backup

**Via phpMyAdmin:**
1. Select database `confess_anonymous`
2. Klik "Export"
3. Format: SQL
4. Klik "Go"

**Via Command Line:**
```bash
mysqldump -u root -p confess_anonymous > backup.sql
```

### Database Restore

```bash
mysql -u root -p confess_anonymous < backup.sql
```

---

## 🐛 Troubleshooting

### Error: "Connection failed"
- ✓ Check MySQL service running
- ✓ Check DB credentials di config.php
- ✓ Check database name correct

### Error: "Table doesn't exist"
- ✓ Re-import database.sql
- ✓ Check database name di config.php

### Error: "Access Denied" saat login
- ✓ Check username/password di database
- ✓ Check password di-hash dengan benar

### Confess tidak tersimpan
- ✓ Check user sudah login
- ✓ Check error message di browser console (F12)
- ✓ Verify file confess.php tidak ada error

---

## 📞 Support & FAQ

### Bagaimana cara reset password admin?

Via phpMyAdmin:
```sql
UPDATE users SET password = PASSWORD('newpassword') WHERE username = 'stefanus';
```

### Bagaimana cara menambah admin user baru?

Via phpMyAdmin atau:
```sql
INSERT INTO users (username, password, role) VALUES ('newadmin', PASSWORD('password123'), 'admin');
```

### Bagaimana cara delete semua confess?

```sql
TRUNCATE TABLE confessions;
```

### Bagaimana cara block/unblock user?

```sql
-- Block user
UPDATE users SET is_blocked = TRUE WHERE username = 'username';

-- Unblock user
UPDATE users SET is_blocked = FALSE WHERE username = 'username';
```

---

## 📜 License

Open source - Bebas digunakan untuk keperluan pribadi maupun komersial.

---

## 🎉 Selamat!

Website Confess Anonymous Anda sudah siap deploy!

Untuk bantuan lebih lanjut, contact: support@yourdomain.com

---

**Version**: 1.0  
**Last Updated**: February 2026  
**Status**: Production Ready ✅
