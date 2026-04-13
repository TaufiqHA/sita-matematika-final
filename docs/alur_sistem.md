# Alur Sistem — SITA (Sistem Informasi Tugas Akhir)

> Dokumen ini mendeskripsikan seluruh alur kerja sistem SITA secara menyeluruh, mulai dari autentikasi hingga kelulusan mahasiswa. Setiap alur dilengkapi dengan pihak yang terlibat, status dokumen, dan trigger notifikasi yang relevan.

---

## Daftar Isi

1. [Alur Autentikasi](#1-alur-autentikasi)
2. [Alur Pengajuan Judul TA](#2-alur-pengajuan-judul-ta)
3. [Alur Penerbitan SK Pembimbing](#3-alur-penerbitan-sk-pembimbing)
4. [Alur Bimbingan TA](#4-alur-bimbingan-ta)
5. [Alur Seminar Proposal](#5-alur-seminar-proposal)
6. [Alur Seminar Hasil](#6-alur-seminar-hasil)
7. [Alur Munaqasyah (Seminar Tutup)](#7-alur-munaqasyah-seminar-tutup)
8. [Alur Yudisium & Kelulusan](#8-alur-yudisium--kelulusan)
9. [Alur Lintas Sistem](#9-alur-lintas-sistem)
10. [Ringkasan Status Dokumen](#10-ringkasan-status-dokumen)
11. [Ringkasan Trigger Notifikasi](#11-ringkasan-trigger-notifikasi)
12. [Peta Keterlibatan Peran per Tahapan](#12-peta-keterlibatan-peran-per-tahapan)

---

## 1. Alur Autentikasi

**Pihak terlibat:** Semua peran (Mahasiswa, DPA, Dosen Pembimbing, TU, Ketua Jurusan, Sekretaris, Admin)

```
[Pengguna] Akses halaman login
        │
        ▼
Input kredensial
├── Mahasiswa   → NIM + password
└── Dosen/Staf → akun dosen/staf + password
        │
        ▼
Sistem memverifikasi kredensial
        │
   ┌────┴────────────────┐
Gagal                 Berhasil
   │                      │
   ▼                      ▼
Tampilkan pesan     Sistem mendeteksi role
error login         pengguna
   │                      │
   ▼                 ┌────┴──────────────────────────────┐
Opsi reset          │             │            │         │
password via    Mahasiswa      Dosen         TU/SEK    Admin/KAJ
email               │             │            │         │
                    ▼             ▼            ▼         ▼
               Dashboard     Dashboard    Dashboard  Dashboard
               Mahasiswa      Dosen        Staf       Admin
```

**Catatan:**
- Reset password dikirim ke email terdaftar
- Sesi login dikelola oleh sistem; logout manual tersedia di semua halaman
- Admin dapat mereset password pengguna mana pun secara manual

---

## 2. Alur Pengajuan Judul TA

**Pihak terlibat:** Mahasiswa → DPA → Ketua Jurusan

**Prasyarat:** Mahasiswa sudah login dan belum memiliki judul TA yang disetujui

```
[Mahasiswa] Mengisi form pengajuan judul TA
  - Judul, bidang, topik, latar belakang singkat
  - Metode penelitian yang direncanakan
  - Upload draft proposal (PDF)
        │
        ▼
Status: "Diajukan"
Notifikasi → DPA yang bersangkutan
        │
        ▼
[DPA] Menerima notifikasi & membuka antrian pengajuan
  - Membaca detail judul + membuka file draft proposal
        │
        ├─────────────────────────────────────────────┐
        │                    │                        │
        ▼                    ▼                        ▼
   [Disetujui]         [Perlu Revisi]            [Ditolak]
        │                    │                        │
        ▼                    ▼                        ▼
Status: "Disetujui"   Status: "Perlu Revisi"   Status: "Ditolak"
Notif → Mahasiswa     Notif + catatan          Notif → Mahasiswa
Notif → Ketua Jurusan → Mahasiswa
        │                    │
        ▼                    ▼
[Lanjut ke Alur 3]   [Mahasiswa] menerima catatan
                     → memperbaiki judul & proposal
                     → mengajukan ulang
                     (kembali ke atas)
```

**Status yang mungkin terjadi:** `Draft` → `Diajukan` → `Perlu Revisi` → `Diajukan` → `Disetujui` / `Ditolak`

---

## 3. Alur Penerbitan SK Pembimbing

**Pihak terlibat:** Ketua Jurusan → Mahasiswa + Dosen Pembimbing TA

**Prasyarat:** Judul TA telah disetujui DPA

```
[Ketua Jurusan] Menerima notifikasi judul disetujui
        │
        ▼
Membuka daftar judul yang menunggu SK
        │
        ▼
Memilih dosen pembimbing TA yang akan ditugaskan
(mempertimbangkan kuota bimbingan aktif per dosen)
        │
        ▼
Menerbitkan SK Pembimbing TA
  - Generate dokumen SK (nomor SK, tanggal, nama mahasiswa & dosen)
  - Menyimpan ke arsip digital
        │
        ▼
Notifikasi → Mahasiswa (SK tersedia, bimbingan bisa dimulai)
Notifikasi → Dosen Pembimbing TA (mendapat mahasiswa bimbingan baru)
        │
        ▼
[Lanjut ke Alur 4 — Bimbingan TA]
```

---

## 4. Alur Bimbingan TA

**Pihak terlibat:** Mahasiswa ↔ Dosen Pembimbing TA

**Prasyarat:** SK Pembimbing TA telah diterbitkan

```
[Mahasiswa] Mengadakan sesi bimbingan dengan dosen
        │
        ▼
[Mahasiswa] Mencatat sesi bimbingan di sistem
  - Tanggal & waktu sesi
  - Topik yang dibahas
  - Catatan singkat hasil diskusi
        │
        ▼
[Dosen Pembimbing] Melihat catatan sesi bimbingan
        │
        ▼
Menambahkan feedback / catatan perbaikan
        │
        ├────────────────────────────┐
        │                           │
        ▼                           ▼
  [ACC Laporan]              [Kembalikan untuk Revisi]
        │                           │
        ▼                           ▼
Status laporan: "ACC"        Status laporan: "Perlu Revisi"
                             Notif → Mahasiswa
                                     │
                                     ▼
                             [Mahasiswa] upload revisi laporan
                             (versi baru tersimpan otomatis)
                             (kembali ke langkah pencatatan sesi)
        │
        ▼ (setelah progres bimbingan dinilai cukup)
[Dosen Pembimbing] Memberikan ACC Pendaftaran Seminar
        │
        ├──────────────────────────────────────────┐
        │                         │                │
        ▼                         ▼                ▼
  ACC Seminar Proposal      ACC Seminar Hasil  ACC Munaqasyah
        │                         │                │
        ▼                         ▼                ▼
  [Lanjut ke Alur 5]        [Lanjut ke Alur 6] [Lanjut ke Alur 7]
```

**Catatan:**
- Setiap upload laporan disimpan sebagai versi baru (versioning otomatis)
- Mahasiswa dapat melihat seluruh riwayat feedback dari dosen
- Sistem mencatat jumlah total sesi bimbingan sebagai syarat pendaftaran seminar

---

## 5. Alur Seminar Proposal

**Pihak terlibat:** Mahasiswa → Dosen Pembimbing → TU → Sekretaris → Ketua Jurusan → Dosen Penguji

**Prasyarat:** SK Pembimbing terbit + ACC Seminar Proposal dari Dosen Pembimbing

```
[Mahasiswa] Mengisi form pendaftaran Seminar Proposal
  - Upload berkas persyaratan yang ditentukan:
    - Laporan proposal (versi terbaru yang di-ACC)
    - Lembar persetujuan pembimbing
    - Form log bimbingan
    - Dokumen syarat lainnya (dikonfigurasi Admin)
        │
        ▼
Status: "Diajukan"
Notifikasi → TU
        │
        ▼
[TU] Membuka antrian pendaftaran Seminar Proposal
  - Menggunakan checklist digital per syarat berkas
        │
        ├──────────────────────────────┐
        │                             │
        ▼                             ▼
  [Berkas Lengkap]            [Berkas Tidak Lengkap]
        │                             │
        ▼                             ▼
Status: "Diverifikasi"         Status: "Perlu Revisi"
                               Notif + keterangan kurang
                               berkas → Mahasiswa
                                        │
                                        ▼
                               [Mahasiswa] melengkapi berkas
                               (kembali ke langkah upload)
        │
        ▼
[TU] Menjadwalkan Seminar Proposal
  - Menentukan slot waktu & ruangan
  - Menugaskan dosen penguji
  - Sistem mendeteksi konflik jadwal dosen otomatis
        │
        ▼
Status: "Dijadwalkan"
Notifikasi → Mahasiswa (jadwal seminar tersedia)
Notifikasi → Dosen Penguji (undangan seminar)
        │
        ▼
[Sekretaris] Mengirim undangan resmi ke dosen penguji
  - Konfirmasi kehadiran dosen penguji dicatat
        │
        ▼
[Ketua Jurusan] Menerbitkan SK Penguji Seminar Proposal
        │
        ▼
Seminar Proposal dilaksanakan
        │
        ▼
[Dosen Penguji + Dosen Pembimbing] Input nilai
  per komponen penilaian yang tersedia
        │
        ▼
Status: "Selesai"
Notifikasi → Mahasiswa (nilai tersedia)
        │
        ▼
[TU] Mencetak berita acara & mengarsipkan dokumen
        │
        ▼
[Lanjut ke Alur 4 — Bimbingan lanjutan menuju Seminar Hasil]
```

---

## 6. Alur Seminar Hasil

**Pihak terlibat:** Mahasiswa → Dosen Pembimbing → TU → Sekretaris → Ketua Jurusan → Dosen Penguji

**Prasyarat:** Lulus Seminar Proposal + ACC Seminar Hasil dari Dosen Pembimbing

```
[Mahasiswa] Mengisi form pendaftaran Seminar Hasil
  - Upload berkas persyaratan:
    - Laporan TA versi terbaru (di-ACC pembimbing)
    - Bukti lulus Seminar Proposal
    - Lembar persetujuan pembimbing
    - Form log bimbingan (dengan jumlah sesi yang memenuhi syarat)
    - Dokumen syarat lainnya
        │
        ▼
Status: "Diajukan"
Notifikasi → TU
        │
        ▼
[TU] Verifikasi kelengkapan berkas (checklist digital)
        │
        ├──────────────────────────────┐
        │                             │
        ▼                             ▼
  [Berkas Lengkap]            [Berkas Tidak Lengkap]
        │                             │
        ▼                             ▼
Status: "Diverifikasi"         Notif + keterangan → Mahasiswa
                               [Mahasiswa] melengkapi berkas
        │
        ▼
[TU] Menjadwalkan Seminar Hasil
  - Slot waktu, ruangan, dosen penguji
  - Deteksi konflik jadwal otomatis
        │
        ▼
Status: "Dijadwalkan"
Notifikasi → Mahasiswa + Dosen Penguji
        │
        ▼
[Sekretaris] Mengirim undangan resmi + konfirmasi kehadiran
        │
        ▼
[Ketua Jurusan] Menerbitkan SK Penguji Seminar Hasil
        │
        ▼
Seminar Hasil dilaksanakan
        │
        ▼
[Dosen Penguji + Dosen Pembimbing] Input nilai
  per komponen penilaian
        │
        ▼
Status: "Selesai"
Notifikasi → Mahasiswa (nilai tersedia)
        │
        ▼
[TU] Cetak berita acara & arsip dokumen
        │
        ▼
[Lanjut ke Alur 7 — Munaqasyah]
```

---

## 7. Alur Munaqasyah (Seminar Tutup)

**Pihak terlibat:** Mahasiswa → Dosen Pembimbing → TU → Ketua Jurusan → Sekretaris → Dosen Penguji

**Prasyarat:** Lulus Seminar Hasil + ACC Munaqasyah dari Dosen Pembimbing + seluruh persyaratan administrasi terpenuhi

```
[Mahasiswa] Mengisi form pendaftaran Munaqasyah
  - Upload berkas persyaratan (lebih ketat dari seminar sebelumnya):
    - Laporan TA final (di-ACC pembimbing)
    - Bukti lulus Seminar Proposal & Seminar Hasil
    - Lembar persetujuan pembimbing untuk Munaqasyah
    - Bukti bebas pustaka
    - Bukti lunas keuangan/administrasi
    - Dokumen administratif lain yang disyaratkan jurusan
        │
        ▼
Status: "Diajukan"
Notifikasi → TU + Ketua Jurusan
        │
        ▼
[TU] Verifikasi kelengkapan & keabsahan berkas
  (termasuk verifikasi syarat adminstrasi: bebas pustaka, lunas keuangan, dll.)
        │
        ├──────────────────────────────┐
        │                             │
        ▼                             ▼
  [Berkas Lengkap]            [Berkas Tidak Lengkap / Tidak Sah]
        │                             │
        ▼                             ▼
Status: "Diverifikasi"         Notif + keterangan → Mahasiswa
                               [Mahasiswa] melengkapi / memperbaiki berkas
        │
        ▼
[Ketua Jurusan] Menyetujui jadwal & menerbitkan SK Penguji Munaqasyah
        │
        ▼
[TU] Membuat jadwal Munaqasyah
  - Slot waktu, ruangan, susunan majelis penguji
        │
        ▼
Status: "Dijadwalkan"
Notifikasi → Mahasiswa + Dosen Penguji
        │
        ▼
[Sekretaris] Mengirim undangan resmi ke majelis penguji
  - Konfirmasi kehadiran dicatat
        │
        ▼
Munaqasyah dilaksanakan
        │
        ▼
[Dosen Penguji + Dosen Pembimbing] Input nilai sidang
  per komponen penilaian yang terstruktur
        │
        ▼
Sistem menghitung nilai akhir TA
(berdasarkan bobot yang dikonfigurasi Admin)
        │
        ├──────────────────────────────────────┐
        │                                      │
        ▼                                      ▼
   [Lulus]                              [Perlu Perbaikan]
        │                                      │
        ▼                                      ▼
Status: "Selesai"                    Mahasiswa menerima catatan
Notifikasi → Mahasiswa               perbaikan dari penguji
(nilai akhir tersedia)               → revisi sesuai batas waktu
        │                            → dikonfirmasi pembimbing
        ▼                            (kembali ke input nilai
[Lanjut ke Alur 8 — Yudisium]        jika diperlukan sidang ulang)
```

---

## 8. Alur Yudisium & Kelulusan

**Pihak terlibat:** TU → Sekretaris → Ketua Jurusan → Mahasiswa

**Prasyarat:** Mahasiswa dinyatakan lulus Munaqasyah

```
[Sistem] Menandai status mahasiswa sebagai "Lulus Munaqasyah"
        │
        ▼
[TU + Sekretaris] Memproses berkas yudisium
  - Mengumpulkan semua dokumen final
  - Memeriksa kelengkapan berkas kelulusan
        │
        ▼
[Ketua Jurusan] Menerbitkan dokumen kelulusan:
  - Surat Keterangan Lulus
  - Transkrip nilai TA
  - Dokumen lain yang diperlukan jurusan
        │
        ▼
[Mahasiswa] Dapat mengunduh dokumen kelulusan dari sistem
        │
        ▼
Status tahapan mahasiswa: "Yudisium / Lulus"
Progress bar mahasiswa: ██████████ 100%
        │
        ▼
[Sistem] Data mahasiswa masuk ke statistik lulusan jurusan
```

---

## 9. Alur Lintas Sistem

### 9.1 Alur Manajemen Pengguna (Admin)

```
[Admin] Login dengan akses penuh
        │
        ├── Tambah pengguna baru
        │     - Input data + assign role
        │     - atau Import massal dari CSV/Excel
        │
        ├── Edit pengguna
        │     - Ubah data, role, atau status aktif
        │
        ├── Nonaktifkan akun
        │     - Pengguna tidak bisa login
        │     - Data tetap tersimpan di sistem
        │
        └── Reset password
              - Kirim link reset ke email pengguna
```

### 9.2 Alur Konfigurasi Sistem (Admin)

```
[Admin] Mengatur konfigurasi sebelum semester baru dimulai
        │
        ├── Mengatur tahun ajaran & semester aktif
        ├── Mengatur deadline TA per semester
        ├── Mengkonfigurasi daftar syarat berkas per jenis seminar
        ├── Mengelola template dokumen (SK, undangan, berita acara)
        └── Mengatur trigger & pesan notifikasi otomatis
```

### 9.3 Alur Monitoring Ketua Jurusan

```
[Ketua Jurusan] Membuka dashboard monitoring
        │
        ├── Statistik mahasiswa per tahapan & angkatan
        ├── Grafik seminar per bulan/semester
        ├── Daftar mahasiswa tanpa progres > 3 bulan
        │     └── Notifikasi otomatis ke DPA + Mahasiswa
        ├── Data beban bimbingan per dosen
        └── Laporan untuk akreditasi & BAN-PT
```

### 9.4 Alur Monitoring DPA

```
[DPA] Membuka dashboard perwalian
        │
        ├── Melihat daftar mahasiswa perwalian beserta tahapan TA
        ├── Melihat mahasiswa yang belum punya judul TA
        ├── Menerima notifikasi pengajuan judul baru
        └── Mengirim catatan / pesan ke mahasiswa perwalian
```

---

## 10. Ringkasan Status Dokumen

Setiap pengajuan dan dokumen dalam sistem mengikuti siklus status berikut:

| Status | Keterangan | Siapa yang Mengubah |
|---|---|---|
| `Draft` | Form sedang diisi, belum disubmit | Mahasiswa |
| `Diajukan` | Sudah disubmit, menunggu tindakan | Mahasiswa (submit) |
| `Perlu Revisi` | Dikembalikan dengan catatan perbaikan | DPA / Dosen Pembimbing / TU |
| `Disetujui` | Disetujui oleh pihak berwenang | DPA / Ketua Jurusan |
| `Ditolak` | Ditolak, perlu pengajuan ulang | DPA |
| `Diverifikasi` | Berkas lengkap, menunggu penjadwalan | TU |
| `Dijadwalkan` | Jadwal seminar/munaqasyah tersedia | TU |
| `Selesai` | Proses tuntas, nilai sudah diinput | Sistem (otomatis) |

**Diagram transisi status pengajuan judul:**
```
Draft → Diajukan → Perlu Revisi → Diajukan → Disetujui
                                           → Ditolak
```

**Diagram transisi status pendaftaran seminar:**
```
Draft → Diajukan → Perlu Revisi → Diajukan → Diverifikasi → Dijadwalkan → Selesai
```

---

## 11. Ringkasan Trigger Notifikasi

| Kejadian | Penerima | Saluran |
|---|---|---|
| Mahasiswa mengajukan judul TA | DPA | In-app |
| DPA memberi keputusan judul | Mahasiswa | In-app |
| Judul disetujui DPA | Ketua Jurusan | In-app |
| SK Pembimbing diterbitkan | Mahasiswa + Dosen Pembimbing TA | In-app |
| Dosen Pembimbing memberi feedback bimbingan | Mahasiswa | In-app |
| Mahasiswa mendaftar Seminar Proposal | TU | In-app |
| TU selesai verifikasi berkas Seminar Proposal | Mahasiswa | In-app |
| Seminar Proposal dijadwalkan | Mahasiswa + Dosen Penguji | In-app |
| Nilai Seminar Proposal diinput | Mahasiswa | In-app |
| Mahasiswa mendaftar Seminar Hasil | TU | In-app |
| TU selesai verifikasi berkas Seminar Hasil | Mahasiswa | In-app |
| Seminar Hasil dijadwalkan | Mahasiswa + Dosen Penguji | In-app |
| Nilai Seminar Hasil diinput | Mahasiswa | In-app |
| Mahasiswa mendaftar Munaqasyah | TU + Ketua Jurusan | In-app |
| TU selesai verifikasi berkas Munaqasyah | Mahasiswa | In-app |
| Munaqasyah dijadwalkan | Mahasiswa + Dosen Penguji | In-app |
| Nilai akhir Munaqasyah diinput | Mahasiswa | In-app |
| Mahasiswa tanpa progres > 3 bulan | DPA + Ketua Jurusan | In-app |
| Deadline semester TA semakin dekat | Mahasiswa yang belum selesai tahapan | In-app |

> **Catatan MVP:** Email otomatis tidak wajib di MVP — notifikasi in-app sudah cukup untuk memvalidasi alur. Email akan diaktifkan di fase pengembangan selanjutnya.

---

## 12. Peta Keterlibatan Peran per Tahapan

| Tahapan | MHS | DPA | DSB | TU | KAJ | SEK | ADM |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| Pengajuan Judul | ✅ | ✅ | — | — | ✅ | — | — |
| Penerbitan SK Pembimbing | — | — | — | — | ✅ | — | — |
| Bimbingan TA | ✅ | — | ✅ | — | — | — | — |
| Pendaftaran Seminar Proposal | ✅ | — | ✅ | ✅ | ✅ | ✅ | — |
| Pelaksanaan Seminar Proposal | ✅ | — | ✅ | ✅ | — | — | — |
| Pendaftaran Seminar Hasil | ✅ | — | ✅ | ✅ | ✅ | ✅ | — |
| Pelaksanaan Seminar Hasil | ✅ | — | ✅ | ✅ | — | — | — |
| Pendaftaran Munaqasyah | ✅ | — | ✅ | ✅ | ✅ | ✅ | — |
| Pelaksanaan Munaqasyah | ✅ | — | ✅ | ✅ | ✅ | — | — |
| Yudisium & Kelulusan | ✅ | — | — | ✅ | ✅ | ✅ | — |
| Manajemen Pengguna & Sistem | — | — | — | — | — | — | ✅ |
| Monitoring & Laporan | — | ✅ | — | ✅ | ✅ | ✅ | ✅ |

**Keterangan singkatan:**
- **MHS** = Mahasiswa
- **DPA** = Dosen Pembimbing Akademik
- **DSB** = Dosen Pembimbing TA
- **TU** = Tata Usaha
- **KAJ** = Ketua Jurusan
- **SEK** = Sekretaris Jurusan
- **ADM** = Admin

---

*Dokumen ini merupakan turunan dari SISTEM_INFORMASI_TUGAS_AKHIR.md dan mvp.md. Diperbarui seiring perkembangan sistem. Versi: 1.0*
