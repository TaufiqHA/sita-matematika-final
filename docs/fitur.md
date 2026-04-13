# Fitur Lengkap SITA — Sistem Informasi Tugas Akhir

> Dokumen ini merangkum seluruh fitur SITA secara lengkap, dikelompokkan berdasarkan peran pengguna, fitur lintas peran, modul sistem, dan alur kerja utama.

---

## Daftar Isi

1. [Fitur per Peran Pengguna](#1-fitur-per-peran-pengguna)
   - 1.1 [Mahasiswa](#11-mahasiswa)
   - 1.2 [Dosen Pembimbing Akademik (DPA)](#12-dosen-pembimbing-akademik-dpa)
   - 1.3 [Dosen Pembimbing TA](#13-dosen-pembimbing-ta)
   - 1.4 [Tata Usaha (TU)](#14-tata-usaha-tu)
   - 1.5 [Ketua Jurusan](#15-ketua-jurusan)
   - 1.6 [Sekretaris Jurusan](#16-sekretaris-jurusan)
   - 1.7 [Admin](#17-admin)
2. [Fitur Lintas Peran](#2-fitur-lintas-peran)
3. [Modul Sistem](#3-modul-sistem)
4. [Alur Proses Utama](#4-alur-proses-utama)
5. [Status Dokumen & Pengajuan](#5-status-dokumen--pengajuan)
6. [Notifikasi Otomatis](#6-notifikasi-otomatis)

---

## 1. Fitur per Peran Pengguna

### 1.1 Mahasiswa

Mahasiswa adalah pengguna utama yang menjalani seluruh proses TA dari awal hingga yudisium.

#### Pengajuan Judul TA
- Mengisi form pengajuan judul: judul, bidang, topik, latar belakang singkat, dan metode yang direncanakan
- Upload draft proposal awal dalam format PDF
- Memantau status review dari DPA secara real-time
- Menerima catatan revisi dan mengajukan ulang jika judul ditolak atau perlu perbaikan
- Melihat riwayat semua pengajuan judul beserta statusnya

#### Pendaftaran Seminar

Terdapat tiga jenis seminar yang harus dilalui secara berurutan:

| Jenis Seminar | Keterangan | Prasyarat |
|---|---|---|
| **Seminar Proposal** | Presentasi rencana penelitian dan metodologi | Judul disetujui DPA, SK Pembimbing terbit |
| **Seminar Hasil** | Presentasi hasil penelitian sebelum sidang akhir | Lulus Seminar Proposal, bimbingan minimal selesai |
| **Seminar Tutup (Munaqasyah)** | Sidang akhir komprehensif sebagai ujian kelulusan TA | Lulus Seminar Hasil, ACC pembimbing, berkas administrasi lengkap |

Untuk masing-masing seminar, mahasiswa dapat:
- Mengisi form pendaftaran sesuai jenis seminar yang dituju
- Upload berkas persyaratan: laporan, lembar persetujuan pembimbing, form bimbingan, dan dokumen lain yang disyaratkan
- Melihat jadwal dan ruangan seminar setelah dijadwalkan TU
- Mencetak kartu peserta seminar
- Menerima notifikasi perubahan status pendaftaran

#### Bimbingan & Progres TA
- Log buku bimbingan digital: mencatat tanggal, topik bahasan, dan catatan per sesi
- Upload revisi laporan per versi dengan penomoran otomatis
- Melihat riwayat catatan dan feedback dari dosen pembimbing
- Progress bar visual tahapan TA: **Judul → Seminar Proposal → Seminar Hasil → Munaqasyah → Yudisium**
- Reminder otomatis jadwal bimbingan berikutnya

#### Administrasi & Kelulusan
- Melihat nilai dari setiap jenis seminar setelah diinput dosen
- Download SK pembimbing, SK penguji, surat keterangan, dan dokumen resmi lainnya
- Repositori dokumen TA pribadi: semua versi laporan dan berkas administrasi
- Melihat status yudisium dan kelulusan pasca-munaqasyah

---

### 1.2 Dosen Pembimbing Akademik (DPA)

DPA adalah dosen wali yang menjadi pintu pertama persetujuan judul TA bagi mahasiswa dalam daftar perwaliannya.

#### Review Judul TA
- Melihat daftar pengajuan judul dari seluruh mahasiswa perwalian yang aktif
- Membaca detail judul, topik, latar belakang, dan draft proposal yang diupload
- Memberi keputusan: **Disetujui**, **Perlu Revisi**, atau **Ditolak** disertai catatan terstruktur
- Menulis catatan perbaikan yang spesifik dan konstruktif untuk mahasiswa
- Melihat riwayat semua judul yang pernah direview beserta keputusannya

#### Monitoring Mahasiswa Perwalian
- Dashboard progres semua mahasiswa dalam daftar perwalian
- Melihat status tahapan tiap mahasiswa: belum ada judul / bimbingan aktif / seminar proposal / seminar hasil / munaqasyah / lulus
- Menerima notifikasi jika ada mahasiswa yang tidak memiliki progres dalam periode tertentu
- Riwayat bimbingan akademik dan komunikasi per mahasiswa
- Statistik mahasiswa perwalian per tahun ajaran

#### Komunikasi & Laporan
- Mengirim pesan atau catatan langsung ke mahasiswa melalui sistem
- Notifikasi otomatis saat ada pengajuan judul baru dari mahasiswa perwalian
- Rekap judul yang telah disetujui per semester
- Laporan mahasiswa yang belum memiliki judul TA
- Export data rekap ke PDF atau Excel

---

### 1.3 Dosen Pembimbing TA

Dosen yang membimbing konten dan kualitas penelitian TA setelah SK pembimbing diterbitkan oleh Ketua Jurusan.

#### Bimbingan TA
- Melihat daftar mahasiswa bimbingan aktif beserta progres masing-masing
- Mengisi catatan dan feedback per sesi bimbingan yang dicatat mahasiswa
- Memberikan persetujuan (ACC) atau mengembalikan laporan untuk direvisi
- Upload materi, referensi, atau template yang relevan untuk mahasiswa
- Melihat riwayat bimbingan lengkap per mahasiswa

#### Seminar & Penilaian
- Memberikan ACC agar mahasiswa bisa mendaftar ke masing-masing seminar (Proposal, Hasil, Munaqasyah)
- Input nilai **Seminar Proposal** per komponen penilaian
- Input nilai **Seminar Hasil** per komponen penilaian
- Input nilai **Munaqasyah** dengan rubrik penilaian terstruktur dan komprehensif
- Melihat jadwal seminar semua mahasiswa bimbingan
- Cetak berita acara bimbingan

#### Sebagai Dosen Penguji (jika ditugaskan)
- Menerima notifikasi undangan sebagai dosen penguji untuk seminar tertentu
- Mengakses dan membaca laporan TA yang akan diuji
- Input nilai dan catatan evaluasi sebagai dosen penguji
- Konfirmasi kehadiran untuk seminar yang dijadwalkan

#### Pengaturan & Jadwal
- Mengatur ketersediaan waktu bimbingan yang bisa dipesan mahasiswa
- Melihat statistik mahasiswa bimbingan per semester
- Notifikasi pengajuan jadwal bimbingan dari mahasiswa

---

### 1.4 Tata Usaha (TU)

Staf administrasi yang menghubungkan mahasiswa, dosen, dan pimpinan jurusan dalam hal administrasi dan penjadwalan.

#### Verifikasi Berkas
- Checklist digital kelengkapan berkas untuk pendaftaran Seminar Proposal, Seminar Hasil, dan Munaqasyah — masing-masing dengan daftar syarat tersendiri
- Verifikasi syarat administrasi: bebas pustaka, lunas keuangan, dan persyaratan lainnya (khususnya ketat untuk Munaqasyah)
- Menolak pendaftaran dengan keterangan berkas yang masih kurang atau tidak sesuai
- Notifikasi otomatis ke mahasiswa jika berkas tidak lengkap
- Riwayat verifikasi dan keputusan per mahasiswa

#### Penjadwalan
- Membuat dan mengelola slot waktu untuk ketiga jenis seminar
- Mengatur ruangan beserta kapasitas dan ketersediaannya
- Menugaskan dosen penguji ke jadwal seminar atau munaqasyah tertentu
- Deteksi konflik jadwal dosen secara otomatis
- Mengirim undangan resmi ke dosen penguji

#### Cetak Dokumen
- Mencetak undangan seminar dan munaqasyah resmi berformat jurusan
- Mencetak berita acara untuk setiap jenis seminar
- Mencetak surat keterangan mahasiswa aktif atau sedang mengerjakan TA
- Generate dan cetak sertifikat/dokumen kelulusan pasca-munaqasyah
- Arsip digital semua dokumen yang telah dicetak

#### Data & Rekap
- Rekap jadwal seminar per jenis (Proposal / Hasil / Munaqasyah) per minggu dan per bulan
- Laporan mahasiswa aktif TA berdasarkan angkatan dan tahapan
- Export data ke format yang diperlukan untuk pelaporan internal

---

### 1.5 Ketua Jurusan

Pimpinan jurusan yang memiliki kewenangan persetujuan tertinggi dan memantau kinerja program TA secara keseluruhan.

#### Persetujuan & Penerbitan SK
- Menerbitkan SK Pembimbing TA setelah judul disetujui DPA
- Menerbitkan SK Penguji untuk Seminar Proposal, Seminar Hasil, dan Munaqasyah
- Menyetujui jadwal munaqasyah
- Tanda tangan digital pada dokumen resmi jurusan
- Riwayat lengkap semua SK yang pernah diterbitkan

#### Dashboard Monitoring
- Statistik mahasiswa TA aktif per angkatan, per tahapan, dan per tahun
- Grafik jumlah seminar (Proposal/Hasil/Munaqasyah) per bulan dan per semester
- Daftar mahasiswa yang melewati batas waktu penyelesaian TA
- Data beban bimbingan per dosen: jumlah mahasiswa aktif yang dibimbing
- Alert otomatis untuk mahasiswa tanpa progres lebih dari 3 bulan

#### Laporan Jurusan
- Laporan yudisium per semester: jumlah lulus, rata-rata nilai, dan sebagainya
- Rata-rata durasi penyelesaian TA per angkatan (termasuk breakdown per tahapan seminar)
- Rekap topik dan bidang penelitian TA yang populer per tahun
- Laporan untuk keperluan akreditasi dan pelaporan BAN-PT
- Export laporan ke format PDF, Excel, atau CSV

#### Manajemen Jurusan
- Kelola data dosen aktif dan kuota bimbingan per dosen
- Mengatur batas waktu pengerjaan TA dan deadline per semester
- Membuat pengumuman resmi yang dikirim ke seluruh mahasiswa TA

---

### 1.6 Sekretaris Jurusan

Mendukung operasional administratif Ketua Jurusan, khususnya dalam koordinasi jadwal dan pengelolaan arsip dokumen jurusan.

#### Koordinasi Seminar & Munaqasyah
- Rekap jadwal mingguan dan bulanan untuk ketiga jenis seminar
- Koordinasi konfirmasi kehadiran dosen penguji per seminar
- Mengirim ulang undangan jika belum ada konfirmasi dari dosen
- Memantau kehadiran dalam pelaksanaan seminar dan munaqasyah
- Mengarsipkan berita acara yang telah selesai per jenis seminar

#### Surat & Dokumen
- Membuat surat undangan dosen penguji berdasarkan template per jenis seminar
- Membuat draft surat tugas dosen untuk keperluan jurusan
- Arsip digital seluruh surat dan dokumen resmi jurusan
- Pengelolaan template surat yang dapat digunakan kembali
- Tanda tangan digital untuk dokumen tertentu jika diberi kewenangan

#### Rekap & Pelaporan
- Rekap data mahasiswa TA aktif dari seluruh angkatan
- Laporan bulanan untuk disampaikan kepada Ketua Jurusan
- Statistik jumlah pendaftaran per jenis seminar (Proposal / Hasil / Munaqasyah) per periode
- Backup otomatis seluruh data arsip jurusan

#### Pengumuman
- Membuat dan mengirim pengumuman kepada mahasiswa TA
- Notifikasi deadline pengumpulan berkas per jenis seminar
- Broadcast informasi jadwal seminar ke papan informasi digital jurusan

---

### 1.7 Admin

Pengelola teknis sistem yang memastikan sistem berjalan lancar, aman, dan sesuai konfigurasi kebutuhan jurusan.

#### Manajemen Pengguna
- Menambah, mengedit, dan menonaktifkan akun pengguna
- Reset password dan mengelola sesi login
- Mengatur role dan hak akses per pengguna
- Import data mahasiswa dan dosen dari file CSV atau Excel
- Log aktivitas login seluruh pengguna

#### Konfigurasi Sistem
- Mengatur tahun ajaran dan semester yang sedang aktif
- Konfigurasi batas waktu dan deadline TA per semester
- Mengelola template dokumen, surat, dan berita acara (terpisah per jenis seminar)
- Mengatur notifikasi dan pesan otomatis: trigger, isi pesan, dan penerima
- Konfigurasi daftar syarat berkas per jenis seminar (Proposal / Hasil / Munaqasyah)

#### Keamanan & Data
- Backup otomatis database secara berkala
- Restore data dari backup jika diperlukan
- Audit log seluruh perubahan data penting: siapa yang mengubah, kapan, dan apa yang diubah
- Monitoring status dan ketersediaan sistem

#### Laporan Sistem
- Log error dan kendala yang terjadi di sistem
- Statistik penggunaan sistem per bulan
- Laporan penggunaan penyimpanan dan kapasitas
- Catatan pemeliharaan dan pembaruan sistem

---

## 2. Fitur Lintas Peran

Fitur-fitur berikut berlaku untuk semua atau sebagian besar peran dalam sistem.

### Autentikasi & Keamanan
- Login dengan kredensial unik per peran (email/NIM + password)
- Manajemen sesi dan logout otomatis setelah tidak aktif
- Reset password mandiri melalui email

### Notifikasi Otomatis
- Notifikasi in-app (ikon lonceng) untuk setiap perubahan status yang relevan
- Email otomatis ke pihak yang berkepentingan saat terjadi perubahan status
- Opsional: integrasi pesan instan untuk notifikasi yang mendesak

### Audit Trail
- Setiap perubahan status dokumen dicatat: siapa yang mengubah, kapan, dan apa yang diubah
- Log tidak dapat dihapus untuk menjamin integritas data
- Dapat diakses oleh Admin dan Ketua Jurusan

### Manajemen File
- Pembatasan tipe file yang diizinkan dan ukuran maksimum per upload
- Penamaan file otomatis berdasarkan identitas mahasiswa, jenis seminar, dan jenis dokumen
- Versioning dokumen: setiap upload baru tersimpan sebagai versi baru tanpa menghapus versi lama

### Tanda Tangan Digital
- Tanda tangan digital untuk dokumen resmi: SK, berita acara, surat keterangan
- Kode verifikasi pada setiap dokumen yang dicetak agar bisa dicek keasliannya
- Mengurangi kebutuhan proses cetak–tanda tangan basah–scan

### Dashboard Progres
- Setiap peran memiliki dashboard yang menampilkan ringkasan data yang relevan
- Mahasiswa melihat progress bar tahapan TA: **Judul → Seminar Proposal → Seminar Hasil → Munaqasyah → Yudisium**
- Dosen melihat daftar mahasiswa dan status tahapan terkini mereka
- Pimpinan melihat statistik agregat seluruh mahasiswa TA di jurusan

---

## 3. Modul Sistem

```
SITA
├── Modul Autentikasi
│   ├── Login / Logout
│   ├── Manajemen sesi
│   └── Reset password
│
├── Modul Pengajuan Judul
│   ├── Form pengajuan (Mahasiswa)
│   ├── Review & keputusan (DPA)
│   └── Riwayat pengajuan
│
├── Modul Bimbingan
│   ├── Log bimbingan digital
│   ├── Upload & versioning laporan
│   └── ACC dan catatan pembimbing
│
├── Modul Seminar Proposal
│   ├── Pendaftaran (Mahasiswa)
│   ├── Verifikasi berkas (TU)
│   ├── Penjadwalan (TU)
│   ├── Undangan penguji (Sekretaris)
│   └── Input nilai (Dosen)
│
├── Modul Seminar Hasil
│   ├── Pendaftaran (Mahasiswa)
│   ├── Verifikasi berkas (TU)
│   ├── Penjadwalan (TU)
│   ├── Undangan penguji (Sekretaris)
│   └── Input nilai (Dosen)
│
├── Modul Seminar Tutup / Munaqasyah
│   ├── Pendaftaran (Mahasiswa)
│   ├── Verifikasi & persetujuan (TU + Ketua Jurusan)
│   ├── Penjadwalan munaqasyah
│   ├── Undangan penguji (Sekretaris)
│   ├── Input & kalkulasi nilai akhir (Dosen)
│   └── Proses yudisium & kelulusan
│
├── Modul Dokumen & SK
│   ├── Generate SK pembimbing & penguji (per jenis seminar)
│   ├── Cetak berita acara (per jenis seminar)
│   ├── Surat keterangan
│   └── Arsip digital
│
├── Modul Notifikasi
│   ├── Notifikasi in-app
│   └── Email otomatis
│
├── Modul Laporan & Statistik
│   ├── Dashboard per peran
│   ├── Laporan rekap jurusan (termasuk per jenis seminar)
│   └── Export data
│
└── Modul Admin
    ├── Manajemen pengguna & role
    ├── Konfigurasi sistem & semester
    ├── Konfigurasi syarat berkas per jenis seminar
    ├── Backup & restore data
    └── Audit log & monitoring
```

---

## 4. Alur Proses Utama

### 4.1 Alur Pengajuan Judul TA

```
Mahasiswa mengisi form judul TA
        │
        ▼
Sistem meneruskan ke DPA yang bersangkutan
        │
        ▼
DPA mereview judul & draft proposal
        │
   ┌────┴───────────────────┐
Ditolak              Perlu Revisi            Disetujui
   │                      │                      │
   ▼                      ▼                      ▼
Notif ke           Notif + catatan       Notif ke Ketua Jurusan
mahasiswa          ke mahasiswa                  │
                          │                      ▼
                   Mahasiswa             Ketua Jurusan terbitkan
                   revisi &              SK Pembimbing TA
                   ajukan ulang                  │
                                                 ▼
                                      Notif ke Mahasiswa &
                                      Dosen Pembimbing TA
                                                 │
                                                 ▼
                                      Proses bimbingan TA dimulai
```

**Pihak yang terlibat:** Mahasiswa → DPA → Ketua Jurusan → Dosen Pembimbing TA

---

### 4.2 Alur Pendaftaran Seminar Proposal

```
Mahasiswa mendapat ACC dari Dosen Pembimbing TA
        │
        ▼
Mahasiswa isi form & upload berkas Seminar Proposal
        │
        ▼
TU memverifikasi kelengkapan berkas
        │
   ┌────┴──────────┐
Tidak Lengkap      Lengkap
   │                  │
   ▼                  ▼
Notif kurang    TU menjadwalkan Seminar Proposal
berkas          (slot waktu, ruangan, penguji)
                      │
                      ▼
              Sekretaris kirim undangan penguji
                      │
                      ▼
              Ketua Jurusan terbitkan SK Penguji
                      │
                      ▼
              Seminar Proposal dilaksanakan
                      │
                      ▼
              Dosen Penguji & Pembimbing input nilai
                      │
                      ▼
              TU cetak berita acara & arsip dokumen
                      │
                      ▼
              Mahasiswa lanjut ke tahap Bimbingan & Seminar Hasil
```

**Pihak yang terlibat:** Mahasiswa → Dosen Pembimbing → TU → Sekretaris → Ketua Jurusan → Dosen Penguji

---

### 4.3 Alur Pendaftaran Seminar Hasil

```
Mahasiswa lulus Seminar Proposal + ACC Pembimbing
        │
        ▼
Mahasiswa isi form & upload berkas Seminar Hasil
        │
        ▼
TU memverifikasi kelengkapan berkas
        │
   ┌────┴──────────┐
Tidak Lengkap      Lengkap
   │                  │
   ▼                  ▼
Notif kurang    TU menjadwalkan Seminar Hasil
berkas          (slot waktu, ruangan, penguji)
                      │
                      ▼
              Sekretaris kirim undangan penguji
                      │
                      ▼
              Ketua Jurusan terbitkan SK Penguji
                      │
                      ▼
              Seminar Hasil dilaksanakan
                      │
                      ▼
              Dosen Penguji & Pembimbing input nilai
                      │
                      ▼
              TU cetak berita acara & arsip dokumen
                      │
                      ▼
              Mahasiswa lanjut ke tahap Munaqasyah
```

**Pihak yang terlibat:** Mahasiswa → Dosen Pembimbing → TU → Sekretaris → Ketua Jurusan → Dosen Penguji

---

### 4.4 Alur Pendaftaran Munaqasyah (Seminar Tutup)

```
Mahasiswa lulus Seminar Hasil + ACC Pembimbing
+ seluruh persyaratan administrasi terpenuhi
        │
        ▼
Mahasiswa isi form & upload berkas Munaqasyah
(berkas lebih lengkap: bebas pustaka, lunas keuangan, dll.)
        │
        ▼
TU memverifikasi kelengkapan berkas
        │
   ┌────┴──────────┐
Tidak Lengkap      Lengkap
   │                  │
   ▼                  ▼
Notif kurang    Ketua Jurusan approve jadwal
berkas          & terbitkan SK Penguji Munaqasyah
                      │
                      ▼
              Sekretaris kirim undangan resmi penguji
                      │
                      ▼
              Munaqasyah dilaksanakan
                      │
                      ▼
              Dosen Penguji & Pembimbing input nilai sidang
                      │
                      ▼
              Sistem menghitung nilai akhir TA
                      │
                ┌─────┴──────────────┐
          Perlu Perbaikan         Lulus
                │                    │
                ▼                    ▼
          Mahasiswa revisi    TU & Sekretaris
          sesuai catatan      proses berkas yudisium
          penguji                    │
                                     ▼
                              Mahasiswa dinyatakan lulus
                              & dokumen kelulusan diterbitkan
```

**Pihak yang terlibat:** Mahasiswa → Dosen Pembimbing → TU → Ketua Jurusan → Sekretaris → Dosen Penguji

---

## 5. Status Dokumen & Pengajuan

Setiap pengajuan dalam sistem memiliki status yang konsisten dan dapat dilacak oleh semua pihak yang berkepentingan:

| Status | Keterangan |
|---|---|
| `Draft` | Mahasiswa masih mengisi form, belum disubmit |
| `Diajukan` | Sudah disubmit, menunggu review atau verifikasi |
| `Diverifikasi` | Berkas sudah diverifikasi TU, menunggu penjadwalan |
| `Dijadwalkan` | Sudah ada jadwal seminar atau munaqasyah |
| `Perlu Revisi` | Dikembalikan dengan catatan perbaikan |
| `Disetujui` | Disetujui oleh pihak berwenang |
| `Ditolak` | Ditolak, mahasiswa perlu mengajukan ulang |
| `Selesai` | Proses tuntas, nilai sudah diinput |

---

## 6. Notifikasi Otomatis

Sistem mengirim notifikasi in-app dan email secara otomatis berdasarkan kejadian berikut:

| Kejadian | Penerima Notifikasi |
|---|---|
| Mahasiswa mengajukan judul | DPA yang bersangkutan |
| DPA memutuskan judul (setuju / revisi / tolak) | Mahasiswa |
| Judul disetujui DPA | Ketua Jurusan |
| SK Pembimbing diterbitkan | Mahasiswa + Dosen Pembimbing TA |
| Mahasiswa mendaftar Seminar Proposal | TU |
| Verifikasi berkas Seminar Proposal selesai | Mahasiswa (lolos atau kurang berkas) |
| Seminar Proposal dijadwalkan | Mahasiswa + semua Dosen Penguji |
| Nilai Seminar Proposal diinput | Mahasiswa |
| Mahasiswa mendaftar Seminar Hasil | TU |
| Verifikasi berkas Seminar Hasil selesai | Mahasiswa (lolos atau kurang berkas) |
| Seminar Hasil dijadwalkan | Mahasiswa + semua Dosen Penguji |
| Nilai Seminar Hasil diinput | Mahasiswa |
| Mahasiswa mendaftar Munaqasyah | TU + Ketua Jurusan |
| Verifikasi berkas Munaqasyah selesai | Mahasiswa (lolos atau kurang berkas) |
| Munaqasyah dijadwalkan | Mahasiswa + semua Dosen Penguji |
| Nilai akhir Munaqasyah diinput | Mahasiswa |
| Mahasiswa tidak ada progres lebih dari 3 bulan | DPA + Ketua Jurusan |
| Deadline semester TA semakin dekat | Mahasiswa yang belum menyelesaikan tahapan |

---

*Dokumen fitur ini dibuat berdasarkan SISTEM INFORMASI TUGAS AKHIR v1.2. Dapat disesuaikan dengan kebutuhan spesifik jurusan.*
