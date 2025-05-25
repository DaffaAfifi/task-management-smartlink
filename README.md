## Task Management Smartlink Teknikal Test

Cara instalasi & menjalankan system secara lokal.

<hr/>

### Initial Setup

-   Install Dependencies & Generate App Key

    ```bash
    composer install && php artisan key:generate
    ```

    -   composer install
    -   php artisan key:generate

-   Copy & Edit .env File

    ```bash
    cp .env.example .env
    ```

    -   Buka file .env dan sesuaikan isi database:

    ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=8889
    DB_DATABASE=task_management
    DB_USERNAME=root
    DB_PASSWORD=root

    QUEUE_CONNECTION=sync
    ```

    -   QUEUE_CONNECTION=sync digunakan agar proses seperti export jalan tanpa konfigurasi queue driver tambahan.

-   Jalankan Migrasi Database

    ```bash
    php artisan migrate:fresh
    ```

-   Generate Filament Shield Permissions

    ```bash
    php artisan shield:generate --all
    ```

    -   Ini akan membuat permissions otomatis dari seluruh resiurce filament.

-   Jalankan Seeder

    ```bash
    php artisan db:seed
    ```

    -   Untuk mengisi data awal: roles, permissions, users, projects, tasks.

<hr/>

### Serve Laravel

-   Jalankan Aplikasi

    ```bash
    php artisan serve
    ```

    -   Akses aplikasi di http://127.0.0.1:8000

<hr />

### Panduan Penggunaan & Penjelasan Fitur

Untuk dokumentasi lengkap mengenai cara penggunaan sistem serta penjelasan setiap fitur, silakan buka file berikut:

📄 [Klik di sini untuk melihat penjelasan fitur](dokumen/penjelasan_fitur.md)
