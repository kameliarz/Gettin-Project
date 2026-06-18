<img align="right" src="https://fonts.gstatic.com/s/e/notoemoji/latest/1f43f_fe0f/512.gif" alt="Squirrel" width="70">

# Gettin

![HTML](https://img.shields.io/badge/HTML-5-orange)
![CSS](https://img.shields.io/badge/CSS-3-blue)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-yellow)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4)
![Laravel](https://img.shields.io/badge/Laravel-Framework-red)
![Blade](https://img.shields.io/badge/Blade-Template-orange)
![MySQL](https://img.shields.io/badge/MySQL-Database-blue)
![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-Styling-38B2AC)

Gettin adalah aplikasi web pre-order makanan kantin kampus berbasis Laravel. Aplikasi ini dibuat untuk membantu mahasiswa memesan makanan terlebih dahulu, memilih waktu pengambilan, dan mengambil pesanan di kantin tanpa harus menunggu antrean terlalu lama.

Aplikasi ini memiliki tiga jenis pengguna, yaitu pelanggan, penjual, dan admin. Pelanggan dapat melihat menu, menambahkan makanan ke keranjang, checkout, dan melihat riwayat pesanan. Penjual dapat mengelola menu, waktu pengambilan, pesanan, dan laporan penjualan. Admin dapat memantau sistem serta mengelola data penjual.

---

## Link Project

| Keterangan        | Link                                                                      |
| ----------------- | ------------------------------------------------------------------------- |
| Repository GitHub | [Gettin Project](https://github.com/kameliarz/Gettin-Project)             |
| Website Deploy    | [Buka Website Gettin](https://gettin-project-production.up.railway.app/)  |
| Laporan Project   | [Buka Laporan Gettin](https://github.com/kameliarz/Gettin-Project/blob/main/1058_Laporan%20Akhir_PWEB.pdf)                        |
| Video Demo        | [Tonton Video Demo](https://youtu.be/mIPANe5G3Go)                         |

---

## Daftar Isi

* [Gettin](#gettin)
* [Link Penting](#link-penting)
* [Tentang Aplikasi](#tentang-aplikasi)
* [Fitur Utama](#fitur-utama)
  * [Pelanggan](#pelanggan)
  * [Penjual](#penjual)
  * [Admin](#admin)
* [Teknologi yang Digunakan](#teknologi-yang-digunakan)
* [Role Pengguna](#role-pengguna)
* [Struktur Database](#struktur-database)
* [Instalasi Project](#instalasi-project)
* [Menjalankan Aplikasi](#menjalankan-aplikasi)
* [Akun Demo](#akun-demo)
* [Data Awal Aplikasi](#data-awal-aplikasi)
* [Halaman Aplikasi](#halaman-aplikasi)
* [Implementasi AJAX / JSON](#implementasi-ajax--json)
* [Alur Penggunaan](#alur-penggunaan)
* [Keamanan dan Validasi](#keamanan-dan-validasi)
* [Screenshot](#screenshot)
* [Pengujian Manual](#pengujian-manual)
* [Struktur Folder Penting](#struktur-folder-penting)
* [Catatan Pengembangan](#catatan-pengembangan)
* [Pengembang](#pengembang)
* [Lisensi](#lisensi)
---

## Fitur Utama

### Pelanggan

* Melihat daftar menu dari kantin yang tersedia.
* Mencari menu berdasarkan nama menu, nama kantin, atau kategori.
* Memfilter menu berdasarkan kategori, kantin, dan rentang harga.
* Melihat menu populer.
* Menambahkan menu ke keranjang.
* Mengubah jumlah item di keranjang.
* Menghapus item dari keranjang.
* Memilih waktu pengambilan pesanan.
* Melakukan checkout.
* Melihat riwayat pemesanan.
* Melihat status pesanan.

### Penjual

* Melihat dashboard pesanan.
* Mengubah status pesanan.
* Menambahkan pesanan manual.
* Mengubah pesanan manual.
* Mengelola data menu kantin.
* Menambahkan menu baru.
* Mengubah data menu.
* Menghapus menu.
* Mengelola slot waktu pengambilan.
* Melihat laporan penjualan.
* Mengunduh laporan penjualan dalam format CSV atau PDF.

### Admin

* Melihat dashboard admin.
* Memantau data transaksi.
* Mengelola data penjual dan kantin.
* Menambahkan akun penjual.
* Mengubah data penjual.

---

## Teknologi yang Digunakan

| Teknologi       | Keterangan                     |
| --------------- | ------------------------------ |
| Laravel         | Framework utama aplikasi       |
| PHP             | Bahasa pemrograman backend     |
| Blade           | Template engine untuk tampilan |
| MySQL / MariaDB | Database relasional            |
| Tailwind CSS    | Styling antarmuka              |
| JavaScript      | Interaktivitas halaman         |
| Alpine.js       | Interaksi frontend ringan      |
| Vite            | Build tool frontend            |
| Laravel Breeze  | Autentikasi pengguna           |

---

## Role Pengguna

| Role      | Deskripsi                                                                     |
| --------- | ----------------------------------------------------------------------------- |
| Pelanggan | Pengguna yang melakukan pemesanan makanan.                                    |
| Penjual   | Pengelola kantin yang mengatur menu, pesanan, waktu pengambilan, dan laporan. |
| Admin     | Pengelola sistem yang dapat memantau data dan mengelola akun penjual.         |

---

## Struktur Database

Beberapa tabel utama yang digunakan dalam aplikasi ini adalah:

| Tabel                | Fungsi                                                             |
| -------------------- | ------------------------------------------------------------------ |
| users                | Menyimpan data akun pengguna dan role pengguna.                    |
| canteens             | Menyimpan data kantin.                                             |
| menu_categories      | Menyimpan kategori menu.                                           |
| menus                | Menyimpan data menu, harga, stok, gambar, dan status menu populer. |
| pickup_slot_options  | Menyimpan pilihan jam pengambilan.                                 |
| canteen_pickup_slots | Menyimpan slot waktu pengambilan untuk setiap kantin.              |
| carts                | Menyimpan keranjang pelanggan berdasarkan kantin.                  |
| cart_items           | Menyimpan item menu di dalam keranjang.                            |
| orders               | Menyimpan data pesanan pelanggan.                                  |
| order_items          | Menyimpan detail menu pada setiap pesanan.                         |
| sessions             | Menyimpan data session login pengguna.                             |

Relasi utama dalam aplikasi:

* Satu penjual dapat mengelola satu kantin.
* Satu kantin memiliki banyak menu.
* Satu menu memiliki satu kategori.
* Satu pelanggan dapat memiliki beberapa keranjang berdasarkan kantin.
* Satu keranjang memiliki banyak item.
* Satu pesanan memiliki banyak item pesanan.
* Satu kantin memiliki banyak slot waktu pengambilan.

---

## Instalasi Project

### 1. Clone repository

```bash
git clone https://github.com/kameliarz/Gettin-Project.git
cd Gettin-Project
```

### 2. Install dependency Laravel

```bash
composer install
```

### 3. Install dependency frontend

```bash
npm install
```

### 4. Salin file environment

```bash
cp .env.example .env
```

Untuk pengguna Windows Command Prompt:

```bash
copy .env.example .env
```

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Konfigurasi database

Buat database baru, misalnya dengan nama:

```sql
CREATE DATABASE gettin;
```

Kemudian sesuaikan konfigurasi database pada file `.env`:

```env
APP_NAME=Gettin
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gettin
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan konfigurasi database lokal masing-masing.

### 7. Jalankan migration dan seeder

```bash
php artisan migrate --seed
```

Perintah tersebut akan membuat tabel database dan mengisi data awal seperti akun pengguna, kantin, kategori menu, menu, slot waktu pengambilan, keranjang, dan contoh pesanan.

---

## Menjalankan Aplikasi

Jalankan server Laravel:

```bash
php artisan serve
```

Jalankan Vite:

```bash
npm run dev
```

Buka aplikasi melalui browser:

```text
http://127.0.0.1:8000
```

Untuk kebutuhan production build, jalankan:

```bash
npm run build
```

---

## Akun Demo

Setelah menjalankan seeder, gunakan akun berikut untuk mencoba aplikasi.

| Role      | Email                                           | Password    |
| --------- | ----------------------------------------------- | ----------- |
| Admin     | [admin@gettin.com](mailto:admin@gettin.com)     | password123 |
| Pelanggan | [budi@gettin.com](mailto:budi@gettin.com)       | password123 |
| Pelanggan | [sari@gettin.com](mailto:sari@gettin.com)       | password123 |
| Penjual   | [barokah@gettin.com](mailto:barokah@gettin.com) | password123 |
| Penjual   | [dharmaw@gettin.com](mailto:dharmaw@gettin.com) | password123 |

---

## Data Awal Aplikasi

Seeder menyediakan data awal agar aplikasi dapat langsung dicoba.

### Kantin

| Nama Kantin          | Lokasi         |
| -------------------- | -------------- |
| Kantin Barokah       | Lobby Fasilkom |
| Kantin Dharma Wanita | Lobby Fasilkom |

### Kategori Menu

* Makanan
* Minuman
* Camilan

### Contoh Menu

| Menu        | Kantin               | Kategori |
| ----------- | -------------------- | -------- |
| Ayam Geprek | Kantin Barokah       | Makanan  |
| Nasi Gila   | Kantin Barokah       | Makanan  |
| Es Jeruk    | Kantin Dharma Wanita | Minuman  |
| Es Teh      | Kantin Dharma Wanita | Minuman  |
| Tahu Kocek  | Kantin Dharma Wanita | Camilan  |

---

## Halaman Aplikasi

| Halaman                  | URL                            | Akses     |
| ------------------------ | ------------------------------ | --------- |
| Beranda                  | `/`                            | Umum      |
| Login                    | `/login`                       | Umum      |
| Register                 | `/register`                    | Umum      |
| Dashboard                | `/dashboard`                   | Login     |
| Menu Pelanggan           | `/pelanggan/menu`              | Pelanggan |
| Keranjang                | `/pelanggan/keranjang`         | Pelanggan |
| Riwayat Pemesanan        | `/pelanggan/riwayat-pemesanan` | Pelanggan |
| Dashboard Penjual        | `/penjual/dashboard`           | Penjual   |
| Kelola Menu              | `/penjual/menu`                | Penjual   |
| Kelola Waktu Pengambilan | `/penjual/waktu`               | Penjual   |
| Laporan Penjualan        | `/penjual/laporan`             | Penjual   |
| Dashboard Admin          | `/admin/dashboard`             | Admin     |
| Kelola Pengguna/Penjual  | `/admin/pengguna`              | Admin     |

---

## Implementasi AJAX / JSON

Aplikasi ini menggunakan komunikasi asinkronus pada beberapa fitur agar halaman dapat diperbarui tanpa reload penuh.

### Filter Menu

Pada halaman menu pelanggan, pengguna dapat mencari dan memfilter menu. Hasil pencarian dikirim ke server, lalu server mengembalikan response JSON berisi tampilan daftar menu yang sudah diperbarui.

### Keranjang

Pada halaman keranjang, proses tambah item, ubah jumlah item, dan hapus item dapat mengembalikan response JSON. Data keranjang dan panel checkout dapat diperbarui secara dinamis.

### Data Menu Penjual

Pada halaman penjual, data menu dapat diambil melalui endpoint data. Penjual dapat melihat, menambah, mengubah, dan menghapus menu.

### Data Waktu Pengambilan

Penjual dapat mengelola slot waktu pengambilan. Data slot dapat dimuat melalui response JSON agar pengelolaan waktu lebih dinamis.

### Laporan Penjualan

Data laporan penjualan dapat ditampilkan berdasarkan tanggal tertentu. Laporan juga dapat diunduh dalam format CSV atau PDF.

---

## Alur Penggunaan

### Alur Pelanggan

1. Pelanggan login ke aplikasi.
2. Pelanggan membuka halaman menu.
3. Pelanggan mencari atau memfilter menu.
4. Pelanggan menambahkan menu ke keranjang.
5. Pelanggan membuka halaman keranjang.
6. Pelanggan memilih slot waktu pengambilan.
7. Pelanggan melakukan checkout.
8. Pesanan masuk ke dashboard penjual.
9. Pelanggan dapat melihat riwayat dan status pesanan.

### Alur Penjual

1. Penjual login ke aplikasi.
2. Penjual membuka dashboard penjual.
3. Penjual melihat pesanan yang masuk.
4. Penjual mengubah status pesanan.
5. Penjual mengelola menu dan stok.
6. Penjual mengatur slot waktu pengambilan.
7. Penjual melihat atau mengunduh laporan penjualan.

### Alur Admin

1. Admin login ke aplikasi.
2. Admin membuka dashboard admin.
3. Admin memantau data transaksi.
4. Admin mengelola data penjual dan kantin.

---

## Keamanan dan Validasi

Beberapa validasi dan keamanan yang diterapkan dalam aplikasi:

* Autentikasi pengguna menggunakan Laravel Breeze.
* Proteksi halaman menggunakan middleware authentication.
* Pembagian akses berdasarkan role pengguna.
* Password disimpan dalam bentuk hash.
* Session digunakan untuk menyimpan status login.
* Validasi input dilakukan pada proses tambah menu, checkout, update keranjang, dan pengelolaan data lainnya.
* Checkout menggunakan transaksi database agar data pesanan, item pesanan, stok menu, dan keranjang tetap konsisten.
* Stok menu dicek sebelum item dimasukkan ke keranjang dan sebelum checkout.
* Slot waktu pengambilan dicek agar hanya slot aktif dan valid yang dapat dipilih.

---

## Pengujian Manual

| Skenario                                | Hasil yang Diharapkan                                         |
| --------------------------------------- | ------------------------------------------------------------- |
| Login menggunakan akun pelanggan        | Pengguna berhasil masuk dan dapat mengakses fitur pelanggan.  |
| Login menggunakan akun penjual          | Pengguna diarahkan ke dashboard penjual.                      |
| Login menggunakan akun admin            | Pengguna diarahkan ke dashboard admin.                        |
| Pelanggan mencari menu                  | Daftar menu berubah sesuai kata kunci pencarian.              |
| Pelanggan memfilter menu                | Menu yang tampil sesuai kategori, kantin, atau rentang harga. |
| Pelanggan menambahkan menu ke keranjang | Menu masuk ke keranjang pelanggan.                            |
| Pelanggan mengubah jumlah item          | Subtotal dan total keranjang ikut berubah.                    |
| Pelanggan checkout                      | Pesanan tersimpan dan stok menu berkurang.                    |
| Penjual mengubah status pesanan         | Status pesanan berhasil diperbarui.                           |
| Penjual menambahkan menu                | Menu baru muncul pada daftar menu.                            |
| Penjual mengubah slot waktu             | Slot waktu pengambilan berhasil diperbarui.                   |
| Admin menambahkan penjual               | Akun penjual dan data kantin berhasil tersimpan.              |

---

## Struktur Folder Penting

```text
app/
├── Http/
│   └── Controllers/
│       ├── Admin/
│       ├── Pelanggan/
│       └── Penjual/
├── Models/

database/
├── migrations/
└── seeders/

resources/
├── css/
├── js/
└── views/

routes/
└── web.php
```

## Pengembang

Nama: Kameliarz
Project: Gettin
Repository: https://github.com/kameliarz/Gettin-Project

---

## Lisensi

Project ini dibuat untuk kebutuhan pembelajaran dan pengembangan aplikasi web.
