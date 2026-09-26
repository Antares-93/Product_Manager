# Mini Project: Product Manager (PHP Native & PDO)

Aplikasi manajemen produk (CRUD) berbasis web yang dibangun menggunakan PHP Native murni dan MySQL (PDO), dirancang dengan standar keamanan input, pencegahan data ganda (PRG), proteksi CSRF & XSS, serta antarmuka responsif.

---

## Identitas Pengembang
* **Nama Lengkap:** [Muhammad Dzakwan Hanif]
* **NIM:** [250180094]
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
