# Digital Twin Smart Retail & Monitoring Stok Real-Time

**Studi Kasus: Yunda Fashion**  
**Mata Kuliah: Manajemen Proyek Perangkat Lunak (MPPL)**

## Anggota Kelompok

| Nama | NIM |
|---|---|
| Adit Apriansyah | 230504130 |
| Arya Arfanda | 230504128 |
| Rana Shofa | 230504106 |
| Salsabila | 230504097 |

## Deskripsi Proyek

Digital Twin Smart Retail merupakan sistem berbasis website untuk membantu Yunda Fashion memantau persediaan pakaian secara digital. Sistem ini mengelola stok berdasarkan model, ukuran, dan warna, serta menggunakan QR Code untuk mempermudah pencatatan barang masuk dan keluar.

Sistem menyediakan dashboard pemantauan stok, tampilan rak digital, riwayat transaksi, serta notifikasi WhatsApp ketika persediaan mencapai batas kritis.

## Fitur Utama

- **Dashboard:** Menampilkan ringkasan kondisi stok dan informasi persediaan.
- **Manajemen Produk:** Mengelola data produk berdasarkan model, ukuran, warna, dan jumlah stok.
- **QR Code:** Menghasilkan QR Code unik untuk setiap varian produk.
- **Scan Barang Masuk dan Keluar:** Mempermudah pencatatan perubahan stok melalui pemindaian QR Code.
- **Rak Digital:** Menampilkan status stok dengan indikator warna.
- **Riwayat Transaksi:** Mencatat aktivitas barang masuk dan keluar.
- **Notifikasi WhatsApp:** Memberikan peringatan ketika stok mencapai batas kritis.
- **Autentikasi:** Membatasi akses sistem melalui proses login.

## Indikator Status Stok

| Warna | Jumlah Stok | Status |
|---|---:|---|
| Hijau | ≥ 10 | Stok aman |
| Kuning | 4–9 | Stok menipis |
| Merah | ≤ 3 | Stok kritis |

Batas minimum stok dapat disesuaikan dengan kebutuhan toko.

## Teknologi yang Digunakan

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** Node.js dan Express.js
- **Database:** MySQL
- **QR Code Scanner:** html5-qrcode
- **Real-Time Update:** Socket.IO
- **Notifikasi WhatsApp:** whatsapp-web.js
- **Version Control:** Git dan GitHub
- **Code Editor:** Visual Studio Code

## Pembagian Tugas

### 1. Adit Apriansyah — Project Manager & Quality Assurance

- Mengatur pembagian tugas dan jadwal pengerjaan proyek.
- Menyusun backlog dan mengelola sprint.
- Mengatur GitHub Projects, Issues, dan pull request.
- Melakukan review terhadap perubahan kode.
- Menyusun skenario pengujian sistem.
- Mengidentifikasi dan mencatat bug.
- Memastikan fitur berjalan sesuai kebutuhan.
- Menyiapkan dokumentasi dan demonstrasi proyek.

### 2. Rana Shofa — Frontend Developer

- Membuat tampilan utama dan navigasi website.
- Mengembangkan dashboard pemantauan stok.
- Membuat antarmuka manajemen produk.
- Mengembangkan tampilan rak digital.
- Membuat halaman riwayat transaksi dan notifikasi.
- Memastikan tampilan responsif.
- Menghubungkan antarmuka dengan API backend.
- Menambahkan validasi pada sisi pengguna.

### 3. Arya Arfanda — Backend & Database Developer

- Merancang ERD dan struktur database MySQL.
- Membuat tabel pengguna, produk, varian produk, dan transaksi stok.
- Mengembangkan autentikasi dan pengaturan hak akses.
- Membuat API untuk pengelolaan data produk.
- Mengembangkan logika transaksi barang masuk dan keluar.
- Memastikan stok tidak menjadi negatif.
- Menyediakan endpoint untuk integrasi frontend dan QR Code.
- Menyiapkan data awal untuk pengujian.

### 4. Salsabila — QR Code & Integration Developer

- Membuat QR Code unik untuk setiap varian produk.
- Mengembangkan fitur pemindaian menggunakan html5-qrcode.
- Menghubungkan hasil pemindaian dengan proses barang masuk dan keluar.
- Mengintegrasikan pemindaian QR Code dengan API stok.
- Membantu integrasi pembaruan rak digital secara real-time.
- Mengembangkan notifikasi WhatsApp untuk stok kritis.
- Menguji QR Code yang tidak valid dan pemindaian berulang.
- Memastikan proses integrasi berjalan dengan baik.

## Rencana Pengerjaan (Sprint)

| Sprint | Fokus Pengerjaan |
|---|---|
| Sprint 1 | Perencanaan, desain sistem, ERD, wireframe, dan persiapan GitHub |
| Sprint 2 | Manajemen produk dan pembuatan QR Code |
| Sprint 3 | Pemindaian barang masuk/keluar dan riwayat transaksi |
| Sprint 4 | Rak digital dan pembaruan stok real-time |
| Sprint 5 | Integrasi notifikasi WhatsApp |
| Sprint 6 | Pengujian, perbaikan bug, dokumentasi, dan finalisasi |

## Alur Kolaborasi GitHub

Pengembangan dilakukan secara kolaboratif menggunakan Git dan GitHub.

- `main`: Menyimpan versi proyek yang stabil.
- `develop`: Menjadi branch integrasi untuk menggabungkan fitur.
- `feature/...`: Digunakan setiap anggota untuk mengembangkan fitur masing-masing.

Setiap anggota mengerjakan fitur pada branch masing-masing. Setelah selesai, perubahan diajukan melalui pull request ke branch `develop` untuk diperiksa dan diuji. Versi yang telah stabil kemudian digabungkan ke branch `main`.

## Kesepakatan Integrasi

Sebelum pengembangan dilakukan secara bersamaan, seluruh anggota perlu menyepakati:

- Struktur folder proyek.
- Struktur database dan penamaan tabel.
- Format ID produk dan varian untuk QR Code.
- Endpoint API yang digunakan.
- Aturan pencatatan stok masuk dan keluar.
- Format respons dan penanganan kesalahan API.
- Pengaturan konfigurasi aplikasi dan penyimpanan kredensial.

Informasi sensitif seperti kata sandi database, token, dan konfigurasi rahasia tidak boleh diunggah ke repositori publik.

## Tujuan Proyek

Proyek ini bertujuan membantu Yunda Fashion mengelola persediaan pakaian dengan lebih terstruktur, mempercepat pencatatan barang masuk dan keluar, mempermudah pemantauan stok, serta memberikan peringatan ketika persediaan mulai menipis.

Sistem ini juga menjadi penerapan praktik manajemen proyek perangkat lunak melalui pembagian tugas, pengelolaan sprint, kolaborasi GitHub, integrasi sistem, dan pengujian perangkat lunak.
