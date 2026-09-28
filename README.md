# Pulse Arena

Aplikasi pemesanan tiket untuk event kompetisi robotik: combat robot, line follower, drone racing, dan robo-soccer.

**Opsi pengerjaan: Opsi 3 (Fullstack).**

## Stack

- Backend: Laravel 13 (PHP 8.5) sebagai REST API
- Database: PostgreSQL
- Frontend: Blade, Bootstrap 5, CSS custom, dan AOS. Data diambil dari API lewat `fetch()`

Saya memilih Laravel dan Bootstrap karena itu stack yang paling saya kuasai, sehingga aplikasi bisa selesai utuh dalam waktu 24 jam.

## Fitur

- Daftar event dan detail event
- Pemesanan tiket dengan validasi nama dan email
- Tiket tidak bisa dipesan jika event sudah kedaluwarsa atau kuota habis
- Daftar tiket yang sudah dipesan, dengan filter `event_id`, `email`, dan `status`
- Event dengan tiket terbanyak dan terendah
- CRUD event melalui REST API (tambah, lihat, ubah, hapus)
- Halaman Admin terpisah di `/admin/events` untuk melihat daftar event, statistik tiket terbanyak/terendah, tambah, edit, dan hapus event
- Event yang sudah memiliki tiket tidak bisa dihapus
- **Catatan:** Halaman Admin belum dilindungi autentikasi/otorisasi; fitur ini ditujukan untuk demo lokal dan perlu pengamanan sebelum dipublikasikan.

## Cara menjalankan

1. Clone repo:
```bash
   git clone https://github.com/rosyidmuwaffaq/pulse-arena.git
   cd pulse-arena
```
2. Pasang dependency:
```bash
   composer install
```
3. Siapkan konfigurasi:
```bash
   copy .env.example .env
   php artisan key:generate
```
4. Buat database PostgreSQL bernama `arena_rx`, lalu isi `DB_USERNAME` dan `DB_PASSWORD` di `.env`.
5. Jalankan migration:
```bash
   php artisan migrate
```
6. Jalankan server:
```bash
   php artisan serve
```
   Buka `http://127.0.0.1:8000`. Halaman user: `/` dan `/tickets`. Halaman admin terpisah: `/admin/events` dan `/admin/events/create`.

Struktur tabel juga tersedia di `database/schema.sql`.

## Endpoint API

| Method | URL | Keterangan |
|---|---|---|
| GET | `/api/events` | Daftar event |
| POST | `/api/events` | Tambah event |
| GET | `/api/events/{id}` | Detail event |
| PUT | `/api/events/{id}` | Ubah event |
| DELETE | `/api/events/{id}` | Hapus event (ditolak jika sudah ada tiket) |
| GET | `/api/events/ranking` | Event dengan tiket terbanyak dan terendah |
| POST | `/api/events/{id}/tickets` | Pesan tiket |
| GET | `/api/tickets` | Daftar tiket, filter: `event_id`, `email`, `status` |

## Status UI

- User: melihat daftar/detail event, memesan tiket, dan melihat daftar tiket yang dipesan. Daftar tiket dimuat otomatis dan dapat difilter berdasarkan email/status.
- Admin (demo): halaman terpisah dari halaman user; daftar event, tambah event, edit event, hapus event, dan ringkasan event dengan tiket terbanyak/terendah.
- Filter tiket `event_id`, `email`, dan `status` tersedia melalui API. Antarmuka daftar tiket menampilkan daftar dan menyediakan filter email/status.
- Belum tersedia login/role Admin maupun autentikasi user.

## Pemetaan persyaratan pengujian

| Persyaratan | Implementasi/status |
|---|---|
| UI daftar event dan detail event | Tersedia pada `/` dan `/events/{id}` |
| UI pemesanan tiket | Tersedia pada halaman detail event |
| UI daftar tiket yang sudah dipesan | Tersedia di `/tickets`, dimuat otomatis dan dapat difilter email/status |
| Form penambahan event dan validasi dasar | Tersedia di halaman Admin `/admin/events/create`; validasi API menggunakan Form Request |
| CRUD Event API | Tersedia: GET, POST, PUT, DELETE `/api/events` |
| Pemesanan dan daftar tiket + filtering | POST `/api/events/{id}/tickets`; GET `/api/tickets` dengan filter `event_id`, `email`, `status` |
| Ranking event dengan tiket terbanyak/terendah | GET `/api/events/ranking` |
| Penolakan pemesanan event kedaluwarsa | Diperiksa pada backend sebelum tiket dibuat |
| Database SQL dan relasi one-to-many | Tabel `events` dan `tickets`, FK `tickets.event_id` |
| Validasi dan error handling | Form Request dan respons error API |
| README, struktur project, dokumentasi | Dijelaskan di README ini; file skema ada di `database/schema.sql` |

**Catatan pengujian:** fitur antarmuka dan API perlu diuji kembali pada lingkungan penguji. Aplikasi demo belum memiliki login/otorisasi; jangan memublikasikan data pemesan asli tanpa menambahkan perlindungan akses.

## Struktur database

Satu event memiliki banyak tiket (one-to-many), dihubungkan lewat `tickets.event_id`.

```mermaid
erDiagram
    EVENTS ||--o{ TICKETS : has
    EVENTS {
        bigint id PK
        string name
        string division
        text description
        string location
        datetime event_date
        int price
        int quota
    }
    TICKETS {
        bigint id PK
        bigint event_id FK
        string buyer_name
        string buyer_email
        string ticket_code
        string status
    }
```

Foreign key memakai `RESTRICT`: event tidak bisa dihapus selama masih memiliki tiket, karena tiket adalah data transaksi yang tidak boleh hilang otomatis.

## Keputusan teknis

- Validasi dipisah ke Form Request agar controller tetap singkat.
- Pemesanan tiket memakai transaksi database dengan `lockForUpdate()`, supaya kuota tidak terlampaui saat dua orang memesan bersamaan.
- Kode tiket dibuat oleh server, bukan diisi oleh user.

## Tantangan yang dihadapi

Kendala terbesar justru hal-hal kecil. Beberapa kali aplikasi error atau halaman jadi kosong cuma karena salah ketik, kurang satu tanda baca, atau file yang lupa di-save. Contohnya halaman detail event sempat putih polos, ternyata filenya belum tersimpan. Pernah juga folder view `tickets` belum kebuat, dan method `index` di controller ternyata belum masuk, jadi Laravel bilang method-nya tidak ditemukan.

Dari situ saya belajar membaca pesan error dengan pelan-pelan, karena biasanya penyebabnya sudah disebut di situ (nama file, nama method, atau baris yang bermasalah). Saya juga jadi punya kebiasaan cek `php artisan route:list` dan log Laravel dulu sebelum menebak-nebak.

## Yang ingin saya tingkatkan

- Autentikasi user, sehingga halaman "Tiket Saya" tidak perlu pencarian lewat email
- Pembayaran dan status tiket yang lebih lengkap
- Automated test untuk aturan pemesanan
- Pagination pada daftar event