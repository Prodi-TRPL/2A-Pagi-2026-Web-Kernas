# Panduan Instalasi & Menjalankan KERNAS di Komputer Lain

Panduan langkah demi langkah ini dikhususkan bagi Anda yang menerima kode sumber (source code) aplikasi KERNAS dalam bentuk file `.zip` dan sudah menginstal aplikasi **Laragon** di komputer Anda.

## Prasyarat
Sebelum memulai, pastikan komputer/laptop Anda telah terpasang:
1. **Laragon** (beserta Nginx/Apache dan MySQL yang sudah berjalan).
2. **PHP** (minimal versi 8.1 atau 8.2 yang umum terpasang pada rilis terbaru Laragon).
3. **Composer** (biasanya sudah otomatis terinstal saat memasang Laragon penuh/full version).

---

## Langkah 1: Ekstrak File ZIP
1. Pindahkan file `.zip` KERNAS yang Anda terima ke dalam folder root dari Laragon Anda. Biasanya folder tersebut berada di:
   `C:\laragon\www\`
2. Ekstrak file `.zip` tersebut di dalam folder `www`.
3. Setelah diekstrak, pastikan struktur foldernya benar (misalnya `C:\laragon\www\KERNAS\app`, `C:\laragon\www\KERNAS\public`, dsb). Jangan sampai terjadi *double folder* seperti `KERNAS\KERNAS\`.

## Langkah 2: Mengatur File Environment (.env)
Aplikasi Laravel membutuhkan file `.env` untuk konfigurasi koneksi database.
1. Buka folder hasil ekstrak tadi (misal `KERNAS`).
2. Cari file bernama `.env.example`.
3. *Copy* (salin) file tersebut dan *paste* di tempat yang sama, lalu ubah nama salinannya menjadi `.env` saja (tanpa embel-embel .example).
4. Buka file `.env` menggunakan Notepad atau teks editor (VS Code).
5. Cari baris berikut dan sesuaikan nama databasenya (misalnya kita beri nama `kernas_db`):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kernas_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(Biarkan password kosong jika Anda tidak pernah mengubah password default MySQL di Laragon)*.

## Langkah 3: Membuat Database di Laragon
1. Buka aplikasi **Laragon** dan pastikan Anda sudah mengklik tombol **Start All** (Apache/Nginx dan MySQL berjalan).
2. Klik tombol **Database** di Laragon (biasanya akan membuka HeidiSQL atau phpMyAdmin).
3. Buat database baru dengan nama yang sama persis seperti yang Anda tulis di file `.env` (misalnya: `kernas_db`).

## Langkah 4: Menjalankan Command (Terminal Laragon)
1. Buka aplikasi Laragon, lalu klik tombol **Terminal**.
2. Arahkan direktori terminal ke dalam folder KERNAS Anda dengan mengetik:
   ```bash
   cd C:\laragon\www\KERNAS
   ```
   *(Sesuaikan tulisan `KERNAS` dengan nama folder hasil ekstrak Anda)*.

3. Jalankan perintah-perintah berikut secara berurutan di dalam terminal tersebut:
   
   **a. Install Dependensi PHP (Composer):**
   ```bash
   composer install
   ```
   
   **b. Generate Application Key:**
   ```bash
   php artisan key:generate
   ```
   
   **c. Jalankan Migrasi Database (Membuat struktur tabel):**
   ```bash
   php artisan migrate
   ```
   *(Ketik `yes` lalu tekan Enter jika ada pertanyaan persetujuan).*

   **d. Tautkan Folder Penyimpanan (Storage Link):**
   ```bash
   php artisan storage:link
   ```
   *(Ini wajib dilakukan agar file-file seperti template surat dan dokumen bisa diakses/didownload)*.

## Langkah 5: Mengakses Aplikasi
Karena Anda menggunakan Laragon, Laragon biasanya otomatis membuatkan URL *virtual host* (contoh: `http://kernas.test`).
Namun, jika Anda ingin menjalankannya secara manual menggunakan server bawaan Laravel:
1. Di terminal yang sama, ketik perintah:
   ```bash
   php artisan serve
   ```
2. Buka browser (Chrome/Edge/Firefox) dan ketikkan alamat:
   **http://127.0.0.1:8000** atau **http://localhost:8000**

Selamat! Aplikasi KERNAS sudah berhasil berjalan di komputer Anda. Anda sudah bisa mulai mencoba *login* menggunakan akun-akun *dummy* atau akun yang sudah didaftarkan sebelumnya.
