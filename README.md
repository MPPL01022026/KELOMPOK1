# Yunda Fashion — Sistem Monitoring Stok Etalase

Sistem sederhana untuk memantau stok produk pakaian Yunda Fashion. Sistem dirancang untuk mencatat perubahan stok melalui pemindaian QR Code, menampilkan status stok pada dashboard, dan mengirimkan notifikasi WhatsApp ketika diperlukan.

> **Status proyek:** Dalam pengembangan  
> **Jenis proyek:** Aplikasi web  
> **Pengguna utama:** Pengelola stok Yunda Fashion

## Fitur Utama

- Dashboard monitoring stok produk.
- Indikator status stok:
  - 🟢 **Hijau:** stok aman.
  - 🟡 **Kuning:** stok menipis dan perlu diperiksa.
  - 🔴 **Merah:** stok kritis atau habis.
- Data produk berdasarkan kategori, warna, ukuran, dan jumlah stok.
- Pemindaian QR Code melalui kamera HP atau laptop.
- Pencatatan riwayat perubahan stok.
- Notifikasi WhatsApp otomatis jika integrasi telah dikonfigurasi.

> **Catatan:** Batas stok untuk status hijau, kuning, dan merah perlu disepakati oleh tim dan ditetapkan pada konfigurasi aplikasi.

## Kategori Produk

Kategori pakaian yang dicatat dalam sistem:

1. Kemeja
2. Blus
3. Gamis
4. Rok
5. Jeans
6. Celana Kain
7. Manset
8. Legging

Ukuran produk: **S, M, L, XL**.  
Warna produk dicatat sesuai variasi yang tersedia di toko.

## Teknologi yang Digunakan

| Bagian | Teknologi | Fungsi |
|---|---|---|
| Frontend | HTML, CSS, JavaScript | Tampilan dashboard dan interaksi pengguna |
| Backend | PHP atau Node.js | Memproses permintaan dan aturan perubahan stok |
| Database | MySQL melalui XAMPP | Menyimpan data produk dan riwayat stok |
| QR Scanner | `html5-qrcode` | Membaca QR Code melalui kamera |
| Notifikasi WhatsApp | `whatsapp-web.js` atau layanan Fonnte | Mengirim notifikasi sesuai konfigurasi |

> Pilih satu teknologi backend utama, PHP **atau** Node.js, agar implementasi dan integrasi lebih konsisten. Periksa dokumentasi dan ketentuan layanan solusi WhatsApp yang dipilih sebelum digunakan.

## Pembagian Tugas Tim

### Orang 1 — Manajemen Proyek dan Testing
- Membuat papan Trello atau GitHub Projects dengan kolom **To Do**, **In Progress**, dan **Done**.
- Menyusun daftar produk beserta warna, ukuran, dan stok awal.
- Menguji integrasi frontend, backend, database, QR scanner, dan notifikasi.
- Mencatat bug dan memastikan perbaikan sebelum proyek dinyatakan selesai.

### Orang 2 — Frontend
- Merancang dashboard monitoring stok Yunda Fashion.
- Menampilkan daftar produk, ukuran, warna, jumlah stok, dan indikator status.
- Memastikan tampilan responsif pada HP dan laptop.
- Mengintegrasikan tampilan dengan endpoint backend.

### Orang 3 — Backend dan Database
- Menyiapkan database MySQL dan tabel produk serta log stok.
- Membuat API atau endpoint untuk membaca produk dan memperbarui stok.
- Memvalidasi setiap permintaan pemindaian QR Code.
- Mencatat perubahan stok ke dalam riwayat transaksi.
- Mencegah stok menjadi negatif dan menangani permintaan yang tidak valid.

### Orang 4 — QR Scanner dan WhatsApp
- Mengintegrasikan library `html5-qrcode`.
- Menghubungkan hasil pemindaian QR Code ke backend.
- Mengintegrasikan notifikasi WhatsApp menggunakan solusi yang dipilih tim.
- Menguji izin kamera, QR yang tidak valid, dan kegagalan pengiriman pesan.

## Alur Kerja Sistem

1. Pengguna membuka dashboard.
2. Pengguna memindai QR Code pada produk.
3. Sistem mengirimkan kode produk dan jenis transaksi ke backend.
4. Backend memvalidasi kode dan transaksi, lalu memperbarui stok di MySQL.
5. Sistem menyimpan perubahan pada log stok.
6. Dashboard menampilkan stok terbaru dan indikator status.
7. Jika aturan notifikasi terpenuhi, sistem mengirimkan pesan WhatsApp.

**Penting:** Pemindaian QR Code saja tidak menentukan apakah stok harus bertambah atau berkurang. Sistem perlu meminta pengguna memilih transaksi **stok masuk** atau **stok keluar**, atau menerapkan alur lain yang disepakati tim. Backend harus memvalidasi transaksi sebelum mengubah stok.

## Struktur Folder yang Disarankan

```text
yunda-fashion/
├── README.md
├── frontend/
│   ├── index.html
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
├── backend/
│   ├── config/
│   │   └── database.php
│   ├── api/
│   │   ├── products.php
│   │   └── stock.php
│   └── services/
│       └── whatsapp.php
├── database/
│   └── schema.sql
├── tests/
│   └── test-cases.md
└── docs/
    └── screenshots/
```

Struktur ini merupakan contoh awal. Sesuaikan nama dan isi folder dengan backend yang benar-benar dipilih. Jika tim menggunakan Node.js, sesuaikan ekstensi file dan struktur modulnya.

## Persiapan dan Instalasi

1. Pasang Git dan XAMPP jika tim memilih PHP serta MySQL.
2. Clone repositori:

   ```bash
   git clone <URL_REPOSITORY>
   cd yunda-fashion
   ```

3. Jalankan Apache dan MySQL melalui XAMPP.
4. Buat database MySQL, lalu impor file `database/schema.sql` jika sudah tersedia.
5. Sesuaikan konfigurasi koneksi database pada backend. Jangan memasukkan kata sandi atau token asli ke repositori publik.
6. Jalankan aplikasi sesuai petunjuk backend yang dipilih.
7. Buka dashboard melalui alamat lokal yang sesuai.
8. Izinkan akses kamera ketika menguji QR scanner. Akses kamera biasanya memerlukan HTTPS atau `localhost`.
9. Uji pengiriman WhatsApp menggunakan konfigurasi uji dan nomor yang telah mendapat persetujuan.

## Rancangan Database

### Tabel `produk`

Kolom yang disarankan:

- `id`
- `kode_qr`
- `nama_produk`
- `kategori`
- `warna`
- `ukuran`
- `stok`
- `batas_stok_minimum`
- `created_at`
- `updated_at`

### Tabel `log_stok`

Kolom yang disarankan:

- `id`
- `produk_id`
- `jenis_transaksi` — `masuk` atau `keluar`
- `jumlah`
- `stok_sebelum`
- `stok_sesudah`
- `waktu_transaksi`
- `keterangan`

Gunakan foreign key untuk menghubungkan `log_stok.produk_id` dengan `produk.id`. Perubahan stok dan pencatatan log sebaiknya dilakukan dalam satu transaksi database agar datanya tetap konsisten.

## Rencana Pengujian

| No. | Skenario | Hasil yang Diharapkan |
|---|---|---|
| 1 | Membuka dashboard | Daftar produk tampil |
| 2 | Memindai QR produk terdaftar | Produk dikenali |
| 3 | Memindai QR tidak terdaftar | Sistem menampilkan pesan kesalahan |
| 4 | Transaksi stok masuk | Stok bertambah sesuai jumlah |
| 5 | Transaksi stok keluar | Stok berkurang sesuai jumlah |
| 6 | Stok keluar melebihi stok tersedia | Transaksi ditolak |
| 7 | Stok melewati batas minimum | Indikator status diperbarui |
| 8 | Notifikasi memenuhi kondisi | Pesan dikirim atau kegagalan dicatat |
| 9 | Kamera tidak diizinkan | Sistem menampilkan petunjuk yang sesuai |
| 10 | Memuat ulang dashboard | Data tetap sesuai database |

## Alur Kolaborasi GitHub

1. Setiap anggota membuat branch sesuai tugas, misalnya `feature/frontend`, `feature/backend`, atau `feature/qr-whatsapp`.
2. Anggota melakukan commit dengan pesan yang jelas.
3. Perubahan diajukan melalui Pull Request.
4. Anggota lain memeriksa perubahan sebelum digabungkan.
5. Orang 1 menjalankan pengujian integrasi setelah fitur digabungkan.
6. Bug dicatat sebagai GitHub Issues dan dipindahkan pada papan proyek sesuai status pengerjaan.

## Checklist Pengembangan

- [ ] Papan proyek dibuat
- [ ] Data produk dan stok awal disiapkan
- [ ] Dashboard frontend selesai
- [ ] Database dan backend selesai
- [ ] QR scanner terintegrasi
- [ ] Notifikasi WhatsApp terintegrasi
- [ ] Pengujian integrasi selesai
- [ ] Dokumentasi dan tangkapan layar diperbarui

## Keamanan dan Catatan

- Jangan menyimpan kata sandi database, API key, token, atau kredensial WhatsApp di GitHub.
- Validasi data pada backend; jangan hanya mengandalkan validasi frontend.
- Batasi akses perubahan stok kepada pengguna yang berwenang.
- Gunakan koneksi aman untuk penggunaan di luar komputer lokal.
- Patuhi ketentuan layanan WhatsApp dan penyedia notifikasi yang digunakan.

## Anggota Kelompok

| No. | Nama | NIM | Tanggung Jawab |
|---|---|---|---|
| 1 | Adit Apriansyah | 230504130 | Manajemen proyek dan testing |
| 2 | Arya Arfanda | 230504128 | Frontend |
| 3 | Rana Shofa | 230504106 | Backend dan database |
| 4 | Salsabila | 230504097 | QR scanner dan notifikasi WhatsApp |

---

Proyek ini dikembangkan untuk membantu pencatatan dan pemantauan stok produk Yunda Fashion secara lebih terstruktur.
