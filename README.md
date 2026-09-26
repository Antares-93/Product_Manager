# Mini Project 2: Product Manager 

Aplikasi manajemen produk (CRUD) berbasis web yang dibangun menggunakan PHP Native murni dan MySQL (PDO), dirancang dengan standar keamanan input, pencegahan data ganda (PRG), proteksi CSRF & XSS, serta antarmuka responsif.

---

## Identitas Pengembang
* **Nama Lengkap:** Muhammad Dzakwan Hanif
* **NIM:** 250180094
* **Mata Kuliah:** Pemrograman Web (Mini Project 2)
  
---

## Fitur Utama & Bonus

### 1. Fitur Utama (CRUD)
* **Create:** Form tambah produk dengan validasi ketat sisi server (`nama >= 3`, `harga > 0`, `stok >= 0`, `nama unik`) dan pengalihan PRG (*Post/Redirect/Get*).
* **Read:** Menampilkan katalog produk dengan layout Card responsif berbasis CSS Grid/Flexbox yang menyesuaikan ukuran layar ponsel hingga desktop.
* **Update:** Form edit data produk berbasis ID dengan validasi nama unik yang mengecualikan ID produk yang sedang aktif diedit.
* **Delete:** Aksi hapus data aman yang diwajibkan menggunakan metode `POST` dan dilindungi token anti-CSRF (bukan tautan link biasa).

### 2. Fitur Bonus
* **Search & Filter:** Pencarian data produk secara dinamis berdasarkan nama atau kategori menggunakan parameter URL `GET` aman yang terikat ke PDO Prepared Statements.
* **Dark Mode Dinamis:** Pilihan tema Gelap/Terang dengan tombol toggle SVG minimalis yang statusnya tersimpan di `localStorage` peramban.

---

## Struktur Direktori Proyek

```text
product-manager/
├── config/
│   └── db.php                  # Konfigurasi koneksi database PDO
├── database/
│   └── store_db.sql            # DDL skema database MySQL
├── includes/
│   ├── functions.php           # Helper keamanan (CSRF, Anti-XSS, Flash Message)
│   ├── header.php              # Komponen header layout & navigasi tema
│   └── footer.php              # Penutup layout HTML
├── public/
│   ├── assets/
│   │   └── style.css           # File stylesheet mandiri (Card, Grid, Dark Mode)
│   ├── index.php               # Halaman utama (Katalog & Pencarian)
│   ├── create.php              # Form dan pemrosesan tambah produk
│   ├── edit.php                # Form dan pemrosesan ubah produk
│   └── delete.php              # Endpoint penanganan hapus produk (POST)
├── index.php                   # Gerbang redirect root ke public/
└── README.md                   # Dokumentasi proyek & panduan instalasi

---

## 4. Prasyarat Sistem

* **PHP:** Versi 8.0 ke atas (dengan ekstensi `pdo_mysql` aktif).
* **Database:** MySQL atau MariaDB.
* **Server Lokal:** XAMPP, Laragon, atau PHP CLI bawaan.
* **Web Browser:** Google Chrome, Mozilla Firefox, Microsoft Edge, atau peramban modern lainnya.

---

## 5. Panduan Instalasi & Menjalankan Aplikasi

### Langkah 1: Persiapan Basis Data (Database)
1. Buka aplikasi **XAMPP Control Panel** atau **Laragon**.
2. Nyalakan modul **MySQL** (dan **Apache**) dengan menekan tombol **Start**.
3. Akses antarmuka phpMyAdmin melalui browser di alamat: `http://localhost/phpmyadmin/`.
4. Buat basis data baru bernama `product_manager`.
5. Impor file **`database/store_db.sql`**:
   * Pilih database `product_manager`.
   * Klik tab **Import**, pilih file `database/store_db.sql`, lalu klik tombol **Go/Kirim**.
   * *(Alternatif)*: Buka tab **SQL**, salin seluruh kode dari file `database/store_db.sql`, lalu jalankan.

### Langkah 2: Konfigurasi Koneksi Database
Buka file **`config/db.php`** menggunakan editor teks (VS Code), lalu pastikan pengaturan koneksi sesuai dengan server lokal Anda:
```php
$host = '127.0.0.1';        // Host MySQL lokal
$db   = 'product_manager';  // Nama database
$user = 'root';             // Username default MySQL
$pass = '';                 // Password default XAMPP (kosongkan jika tanpa password)

### Langkah 3: Menjalankan Aplikasi

Pilih salah satu metode berikut:

* **Opsi A: Menggunakan XAMPP / Laragon (Disarankan)**
  1. Pastikan folder proyek berada di direktori web server:
     * **XAMPP:** `C:\xampp\htdocs\product-manager`
     * **Laragon:** `C:\laragon\www\product-manager`
  2. Buka browser dan kunjungi alamat:
     ```text
     http://localhost/product_manager/
     ```

* **Opsi B: Menggunakan PHP Built-in Server (Terminal / CMD)**
  1. Buka Terminal atau Command Prompt di folder utama proyek `product-manager`.
  2. Jalankan perintah server lokal berikut:
     ```bash
     php -S localhost:8000 -t public
     ```
  3. Buka browser dan akses tautan: `http://localhost:8000`[cite: 3].

---

## 6. Implementasi Kontrol Keamanan

| Vektor Ancaman | Kontrol Keamanan yang Diterapkan |
| :--- | :--- |
| **SQL Injection (Query)** | Seluruh eksekusi query (`SELECT`, `INSERT`, `UPDATE`, `DELETE`) menggunakan **PDO Prepared Statements** (`$pdo->prepare()`) tanpa menyambung string (*concatenation*) secara mentah. |
| **Cross-Site Scripting (Output)** | Seluruh data dinamis yang dicetak ke HTML disanitasi menggunakan fungsi pembungkus `e()` yang menjalankan `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')`. |
| **Cross-Site Request Forgery (Alur Request)** | Manipulasi data pada form `POST` (termasuk tombol Delete) diproteksi menggunakan token sesi acak `bin2hex(random_bytes(32))` dan diverifikasi melalui `hash_equals()`. |
| **Form Resubmission / Data Ganda** | Menerapkan arsitektur **Post/Redirect/Get (PRG)** menggunakan header pengalihan HTTP (`header("Location: index.php"); exit;`) setelah mutasi database berhasil. |

---

## 7. Refleksi Keamanan (Slide 20)

> **Pertanyaan:** *Di bagian mana aplikasi paling rentan: input, query, output, atau alur request? Jelaskan kontrol keamanan yang telah Anda implementasikan.*

**Jawaban Refleksi:**
Secara umum, **lapisan query dan alur request** merupakan area yang paling kritis dengan dampak risiko tertinggi jika berhasil dieksploitasi:
1. **Lapisan Query:** Jika rentan terhadap SQL Injection, penyerang dapat membaca kredensial rahasia, merusak integritas tabel, hingga menghapus database secara keseluruhan. Kontrol keamanan yang diterapkan adalah **PDO Prepared Statements** untuk memisahkan instruksi SQL dari parameter data masukan.
2. **Lapisan Alur Request:** Manipulasi alur request dapat mengakibatkan aksi destruktif tanpa sepengetahuan pengguna (*CSRF*) serta penggandaan data akibat penyampaian ulang form (*resubmission*). Kontrol keamanan yang diterapkan adalah pembatasan metode mutasi hanya melalui **`POST`**, verifikasi **Token CSRF** berbasis sesi, dan pengalihan status menggunakan siklus **PRG**.
3. **Lapisan Input & Output:** Integritas data dijaga lewat validasi tipe data dan batasan nilai di sisi server, sedangkan peramban pengguna dilindungi dari injeksi skrip berbahaya (*XSS*) menggunakan fungsi sanitasi `htmlspecialchars`.

---

## 8. Checklist Pengujian Mandiri (Demo)

- [x] **Tambah produk valid:** Data tersimpan ke MySQL dan langsung muncul di daftar katalog.
- [x] **Nama < 3 karakter:** Ditolak sistem dan menampilkan notifikasi kesalahan di bawah kolom nama.
- [x] **Harga <= 0 / Stok < 0:** Input bernilai nol/negatif pada harga atau negatif pada stok ditolak sistem.
- [x] **Refresh setelah create:** Refresh halaman browser tidak memicu dialog resubmission maupun duplikasi data baru (PRG aktif).
- [x] **Nama berisi tag HTML (`<b>Promo</b>`):** Output disanitasi dan tampil sebagai teks biasa `<b>Promo</b>`, bukan teks tebal.
- [x] **Responsivitas tampilan:** Kartu produk otomatis membungkus rapi menjadi satu kolom saat resolusi diperkecil ke ukuran layar ponsel.

---

## 9. Penyelesaian Masalah (Troubleshooting)

* **Muncul tampilan "Index of /product-manager":**
  Pastikan berkas `index.php` pada folder terluar sudah berisi skrip `header('Location: public/'); exit;`, atau buka langsung tautan `http://localhost/product-manager/public/` pada browser.
* **Koneksi Database Gagal / PDOException:**
  Pastikan modul MySQL di panel kontrol sudah berstatus aktif (*Running*) dan kredensial di `config/db.php` sudah tepat.
* **Perubahan Tampilan / Dark Mode Tidak Berfungsi:**
  Lakukan *Hard Refresh* pada peramban dengan menekan kombinasi tombol **Ctrl + F5** (atau **Cmd + Shift + R**) untuk memuat ulang berkas stylesheet `public/assets/style.css`.
