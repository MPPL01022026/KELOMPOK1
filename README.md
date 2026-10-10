# Digital Twin Smart Retail Yunda Fashion

Sistem monitoring stok dan etalase digital real-time berbasis web untuk UMKM **Yunda Fashion** (toko baju wanita). Dibuat sebagai tugas mata kuliah **Manajemen Proyek Perangkat Lunak (MPPL)**.

> Ganti teks dalam tanda `[ ]` dengan data project kalian.

---

## Daftar Isi

- [Latar Belakang](#latar-belakang)
- [Fitur Utama](#fitur-utama)
- [Cara Kerja Sistem](#cara-kerja-sistem)
- [Tech Stack](#tech-stack)
- [Struktur Folder](#struktur-folder)
- [Cara Instalasi](#cara-instalasi)
- [Cara Menjalankan](#cara-menjalankan)
- [Alur Kerja Git](#alur-kerja-git)
- [Progres Sprint](#progres-sprint)
- [Tim Pengembang](#tim-pengembang)

---

## Latar Belakang

Yunda Fashion menjual kemeja, blus, gamis, rok, celana jeans, celana kain, manset, dan lejing dalam berbagai ukuran dan warna. Pengecekan stok masih manual sehingga muncul tiga masalah:

1. Data stok tidak akurat karena dicek manual per varian, ukuran, dan warna.
2. Risiko stockout di produk best-seller karena informasi restok terlambat.
3. Belum ada visibilitas etalase toko secara real-time.

## Fitur Utama

- **QR Code per varian**: setiap pakaian punya stiker QR unik (produk, ukuran, warna).
- **Scan barang masuk/keluar**: lewat kamera HP atau browser, tanpa install aplikasi.
- **Etalase digital real-time**: warna indikator berubah live.
  - 🟢 Hijau: stok aman
  - 🟡 Kuning: stok menipis
  - 🔴 Merah: stok kritis
- **Smart Alert WhatsApp**: notifikasi otomatis ke pemilik saat stok kritis.

## Cara Kerja Sistem

```
Stiker QR di baju -> Scan (kamera HP/browser) -> Stok di MySQL ter-update
-> Etalase digital berubah warna -> Stok kritis? -> Alert WhatsApp terkirim
```

## Tech Stack

| Bagian | Teknologi |
|---|---|
| Frontend | HTML, CSS, JavaScript |
| Backend | [PHP / Node.js] |
| Database | MySQL (XAMPP) |
| QR Scanner | [html5-qrcode](https://github.com/mebjas/html5-qrcode) |
| Notifikasi | [whatsapp-web.js](https://github.com/pedroslopez/whatsapp-web.js) / [Fonnte] |
| Version Control | Git & GitHub |

## Struktur Folder

Sesuaikan dengan struktur project kalian.

```
nama-repo/
├── frontend/        # tampilan dashboard & halaman scanner
├── backend/         # script PHP/Node.js (API update stok)
├── database/        # file .sql (struktur & data awal)
├── docs/            # dokumen konsep, laporan, dan gambar
├── .env.example     # contoh konfigurasi (tanpa data rahasia)
├── .gitignore
└── README.md
```

## Cara Instalasi

### Prasyarat

- XAMPP (Apache & MySQL)
- [Node.js versi XX] (kalau backend memakai Node.js)
- Git
- Nomor WhatsApp aktif untuk bot alert

### Langkah

1. **Clone repository**
   ```bash
   git clone [URL-repository-kalian]
   cd [nama-repo]
   ```

2. **Siapkan database**
   - Jalankan Apache dan MySQL di XAMPP.
   - Buka `http://localhost/phpmyadmin`, buat database `[nama_database]`.
   - Import file `database/[nama_file].sql`.

3. **Atur konfigurasi**
   ```bash
   cp .env.example .env
   ```
   Lalu isi `.env`, misalnya:
   ```
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=
   DB_NAME=[nama_database]
   ```

4. **Install dependensi** (khusus Node.js)
   ```bash
   npm install
   ```

## Cara Menjalankan

1. Pastikan Apache dan MySQL di XAMPP sudah berjalan.
2. Jalankan backend:
   ```bash
   [npm start / taruh folder di htdocs untuk PHP]
   ```
3. Buka di browser:
   - Etalase digital: `http://localhost/[path-dashboard]`
   - Halaman scanner: `http://localhost/[path-scanner]`
4. **Pertama kali memakai WhatsApp bot**: scan QR yang muncul di terminal memakai WhatsApp pemilik/pengirim, sampai statusnya terhubung.

> Akses kamera di browser HP biasanya butuh `https` atau `localhost`. Kalau scanner tidak bisa dibuka dari HP, coba lewat tunnel atau hosting dengan HTTPS.

## Alur Kerja Git

Kami memakai **Git Feature Branching**:

- `main` : versi stabil
- `development` : tempat gabungan pekerjaan yang sedang berjalan
- `feature/*` : satu branch untuk satu fitur, misalnya `feature/web-scanner`

Langkah kerja:

```bash
git checkout development
git pull origin development
git checkout -b feature/nama-fitur
# ... koding, lalu commit
git add .
git commit -m "feat: deskripsi singkat perubahan"
git push origin feature/nama-fitur
```

Setelah itu buka **Pull Request** ke `development`. Minimal satu anggota lain me-review sebelum di-merge.

Contoh awalan pesan commit: `feat:` (fitur baru), `fix:` (perbaikan bug), `docs:` (dokumentasi), `style:` (tampilan).

> Jangan commit file `.env` dan folder sesi WhatsApp (misalnya `.wwebjs_auth/`). Masukkan ke `.gitignore`.

## Progres Sprint

Agile Scrum, 6 sprint.

| Sprint | Fokus | Status |
|---|---|---|
| 1 | Perencanaan & perancangan | [ ] |
| 2 | Manajemen produk & QR Code | [ ] |
| 3 | Scan barang masuk & keluar | [ ] |
| 4 | Etalase digital real-time | [ ] |
| 5 | Smart Alert WhatsApp | [ ] |
| 6 | Pengujian & finalisasi | [ ] |

Papan kerja: [link Trello / GitHub Projects]

## Tim Pengembang

| Nama | NIM | GitHub |
|---|---|---|
| [Nama 1] | [NIM] | [@username] |
| [Nama 2] | [NIM] | [@username] |
| [Nama 3] | [NIM] | [@username] |
| [Nama 4] | [NIM] | [@username] |

Mata kuliah: Manajemen Proyek Perangkat Lunak (MPPL)
Dosen pengampu: [Nama Dosen]
Universitas: [Nama Universitas]

## Lisensi

[Pilih salah satu, misalnya MIT, atau tulis "Untuk keperluan akademik".]
