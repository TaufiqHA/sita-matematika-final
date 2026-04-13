<?php

namespace Tests\Feature;

use App\Models\PendaftaranSeminar;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PendaftaranSeminarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_mahasiswa_can_submit_pendaftaran_with_berkas(): void
    {
        Storage::fake('private');
        $user = User::where('email', 'mhs1@test.com')->first();

        $response = $this->actingAs($user)->withHeaders([
            'Referer' => 'http://localhost',
        ])->postJson('/api/v1/seminar/pendaftaran', [
            'jenis_seminar' => 'proposal',
            'berkas' => [
                [
                    'nama_berkas' => 'KRS',
                    'file' => UploadedFile::fake()->create('krs.pdf', 500),
                ],
                [
                    'nama_berkas' => 'Kwitansi',
                    'file' => UploadedFile::fake()->create('kwitansi.jpg', 500),
                ],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('pendaftaran_seminars', [
            'mahasiswa_id' => $user->id,
            'jenis_seminar' => 'proposal',
            'status' => 'diajukan',
        ]);

        $this->assertDatabaseCount('berkas_seminars', 2);

        $paths = collect($response->json('berkas'))->pluck('file_path');
        foreach ($paths as $path) {
            Storage::disk('private')->assertExists($path);
        }
    }

    public function test_tu_can_see_all_pendaftaran(): void
    {
        $mhs1 = User::where('email', 'mhs1@test.com')->first();
        $mhs2 = User::where('email', 'mhs2@test.com')->first();
        $tu = User::where('email', 'tu@test.com')->first();

        PendaftaranSeminar::create(['mahasiswa_id' => $mhs1->id, 'jenis_seminar' => 'proposal']);
        PendaftaranSeminar::create(['mahasiswa_id' => $mhs2->id, 'jenis_seminar' => 'hasil']);

        $response = $this->actingAs($tu)->withHeaders([
            'Referer' => 'http://localhost',
        ])->getJson('/api/v1/seminar/pendaftaran');

        $response->assertOk();
        $response->assertJsonCount(2);
    }

    public function test_tu_can_verify_pendaftaran_and_berkas(): void
    {
        $mhs1 = User::where('email', 'mhs1@test.com')->first();
        $tu = User::where('email', 'tu@test.com')->first();

        $pendaftaran = PendaftaranSeminar::create(['mahasiswa_id' => $mhs1->id, 'jenis_seminar' => 'proposal']);
        $berkas = $pendaftaran->berkas()->create([
            'nama_berkas' => 'KRS',
            'file_path' => 'dummy/path.pdf',
        ]);

        $response = $this->actingAs($tu)->withHeaders([
            'Referer' => 'http://localhost',
        ])->patchJson("/api/v1/seminar/pendaftaran/{$pendaftaran->id}/verifikasi", [
            'status' => 'diverifikasi',
            'catatan_tu' => 'Lengkap',
            'berkas_status' => [
                ['id' => $berkas->id, 'status' => 'valid'],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('pendaftaran_seminars', [
            'id' => $pendaftaran->id,
            'status' => 'diverifikasi',
            'catatan_tu' => 'Lengkap',
        ]);

        $this->assertDatabaseHas('berkas_seminars', [
            'id' => $berkas->id,
            'status_verifikasi' => 'valid',
        ]);
    }

    public function test_mahasiswa_can_upload_additional_berkas(): void
    {
        Storage::fake('private');
        $user = User::where('email', 'mhs1@test.com')->first();
        $pendaftaran = PendaftaranSeminar::create(['mahasiswa_id' => $user->id, 'jenis_seminar' => 'proposal']);

        $response = $this->actingAs($user)->withHeaders([
            'Referer' => 'http://localhost',
        ])->postJson("/api/v1/seminar/pendaftaran/{$pendaftaran->id}/berkas", [
            'nama_berkas' => 'Revisi Proposal',
            'file' => UploadedFile::fake()->create('revisi.pdf', 1000),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('berkas_seminars', [
            'pendaftaran_id' => $pendaftaran->id,
            'nama_berkas' => 'Revisi Proposal',
        ]);

        Storage::disk('private')->assertExists($response->json('file_path'));
    }
}
