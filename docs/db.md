# Rancangan Database — SITA (Sistem Informasi Tugas Akhir)

> Rancangan ini menggunakan konvensi **PostgreSQL**. Semua tabel menggunakan `UUID` sebagai primary key, `snake_case` untuk penamaan kolom, dan `TIMESTAMPTZ` untuk semua kolom waktu. Relasi antar tabel menggunakan foreign key eksplisit.

---

## Daftar Isi

1. [Diagram Relasi Entitas (ERD Overview)](#1-diagram-relasi-entitas-erd-overview)
2. [Kelompok: Pengguna & Autentikasi](#2-kelompok-pengguna--autentikasi)
3. [Kelompok: Pengajuan Judul & SK](#3-kelompok-pengajuan-judul--sk)
4. [Kelompok: Bimbingan TA](#4-kelompok-bimbingan-ta)
5. [Kelompok: Seminar & Penjadwalan](#5-kelompok-seminar--penjadwalan)
6. [Kelompok: Penilaian](#6-kelompok-penilaian)
7. [Kelompok: Dokumen Resmi](#7-kelompok-dokumen-resmi)
8. [Kelompok: Notifikasi](#8-kelompok-notifikasi)
9. [Kelompok: Konfigurasi Sistem](#9-kelompok-konfigurasi-sistem)
10. [Kelompok: Audit & Log](#10-kelompok-audit--log)
11. [Ringkasan Semua Tabel](#11-ringkasan-semua-tabel)
12. [Enum & Konstanta](#12-enum--konstanta)
13. [Indeks](#13-indeks)
14. [Catatan Desain](#14-catatan-desain)

---

## 1. Diagram Relasi Entitas (ERD Overview)

```
┌─────────────┐        ┌──────────────┐        ┌─────────────┐
│    users    │───1:1──│   mahasiswas │        │    dosens   │
│             │───1:1──│              │        │             │
└─────────────┘        └──────┬───────┘        └──────┬──────┘
       │                      │                       │
       │               ┌──────┴───────┐               │
       │               │pengajuan_    │               │
       │               │   juduls     │               │
       │               └──────┬───────┘               │
       │                      │ (disetujui)            │
       │               ┌──────┴───────┐               │
       │               │sk_pembimbing │───────────────►│
       │               └──────┬───────┘               │
       │                      │                       │
       │               ┌──────┴───────┐               │
       │               │sesi_bimbing  │◄──────────────┤
       │               │    ans       │               │
       │               └──────┬───────┘               │
       │                      │                       │
       │               ┌──────┴───────┐               │
       │               │  laporan_tas │               │
       │               └──────┬───────┘               │
       │                      │                       │
       │               ┌──────┴───────┐               │
       │               │pendaftaran_  │               │
       │               │  seminars    │               │
       │               └──────┬───────┘               │
       │                      │                       │
       │               ┌──────┴───────┐               │
       │               │jadwal_seminar│               │
       │               │     s        │               │
       │               └──────┬───────┘               │
       │                      │                       │
       │         ┌────────────┼───────────┐           │
       │         │            │           │           │
       │    ┌────┴─────┐ ┌───┴────┐ ┌────┴──────┐    │
       │    │jadwal_   │ │berkas_ │ │penguji_   │◄──┤
       │    │pengujinya│ │seminar │ │seminars   │    │
       │    └──────────┘ └────────┘ └───────────┘    │
       │                      │                       │
       │               ┌──────┴───────┐               │
       │               │nilai_seminar │◄──────────────┘
       │               │     s        │
       │               └──────────────┘
       │
  ┌────┴──────────────────────────────────┐
  │            notifikasis                │
  └───────────────────────────────────────┘
```

---

## 2. Kelompok: Pengguna & Autentikasi

### Tabel `users`

Tabel pusat untuk semua pengguna sistem, terlepas dari perannya.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | Primary key, default `gen_random_uuid()` |
| `nama_lengkap` | `VARCHAR(150)` NOT NULL | Nama lengkap pengguna |
| `email` | `VARCHAR(150)` UNIQUE NOT NULL | Email untuk login & notifikasi |
| `password_hash` | `TEXT` NOT NULL | Hash bcrypt dari password |
| `role` | `user_role` ENUM NOT NULL | Lihat [Enum user_role](#enum-user_role) |
| `is_active` | `BOOLEAN` NOT NULL DEFAULT `true` | Akun aktif atau dinonaktifkan |
| `avatar_url` | `TEXT` | URL foto profil (opsional) |
| `last_login_at` | `TIMESTAMPTZ` | Waktu login terakhir |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |
| `updated_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `password_reset_tokens`

Menyimpan token sementara untuk proses reset password via email.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `user_id` | `UUID` FK → `users.id` | |
| `token_hash` | `TEXT` NOT NULL | Hash dari token yang dikirim ke email |
| `expires_at` | `TIMESTAMPTZ` NOT NULL | Token kedaluwarsa (misal: 1 jam) |
| `used_at` | `TIMESTAMPTZ` | Null jika belum digunakan |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `mahasiswas`

Data spesifik mahasiswa, berelasi 1:1 dengan `users`.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `user_id` | `UUID` UNIQUE FK → `users.id` | |
| `nim` | `VARCHAR(20)` UNIQUE NOT NULL | Nomor Induk Mahasiswa |
| `angkatan` | `SMALLINT` NOT NULL | Tahun masuk, contoh: `2021` |
| `program_studi` | `VARCHAR(100)` NOT NULL | Nama program studi |
| `dpa_id` | `UUID` FK → `dosens.id` | Dosen Pembimbing Akademik yang ditugaskan |
| `tahap_ta_saat_ini` | `tahap_ta` ENUM | Tahapan TA terkini mahasiswa |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |
| `updated_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `dosens`

Data spesifik dosen, berelasi 1:1 dengan `users`.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `user_id` | `UUID` UNIQUE FK → `users.id` | |
| `nidn` | `VARCHAR(20)` UNIQUE NOT NULL | Nomor Induk Dosen Nasional |
| `bidang_keahlian` | `TEXT` | Bidang keahlian dosen |
| `kuota_bimbingan` | `SMALLINT` NOT NULL DEFAULT `5` | Maks. mahasiswa bimbingan aktif |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |
| `updated_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

## 3. Kelompok: Pengajuan Judul & SK

### Tabel `pengajuan_juduls`

Menyimpan semua pengajuan judul TA dari mahasiswa, termasuk riwayat revisi (satu baris per pengajuan).

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `mahasiswa_id` | `UUID` FK → `mahasiswas.id` NOT NULL | |
| `dpa_id` | `UUID` FK → `dosens.id` NOT NULL | DPA yang mereview |
| `judul` | `TEXT` NOT NULL | Judul TA yang diajukan |
| `bidang` | `VARCHAR(100)` | Bidang penelitian |
| `topik` | `VARCHAR(150)` | Topik spesifik |
| `latar_belakang` | `TEXT` | Latar belakang singkat |
| `metode_penelitian` | `TEXT` | Metode yang direncanakan |
| `file_draft_proposal` | `TEXT` NOT NULL | Path/URL file draft proposal (PDF) |
| `status` | `status_pengajuan` ENUM NOT NULL DEFAULT `'diajukan'` | Lihat [Enum](#enum-status_pengajuan) |
| `catatan_dpa` | `TEXT` | Catatan revisi atau alasan penolakan dari DPA |
| `nomor_revisi` | `SMALLINT` NOT NULL DEFAULT `0` | Berapa kali sudah direvisi dan diajukan ulang |
| `diputuskan_at` | `TIMESTAMPTZ` | Waktu DPA memberikan keputusan |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | Tanggal pengajuan |
| `updated_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `sk_pembimbings`

Menyimpan Surat Keputusan Pembimbing TA yang diterbitkan Ketua Jurusan.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `mahasiswa_id` | `UUID` UNIQUE FK → `mahasiswas.id` NOT NULL | 1 mahasiswa = 1 SK aktif |
| `dosen_pembimbing_id` | `UUID` FK → `dosens.id` NOT NULL | Dosen yang ditugaskan sebagai pembimbing |
| `pengajuan_judul_id` | `UUID` FK → `pengajuan_juduls.id` NOT NULL | Judul yang menjadi dasar SK |
| `nomor_sk` | `VARCHAR(100)` UNIQUE NOT NULL | Nomor surat resmi SK |
| `tanggal_terbit` | `DATE` NOT NULL | Tanggal SK diterbitkan |
| `diterbitkan_oleh` | `UUID` FK → `users.id` NOT NULL | Ketua Jurusan yang menerbitkan |
| `file_sk` | `TEXT` | Path/URL file SK yang digenerate |
| `is_aktif` | `BOOLEAN` NOT NULL DEFAULT `true` | Untuk menandai jika SK diganti |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

## 4. Kelompok: Bimbingan TA

### Tabel `sesi_bimbingans`

Log digital setiap sesi bimbingan yang dicatat mahasiswa.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `mahasiswa_id` | `UUID` FK → `mahasiswas.id` NOT NULL | |
| `dosen_id` | `UUID` FK → `dosens.id` NOT NULL | Dosen pembimbing yang hadir |
| `sk_pembimbing_id` | `UUID` FK → `sk_pembimbings.id` NOT NULL | SK yang mendasari bimbingan ini |
| `tanggal_sesi` | `DATE` NOT NULL | Tanggal bimbingan berlangsung |
| `topik_bahasan` | `TEXT` NOT NULL | Topik yang dibahas dalam sesi |
| `catatan_mahasiswa` | `TEXT` | Catatan dari perspektif mahasiswa |
| `catatan_dosen` | `TEXT` | Feedback / catatan dari dosen |
| `status` | `status_sesi_bimbingan` ENUM NOT NULL DEFAULT `'dicatat'` | Lihat [Enum](#enum-status_sesi_bimbingan) |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |
| `updated_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `laporan_tas`

Versi-versi laporan TA yang diupload mahasiswa. Setiap upload baru = baris baru (versioning).

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `mahasiswa_id` | `UUID` FK → `mahasiswas.id` NOT NULL | |
| `sk_pembimbing_id` | `UUID` FK → `sk_pembimbings.id` NOT NULL | |
| `nomor_versi` | `SMALLINT` NOT NULL | Versi ke-N dari laporan ini |
| `judul_versi` | `VARCHAR(200)` | Label singkat versi (opsional) |
| `file_laporan` | `TEXT` NOT NULL | Path/URL file laporan (PDF) |
| `catatan_upload` | `TEXT` | Catatan dari mahasiswa saat upload |
| `status_acc` | `status_acc` ENUM NOT NULL DEFAULT `'menunggu'` | Lihat [Enum](#enum-status_acc) |
| `catatan_dosen` | `TEXT` | Feedback dosen setelah mereview |
| `direview_oleh` | `UUID` FK → `dosens.id` | Dosen yang memberi ACC/revisi |
| `direview_at` | `TIMESTAMPTZ` | Waktu ACC/revisi diberikan |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

> **Catatan:** Gunakan `UNIQUE(mahasiswa_id, nomor_versi)` untuk memastikan tidak ada duplikasi nomor versi per mahasiswa.

---

### Tabel `acc_seminars`

Menyimpan persetujuan (ACC) dari dosen pembimbing agar mahasiswa boleh mendaftar ke seminar tertentu.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `mahasiswa_id` | `UUID` FK → `mahasiswas.id` NOT NULL | |
| `dosen_id` | `UUID` FK → `dosens.id` NOT NULL | Dosen pembimbing yang memberi ACC |
| `jenis_seminar` | `jenis_seminar` ENUM NOT NULL | Proposal / Hasil / Munaqasyah |
| `laporan_id` | `UUID` FK → `laporan_tas.id` NOT NULL | Versi laporan yang di-ACC |
| `diberikan_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |
| `catatan` | `TEXT` | Catatan tambahan dari dosen |

> **Constraint:** `UNIQUE(mahasiswa_id, jenis_seminar)` — satu ACC per jenis seminar per mahasiswa.

---

## 5. Kelompok: Seminar & Penjadwalan

### Tabel `pendaftaran_seminars`

Satu baris per pendaftaran seminar oleh mahasiswa. Digunakan untuk ketiga jenis seminar.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `mahasiswa_id` | `UUID` FK → `mahasiswas.id` NOT NULL | |
| `jenis_seminar` | `jenis_seminar` ENUM NOT NULL | Proposal / Hasil / Munaqasyah |
| `acc_seminar_id` | `UUID` FK → `acc_seminars.id` NOT NULL | ACC yang menjadi syarat daftar |
| `status` | `status_pendaftaran` ENUM NOT NULL DEFAULT `'diajukan'` | Lihat [Enum](#enum-status_pendaftaran) |
| `catatan_tu` | `TEXT` | Keterangan berkas kurang atau alasan penolakan TU |
| `diverifikasi_oleh` | `UUID` FK → `users.id` | TU yang memverifikasi |
| `diverifikasi_at` | `TIMESTAMPTZ` | |
| `nomor_pendaftaran` | `VARCHAR(50)` UNIQUE | Nomor registrasi yang digenerate sistem |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | Tanggal mendaftar |
| `updated_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `berkas_pendaftarans`

Menyimpan setiap file berkas yang diupload mahasiswa saat mendaftar seminar.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `pendaftaran_id` | `UUID` FK → `pendaftaran_seminars.id` NOT NULL | |
| `syarat_berkas_id` | `UUID` FK → `syarat_berkass.id` NOT NULL | Jenis berkas yang diupload |
| `file_url` | `TEXT` NOT NULL | Path/URL file yang diupload |
| `nama_file_asli` | `VARCHAR(255)` | Nama file asli dari mahasiswa |
| `ukuran_bytes` | `INTEGER` | Ukuran file dalam bytes |
| `status_verifikasi` | `status_verifikasi_berkas` ENUM NOT NULL DEFAULT `'belum_diperiksa'` | |
| `catatan_verifikasi` | `TEXT` | Catatan TU jika berkas ditolak |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `jadwal_seminars`

Jadwal seminar yang dibuat oleh TU setelah berkas terverifikasi.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `pendaftaran_id` | `UUID` UNIQUE FK → `pendaftaran_seminars.id` NOT NULL | 1 pendaftaran = 1 jadwal |
| `jenis_seminar` | `jenis_seminar` ENUM NOT NULL | Redundan untuk kemudahan query |
| `tanggal` | `DATE` NOT NULL | Tanggal pelaksanaan |
| `waktu_mulai` | `TIME` NOT NULL | |
| `waktu_selesai` | `TIME` NOT NULL | |
| `ruangan` | `VARCHAR(100)` NOT NULL | Nama/kode ruangan |
| `dibuat_oleh` | `UUID` FK → `users.id` NOT NULL | TU yang membuat jadwal |
| `disetujui_kajur` | `BOOLEAN` NOT NULL DEFAULT `false` | Khusus Munaqasyah, butuh persetujuan Kajur |
| `disetujui_at` | `TIMESTAMPTZ` | |
| `sk_penguji_id` | `UUID` FK → `sk_pengujis.id` | SK Penguji yang terkait |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |
| `updated_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `penguji_seminars`

Tabel pivot yang menyimpan daftar dosen penguji yang ditugaskan ke satu jadwal seminar.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `jadwal_id` | `UUID` FK → `jadwal_seminars.id` NOT NULL | |
| `dosen_id` | `UUID` FK → `dosens.id` NOT NULL | Dosen penguji yang ditugaskan |
| `peran_penguji` | `peran_penguji` ENUM NOT NULL | Ketua / Anggota / Pembimbing |
| `status_konfirmasi` | `status_konfirmasi` ENUM NOT NULL DEFAULT `'menunggu'` | |
| `dikonfirmasi_at` | `TIMESTAMPTZ` | |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

> **Constraint:** `UNIQUE(jadwal_id, dosen_id)` — satu dosen tidak boleh masuk dua kali di jadwal yang sama.

---

### Tabel `sk_pengujis`

Surat Keputusan Penguji per seminar, diterbitkan Ketua Jurusan.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `jadwal_id` | `UUID` UNIQUE FK → `jadwal_seminars.id` NOT NULL | |
| `jenis_seminar` | `jenis_seminar` ENUM NOT NULL | |
| `nomor_sk` | `VARCHAR(100)` UNIQUE NOT NULL | |
| `tanggal_terbit` | `DATE` NOT NULL | |
| `diterbitkan_oleh` | `UUID` FK → `users.id` NOT NULL | Ketua Jurusan |
| `file_sk` | `TEXT` | Path/URL file SK |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

## 6. Kelompok: Penilaian

### Tabel `komponen_nilais`

Master data komponen penilaian per jenis seminar, dikonfigurasi oleh Admin.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `jenis_seminar` | `jenis_seminar` ENUM NOT NULL | Berlaku untuk seminar mana |
| `nama_komponen` | `VARCHAR(150)` NOT NULL | Contoh: "Penguasaan Materi", "Sistematika" |
| `bobot_persen` | `NUMERIC(5,2)` NOT NULL | Bobot dalam persen (total per jenis = 100) |
| `deskripsi` | `TEXT` | Penjelasan kriteria penilaian |
| `is_aktif` | `BOOLEAN` NOT NULL DEFAULT `true` | |
| `urutan` | `SMALLINT` NOT NULL DEFAULT `0` | Urutan tampil di form penilaian |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `nilai_seminars`

Nilai yang diinput dosen (pembimbing maupun penguji) per komponen penilaian.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `jadwal_id` | `UUID` FK → `jadwal_seminars.id` NOT NULL | |
| `dosen_id` | `UUID` FK → `dosens.id` NOT NULL | Dosen yang menginput nilai |
| `komponen_id` | `UUID` FK → `komponen_nilais.id` NOT NULL | |
| `nilai` | `NUMERIC(5,2)` NOT NULL | Nilai 0–100 |
| `catatan` | `TEXT` | Catatan penilaian per komponen |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |
| `updated_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

> **Constraint:** `UNIQUE(jadwal_id, dosen_id, komponen_id)` — satu dosen tidak menginput komponen yang sama dua kali.

---

### Tabel `rekap_nilai_seminars`

Rekap nilai akhir per seminar per mahasiswa, dihitung sistem setelah semua nilai masuk.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `jadwal_id` | `UUID` UNIQUE FK → `jadwal_seminars.id` NOT NULL | |
| `mahasiswa_id` | `UUID` FK → `mahasiswas.id` NOT NULL | |
| `jenis_seminar` | `jenis_seminar` ENUM NOT NULL | |
| `nilai_rata_rata` | `NUMERIC(5,2)` | Rata-rata tertimbang dari semua penguji |
| `nilai_akhir` | `NUMERIC(5,2)` | Nilai final setelah kalkulasi bobot |
| `predikat` | `VARCHAR(20)` | Contoh: "A", "B+", "Sangat Memuaskan" |
| `status_kelulusan` | `status_kelulusan` ENUM NOT NULL | Lulus / Perlu Perbaikan / Tidak Lulus |
| `catatan_umum` | `TEXT` | Catatan umum hasil seminar |
| `dihitung_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | Waktu kalkulasi dilakukan |

---

## 7. Kelompok: Dokumen Resmi

### Tabel `dokumen_resmis`

Arsip semua dokumen resmi yang digenerate atau diupload ke sistem.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `jenis_dokumen` | `jenis_dokumen` ENUM NOT NULL | Lihat [Enum](#enum-jenis_dokumen) |
| `entitas_tipe` | `VARCHAR(50)` NOT NULL | Nama tabel yang direferensikan (polymorphic) |
| `entitas_id` | `UUID` NOT NULL | ID record yang direferensikan |
| `judul` | `VARCHAR(200)` NOT NULL | Nama/judul dokumen |
| `file_url` | `TEXT` NOT NULL | Path/URL file dokumen |
| `digenerate_oleh` | `UUID` FK → `users.id` | Pengguna yang minta generate |
| `template_id` | `UUID` FK → `template_dokumens.id` | Template yang digunakan |
| `is_final` | `BOOLEAN` NOT NULL DEFAULT `true` | |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `template_dokumens`

Master template dokumen (SK, undangan, berita acara) yang dikelola Admin.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `jenis_dokumen` | `jenis_dokumen` ENUM NOT NULL | |
| `nama_template` | `VARCHAR(150)` NOT NULL | Nama deskriptif template |
| `konten_template` | `TEXT` NOT NULL | Konten template dengan placeholder `{{variable}}` |
| `is_aktif` | `BOOLEAN` NOT NULL DEFAULT `true` | Hanya satu template aktif per jenis |
| `dibuat_oleh` | `UUID` FK → `users.id` NOT NULL | |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |
| `updated_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

## 8. Kelompok: Notifikasi

### Tabel `notifikasis`

Semua notifikasi in-app untuk setiap pengguna.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `user_id` | `UUID` FK → `users.id` NOT NULL | Penerima notifikasi |
| `judul` | `VARCHAR(200)` NOT NULL | Judul singkat notifikasi |
| `pesan` | `TEXT` NOT NULL | Isi pesan notifikasi |
| `tipe` | `tipe_notifikasi` ENUM NOT NULL | Lihat [Enum](#enum-tipe_notifikasi) |
| `entitas_tipe` | `VARCHAR(50)` | Tabel yang memicu notifikasi |
| `entitas_id` | `UUID` | ID record yang memicu notifikasi |
| `action_url` | `TEXT` | URL halaman yang relevan (deep link) |
| `is_read` | `BOOLEAN` NOT NULL DEFAULT `false` | |
| `read_at` | `TIMESTAMPTZ` | |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

## 9. Kelompok: Konfigurasi Sistem

### Tabel `semester_aktifs`

Menyimpan konfigurasi tahun ajaran dan semester yang sedang aktif.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `tahun_ajaran` | `VARCHAR(10)` NOT NULL | Contoh: `"2024/2025"` |
| `semester` | `semester_tipe` ENUM NOT NULL | `ganjil` / `genap` |
| `tanggal_mulai` | `DATE` NOT NULL | |
| `tanggal_selesai` | `DATE` NOT NULL | |
| `deadline_pengajuan_judul` | `DATE` | |
| `deadline_seminar_proposal` | `DATE` | |
| `deadline_seminar_hasil` | `DATE` | |
| `deadline_munaqasyah` | `DATE` | |
| `is_aktif` | `BOOLEAN` NOT NULL DEFAULT `false` | Hanya satu baris boleh `true` |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `syarat_berkass`

Daftar berkas yang wajib diupload saat mendaftar seminar tertentu, dikonfigurasi Admin.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `jenis_seminar` | `jenis_seminar` ENUM NOT NULL | Syarat berlaku untuk seminar apa |
| `nama_berkas` | `VARCHAR(150)` NOT NULL | Nama berkas yang disyaratkan |
| `deskripsi` | `TEXT` | Penjelasan berkas |
| `tipe_file_diizinkan` | `TEXT[]` | Contoh: `['pdf', 'docx']` |
| `ukuran_maks_mb` | `SMALLINT` NOT NULL DEFAULT `10` | Ukuran maksimum upload |
| `is_wajib` | `BOOLEAN` NOT NULL DEFAULT `true` | Wajib atau opsional |
| `urutan` | `SMALLINT` NOT NULL DEFAULT `0` | Urutan tampil di form |
| `is_aktif` | `BOOLEAN` NOT NULL DEFAULT `true` | |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `pengumumans`

Pengumuman resmi dari Kajur atau Sekretaris kepada mahasiswa TA.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `judul` | `VARCHAR(200)` NOT NULL | |
| `isi` | `TEXT` NOT NULL | |
| `target_role` | `user_role[]` | Role mana yang menerima (null = semua) |
| `dibuat_oleh` | `UUID` FK → `users.id` NOT NULL | |
| `published_at` | `TIMESTAMPTZ` | Null = draft |
| `expired_at` | `TIMESTAMPTZ` | Pengumuman tidak tampil setelah ini |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

## 10. Kelompok: Audit & Log

### Tabel `audit_logs`

Mencatat setiap perubahan data penting di sistem. Tidak dapat dihapus.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `user_id` | `UUID` FK → `users.id` | Pengguna yang melakukan aksi (null jika sistem) |
| `aksi` | `VARCHAR(100)` NOT NULL | Contoh: `"UPDATE status_pengajuan"` |
| `tabel_terdampak` | `VARCHAR(100)` NOT NULL | Nama tabel yang berubah |
| `record_id` | `UUID` | ID record yang berubah |
| `data_sebelum` | `JSONB` | Snapshot data sebelum perubahan |
| `data_sesudah` | `JSONB` | Snapshot data setelah perubahan |
| `ip_address` | `INET` | IP address pengguna |
| `user_agent` | `TEXT` | Browser/klien yang digunakan |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

### Tabel `login_logs`

Mencatat setiap aktivitas login dan logout.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | `UUID` PK | |
| `user_id` | `UUID` FK → `users.id` NOT NULL | |
| `aksi` | `VARCHAR(20)` NOT NULL | `login` / `logout` / `login_gagal` |
| `ip_address` | `INET` | |
| `user_agent` | `TEXT` | |
| `created_at` | `TIMESTAMPTZ` NOT NULL DEFAULT `now()` | |

---

## 11. Ringkasan Semua Tabel

| # | Nama Tabel | Kelompok | Keterangan Singkat |
|---|---|---|---|
| 1 | `users` | Auth | Semua pengguna sistem |
| 2 | `password_reset_tokens` | Auth | Token reset password |
| 3 | `mahasiswas` | Auth | Data spesifik mahasiswa |
| 4 | `dosens` | Auth | Data spesifik dosen |
| 5 | `pengajuan_juduls` | Judul | Pengajuan & riwayat revisi judul TA |
| 6 | `sk_pembimbings` | Judul | SK Pembimbing TA |
| 7 | `sesi_bimbingans` | Bimbingan | Log sesi bimbingan digital |
| 8 | `laporan_tas` | Bimbingan | Versi laporan TA (versioning) |
| 9 | `acc_seminars` | Bimbingan | ACC dosen pembimbing per jenis seminar |
| 10 | `pendaftaran_seminars` | Seminar | Pendaftaran seminar (ketiga jenis) |
| 11 | `berkas_pendaftarans` | Seminar | File berkas yang diupload mahasiswa |
| 12 | `jadwal_seminars` | Seminar | Jadwal seminar yang dibuat TU |
| 13 | `penguji_seminars` | Seminar | Dosen penguji per jadwal seminar |
| 14 | `sk_pengujis` | Seminar | SK Penguji per seminar |
| 15 | `komponen_nilais` | Penilaian | Master komponen penilaian per seminar |
| 16 | `nilai_seminars` | Penilaian | Nilai per komponen per dosen |
| 17 | `rekap_nilai_seminars` | Penilaian | Rekap nilai akhir per seminar |
| 18 | `dokumen_resmis` | Dokumen | Arsip semua dokumen resmi |
| 19 | `template_dokumens` | Dokumen | Template SK, undangan, berita acara |
| 20 | `notifikasis` | Notifikasi | Notifikasi in-app semua pengguna |
| 21 | `semester_aktifs` | Konfigurasi | Tahun ajaran & deadline |
| 22 | `syarat_berkass` | Konfigurasi | Daftar syarat berkas per seminar |
| 23 | `pengumumans` | Konfigurasi | Pengumuman jurusan |
| 24 | `audit_logs` | Log | Log perubahan data penting |
| 25 | `login_logs` | Log | Log aktivitas login/logout |

---

## 12. Enum & Konstanta

### Enum `user_role`
```sql
CREATE TYPE user_role AS ENUM (
    'mahasiswa',
    'dpa',              -- Dosen Pembimbing Akademik
    'dosen_pembimbing', -- Dosen Pembimbing TA
    'tata_usaha',
    'ketua_jurusan',
    'sekretaris',
    'admin'
);
```

### Enum `tahap_ta`
```sql
CREATE TYPE tahap_ta AS ENUM (
    'belum_ada_judul',
    'pengajuan_judul',
    'bimbingan',
    'seminar_proposal',
    'pasca_seminar_proposal',
    'seminar_hasil',
    'pasca_seminar_hasil',
    'munaqasyah',
    'yudisium',
    'lulus'
);
```

### Enum `status_pengajuan`
```sql
CREATE TYPE status_pengajuan AS ENUM (
    'draft',
    'diajukan',
    'perlu_revisi',
    'disetujui',
    'ditolak'
);
```

### Enum `status_pendaftaran`
```sql
CREATE TYPE status_pendaftaran AS ENUM (
    'draft',
    'diajukan',
    'perlu_revisi',    -- Berkas kurang/tidak valid
    'diverifikasi',    -- Berkas lengkap, menunggu jadwal
    'dijadwalkan',
    'selesai'
);
```

### Enum `status_acc`
```sql
CREATE TYPE status_acc AS ENUM (
    'menunggu',
    'acc',
    'perlu_revisi'
);
```

### Enum `status_sesi_bimbingan`
```sql
CREATE TYPE status_sesi_bimbingan AS ENUM (
    'dicatat',          -- Mahasiswa sudah catat, belum direspons dosen
    'direspons',        -- Dosen sudah beri feedback
    'dikonfirmasi'      -- Selesai, kedua pihak konfirmasi
);
```

### Enum `jenis_seminar`
```sql
CREATE TYPE jenis_seminar AS ENUM (
    'proposal',
    'hasil',
    'munaqasyah'
);
```

### Enum `peran_penguji`
```sql
CREATE TYPE peran_penguji AS ENUM (
    'ketua_penguji',
    'anggota_penguji',
    'pembimbing'
);
```

### Enum `status_konfirmasi`
```sql
CREATE TYPE status_konfirmasi AS ENUM (
    'menunggu',
    'dikonfirmasi',
    'ditolak'
);
```

### Enum `status_kelulusan`
```sql
CREATE TYPE status_kelulusan AS ENUM (
    'lulus',
    'perlu_perbaikan',
    'tidak_lulus'
);
```

### Enum `status_verifikasi_berkas`
```sql
CREATE TYPE status_verifikasi_berkas AS ENUM (
    'belum_diperiksa',
    'valid',
    'tidak_valid'
);
```

### Enum `jenis_dokumen`
```sql
CREATE TYPE jenis_dokumen AS ENUM (
    'sk_pembimbing',
    'sk_penguji_proposal',
    'sk_penguji_hasil',
    'sk_penguji_munaqasyah',
    'berita_acara_proposal',
    'berita_acara_hasil',
    'berita_acara_munaqasyah',
    'undangan_penguji',
    'surat_keterangan_lulus',
    'kartu_peserta_seminar'
);
```

### Enum `tipe_notifikasi`
```sql
CREATE TYPE tipe_notifikasi AS ENUM (
    'pengajuan_judul',
    'keputusan_judul',
    'sk_terbit',
    'feedback_bimbingan',
    'acc_seminar',
    'verifikasi_berkas',
    'jadwal_seminar',
    'nilai_masuk',
    'deadline',
    'pengumuman',
    'sistem'
);
```

### Enum `semester_tipe`
```sql
CREATE TYPE semester_tipe AS ENUM (
    'ganjil',
    'genap'
);
```

---

## 13. Indeks

Indeks berikut disarankan untuk mempercepat query yang paling sering dijalankan:

```sql
-- users
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

-- mahasiswas
CREATE INDEX idx_mahasiswas_nim ON mahasiswas(nim);
CREATE INDEX idx_mahasiswas_dpa_id ON mahasiswas(dpa_id);
CREATE INDEX idx_mahasiswas_angkatan ON mahasiswas(angkatan);

-- pengajuan_juduls
CREATE INDEX idx_pengajuan_juduls_mahasiswa ON pengajuan_juduls(mahasiswa_id);
CREATE INDEX idx_pengajuan_juduls_dpa ON pengajuan_juduls(dpa_id);
CREATE INDEX idx_pengajuan_juduls_status ON pengajuan_juduls(status);

-- sesi_bimbingans
CREATE INDEX idx_sesi_bimbingans_mahasiswa ON sesi_bimbingans(mahasiswa_id);
CREATE INDEX idx_sesi_bimbingans_dosen ON sesi_bimbingans(dosen_id);
CREATE INDEX idx_sesi_bimbingans_tanggal ON sesi_bimbingans(tanggal_sesi DESC);

-- laporan_tas
CREATE INDEX idx_laporan_tas_mahasiswa ON laporan_tas(mahasiswa_id);
CREATE UNIQUE INDEX idx_laporan_tas_versi ON laporan_tas(mahasiswa_id, nomor_versi);

-- pendaftaran_seminars
CREATE INDEX idx_pendaftaran_mahasiswa ON pendaftaran_seminars(mahasiswa_id);
CREATE INDEX idx_pendaftaran_jenis ON pendaftaran_seminars(jenis_seminar);
CREATE INDEX idx_pendaftaran_status ON pendaftaran_seminars(status);

-- jadwal_seminars
CREATE INDEX idx_jadwal_tanggal ON jadwal_seminars(tanggal);
CREATE INDEX idx_jadwal_jenis ON jadwal_seminars(jenis_seminar);

-- penguji_seminars
CREATE INDEX idx_penguji_jadwal ON penguji_seminars(jadwal_id);
CREATE INDEX idx_penguji_dosen ON penguji_seminars(dosen_id);

-- nilai_seminars
CREATE INDEX idx_nilai_jadwal ON nilai_seminars(jadwal_id);
CREATE INDEX idx_nilai_dosen ON nilai_seminars(dosen_id);

-- notifikasis
CREATE INDEX idx_notifikasi_user ON notifikasis(user_id);
CREATE INDEX idx_notifikasi_unread ON notifikasis(user_id, is_read) WHERE is_read = false;

-- audit_logs
CREATE INDEX idx_audit_user ON audit_logs(user_id);
CREATE INDEX idx_audit_tabel ON audit_logs(tabel_terdampak);
CREATE INDEX idx_audit_created ON audit_logs(created_at DESC);
```

---

## 14. Catatan Desain

### Pendekatan Generik untuk Tabel Seminar
Tabel `pendaftaran_seminars`, `jadwal_seminars`, dan `nilai_seminars` dirancang generik menggunakan kolom `jenis_seminar` (ENUM). Ini menghindari duplikasi tiga tabel terpisah dengan struktur hampir identik. Komponen penilaian yang berbeda per jenis seminar ditangani oleh tabel `komponen_nilais` yang juga berkolom `jenis_seminar`.

### Polymorphic Reference di `dokumen_resmis`
Tabel `dokumen_resmis` menggunakan pattern polymorphic (`entitas_tipe` + `entitas_id`) agar satu tabel dapat menyimpan arsip dokumen yang merujuk ke berbagai entitas (SK Pembimbing, Jadwal Seminar, dll.) tanpa membuat kolom FK yang terlalu banyak dan sparse.

### Soft Delete
Tabel pengguna dan tabel konfigurasi menggunakan pola `is_active` / `is_aktif` daripada penghapusan fisik, untuk menjaga integritas referensial dan memungkinkan audit historis.

### Versioning Laporan
Setiap upload laporan baru menghasilkan baris baru di `laporan_tas` dengan `nomor_versi` yang bertambah. File lama tidak pernah dihapus. Query versi aktif cukup menggunakan `ORDER BY nomor_versi DESC LIMIT 1`.

### Fase Implementasi Database

| Fase | Tabel yang Dibuat |
|---|---|
| **MVP** | `users`, `password_reset_tokens`, `mahasiswas`, `dosens`, `pengajuan_juduls`, `sk_pembimbings`, `sesi_bimbingans`, `laporan_tas`, `acc_seminars`, `pendaftaran_seminars`, `berkas_pendaftarans`, `jadwal_seminars`, `penguji_seminars`, `sk_pengujis`, `komponen_nilais`, `nilai_seminars`, `notifikasis`, `semester_aktifs`, `syarat_berkass`, `audit_logs`, `login_logs` |
| **Pasca-MVP** | `rekap_nilai_seminars` (kalkulasi nilai akhir), `dokumen_resmis`, `template_dokumens`, `pengumumans` |

---

*Rancangan ini dapat disesuaikan dengan kebutuhan spesifik jurusan, terutama pada bobot penilaian dan daftar syarat berkas per jenis seminar. Versi: 1.0*
