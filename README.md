from pathlib import Path

path = Path("/mnt/data/README.md")
content = """<div align="center">

# DIGITAL TWIN SMART RETAIL
### Monitoring Stok Real-Time — Yunda Fashion

**Mata Kuliah: Manajemen Proyek Perangkat Lunak (MPPL)**

</div>

---

## Tentang Proyek

Proyek ini merupakan aplikasi web untuk membantu UMKM Yunda Fashion mengelola persediaan pakaian berdasarkan model, ukuran, dan warna. Sistem menggunakan konsep **Digital Twin**, yaitu representasi digital kondisi stok toko yang diperbarui berdasarkan transaksi barang masuk dan keluar.

## Anggota Kelompok

| Nama | NIM | Tanggung Jawab Utama |
|:---|:---:|:---|
| **Adit Apriansyah** | `230504130` | Project Manager & Quality Assurance |
| **Arya Arfanda** | `230504128` | Front-end Developer |
| **Rana Shofa** | `230504106` | Back-end & Database Developer |
| **Salsabila** | `230504097` | QR Code & Integration Developer |

## Latar Belakang

Yunda Fashion menjual berbagai jenis pakaian dengan banyak varian ukuran dan warna. Pencatatan manual dapat menyebabkan perbedaan antara jumlah stok pada catatan dan barang yang tersedia. Selain itu, pemilik berisiko terlambat mengetahui produk yang hampir habis dan belum dapat memantau kondisi stok secara praktis dari luar toko.

Sistem ini dirancang untuk mencatat perubahan stok secara terstruktur dan menampilkan informasi persediaan melalui satu website.

## Fitur Utama

- **Dashboard:** ringkasan jumlah stok, produk terdaftar, stok menipis, dan stok kritis.
- **Manajemen produk dan varian:** tambah, lihat, ubah, dan hapus produk serta pengaturan kategori, ukuran, warna, jumlah stok, dan batas stok.
- **QR Code:** membuat QR Code unik untuk setiap varian produk dan menyiapkannya untuk dicetak.
- **Transaksi stok:** pemindaian QR Code untuk mencatat barang masuk dan barang keluar.
- **Riwayat transaksi:** menyimpan waktu transaksi, jenis transaksi, jumlah, varian, dan pengguna yang melakukan transaksi.
- **Etalase digital:** menampilkan status stok dengan indikator warna.
- **Smart Alert WhatsApp:** mengirimkan peringatan ketika stok varian mencapai batas kritis.
- **Autentikasi dan hak akses:** membedakan akses pemilik dan staf toko.

### Indikator Stok

| Warna | Status | Contoh Batas |
|:---:|:---:|:---|
| 🟢 | Aman | 10 pcs atau lebih |
| 🟡 | Menipis | 4–9 pcs |
| 🔴 | Kritis | 3 pcs atau kurang |

Batas di atas merupakan nilai contoh dan sebaiknya dapat diatur sesuai kebutuhan toko atau masing-masing produk.

---

## Pembagian Jobdesk Anggota

### 1. Adit Apriansyah — Project Manager & Quality Assurance

**Fokus:** pengelolaan proyek, koordinasi, dan pengujian.

**Tugas:**
- Menyusun product backlog, jadwal, target sprint, dan pembagian pekerjaan.
- Mengelola GitHub Projects/Issues serta memantau progres setiap anggota.
- Menetapkan standar kerja bersama, termasuk aturan branch dan commit.
- Meninjau Pull Request dan membantu koordinasi integrasi antarmodul.
- Menyusun skenario pengujian untuk fitur-fitur utama.
- Menguji alur sistem, mencatat bug, memantau perbaikannya, dan menyusun dokumentasi pengujian.
- Menyiapkan laporan progres dan bahan demonstrasi akhir.

**Hasil kerja:** backlog dan jadwal proyek, catatan progres, test case, laporan bug, serta dokumentasi proyek.

### 2. Arya Arfanda — Front-end Developer

**Fokus:** antarmuka dan pengalaman pengguna.

**Tugas:**
- Membuat layout utama, navigasi, dan dashboard.
- Membuat halaman manajemen produk dan formulir produk/varian.
- Membuat tampilan etalase digital beserta indikator warna stok.
- Membuat halaman riwayat transaksi dan tampilan notifikasi.
- Membuat antarmuka responsif untuk laptop dan HP.
- Menghubungkan antarmuka dengan endpoint/API yang disepakati bersama back-end developer.
- Menangani validasi input dan pesan kesalahan pada sisi antarmuka.

**Hasil kerja:** halaman website, komponen UI, desain responsif, dan integrasi antarmuka dengan API.

### 3. Rana Shofa — Back-end & Database Developer

**Fokus:** penyimpanan data dan logika bisnis.

**Tugas:**
- Merancang ERD dan skema database MySQL.
- Menyiapkan tabel pengguna, produk, varian, transaksi stok, dan data pendukung.
- Membuat autentikasi serta hak akses pemilik dan staf.
- Mengembangkan proses CRUD produk dan varian.
- Membuat proses barang masuk dan keluar dengan validasi jumlah stok.
- Mencegah stok menjadi negatif dan menjaga konsistensi transaksi.
- Menyediakan API/endpoint untuk dashboard, etalase digital, riwayat transaksi, dan modul QR.
- Mengatur konfigurasi database serta menyiapkan data awal untuk pengujian.

**Hasil kerja:** ERD, skema database, autentikasi, API, dan logika transaksi stok.

### 4. Salsabila — QR Code & Integration Developer

**Fokus:** pemindaian barang dan integrasi fitur otomatis.

**Tugas:**
- Membuat QR Code unik berdasarkan identitas setiap varian produk.
- Mengembangkan fitur scan QR melalui kamera browser/HP menggunakan `html5-qrcode`.
- Membuat alur pemilihan mode barang masuk atau barang keluar.
- Menghubungkan hasil scan dengan API transaksi stok yang dibuat back-end developer.
- Mengembangkan mekanisme pembaruan etalase digital secara real-time bersama anggota front-end dan back-end.
- Mengintegrasikan notifikasi WhatsApp untuk stok kritis.
- Menguji QR tidak valid, pemindaian berulang, kegagalan koneksi, dan kondisi stok kritis.

**Hasil kerja:** generator dan pemindai QR, integrasi transaksi, pembaruan real-time, serta integrasi notifikasi.

> Pembagian di atas menunjukkan penanggung jawab utama. Seluruh anggota tetap berkolaborasi saat integrasi, code review, dan pengujian. Jobdesk dapat disesuaikan setelah kemampuan dan kesepakatan tim dibahas bersama.

---

## Teknologi yang Direncanakan

| Komponen | Teknologi |
|:---|:---|
| Front-end | HTML, CSS, JavaScript |
| Back-end | Node.js dan Express.js |
| Database | MySQL |
| Scan QR Code | `html5-qrcode` |
| Pembaruan real-time | Socket.IO (jika diterapkan) |
| Notifikasi WhatsApp | `whatsapp-web.js` |
| Version control | Git dan GitHub |
| Editor | Visual Studio Code |

**Catatan:** `whatsapp-web.js` menggunakan pendekatan tidak resmi dan sesi WhatsApp dapat terputus. Integrasi perlu diuji dan menggunakan akun/nomor yang mendapat izin. Fitur real-time memerlukan mekanisme pembaruan seperti Socket.IO atau pendekatan lain yang disepakati tim.

## Rencana Sprint

| Sprint | Fokus | Target |
|:---:|:---|:---|
| **1** | Perencanaan dan desain | Backlog, ERD, wireframe, struktur proyek, dan repository |
| **2** | Produk dan QR Code | CRUD produk/varian dan generator QR |
| **3** | Transaksi stok | Scan QR, barang masuk/keluar, dan riwayat transaksi |
| **4** | Etalase digital | Status stok dan pembaruan tampilan |
| **5** | Smart Alert | Notifikasi stok kritis |
| **6** | Pengujian dan finalisasi | Perbaikan bug, dokumentasi, dan demo |

## Alur Kerja GitHub

1. Gunakan `main` untuk versi stabil dan `develop` untuk integrasi fitur.
2. Setiap anggota membuat branch fitur dari `develop`, misalnya:
   - `feature/dashboard-ui`
   - `feature/product-management`
   - `feature/stock-api`
   - `feature/qr-scan`
3. Buat commit dengan pesan yang jelas dan spesifik.
4. Jika fitur selesai, ajukan Pull Request ke `develop`.
5. Minta anggota lain meninjau perubahan sebelum merge.
6. Uji integrasi di `develop`. Gabungkan ke `main` ketika versi dinilai stabil.

## Kesepakatan Integrasi

Sebelum pengembangan paralel, tim perlu menyepakati:
- Struktur folder dan cara menjalankan aplikasi.
- Skema database serta penamaan tabel/kolom.
- Format data produk dan varian, termasuk ID unik untuk QR Code.
- Endpoint/API yang digunakan front-end dan modul QR.
- Aturan perubahan stok dan validasi transaksi.
- Format respons sukses dan pesan kesalahan.
- File konfigurasi yang diperlukan; rahasia seperti kata sandi, token, dan kredensial tidak boleh di-commit ke GitHub.

## Kebutuhan Non-Fungsional

- Antarmuka mudah dipahami oleh pemilik dan staf toko.
- Tampilan responsif di laptop dan perangkat seluler.
- Data stok dan riwayat transaksi konsisten.
- Halaman hanya dapat diakses sesuai hak pengguna.
- Perubahan stok tercatat dan dapat ditelusuri.
- Sistem memberi informasi yang jelas ketika terjadi kesalahan.

## Manfaat yang Diharapkan

- Mengurangi kesalahan pencatatan stok.
- Memudahkan pemantauan varian berdasarkan ukuran dan warna.
- Membantu pemilik mengetahui stok yang menipis atau kritis.
- Mempercepat pencatatan barang masuk dan keluar.
- Menyediakan riwayat pergerakan stok untuk membantu keputusan restok.
- Memberikan pengalaman kerja tim menggunakan Agile Scrum dan GitHub.

---

## Status Proyek

**Status:** Perencanaan dan pengembangan.

Fitur dan teknologi di atas merupakan rencana awal. Implementasi akhir bergantung pada hasil pengembangan, pengujian, dan evaluasi kebutuhan Yunda Fashion.

---

<div align="center">

Dibuat untuk proyek mata kuliah **Manajemen Proyek Perangkat Lunak (MPPL)**.

</div>
"""
path.write_text(content, encoding="utf-8")
print(f"README.md yang diperbarui tersedia di: {path}")
