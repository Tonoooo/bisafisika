# Fitur Lupa Password - BelajarFisika

## Deskripsi
Fitur lupa password telah diaktifkan di aplikasi BelajarFisika menggunakan Filament. Pengguna dapat mereset password mereka melalui email.

## Komponen yang Telah Dibuat

### 1. Halaman Login Custom (`app/Filament/Pages/CustomLogin.php`)
- Menambahkan link "Lupa Password?" di halaman login
- Link mengarah ke halaman request password reset

### 2. Halaman Request Password Reset (`app/Filament/Pages/CustomPasswordResetRequest.php`)
- Form untuk memasukkan email
- Tombol "Kirim Link Reset Password"
- Tombol "Kembali ke Login"

### 3. Notification Custom (`app/Notifications/CustomResetPasswordNotification.php`)
- Email template dalam bahasa Indonesia
- Menggunakan route Filament untuk reset password
- Subject email yang sesuai dengan aplikasi

### 4. User Model Update (`app/Models/User.php`)
- Menambahkan method `sendPasswordResetNotification()`
- Menggunakan notification custom untuk reset password

### 5. Template Email (`resources/views/vendor/notifications/email.blade.php`)
- Template email dalam bahasa Indonesia
- Desain yang sesuai dengan aplikasi

## Konfigurasi

### AdminPanelProvider (`app/Providers/Filament/AdminPanelProvider.php`)
```php
->passwordReset(\App\Filament\Pages\CustomPasswordResetRequest::class)
```

### Konfigurasi Email (`.env`)
```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Konfigurasi Auth (`config/auth.php`)
```php
'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60, // Token kadaluarsa dalam 60 menit
        'throttle' => 60, // Rate limiting 60 detik
    ],
],
```

## Cara Penggunaan

### Untuk Pengguna:
1. Buka halaman login di `/admin`
2. Klik link "Lupa Password?" di bawah form login
3. Masukkan email yang terdaftar
4. Klik "Kirim Link Reset Password"
5. Cek email dan klik link reset password
6. Masukkan password baru dan konfirmasi
7. Klik "Reset Password"

### Untuk Developer:
1. Pastikan konfigurasi email sudah benar di `.env`
2. Jalankan migration untuk tabel password reset tokens
3. Test fitur dengan email yang valid

## Database
Tabel `password_reset_tokens` sudah tersedia melalui migration:
```php
Schema::create('password_reset_tokens', function (Blueprint $table) {
    $table->string('email')->primary();
    $table->string('token');
    $table->timestamp('created_at')->nullable();
});
```

## Keamanan
- Token reset password kadaluarsa dalam 60 menit
- Rate limiting mencegah spam request
- Email dikirim ke alamat yang terdaftar
- Password baru harus minimal 8 karakter

## Troubleshooting

### Email tidak terkirim:
1. Periksa konfigurasi SMTP di `.env`
2. Pastikan `MAIL_FROM_ADDRESS` sudah diset
3. Cek log Laravel untuk error

### Link reset tidak berfungsi:
1. Pastikan token belum kadaluarsa
2. Periksa route Filament sudah terdaftar
3. Cek apakah email yang dimasukkan benar

### Halaman tidak muncul:
1. Clear cache: `php artisan cache:clear`
2. Clear config: `php artisan config:clear`
3. Restart server development 