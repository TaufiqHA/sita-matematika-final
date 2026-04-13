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
