# Penjelasan Fitur Task Management Smartlink

## Hak Akses Berdasarkan Role

Sistem ini memiliki 3 role utama:

### 1. Super Admin

-   Mengelola semua data termasuk role, user, permission (Filament Shield)
-   Akses penuh ke fitur:
    -   Manajemen Roles
    -   Manajemen Users
    -   Manajemen Projects
    -   Manajemen Tasks

> Login:
>
> -   Email: `superadmin@example.com`
> -   Password: `123456`

### 2. Admin

-   Mengelola semua data kecuali pengaturan roles
-   Fitur yang tersedia:
    -   Manajemen Projects
    -   Manajemen Employees (User dengan role "user")
    -   Manajemen Tasks

> Login:
>
> -   Email: `admin1@example.com`
> -   Password: `123456`

### 3. User

-   Hanya bisa melihat proyek dan task yang ditugaskan
-   Melihat dan memperbarui task miliknya sendiri
-   Tidak bisa mengedit atau menghapus proyek/tugas lain

> Login:
>
> -   Email: `user1@example.com`
> -   Password: `123456`

---

## Fitur Utama Aplikasi

### 1. Manajemen Project

-   Create, View, Edit, Delete project (oleh admin/superadmin)
-   Menambahkan task ke dalam project
-   Melihat ringkasan jumlah task berdasarkan status

### 2. Manajemen Task

-   Membuat task berdasarkan project
-   Assign ke user tertentu
-   Terdapat status: `To Do`, `In Progress`, `Done`
-   Fitur pelacakan waktu (`Do`, `Pause`, `Resume`, `Finish`) untuk user

### 3. Time Tracking (Untuk User)

-   User dapat menekan tombol **Do Task** untuk memulai pengerjaan, merubah status task yang awalnya `To Do` menjadi `In Progress`
-   Bisa dijeda **(Pause)** atau dilanjutkan **(Resume)**
-   Saat selesai, klik **Finish** → status jadi `Done`, waktu dihitung total
-   Sistem mencatat durasi total waktu pengerjaan

### 4. Laporan Progres

-   Ringkasan jumlah task berdasarkan status:
    -   Per Project
    -   Per User
-   Ditampilkan langsung di halaman detail (infolist) dan export .csv atau .xlsx

---

## Catatan Tambahan

-   Fitur export tugas tersedia via tombol Export
-   Divisi user (IT, HR, Operasional) bisa ditentukan saat pembuatan user
-   Autentikasi & permission dikendalikan oleh plugin [Filament Shield](https://github.com/bezhanSalleh/filament-shield)

---

## Video Demo

Untuk petunjuk penggunaan lebih lanjut, silakan tonton video demo berikut:

[Tonton di YouTube](https://youtu.be/bRxYH5KsIQo)

---
