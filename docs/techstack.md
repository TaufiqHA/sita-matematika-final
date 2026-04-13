# Tech Stack — Sistem Informasi Tugas Akhir (SITA)

> Dokumen ini mendefinisikan tech stack yang digunakan dalam pengembangan SITA, mencakup backend, frontend, database, infrastruktur, dan tooling pendukung. Stack dipilih berdasarkan kebutuhan sistem, kemudahan pemeliharaan, dan kesesuaian dengan scope MVP.

---

## Ringkasan Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 11 (PHP 8.3) |
| Frontend | Vue.js 3 (Composition API) |
| UI Framework | Tailwind CSS + shadcn-vue |
| State Management | Pinia |
| HTTP Client | Axios |
| Database | MySQL 8 |
| Auth | Laravel Sanctum (SPA Auth) |
| File Storage | Laravel Storage (local/S3-compatible) |
| Queue & Job | Laravel Queue + Database Driver (MVP) |
| Realtime / Notifikasi | Laravel Broadcasting + Reverb (WebSocket) |
| PDF Generate | barryvdh/laravel-dompdf |
| Build Tool | Vite |
| Testing Backend | PHPUnit + Pest |
| Testing Frontend | Vitest + Vue Test Utils |
| Version Control | Git + GitHub |
| Deployment | VPS / Shared Hosting (Apache/Nginx + PHP-FPM) |

---

## 1. Backend — Laravel 11

### Versi & Prasyarat

| Item | Versi |
|---|---|
| PHP | >= 8.3 |
| Laravel | 11.x |
| Composer | >= 2.x |

### Arsitektur

SITA menggunakan pola **API-based SPA**: Laravel berfungsi murni sebagai **REST API backend** yang melayani request dari Vue.js frontend. Tidak ada Blade view yang digunakan untuk halaman aplikasi (kecuali untuk generate PDF/dokumen saja).

```
Vue.js (SPA) ──→ REST API ──→ Laravel ──→ MySQL
```

### Struktur Direktori Backend (Laravel)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── Mahasiswa/
│   │   ├── Dosen/
│   │   ├── TataUsaha/
│   │   ├── KetuaJurusan/
│   │   ├── Sekretaris/
│   │   └── Admin/
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Requests/          ← Form Request Validation
├── Models/
├── Services/              ← Business logic layer
├── Notifications/         ← Laravel Notifications
├── Jobs/                  ← Queue jobs
├── Policies/              ← Authorization policies
└── Enums/                 ← Status enum (StatusPengajuan, JenisSeminar, dst.)
```

### Package Laravel yang Digunakan

| Package | Fungsi |
|---|---|
| `laravel/sanctum` | Autentikasi SPA (cookie-based session) |
| `spatie/laravel-permission` | Manajemen role & permission (Mahasiswa, DPA, DSB, TU, Kajur, Sekretaris, Admin) |
| `barryvdh/laravel-dompdf` | Generate dokumen PDF (SK, berita acara, undangan) |
| `spatie/laravel-activitylog` | Audit log perubahan data |
| `spatie/laravel-medialibrary` | Manajemen file upload dengan versioning |
| `laravel/reverb` | WebSocket server untuk notifikasi realtime |
| `intervention/image` | Resize/compress gambar upload (jika diperlukan) |
| `maatwebsite/excel` | Export data ke Excel (Pasca-MVP) |

### Autentikasi & Otorisasi

- **Autentikasi:** Laravel Sanctum dengan mode SPA (cookie-based, bukan token header) untuk keamanan yang lebih baik pada browser.
- **Otorisasi:** Kombinasi `spatie/laravel-permission` untuk role-based access dan Laravel Policies untuk otorisasi per-resource.
- **Role yang didefinisikan:** `mahasiswa`, `dpa`, `dosen_pembimbing`, `tata_usaha`, `ketua_jurusan`, `sekretaris`, `admin`.

### Konvensi API

- Base URL: `/api/v1/`
- Format response standar:
  ```json
  {
    "success": true,
    "message": "...",
    "data": { ... }
  }
  ```
- Error response:
  ```json
  {
    "success": false,
    "message": "...",
    "errors": { ... }
  }
  ```
- Semua endpoint yang membutuhkan auth dilindungi middleware `auth:sanctum` + `role:nama_role`.

### File Upload

- Menggunakan `spatie/laravel-medialibrary` untuk mengelola file dengan fitur koleksi dan versioning.
- File disimpan di `storage/app/private/` dan tidak dapat diakses langsung via URL publik — harus melalui endpoint yang memvalidasi izin akses.
- Tipe file yang diizinkan: **PDF** (untuk laporan/berkas), **JPG/PNG** (jika diperlukan untuk dokumen tertentu).
- Ukuran maksimum per file: **10 MB** (dapat dikonfigurasi via Admin).

---

## 2. Frontend — Vue.js 3

### Versi & Prasyarat

| Item | Versi |
|---|---|
| Node.js | >= 20.x (LTS) |
| Vue.js | 3.x (Composition API) |
| Vite | 5.x |
| npm / pnpm | pnpm direkomendasikan |

### Arsitektur Frontend

Vue.js digunakan sebagai **Single Page Application (SPA)** yang terpisah dari backend Laravel. Komunikasi dilakukan sepenuhnya melalui REST API.

### Library & Package Frontend

| Package | Fungsi |
|---|---|
| `vue-router` | Routing SPA antar halaman |
| `pinia` | State management (menggantikan Vuex) |
| `axios` | HTTP client untuk komunikasi ke API Laravel |
| `@vueuse/core` | Utility composables (useStorage, useDark, dst.) |
| `tailwindcss` | Utility-first CSS framework |
| `shadcn-vue` | Komponen UI berbasis Radix Vue + Tailwind |
| `vee-validate` + `zod` | Validasi form di sisi frontend |
| `vue-sonner` | Toast notification |
| `lucide-vue-next` | Icon set |
| `@tanstack/vue-table` | Tabel data yang fleksibel (untuk list mahasiswa, rekap, dll.) |
| `laravel-echo` + `pusher-js` | Koneksi WebSocket untuk notifikasi realtime (via Reverb) |
| `dayjs` | Manipulasi tanggal (ringan, pengganti moment.js) |
| `nprogress` | Progress bar saat navigasi antar halaman |

### Struktur Direktori Frontend (Vue.js)

```
src/
├── assets/
├── components/
│   ├── ui/                ← Komponen generik (Button, Input, Modal, dst.)
│   ├── shared/            ← Komponen yang dipakai lintas fitur
│   └── [feature]/         ← Komponen spesifik per fitur
├── composables/           ← Custom composables (useAuth, useNotification, dst.)
├── layouts/
│   ├── AuthLayout.vue
│   ├── DashboardLayout.vue
│   └── GuestLayout.vue
├── pages/                 ← Halaman per fitur & per peran
│   ├── auth/
│   ├── mahasiswa/
│   ├── dosen/
│   ├── tata-usaha/
│   ├── ketua-jurusan/
│   ├── sekretaris/
│   └── admin/
├── router/
│   └── index.js           ← Route definitions + route guards
├── services/              ← API service layer (satu file per domain)
│   ├── auth.service.js
│   ├── pengajuan.service.js
│   ├── bimbingan.service.js
│   ├── seminar.service.js
│   └── ...
├── stores/                ← Pinia stores
│   ├── auth.store.js
│   ├── notification.store.js
│   └── ...
└── utils/                 ← Helper functions
```

### Route Guard & Per-Role Access

Vue Router dilengkapi navigation guard yang memvalidasi:
1. Apakah pengguna sudah login (redirect ke `/login` jika belum).
2. Apakah role pengguna sesuai dengan route yang dituju (redirect ke `/403` jika tidak berwenang).

Setiap halaman mendefinisikan `meta.roles` untuk menentukan role mana yang boleh mengaksesnya.

---

## 3. Database — MySQL 8

### Koneksi

Laravel terhubung ke MySQL menggunakan konfigurasi `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sita_db
DB_USERNAME=sita_user
DB_PASSWORD=...
```

### Konvensi Database

- Semua nama tabel menggunakan **snake_case** dan **plural** (sesuai konvensi Laravel).
- Primary key: `id` (ULID atau Auto-increment — ULID direkomendasikan untuk menghindari enumerable ID).
- Semua tabel menggunakan `timestamps` (`created_at`, `updated_at`) dan `softDeletes` (`deleted_at`) untuk tabel penting.
- Setiap migrasi ditulis atomik dan dapat di-rollback.

### Tabel Utama (MVP)

| Tabel | Keterangan |
|---|---|
| `users` | Akun semua peran |
| `mahasiswas` | Data tambahan mahasiswa |
| `dosens` | Data tambahan dosen |
| `pengajuan_juduls` | Riwayat pengajuan judul TA |
| `surat_keputusans` | SK pembimbing & penguji |
| `sesi_bimbingans` | Log buku bimbingan digital |
| `laporan_tas` | Versi laporan TA yang diupload |
| `pendaftaran_seminars` | Pendaftaran seminar (Proposal/Hasil/Munaqasyah) |
| `jadwal_seminars` | Jadwal dan ruangan seminar |
| `penguji_seminars` | Dosen penguji per jadwal seminar |
| `nilai_seminars` | Input nilai dari dosen |
| `notifikasis` | Notifikasi in-app |
| `activity_log` | Audit log (dikelola spatie/activitylog) |
| `media` | File upload (dikelola spatie/laravel-medialibrary) |

---

## 4. Realtime & Notifikasi

### Notifikasi In-App

- Menggunakan **Laravel Notifications** yang disimpan ke tabel `notifications` (database channel).
- Frontend membaca notifikasi via endpoint `/api/v1/notifications`.
- Notifikasi baru dipush ke frontend secara realtime menggunakan **Laravel Reverb** (WebSocket).

### WebSocket — Laravel Reverb

- Laravel Reverb digunakan sebagai WebSocket server yang di-host bersama aplikasi (tidak perlu layanan pihak ketiga seperti Pusher).
- Frontend terhubung menggunakan `laravel-echo` + `pusher-js` (kompatibel dengan Reverb).
- Setiap notifikasi baru meng-broadcast event ke channel private milik pengguna yang bersangkutan.

```
[Laravel Event] → [Reverb WebSocket Server] → [Laravel Echo di Vue] → [Update UI]
```

---

## 5. Queue & Background Jobs

- **Driver:** Database (MVP) — job disimpan di tabel `jobs`, tidak membutuhkan Redis di fase awal.
- **Worker:** Dijalankan dengan `php artisan queue:work` sebagai background process (via Supervisor di VPS).
- **Pasca-MVP:** Bisa diupgrade ke Redis untuk performa yang lebih baik jika beban meningkat.

**Job yang digunakan di MVP:**

| Job | Trigger |
|---|---|
| `SendNotificationJob` | Setiap perubahan status yang memicu notifikasi |
| `GenerateSKJob` | Generate dokumen SK saat diterbitkan Kajur |
| `SendEmailNotificationJob` | Email otomatis (jika diaktifkan) |

---

## 6. Build & Tooling

### Build Tool

- **Vite 5** digunakan sebagai bundler untuk frontend Vue.js (jauh lebih cepat dari Webpack).
- Mode development: `vite dev` dengan hot module replacement (HMR).
- Mode production: `vite build` menghasilkan aset statis di folder `public/` atau dideploy terpisah.

### Code Quality

| Tool | Fungsi |
|---|---|
| **ESLint** + `eslint-plugin-vue` | Linting kode JavaScript/Vue |
| **Prettier** | Auto-formatting kode |
| **PHP CS Fixer** / **Pint** | Formatting kode PHP (Laravel Pint sudah built-in di Laravel 11) |
| **Larastan** (PHPStan) | Static analysis untuk kode PHP |

### Testing

| Layer | Tool |
|---|---|
| Backend Unit & Feature Test | **Pest** (wrapper modern di atas PHPUnit) |
| Frontend Unit Test | **Vitest** |
| Frontend Component Test | **Vue Test Utils** |
| API Test | Pest HTTP tests (bawaan Laravel) |

---

## 7. Deployment

### Target Deployment (MVP)

- **Server:** VPS Linux (Ubuntu 22.04 LTS) atau Shared Hosting yang mendukung PHP 8.3.
- **Web Server:** Nginx + PHP-FPM (direkomendasikan) atau Apache.
- **Process Manager:** Supervisor untuk mengelola queue worker dan Reverb WebSocket server.

### Struktur Deployment

```
Server
├── /var/www/sita/              ← Root aplikasi Laravel
│   ├── public/                 ← Document root Nginx
│   └── ...
├── Nginx config                ← Reverse proxy ke PHP-FPM + Reverb
├── Supervisor config           ← Kelola queue:work & reverb:start
└── MySQL 8                     ← Database server
```

### Environment & Konfigurasi Penting

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sita.domain.ac.id

DB_CONNECTION=mysql
CACHE_STORE=database        # atau redis jika tersedia
QUEUE_CONNECTION=database   # atau redis pasca-MVP
SESSION_DRIVER=database

BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...

FILESYSTEM_DISK=local       # atau s3-compatible pasca-MVP
MAX_UPLOAD_SIZE=10240       # 10 MB dalam KB

MAIL_MAILER=smtp            # Untuk notifikasi email (opsional di MVP)
```

### HTTPS

- Sertifikat SSL **wajib** menggunakan **Let's Encrypt** (gratis via Certbot) atau sertifikat dari institusi.
- Seluruh traffic HTTP di-redirect ke HTTPS.
- Cookie Sanctum dikonfigurasi `secure: true` dan `same_site: lax`.

---

## 8. Keamanan

| Aspek | Implementasi |
|---|---|
| Autentikasi | Laravel Sanctum (SPA cookie-based) |
| Otorisasi | Spatie Permission + Laravel Policies |
| CSRF Protection | Bawaan Laravel (aktif untuk SPA via Sanctum) |
| SQL Injection | Eloquent ORM + Query Builder (parameterized query) |
| XSS | Vue.js auto-escaping + CSP header |
| File Upload Validation | MIME type check + ukuran maksimum |
| Rate Limiting | Laravel Rate Limiter pada endpoint login & upload |
| Audit Trail | Spatie Activity Log |
| Password | Bcrypt (Laravel default) + minimum 8 karakter |

---

## 9. Keputusan Desain & Alasan Pemilihan Stack

| Keputusan | Alasan |
|---|---|
| **Laravel** sebagai backend | Mature, dokumentasi lengkap, ekosistem package yang kaya, cocok untuk sistem CRUD-heavy dengan aturan bisnis kompleks |
| **Vue.js 3** sebagai frontend | Composition API lebih terstruktur untuk fitur kompleks; learning curve lebih rendah dibanding React untuk tim yang sudah familiar PHP/Laravel |
| **Sanctum** bukan JWT | Lebih aman untuk SPA (cookie httpOnly vs localStorage), lebih sederhana untuk diimplementasikan, sudah built-in di Laravel |
| **Pinia** bukan Vuex | Lebih simpel, TypeScript-friendly, dan merupakan state manager resmi yang direkomendasikan untuk Vue 3 |
| **MySQL** bukan PostgreSQL | Lebih umum di hosting Indonesia, familiar bagi kebanyakan developer, cukup untuk kebutuhan SITA |
| **Reverb** bukan Pusher | Tidak ada biaya tambahan layanan pihak ketiga; self-hosted; cocok untuk skala jurusan |
| **Database Queue** bukan Redis (MVP) | Mengurangi kompleksitas setup di fase awal; bisa diupgrade ke Redis jika diperlukan |
| **DomPDF** untuk PDF | Paling mudah diintegrasikan dengan Laravel; cukup untuk dokumen sederhana (SK, berita acara) |
| **Spatie Media Library** | Menangani versioning file dengan bersih; mendukung multiple koleksi per model |

---

## 10. Yang Belum Diputuskan (Perlu Diskusi)

| Item | Opsi yang Tersedia |
|---|---|
| Hosting/server institusi | VPS mandiri vs hosting institusi kampus — perlu konfirmasi infrastruktur yang tersedia |
| Storage file produksi | Local disk vs object storage (MinIO/S3-compatible) untuk skalabilitas |
| Email provider | SMTP institusi vs layanan transaksional (Mailgun, Resend) |
| Upgrade Redis | Kapan dan apakah diperlukan bergantung pada beban pengguna nyata |
| TypeScript di frontend | Bisa ditambahkan untuk keamanan tipe — keputusan disesuaikan dengan kapabilitas tim |

---

*Dokumen ini bersifat living document dan dapat diperbarui seiring perkembangan proyek. Versi: 1.0*
