# Timeline Pengerjaan — SITA (2 Hari Sprint Demo)

> **Konteks:** Solo developer, 12+ jam/hari, tujuan: **demo ke dosen/jurusan**.
>
> ⚠️ **Catatan Penting — Baca Dulu:**
> MVP penuh (9 modul, 7 peran) **tidak mungkin selesai dalam 2 hari** oleh satu orang. Dokumen ini dirancang untuk menghasilkan **demo yang meyakinkan dan fungsional** — bukan sistem production-ready. Strateginya adalah: buat alur inti berjalan end-to-end, sisanya bisa diperlihatkan sebagai UI mockup atau fitur "coming soon". Dosen/jurusan menilai **pemahaman sistem dan alur logika**, bukan kelengkapan fitur.

---

## Strategi Demo

**Yang HARUS jalan nyata (end-to-end):**
- Login multi-peran (Mahasiswa, DPA, TU, Admin)
- Mahasiswa mengajukan judul → DPA review & setujui → notifikasi masuk
- Mahasiswa mendaftar Seminar Proposal → TU verifikasi berkas

**Yang boleh jadi UI saja (dummy/hardcoded):**
- Penjadwalan seminar oleh TU
- Penerbitan SK oleh Ketua Jurusan
- Input nilai dosen
- Dashboard statistik
- Semua fitur Pasca-MVP

**Yang boleh diskip total:**
- Generate PDF dokumen
- WebSocket realtime (notifikasi cukup polling)
- Export Excel
- Manajemen pengguna Admin (cukup seeder)

---

## Hari 1 — Pondasi & Alur Backend (Target: API inti jalan)

### Blok 1 — Setup & Fondasi (Jam 1–3)

**Target:** Project siap dikoding, database terhubung, auth berjalan.

- [ ] Buat project Laravel 11 baru (`laravel new sita-backend`)
- [ ] Buat project Vue 3 baru (`npm create vue@latest sita-frontend`)
- [ ] Setup database MySQL, buat DB `sita_db`
- [ ] Install package backend prioritas:
  - `laravel/sanctum`
  - `spatie/laravel-permission`
  - `spatie/laravel-activitylog`
- [ ] Konfigurasi CORS (`config/cors.php`) agar Vue bisa hit API
- [ ] Setup Sanctum untuk SPA auth (konfigurasi `stateful domains`)
- [ ] Buat migrasi & seeder:
  - Tabel `users` (tambah kolom `role`)
  - Tabel `mahasiswas` (nim, angkatan, program_studi, dpa_id)
  - Tabel `dosens` (nidn, bidang_keahlian)
  - Seeder: 1 admin, 2 mahasiswa, 1 DPA, 1 TU, 1 Kajur
- [ ] Test login via Postman/Insomnia → dapat cookie session

---

### Blok 2 — Modul Pengajuan Judul (Jam 3–6)

**Target:** Mahasiswa bisa ajukan judul, DPA bisa approve/tolak via API.

**Migrasi:**
- [ ] Tabel `pengajuan_juduls`: `id`, `mahasiswa_id`, `judul`, `topik`, `bidang`, `latar_belakang`, `file_proposal`, `status` (enum: draft/diajukan/perlu_revisi/disetujui/ditolak), `catatan_dpa`, `timestamps`

**Models & Relations:**
- [ ] Model `PengajuanJudul` dengan relasi ke `User` (mahasiswa) dan `User` (DPA)

**Controllers & Routes:**
- [ ] `POST /api/v1/pengajuan-judul` — mahasiswa submit judul
- [ ] `GET /api/v1/pengajuan-judul` — list (filter by role: mahasiswa lihat milik sendiri, DPA lihat dari mahasiswa perwalian)
- [ ] `GET /api/v1/pengajuan-judul/{id}` — detail
- [ ] `PATCH /api/v1/pengajuan-judul/{id}/review` — DPA beri keputusan
- [ ] Upload file PDF proposal (simpan ke `storage/app/private/proposals/`)

**Validasi:**
- [ ] Form Request untuk submit & review
- [ ] Middleware: hanya mahasiswa bisa submit, hanya DPA bisa review

---

### Blok 3 — Modul Pendaftaran Seminar Proposal (Jam 6–9)

**Target:** Mahasiswa bisa daftar seminar, TU bisa verifikasi berkas.

**Migrasi:**
- [ ] Tabel `pendaftaran_seminars`: `id`, `mahasiswa_id`, `jenis_seminar` (enum: proposal/hasil/munaqasyah), `status` (enum: diajukan/perlu_revisi/diverifikasi/dijadwalkan/selesai), `catatan_tu`, `timestamps`
- [ ] Tabel `berkas_seminars`: `id`, `pendaftaran_id`, `nama_berkas`, `file_path`, `status_verifikasi`

**Controllers & Routes:**
- [ ] `POST /api/v1/seminar/pendaftaran` — mahasiswa daftar seminar
- [ ] `POST /api/v1/seminar/pendaftaran/{id}/berkas` — upload berkas
- [ ] `GET /api/v1/seminar/pendaftaran` — list (filter by role)
- [ ] `PATCH /api/v1/seminar/pendaftaran/{id}/verifikasi` — TU verifikasi

---

### Blok 4 — Notifikasi & Polish API (Jam 9–11)

**Target:** Notifikasi in-app dasar berjalan, semua endpoint terproteksi.

- [ ] Buat tabel `notifikasis` (atau pakai Laravel built-in database notifications)
- [ ] Buat `NotificationHelper` untuk push notif ke user tertentu
- [ ] Trigger notifikasi:
  - Judul diajukan → notif ke DPA
  - DPA review → notif ke Mahasiswa
  - Mahasiswa daftar seminar → notif ke TU
  - TU verifikasi → notif ke Mahasiswa
- [ ] `GET /api/v1/notifications` — list notif user aktif
- [ ] `PATCH /api/v1/notifications/{id}/read` — tandai sudah dibaca
- [ ] Review semua middleware & route protection
- [ ] Test seluruh alur di Postman (pastikan tidak ada 500 error)

---

### Blok 5 — Istirahat & Evaluasi Hari 1 (Jam 11–12)

- [ ] Catat bug/issue yang belum selesai
- [ ] Prioritaskan ulang untuk Hari 2
- [ ] **Pastikan:** Login, pengajuan judul, verifikasi seminar, dan notifikasi sudah jalan via API

---

## Hari 2 — Frontend Vue.js & Persiapan Demo

### Blok 6 — Setup Frontend & Auth (Jam 1–2)

**Target:** Vue app bisa login, session tersimpan, routing per role berjalan.

- [ ] Install dependencies: `vue-router`, `pinia`, `axios`, `tailwindcss`, `shadcn-vue`, `lucide-vue-next`
- [ ] Setup Axios instance dengan `withCredentials: true` (untuk Sanctum cookie)
- [ ] Buat `auth.store.js` (Pinia): state user, login, logout, fetchUser
- [ ] Buat halaman Login (`/login`) dengan form NIM/email + password
- [ ] Setup Vue Router dengan navigation guard berbasis role
- [ ] Layout: `DashboardLayout.vue` (sidebar + header + slot konten)
- [ ] Sidebar dinamis — menu berubah sesuai role yang login

---

### Blok 7 — Halaman Mahasiswa (Jam 2–5)

**Target:** Alur mahasiswa bisa didemonstrasikan secara visual.

**Halaman yang dibuat:**
- [ ] `/mahasiswa/dashboard` — progress bar tahapan TA + ringkasan status
- [ ] `/mahasiswa/pengajuan-judul` — form pengajuan + list riwayat pengajuan dengan badge status
- [ ] `/mahasiswa/bimbingan` — UI log bimbingan (boleh dummy data untuk demo)
- [ ] `/mahasiswa/seminar/daftar` — form pendaftaran Seminar Proposal + upload berkas
- [ ] `/mahasiswa/seminar/status` — status verifikasi + jadwal (jika sudah ada)
- [ ] Komponen `NotificationBell.vue` — ikon lonceng + dropdown list notifikasi

---

### Blok 8 — Halaman DPA & TU (Jam 5–8)

**Target:** DPA bisa review judul, TU bisa verifikasi — alur demo end-to-end selesai.

**Halaman DPA:**
- [ ] `/dpa/dashboard` — daftar mahasiswa perwalian + status tahapan
- [ ] `/dpa/pengajuan-judul` — list pengajuan masuk dengan filter status
- [ ] `/dpa/pengajuan-judul/{id}` — detail + tombol Setujui / Perlu Revisi / Tolak + form catatan

**Halaman TU:**
- [ ] `/tu/dashboard` — antrian pendaftaran seminar
- [ ] `/tu/seminar/verifikasi` — list pendaftaran masuk
- [ ] `/tu/seminar/verifikasi/{id}` — detail berkas + checklist + tombol Verifikasi / Kembalikan

---

### Blok 9 — Halaman Admin, Kajur & Sekretaris (Jam 8–9)

**Target:** Peran pendukung punya tampilan yang representatif untuk demo — boleh sebagian dummy.

- [ ] `/admin/pengguna` — tabel list user + badge role (data dari seeder, CRUD opsional)
- [ ] `/kajur/dashboard` — daftar mahasiswa TA aktif + tombol "Terbitkan SK" (UI saja, boleh dummy)
- [ ] `/sekretaris/seminar` — rekap jadwal seminar (UI saja, data hardcoded untuk demo)

> **Tips demo:** Halaman ini cukup terlihat rapi dan berisi data. Dosen tidak akan mencoba klik semua tombol — mereka menilai konsep dan alur.

---

### Blok 10 — Polish UI & Persiapan Demo (Jam 9–11)

**Target:** Aplikasi terlihat profesional dan siap dipresentasikan.

- [ ] Pastikan semua halaman responsive (minimal di layar laptop)
- [ ] Loading state pada semua tombol aksi (spinner saat API call)
- [ ] Toast notification untuk aksi berhasil/gagal (`vue-sonner`)
- [ ] Empty state yang rapi saat data kosong
- [ ] Pesan error yang informatif (bukan raw JSON)
- [ ] Favicon dan judul halaman "SITA — Sistem Informasi Tugas Akhir"
- [ ] **Siapkan skenario demo:** login sebagai masing-masing peran, jalankan alur inti dari awal sampai akhir tanpa error

---

### Blok 11 — Uji Coba & Backup (Jam 11–12)

- [ ] Jalankan full demo simulation sendirian minimal 2 kali end-to-end
- [ ] Catat titik-titik yang mungkin ditanyakan dosen (jawaban teknis)
- [ ] Screenshot / screen record alur sebagai backup jika ada masalah teknis saat demo
- [ ] Siapkan slide singkat (3–5 slide) sebagai pengantar sebelum live demo:
  - Latar belakang & masalah yang diselesaikan
  - Peran pengguna
  - Arsitektur sistem (dari `techstack.md`)
  - Demo live

---

## Ringkasan Target per Hari

### Hari 1 — End of Day Checklist
- [ ] ✅ Laravel API jalan, database termigrasi, seeder terisi
- [ ] ✅ Login/logout via Sanctum berfungsi
- [ ] ✅ Pengajuan judul: submit, review DPA, notifikasi — end-to-end via API
- [ ] ✅ Pendaftaran seminar: submit, upload berkas, verifikasi TU — end-to-end via API
- [ ] ✅ Semua endpoint terproteksi middleware role

### Hari 2 — End of Day Checklist
- [ ] ✅ Frontend Vue bisa login dan routing per role berjalan
- [ ] ✅ Halaman mahasiswa: pengajuan judul + daftar seminar — connect ke API nyata
- [ ] ✅ Halaman DPA: review judul — connect ke API nyata
- [ ] ✅ Halaman TU: verifikasi berkas — connect ke API nyata
- [ ] ✅ Halaman Admin/Kajur/Sekretaris: tampil rapi (boleh sebagian dummy)
- [ ] ✅ Notifikasi in-app muncul di UI
- [ ] ✅ Full demo bisa dijalankan end-to-end tanpa error

---

## Yang Realistis Disampaikan ke Dosen Saat Demo

Jika dosen bertanya kenapa fitur X belum jalan, jawaban yang tepat:

> *"Fitur ini sudah dirancang di dokumen spesifikasi dan database, namun sengaja diprioritaskan setelah alur inti stabil — sesuai pendekatan MVP yang kami gunakan. Berikutnya akan dikerjakan di Fase 2."*

Ini menunjukkan **pemahaman sistem dan manajemen prioritas** — justru nilai plus di mata dosen.

---

## Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| API tidak selesai di Hari 1 | Potong fitur notifikasi, fokus ke pengajuan judul saja |
| Frontend tidak sempat connect ke API | Gunakan dummy data (`ref([...])`) agar UI tetap bisa didemonstrasikan |
| Bug muncul saat demo | Siapkan video rekaman sebagai backup |
| Waktu habis sebelum polish | UI cukup rapi, alur fungsional lebih penting dari estetika |
| Pertanyaan teknis detail | Jawab dengan merujuk ke dokumen (`techstack.md`, `db.md`, `mvp.md`) yang sudah disiapkan |

---

*2 hari adalah sprint yang sangat ketat untuk sistem sekompleks SITA. Dokumen ini dirancang agar Anda keluar dari demo dengan hasil yang meyakinkan — bukan sistem sempurna, tapi sistem yang menunjukkan pemahaman mendalam dan eksekusi yang terencana.*
