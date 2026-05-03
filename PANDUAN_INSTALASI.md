# Panduan Instalasi dan Menjalankan Projek BelajarFisika

Dokumen ini berisi panduan langkah demi langkah untuk menginstal dan menjalankan website **BelajarFisika** baik di komputer lokal (Windows/Mac) maupun di server (VPS).

---

## 🛠️ Prasyarat Sistem (Prerequisites)

Sebelum memulai, pastikan perangkat sudah terinstal perangkat lunak berikut:
1. **PHP** (Versi 8.1 atau lebih baru)
2. **Composer** (Untuk mengelola package PHP Laravel)
3. **Node.js & NPM** (Untuk mengelola package JavaScript & CSS/Vite)
4. **Git** (Untuk mengambil source code)
5. **Database MySQL / MariaDB** (Bisa menggunakan XAMPP, Laragon, atau menginstal langsung)

---

## 💻 Panduan Instalasi di Komputer Lokal (Pemula / Laragon / XAMPP)

Jika ingin menjalankan, memodifikasi, atau mengembangkan website ini di komputer/laptop, ikuti langkah-langkah berikut:

### 1. Kloning Projek (Clone Repository)
Buka Terminal / Command Prompt / Git Bash, lalu arahkan ke folder web server (misal: `C:\laragon\www` atau `C:\xampp\htdocs`). Jalankan perintah ini:
```bash
git clone https://github.com/Tonoooo/bisafisika.git
cd bisafisika
```

### 2. Instalasi Dependensi PHP (Composer)
Aplikasi ini menggunakan berbagai pustaka (library) PHP. Unduh semuanya dengan menjalankan:
```bash
composer install
```

### 3. Instalasi Dependensi Frontend (NPM)
Unduh library untuk tampilan (seperti Tailwind CSS, AlpineJS, Filament, dll):
```bash
npm install
```

### 4. Konfigurasi Lingkungan Kerja (`.env`)
Laravel membutuhkan file `.env` untuk menyimpan konfigurasi (seperti sandi database).
1. Salin file `.env.example` dan ubah namanya menjadi `.env` (di Windows bisa dengan *copy-paste* lalu di-*rename*).
2. Buka file `.env` menggunakan teks editor (Notepad / VS Code).
3. Sesuaikan pengaturan koneksi database di bagian ini (pastikan sesuai dengan pengaturan XAMPP/Laragon):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bisafisika
DB_USERNAME=root
DB_PASSWORD=
```
*(Penting: Buka aplikasi database seperti phpMyAdmin / HeidiSQL, lalu buat database kosong dengan nama `bisafisika`)*.

### 5. Generate Application Key
Jalankan perintah ini untuk membuat kunci keamanan sesi aplikasi:
```bash
php artisan key:generate
```

### 6. Migrasi Database
Buat struktur tabel otomatis ke dalam database yang telah buat tadi:
```bash
php artisan migrate
```

### 7. Buat Tautan Penyimpanan (Storage Link)
Sangat penting agar gambar/dokumen yang diunggah oleh user dapat diakses/ditampilkan:
```bash
php artisan storage:link
```

### 8. Jalankan Aplikasi!
Untuk menjalankan aplikasi secara sempurna (termasuk *styling* otomatis), membutuhkan dua Terminal/CMD yang berjalan secara bersamaan:

**Terminal 1 (Menjalankan mesin/server Laravel):**
```bash
php artisan serve
```

**Terminal 2 (Menjalankan mesin tampilan/Vite secara live):**
```bash
npm run dev
```

Selamat! Website kini dapat diakses melalui browser di alamat: **`http://localhost:8000`**

---

## 🌐 Panduan Instalasi di Server (VPS Ubuntu / Debian)

Untuk menjalankan aplikasi ini di lingkungan *Production* (Server VPS yang dapat diakses publik dari internet 24 jam), langkahnya sedikit berbeda. Kita asumsikan ini adalah *VPS fresh install*.

### 1. Update & Install Paket Dasar
Buka SSH VPS, lalu perbarui daftar paket:
```bash
sudo apt update
sudo apt install software-properties-common git curl unzip -y
```

### 2. Install PHP, Composer, Node.js & MySQL
Instal semua mesin yang dibutuhkan (misalnya PHP 8.2):
```bash
# Install PHP 8.2 & Ekstensinya
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install MySQL Server
sudo apt install mysql-server -y
```

### 3. Setup Database VPS
Masuk ke terminal MySQL:
```bash
sudo mysql
```
Jalankan perintah query berikut (ganti kata sandi sesuai keinginan):
```sql
CREATE DATABASE bisafisika;
CREATE USER 'adminfisika'@'localhost' IDENTIFIED BY 'PasswordSangatKuat!123';
GRANT ALL PRIVILEGES ON bisafisika.* TO 'adminfisika'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Kloning Source Code & Setup
Pindah ke direktori tempat website biasanya disimpan (misal di `/var/www`):
```bash
cd /var/www
sudo git clone https://github.com/Tonoooo/bisafisika.git
cd bisafisika
```

Instalasi library (untuk *production*, kita optimasi):
```bash
sudo composer install --optimize-autoloader --no-dev
sudo npm install
sudo npm run build
```
*(Catatan: Di VPS production, kita selalu menggunakan `npm run build` untuk mengkompresi CSS/JS, BUKAN `npm run dev`)*.

Copy dan atur `.env`:
```bash
sudo cp .env.example .env
sudo nano .env
```
Edit isinya agar sesuai dengan mode *Production*:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain.com

DB_DATABASE=bisafisika
DB_USERNAME=adminfisika
DB_PASSWORD=PasswordSangatKuat!123
```
Tekan `Ctrl+X`, lalu `Y`, lalu `Enter` untuk menyimpan.

Lanjutkan setup Artisan:
```bash
sudo php artisan key:generate
sudo php artisan migrate --force
sudo php artisan storage:link
sudo php artisan optimize:clear
```

### 5. Atur Hak Akses File (Permissions)
Server web (Nginx/Apache) butuh akses penuh untuk membaca dan mengunggah file gambar:
```bash
sudo chown -R www-data:www-data /var/www/bisafisika
sudo chmod -R 775 /var/www/bisafisika/storage
sudo chmod -R 775 /var/www/bisafisika/bootstrap/cache
```

### 6. Selesai
Setelah ini, hanya tinggal mengatur *Web Server block* (seperti **Nginx** atau **Apache**) agar mengarah ke folder direktori `/var/www/bisafisika/public`.

---

### 🚨 Troubleshooting Umum
1. **Pesan Error 500 saat dibuka:** 
   Periksa log untuk detail kesalahannya dengan melihat ke dalam file `storage/logs/laravel.log`. Kemungkinan terbesar ada masalah izin file (chown/chmod) atau kredensial `.env` salah.
2. **Tampilan hancur atau berantakan:** 
   Ini artinya aset Tailwind CSS belum di-render. Pastikan *terminal kedua* sedang menjalankan `npm run dev` (di lokal), atau telah berhasil menjalankan `npm run build` (di VPS).
3. **Gambar/Foto Profil Tidak Muncul:** 
   Pastikan sudah menjalankan perintah `php artisan storage:link`.
