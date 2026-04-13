# MVP — Sistem Informasi Tugas Akhir (SITA)

> MVP (Minimum Viable Product) ini mendefinisikan fitur paling esensial yang harus berjalan agar SITA dapat digunakan secara nyata oleh jurusan. Fokusnya adalah pada alur inti yang paling sering dipakai dan paling kritis jika terganggu, yaitu: **pengajuan judul → bimbingan → pendaftaran & pelaksanaan Seminar Proposal**.
>
> Fitur lanjutan (Seminar Hasil, Munaqasyah, laporan statistik, tanda tangan digital, dll.) dikerjakan setelah MVP stabil di lapangan.

---

## Lingkup MVP

| # | Modul | Status |
|---|---|---|
| 1 | Autentikasi semua peran | ✅ MVP |
| 2 | Pengajuan & persetujuan judul TA | ✅ MVP |
| 3 | Penerbitan SK Pembimbing | ✅ MVP |
| 4 | Log bimbingan digital | ✅ MVP |
| 5 | Pendaftaran & verifikasi berkas Seminar Proposal | ✅ MVP |
| 6 | Penjadwalan Seminar Proposal | ✅ MVP |
| 7 | Input nilai Seminar Proposal | ✅ MVP |
| 8 | Notifikasi in-app dasar | ✅ MVP |
| 9 | Manajemen pengguna & role (Admin) | ✅ MVP |
| 10 | Seminar Hasil & Munaqasyah | ⏳ Pasca-MVP |
| 11 | Laporan statistik & dashboard lanjutan | ⏳ Pasca-MVP |
| 12 | Tanda tangan digital | ⏳ Pasca-MVP |
| 13 | Export PDF/Excel, repositori publik | ⏳ Pasca-MVP |

---

## Fitur MVP per Peran

### Mahasiswa

**Autentikasi**
- Login dan logout menggunakan NIM + password
- Reset password via email

**Pengajuan Judul TA**
- Mengisi form pengajuan judul: judul, topik, bidang, dan latar belakang singkat
- Upload draft proposal dalam format PDF
- Melihat status pengajuan: Draft / Diajukan / Perlu Revisi / Disetujui / Ditolak
- Menerima catatan revisi dari DPA dan mengajukan ulang

**Bimbingan**
- Mencatat sesi bimbingan: tanggal, topik bahasan, dan catatan singkat
- Upload laporan/revisi (satu versi aktif; versioning sederhana)
- Melihat catatan dan feedback dari dosen pembimbing
- Melihat status ACC dari dosen pembimbing

**Pendaftaran Seminar Proposal**
- Mengisi form pendaftaran Seminar Proposal
- Upload berkas persyaratan yang ditentukan
- Melihat status verifikasi berkas oleh TU
- Melihat jadwal seminar (tanggal, waktu, ruangan, daftar penguji) setelah dijadwalkan
- Melihat nilai setelah diinput dosen

**Progress & Notifikasi**
- Progress bar sederhana tahapan TA yang sudah dicapai
- Notifikasi in-app untuk perubahan status yang relevan (judul disetujui/ditolak, berkas diverifikasi, jadwal terbit, nilai masuk)

---

### Dosen Pembimbing Akademik (DPA)

**Autentikasi**
- Login dan logout menggunakan akun dosen

**Review Judul TA**
- Melihat daftar pengajuan judul dari mahasiswa perwalian
- Membaca detail judul dan membuka file draft proposal
- Memberi keputusan: Disetujui / Perlu Revisi / Ditolak disertai catatan
- Melihat riwayat keputusan yang pernah diberikan

**Monitoring Dasar**
- Daftar mahasiswa perwalian beserta status tahapan TA terkini
- Notifikasi in-app saat ada pengajuan judul baru

---

### Dosen Pembimbing TA

**Autentikasi**
- Login dan logout menggunakan akun dosen

**Bimbingan**
- Melihat daftar mahasiswa bimbingan aktif
- Menambahkan catatan/feedback per sesi bimbingan yang dicatat mahasiswa
- Memberikan ACC atau mengembalikan laporan untuk direvisi
- Memberikan ACC pendaftaran Seminar Proposal

**Penilaian**
- Input nilai Seminar Proposal per komponen penilaian
- Notifikasi jadwal seminar mahasiswa bimbingan

**Sebagai Dosen Penguji (jika ditugaskan)**
- Menerima notifikasi undangan sebagai penguji
- Mengakses laporan TA yang akan diuji
- Input nilai sebagai dosen penguji

---

### Tata Usaha (TU)

**Autentikasi**
- Login dan logout

**Verifikasi Berkas**
- Melihat antrian pendaftaran Seminar Proposal yang masuk
- Checklist digital kelengkapan berkas per pendaftaran
- Menyetujui atau menolak pendaftaran dengan keterangan kekurangan berkas
- Notifikasi otomatis ke mahasiswa atas hasil verifikasi

**Penjadwalan Seminar Proposal**
- Membuat slot jadwal: tanggal, waktu, dan ruangan
- Menugaskan dosen penguji ke jadwal seminar
- Sistem mendeteksi jika ada konflik jadwal dosen
- Mencetak undangan seminar (format sederhana)

**Rekap**
- Daftar pendaftaran Seminar Proposal per status (antrian / diverifikasi / dijadwalkan / selesai)

---

### Ketua Jurusan

**Autentikasi**
- Login dan logout

**Penerbitan SK**
- Menerima notifikasi judul yang sudah disetujui DPA
- Menerbitkan SK Pembimbing TA (generate dokumen sederhana)
- Menerbitkan SK Penguji Seminar Proposal
- Riwayat SK yang pernah diterbitkan

**Monitoring Dasar**
- Daftar mahasiswa TA aktif beserta tahapan terkini
- Daftar mahasiswa tanpa progres lebih dari 3 bulan

---

### Sekretaris Jurusan

**Autentikasi**
- Login dan logout

**Koordinasi Seminar Proposal**
- Melihat rekap jadwal Seminar Proposal yang telah dibuat TU
- Mengirim undangan ke dosen penguji
- Mencatat konfirmasi kehadiran dosen penguji
- Mengarsipkan berita acara setelah seminar selesai

**Surat & Dokumen**
- Membuat surat undangan dosen penguji dari template yang tersedia

---

### Admin

**Autentikasi**
- Login dan logout dengan akses penuh

**Manajemen Pengguna**
- Tambah, edit, dan nonaktifkan akun pengguna
- Atur role per pengguna (Mahasiswa / DPA / Dosen Pembimbing / TU / Kajur / Sekretaris / Admin)
- Reset password pengguna
- Import data mahasiswa dan dosen dari file CSV

**Konfigurasi Sistem**
- Mengatur tahun ajaran dan semester aktif
- Konfigurasi daftar syarat berkas Seminar Proposal
- Mengelola template dokumen dasar (SK, undangan, berita acara)

**Keamanan & Log**
- Audit log perubahan data penting
- Backup database manual

---

## Alur MVP: Pengajuan Judul → Seminar Proposal

```
[Mahasiswa] Isi form judul + upload draft proposal
        │
        ▼
[DPA] Review judul
        │
   ┌────┴─────────────────┐
Perlu Revisi           Disetujui
   │                       │
   ▼                       ▼
[Mahasiswa]         [Ketua Jurusan] Terbitkan SK Pembimbing
revisi & ajukan ulang      │
                           ▼
                  [Mahasiswa + Dosen Pembimbing] mulai bimbingan
                           │
                  [Mahasiswa] catat sesi bimbingan
                  [Dosen] beri feedback & ACC laporan
                           │
                           ▼
                  [Dosen Pembimbing] beri ACC Seminar Proposal
                           │
                           ▼
                  [Mahasiswa] isi form + upload berkas Seminar Proposal
                           │
                           ▼
                  [TU] verifikasi berkas
                           │
                   ┌───────┴──────────┐
               Kurang berkas       Lengkap
                   │                  │
                   ▼                  ▼
           [Mahasiswa]          [TU] jadwalkan seminar
           lengkapi berkas      (waktu, ruangan, penguji)
                                       │
                                       ▼
                              [Sekretaris] kirim undangan penguji
                                       │
                                       ▼
                              [Ketua Jurusan] terbitkan SK Penguji
                                       │
                                       ▼
                              Seminar Proposal dilaksanakan
                                       │
                                       ▼
                              [Dosen] input nilai
                                       │
                                       ▼
                              [Mahasiswa] melihat nilai
                              → lanjut ke Seminar Hasil (Pasca-MVP)
```

---

## Status Dokumen MVP

| Status | Digunakan di MVP |
|---|---|
| `Draft` | ✅ |
| `Diajukan` | ✅ |
| `Perlu Revisi` | ✅ |
| `Disetujui` | ✅ |
| `Ditolak` | ✅ |
| `Diverifikasi` | ✅ |
| `Dijadwalkan` | ✅ |
| `Selesai` | ✅ |

---

## Notifikasi MVP (In-App)

| Kejadian | Penerima |
|---|---|
| Mahasiswa mengajukan judul | DPA |
| DPA memberi keputusan judul | Mahasiswa |
| SK Pembimbing diterbitkan | Mahasiswa + Dosen Pembimbing |
| Mahasiswa mendaftar Seminar Proposal | TU |
| TU selesai verifikasi berkas | Mahasiswa |
| Seminar Proposal dijadwalkan | Mahasiswa + Dosen Penguji |
| Nilai Seminar Proposal diinput | Mahasiswa |

> Email otomatis tidak wajib di MVP — notifikasi in-app sudah cukup untuk validasi alur.

---

## Yang Sengaja Ditunda (Pasca-MVP)

| Fitur | Alasan Ditunda |
|---|---|
| Seminar Hasil & Munaqasyah | Bergantung pada alur Proposal yang sudah stabil |
| Kalkulasi nilai akhir & yudisium | Kompleks; butuh konfirmasi bobot nilai dari jurusan |
| Tanda tangan digital | Integrasi teknis tambahan; tidak kritis di fase awal |
| Export PDF/Excel laporan | Nice-to-have; bisa digantikan sementara dengan print halaman |
| Dashboard statistik lanjutan (grafik, BAN-PT) | Butuh data historis yang cukup dulu |
| Repositori TA publik | Fitur pengayaan, bukan inti alur kerja |
| Reminder otomatis mahasiswa tanpa progres | Butuh scheduler; implementasi setelah inti stabil |
| Import CSV massal & backup otomatis | Bisa dilakukan manual di fase awal |

---

*MVP ini mencakup alur paling kritis: dari pengajuan judul hingga pelaksanaan Seminar Proposal. Setelah MVP live dan divalidasi pengguna nyata, pengembangan dilanjutkan ke Seminar Hasil, Munaqasyah, dan fitur penyempurnaan lainnya.*
