## Langkah-Langkah Menjalankan

1. Clone Repositori
```bash
git clone https://github.com/YonanPrasetyo/backend_administrasi_rt.git
cd backend_administrasi_rt
```

2. Instal Dependensi Composer
```bash
composer install
```

3. Konfigurasi .env
```bash
cp .env.example .env
```

4. Atur Konfigurasi Basis Data
- Buka `.env`
- Sesuaikan `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

5. Migrasi Basis Data
```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

6. Generate Key
```bash
php artisan key:generate
```

7. Jalankan Aplikasi
```bash
php artisan serve
```
