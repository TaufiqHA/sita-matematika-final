# Planning Implementasi Blok 1: Setup & Fondasi (Backend Saja)

Dokumen ini berisi langkah-langkah terstruktur dan komprehensif untuk mengimplementasikan **Blok 1** dari `docs/timeline.md`. Instruksi ini ditujukan untuk **Junior Developer** atau **AI Model** untuk dieksekusi langkah demi langkah. Fokus implementasi murni pada sisi **Backend (API)**.

## Tujuan Utama Blok 1
1. Instalasi package tambahan prioritas (`spatie/laravel-permission` & `spatie/laravel-activitylog`).
2. Konfigurasi Database, CORS, dan Laravel Sanctum untuk SPA Authentication.
3. Pembuatan struktur tabel (Migrations) untuk ekstensi user: `mahasiswas` dan `dosens`.
4. Setup Seeder dengan role dan data dummy untuk keperluan testing.
5. Pembuatan Endpoint Authentication berbasis cookie/session via Sanctum (Login, Logout, Get User).

---

## Langkah 1: Instalasi Package & Setup Konfigurasi

### 1.1 Install Dependencies
Jalankan perintah berikut di terminal:
```bash
composer require spatie/laravel-permission spatie/laravel-activitylog
```

### 1.2 Publish Configuration & Migrations
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
```

### 1.3 Setup CORS dan Sanctum
**Instruksi:**
1. Buka file `.env`. Pastikan ada variabel `SANCTUM_STATEFUL_DOMAINS` yang diset ke domain frontend (misal: `localhost,localhost:5173,127.0.0.1,127.0.0.1:5173`).
2. Pastikan file konfigurasi `config/cors.php` sudah diset agar frontend Vue bisa melakukan hit API. Pastikan opsi `supports_credentials` diset ke `true`. (Jika file tidak ada, publish file konfigurasi CORS bawaan framework).
3. Buka konfigurasi middleware (`bootstrap/app.php` jika di Laravel 11). Pastikan API middleware memuat stateful middleware untuk Sanctum jika belum otomatis tersetting.

---

## Langkah 2: Pembuatan Migrations & Models

### 2.1 Update Migrasi Tabel `users`
**Instruksi:**
Buka file migrasi tabel `users` bawaan Laravel (di `database/migrations/0001_01_01_000000_create_users_table.php`).
- Tambahkan kolom role: `$table->string('role')->default('mahasiswa');` (untuk memudahkan deteksi role secara ringkas tanpa query tambahan, meskipun kita akan tetap menggunakan Spatie Permission untuk hal yang lebih kompleks).
- Tambahkan `'role'` ke `$fillable` array pada model `App\Models\User`.

### 2.2 Buat Model dan Migrasi `Mahasiswa`
```bash
php artisan make:model Mahasiswa -m
```
**Instruksi Struktur Tabel `mahasiswas` di Migration:**
- `$table->id();`
- `$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();` (Tautan ke tabel Users)
- `$table->string('nim')->unique();`
- `$table->string('angkatan');`
- `$table->string('program_studi');`
- `$table->foreignId('dpa_id')->nullable()->constrained('users')->nullOnDelete();` (Dosen Pembimbing Akademik)
- `$table->timestamps();`

### 2.3 Buat Model dan Migrasi `Dosen`
```bash
php artisan make:model Dosen -m
```
**Instruksi Struktur Tabel `dosens` di Migration:**
- `$table->id();`
- `$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();` (Tautan ke tabel Users)
- `$table->string('nidn')->unique();`
- `$table->string('bidang_keahlian')->nullable();`
- `$table->timestamps();`

### 2.4 Jalankan Migrasi
Setelah file migration disesuaikan, jalankan:
```bash
php artisan migrate
```

---

## Langkah 3: Setup Relasi pada Model

**Instruksi untuk `App\Models\User`:**
Tambahkan trait Spatie dan relasi ke entitas lain.
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasApiTokens, Notifiable; // dsb
    
    // ... relasi ...
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class);
    }
}
```

**Instruksi untuk `App\Models\Mahasiswa`:**
Tambahkan `$fillable` fields dan relasi ini:
```php
public function user() { return $this->belongsTo(User::class); }
public function dpa() { return $this->belongsTo(User::class, 'dpa_id'); }
```

**Instruksi untuk `App\Models\Dosen`:**
Tambahkan `$fillable` fields dan relasi ini:
```php
public function user() { return $this->belongsTo(User::class); }
```

---

## Langkah 4: Pembuatan Seeder

### 4.1 Buat Role Seeder
```bash
php artisan make:seeder RoleSeeder
```
**Instruksi:**
Gunakan `Spatie\Permission\Models\Role`. Buat Role: `Admin`, `Mahasiswa`, `DPA`, `TU`, `Kajur`.

### 4.2 Update DatabaseSeeder
**Instruksi `DatabaseSeeder`:**
Pastikan `RoleSeeder::class` dipanggil terlebih dahulu, kemudian buat entri dummy user sesuai spesifikasi berikut:
1. **User Admin**: Buat 1 user (role `Admin`, email `admin@test.com`). Assign role menggunakan trait Spatie `$user->assignRole('Admin')`.
2. **User TU**: Buat 1 user (role `TU`, email `tu@test.com`).
3. **User Kajur**: Buat 1 user (role `Kajur`, email `kajur@test.com`).
4. **User Dosen/DPA**: Buat 1 user (role `DPA`, email `dpa@test.com`). Setelah user dibuat, buat entri di tabel `dosens` yang mengarah ke `user_id` dosen tersebut.
5. **User Mahasiswa**: Buat 2 user (role `Mahasiswa`, email `mhs1@test.com` & `mhs2@test.com`). Setelah user dibuat, buat entri di tabel `mahasiswas` untuk masing-masing user, pastikan atribut `dpa_id` diisi dengan `user_id` dari user DPA yang dibuat di poin 4.

Jalankan perintah seeder:
```bash
php artisan db:seed
```

---

## Langkah 5: Endpoint SPA Authentication

Berdasarkan timeline, aplikasi akan dikomunikasikan via Vue.js dengan Sanctum (SPA Cookie-based authentication).

### 5.1 Buat AuthController
```bash
php artisan make:controller Api/AuthController
```

### 5.2 Implementasi Controller Auth
**Instruksi:**
Di `AuthController`, buat tiga fungsi:
1. `login(Request $request)`:
   - Validasi parameter `email` dan `password`.
   - Lakukan otentikasi menggunakan session stateful: `Auth::guard('web')->attempt()`.
   - Gunakan `$request->session()->regenerate()` jika berhasil, dan return JSON response data user. Tidak perlu mereturn token plain-text jika menggunakan metode SPA Sanctum.
2. `logout(Request $request)`:
   - `Auth::guard('web')->logout();`
   - `$request->session()->invalidate();`
   - `$request->session()->regenerateToken();`
   - Return pesan JSON logout success.
3. `me(Request $request)`:
   - Kembalikan data user yang sedang login beserta role-nya (dan data relasi `mahasiswa` / `dosen` jika ada). `return response()->json($request->user()->load(['roles', 'mahasiswa', 'dosen']));`.

### 5.3 Daftarkan Routes
Tambahkan di file `routes/api.php` (dengan syarat file ini dipetakan di `bootstrap/app.php` untuk API):
```php
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
```
*Catatan:* Laravel 11 sudah mendefinisikan rute khusus untuk CSRF cookie di `GET /sanctum/csrf-cookie`.

---

## Langkah 6: Tahap Verifikasi (Acceptance Criteria)
Sebelum melanjutkan ke **Blok 2**, Junior Dev/AI Agent harus memverifikasi hal-hal ini (via Postman / Insomnia / cURL):
1. [ ] Memastikan `GET /sanctum/csrf-cookie` mengembalikan XSRF-TOKEN di cookie (bukan 404).
2. [ ] Memastikan `POST /api/login` dengan payload test berhasil memberikan response cookie session dan data user lengkap dengan relasi role.
3. [ ] Memastikan rute terproteksi `GET /api/me` mereturn data user ketika dipanggil dengan header dan cookie session yang tepat.
4. [ ] Memastikan data user hasil seeding di database berisi tabel yang terhubung (User punya 1 Mahasiswa atau 1 Dosen sesuai role).

**Akhir dari Perencanaan Blok 1.** Jika berhasil dicentang semua, sistem authentication telah berjalan sesuai kebutuhan proyek.

---

# Planning Implementasi Blok 2: Modul Pengajuan Judul (Backend)

Dokumen ini berisi langkah-langkah terstruktur untuk mengimplementasikan **Blok 2** dari `docs/timeline.md`. Fokus pada fitur pengajuan judul oleh Mahasiswa dan review (approve/tolak) oleh DPA (Dosen Pembimbing Akademik).

## Tujuan Utama Blok 2
1. Membuat tabel `pengajuan_juduls` beserta Model dan Relasinya.
2. Membuat Form Request untuk validasi input (submit judul & review DPA).
3. Membuat Controller `PengajuanJudulController` untuk menangani CRUD dan logika bisnis.
4. Menambahkan endpoint API yang dilindungi oleh middleware role-based.
5. Menangani upload file dokumen proposal PDF ke local storage private.

---

## Langkah 1: Pembuatan Migration dan Model

Jalankan perintah berikut di terminal untuk membuat Model beserta Migration-nya:
```bash
php artisan make:model PengajuanJudul -m
```

### 1.1 Struktur Tabel `pengajuan_juduls` di Migration
Buka file migrasi yang baru saja dibuat di folder `database/migrations/` dan sesuaikan struktur tabelnya:
```php
public function up()
{
    Schema::create('pengajuan_juduls', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
        $table->string('judul');
        $table->string('topik');
        $table->string('bidang');
        $table->text('latar_belakang');
        $table->string('file_proposal')->nullable(); // Path file PDF
        $table->enum('status', ['draft', 'diajukan', 'perlu_revisi', 'disetujui', 'ditolak'])->default('diajukan');
        $table->text('catatan_dpa')->nullable();
        $table->timestamps();
    });
}
```

Jalankan migrasi:
```bash
php artisan migrate
```

---

## Langkah 2: Setup Relasi pada Model

Buka model `App\Models\PengajuanJudul` dan tambahkan properti fillable serta relasinya:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengajuanJudul extends Model
{
    use HasFactory;

    protected $fillable = [
        'mahasiswa_id',
        'judul',
        'topik',
        'bidang',
        'latar_belakang',
        'file_proposal',
        'status',
        'catatan_dpa',
    ];

    // Relasi ke tabel users (mahasiswa yang mengajukan)
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}
```

**Instruksi Tambahan untuk `App\Models\User`:**
Pastikan model `User` sudah memiliki fungsi untuk mengambil daftar pengajuan (untuk mahasiswa).
```php
public function pengajuanJuduls()
{
    return $this->hasMany(PengajuanJudul::class, 'mahasiswa_id');
}
```

---

## Langkah 3: Pembuatan Form Request (Validasi)

### 3.1 Request untuk Submit Judul
Jalankan perintah:
```bash
php artisan make:request StorePengajuanJudulRequest
```
**Instruksi `StorePengajuanJudulRequest`:**
- Ubah method `authorize()` agar me-return `true` (atau batasi hanya untuk role mahasiswa).
- Tambahkan rules di method `rules()`:
```php
return [
    'judul' => 'required|string|max:255',
    'topik' => 'required|string|max:255',
    'bidang' => 'required|string|max:255',
    'latar_belakang' => 'required|string',
    'file_proposal' => 'required|file|mimes:pdf|max:5120', // Maks 5MB
];
```

### 3.2 Request untuk Review DPA
Jalankan perintah:
```bash
php artisan make:request ReviewPengajuanJudulRequest
```
**Instruksi `ReviewPengajuanJudulRequest`:**
- Ubah method `authorize()` menjadi `true` (atau batasi khusus role DPA).
- Tambahkan rules:
```php
return [
    'status' => 'required|in:perlu_revisi,disetujui,ditolak',
    'catatan_dpa' => 'nullable|string'
];
```

---

## Langkah 4: Pembuatan Controller

Jalankan perintah:
```bash
php artisan make:controller Api/v1/PengajuanJudulController
```

**Instruksi untuk `PengajuanJudulController`:**
1. **`index(Request $request)`**
   - Ambil data `PengajuanJudul`.
   - **Filter Role:**
     - Jika user login adalah `Mahasiswa` (cek role): kembalikan hanya pengajuan milik user tersebut (`where('mahasiswa_id', $user->id)`).
     - Jika user login adalah `DPA` (cek role): kembalikan daftar pengajuan dari seluruh mahasiswa perwaliannya. Anda bisa melakukan join dengan tabel `mahasiswas` di mana `dpa_id` sama dengan ID Dosen/User login saat ini.
   - Return menggunakan `response()->json()`.

2. **`store(StorePengajuanJudulRequest $request)`**
   - Ambil data tervalidasi.
   - Simpan file PDF ke `storage/app/private/proposals`.
     *(Contoh: `$path = $request->file('file_proposal')->store('proposals', 'private');`)*
   - Simpan record ke database menggunakan `$request->user()->pengajuanJuduls()->create([...])` dengan path file yang sudah didapat dan status default `diajukan`.
   - Return response data dengan HTTP code 201 (Created).

3. **`show($id)`**
   - Cari data berdasarkan ID (`PengajuanJudul::with('mahasiswa')->findOrFail($id);`).
   - *Otorisasi (Optional):* Pastikan hanya pembuat (mahasiswa) atau DPA terkait yang bisa melihat detail (bisa diletakkan di logic atau Policy).
   - Return detail record dalam bentuk JSON.

4. **`review(ReviewPengajuanJudulRequest $request, $id)`**
   - Cari data berdasarkan ID.
   - Update field `status` dan `catatan_dpa` sesuai payload request.
   - Return JSON response berisi data terbaru.

---

## Langkah 5: Setup Routes & Middleware

Buka file `routes/api.php` dan daftarkan routing untuk fitur ini.

Tambahkan di dalam blok middleware Sanctum yang sudah dibuat sebelumnya di Blok 1:

```php
use App\Http\Controllers\Api\v1\PengajuanJudulController;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    
    // Rute untuk Mahasiswa & DPA (Filter list data ditangani di controller)
    Route::get('/pengajuan-judul', [PengajuanJudulController::class, 'index']);
    Route::get('/pengajuan-judul/{id}', [PengajuanJudulController::class, 'show']);

    // Khusus Mahasiswa (Role middleware disesuaikan dengan Spatie Permission)
    Route::post('/pengajuan-judul', [PengajuanJudulController::class, 'store'])
        ->middleware('role:Mahasiswa');

    // Khusus DPA
    Route::patch('/pengajuan-judul/{id}/review', [PengajuanJudulController::class, 'review'])
        ->middleware('role:DPA');

});
```

*(Catatan: Pastikan Spatie Permission middleware alias seperti `role` sudah didaftarkan pada `bootstrap/app.php` jika menggunakan Laravel 11, atau `Kernel.php` untuk versi lama, sehingga bisa digunakan di rute).*

---

## Langkah 6: Tahap Verifikasi (Acceptance Criteria)

Sebelum berpindah ke Blok selanjutnya, pastikan hal-hal berikut telah diuji (bisa menggunakan Postman/Insomnia):

1. [ ] **Role Mahasiswa - Submit Judul:** `POST /api/v1/pengajuan-judul` dengan form-data (termasuk file PDF `file_proposal`) berhasil mengembalikan HTTP 201 dan menyimpan file PDF di folder `storage/app/private/proposals`.
2. [ ] **Role Mahasiswa - List Judul:** `GET /api/v1/pengajuan-judul` saat login sebagai Mahasiswa hanya menampilkan pengajuan judul miliknya sendiri.
3. [ ] **Role DPA - List Judul Mahasiswa Bimbingan:** `GET /api/v1/pengajuan-judul` saat login sebagai DPA menampilkan daftar pengajuan dari mahasiswa bimbingannya.
4. [ ] **Role DPA - Review Judul:** `PATCH /api/v1/pengajuan-judul/{id}/review` berhasil mengubah status menjadi `disetujui`, `perlu_revisi`, atau `ditolak` beserta catatan, serta mengembalikan JSON yang benar.
5. [ ] **File Storage & Otorisasi:** Mengecek bahwa file fisik benar-benar ada di storage, dan pengguna yang role-nya tidak sesuai (misal Mahasiswa mencoba hit endpoint review DPA) mendapatkan response `403 Forbidden`.

**Akhir dari Perencanaan Blok 2.**

---

# Planning Implementasi Blok 3: Modul Pendaftaran Seminar Proposal (Backend)

Dokumen ini berisi langkah-langkah terstruktur untuk mengimplementasikan **Blok 3** dari `docs/timeline.md`. Fokus pada fitur mahasiswa mendaftar seminar proposal (termasuk upload berkas terkait) dan review/verifikasi oleh Staf TU (Tata Usaha).

## Tujuan Utama Blok 3
1. Membuat tabel `pendaftaran_seminars` beserta Model dan Relasinya.
2. Membuat tabel `berkas_seminars` untuk menyimpan multiple file per pendaftaran.
3. Membuat Form Request untuk validasi input (submit pendaftaran, upload berkas tambahan, verifikasi TU).
4. Membuat Controller `PendaftaranSeminarController` untuk menangani CRUD.
5. Menambahkan endpoint API yang dilindungi oleh middleware berbasis role.

---

## Langkah 1: Pembuatan Migration dan Model

Jalankan perintah berikut di terminal untuk membuat Model beserta Migration untuk Pendaftaran Seminar dan Berkas Seminar:
```bash
php artisan make:model PendaftaranSeminar -m
php artisan make:model BerkasSeminar -m
```

### 1.1 Struktur Tabel `pendaftaran_seminars` di Migration
Buka file migrasi untuk `pendaftaran_seminars` di `database/migrations/` dan sesuaikan struktur tabelnya:
```php
public function up()
{
    Schema::create('pendaftaran_seminars', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
        $table->enum('jenis_seminar', ['proposal', 'hasil', 'munaqasyah']);
        $table->enum('status', ['diajukan', 'perlu_revisi', 'diverifikasi', 'dijadwalkan', 'selesai'])->default('diajukan');
        $table->text('catatan_tu')->nullable();
        $table->timestamps();
    });
}
```

### 1.2 Struktur Tabel `berkas_seminars` di Migration
Buka file migrasi untuk `berkas_seminars` dan sesuaikan:
```php
public function up()
{
    Schema::create('berkas_seminars', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pendaftaran_id')->constrained('pendaftaran_seminars')->cascadeOnDelete();
        $table->string('nama_berkas'); // Contoh: 'KRS', 'Kwitansi', 'Lembar Persetujuan'
        $table->string('file_path');   // Path file
        $table->string('status_verifikasi')->default('pending'); // pending, valid, tidak_valid
        $table->timestamps();
    });
}
```

Jalankan migrasi:
```bash
php artisan migrate
```

---

## Langkah 2: Setup Relasi pada Model

### 2.1 Model `PendaftaranSeminar`
Buka `App\Models\PendaftaranSeminar` dan tambahkan properti fillable serta relasi:
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendaftaranSeminar extends Model
{
    use HasFactory;

    protected $fillable = [
        'mahasiswa_id',
        'jenis_seminar',
        'status',
        'catatan_tu',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function berkas()
    {
        return $this->hasMany(BerkasSeminar::class, 'pendaftaran_id');
    }
}
```

### 2.2 Model `BerkasSeminar`
Buka `App\Models\BerkasSeminar` dan tambahkan hal berikut:
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BerkasSeminar extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftaran_id',
        'nama_berkas',
        'file_path',
        'status_verifikasi',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranSeminar::class, 'pendaftaran_id');
    }
}
```

**Instruksi Tambahan untuk `App\Models\User`:**
Tambahkan fungsi untuk mengambil daftar pendaftaran seminar bagi mahasiswa.
```php
public function pendaftaranSeminars()
{
    return $this->hasMany(PendaftaranSeminar::class, 'mahasiswa_id');
}
```

---

## Langkah 3: Pembuatan Form Request (Validasi)

### 3.1 Request untuk Submit Pendaftaran (Beserta Berkas)
Jalankan perintah:
```bash
php artisan make:request StorePendaftaranSeminarRequest
```
**Instruksi `StorePendaftaranSeminarRequest`:**
- Ubah method `authorize()` menjadi `true` (atau batasi khusus role Mahasiswa).
- Tambahkan rules di method `rules()`. Kita mengasumsikan pendaftaran langsung di-submit beserta array file berkas.
```php
return [
    'jenis_seminar' => 'required|in:proposal,hasil,munaqasyah',
    'berkas' => 'required|array',
    'berkas.*.nama_berkas' => 'required|string|max:255',
    'berkas.*.file' => 'required|file|mimes:pdf,jpg,png|max:5120', // Maks 5MB per file
];
```

### 3.2 Request untuk Verifikasi TU
Jalankan perintah:
```bash
php artisan make:request VerifikasiSeminarRequest
```
**Instruksi `VerifikasiSeminarRequest`:**
- Ubah method `authorize()` menjadi `true` (atau batasi khusus role TU).
- Tambahkan rules:
```php
return [
    'status' => 'required|in:diverifikasi,perlu_revisi',
    'catatan_tu' => 'nullable|string',
    // Opsional: Jika TU mereview per berkas
    'berkas_status' => 'nullable|array',
    'berkas_status.*.id' => 'required|exists:berkas_seminars,id',
    'berkas_status.*.status' => 'required|in:valid,tidak_valid',
];
```

---

## Langkah 4: Pembuatan Controller

Jalankan perintah:
```bash
php artisan make:controller Api/v1/PendaftaranSeminarController
```

**Instruksi untuk `PendaftaranSeminarController`:**

1. **`index(Request $request)`**
   - Ambil data `PendaftaranSeminar` beserta relasi `berkas` dan `mahasiswa`.
   - **Filter Role:**
     - Jika role = `Mahasiswa`: filter berdasarkan `$user->id`.
     - Jika role = `TU`: tampilkan seluruh data pendaftaran yang ada (biasanya diurutkan dari yang statusnya `diajukan`).
   - Return menggunakan JSON.

2. **`store(StorePendaftaranSeminarRequest $request)`**
   - Gunakan `DB::transaction()` untuk memastikan integritas data.
   - Buat record utama: `$pendaftaran = $request->user()->pendaftaranSeminars()->create(['jenis_seminar' => $request->jenis_seminar]);`.
   - Lakukan loop pada array `$request->berkas`:
     - Simpan masing-masing file ke `storage/app/private/berkas_seminar`.
     - Buat record `BerkasSeminar` terkait ke tabel.
   - Commit transaction dan return response data dengan HTTP code 201.

3. **`uploadBerkasTambahan(Request $request, $id)`** *(Optional endpoint, tapi ditulis di docs timeline)*
   - Endpoint khusus jika mahasiswa ingin menambah berkas secara susulan (karena revisi).
   - Validasi input `nama_berkas` dan `file`. Simpan file dan hubungkan ke ID pendaftaran bersangkutan.

4. **`verifikasi(VerifikasiSeminarRequest $request, $id)`**
   - Cari data `PendaftaranSeminar` berdasarkan ID.
   - Update field `status` dan `catatan_tu` (hanya jika aktor adalah TU).
   - Jika TU juga mengirim array `berkas_status`, lakukan iterasi dan update field `status_verifikasi` untuk setiap `BerkasSeminar` yang dituju.
   - Return JSON response data terbaru.

---

## Langkah 5: Setup Routes & Middleware

Buka file `routes/api.php`, tambahkan rute Pendaftaran Seminar di dalam block middleware Sanctum:

```php
use App\Http\Controllers\Api\v1\PendaftaranSeminarController;

Route::middleware('auth:sanctum')->prefix('v1/seminar')->group(function () {
    
    // List pendaftaran seminar (di-filter role pada controller)
    Route::get('/pendaftaran', [PendaftaranSeminarController::class, 'index']);
    Route::get('/pendaftaran/{id}', [PendaftaranSeminarController::class, 'show']);

    // Khusus Mahasiswa
    Route::post('/pendaftaran', [PendaftaranSeminarController::class, 'store'])
        ->middleware('role:Mahasiswa');
    Route::post('/pendaftaran/{id}/berkas', [PendaftaranSeminarController::class, 'uploadBerkasTambahan'])
        ->middleware('role:Mahasiswa');

    // Khusus TU
    Route::patch('/pendaftaran/{id}/verifikasi', [PendaftaranSeminarController::class, 'verifikasi'])
        ->middleware('role:TU');

});
```

---

## Langkah 6: Tahap Verifikasi (Acceptance Criteria)

Sebelum menganggap Blok 3 selesai, pastikan skenario berikut telah diuji (via Postman/Insomnia):

1. [ ] **Submit Pendaftaran (Mahasiswa):** Melakukan `POST /api/v1/seminar/pendaftaran` dengan form-data yang berisi payload `jenis_seminar` dan *multiple files* `berkas[0][nama_berkas]`, `berkas[0][file]`, dll. Berhasil mengembalikan HTTP 201 beserta entri relasi di `berkas_seminars`.
2. [ ] **List Pendaftaran (Mahasiswa & TU):** `GET /api/v1/seminar/pendaftaran` menampilkan hasil yang benar sesuai otorisasi pemanggil (Mahasiswa melihat datanya sendiri, TU melihat semua pendaftaran).
3. [ ] **Verifikasi TU:** `PATCH /api/v1/seminar/pendaftaran/{id}/verifikasi` oleh TU berhasil merubah status (misal menjadi `diverifikasi` atau `perlu_revisi`) dan menyimpan catatan TU.
4. [ ] **Upload File Susulan (Optional):** Mahasiswa bisa melakukan POST di rute `/pendaftaran/{id}/berkas` untuk melampirkan berkas yang kurang atau direvisi.

**Akhir dari Perencanaan Blok 3.**
