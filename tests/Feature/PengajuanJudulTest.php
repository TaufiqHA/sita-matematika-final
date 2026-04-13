<?php

namespace Tests\Feature;

use App\Models\PengajuanJudul;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengajuanJudulTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_mahasiswa_can_submit_judul(): void
    {
        Storage::fake('private');
        $user = User::where('email', 'mhs1@test.com')->first();
        $file = UploadedFile::fake()->create('proposal.pdf', 1000);

        $response = $this->actingAs($user)->withHeaders([
            'Referer' => 'http://localhost',
        ])->postJson('/api/v1/pengajuan-judul', [
            'judul' => 'Judul Test',
            'topik' => 'Topik Test',
            'bidang' => 'Bidang Test',
            'latar_belakang' => 'Latar Belakang Test',
            'file_proposal' => $file,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('pengajuan_juduls', [
            'mahasiswa_id' => $user->id,
            'judul' => 'Judul Test',
            'status' => 'diajukan',
        ]);

        $path = $response->json('file_proposal');
        Storage::disk('private')->assertExists($path);
    }

    public function test_mahasiswa_can_only_see_own_pengajuan(): void
    {
        $mhs1 = User::where('email', 'mhs1@test.com')->first();
        $mhs2 = User::where('email', 'mhs2@test.com')->first();

        PengajuanJudul::create([
            'mahasiswa_id' => $mhs1->id,
            'judul' => 'Judul MHS 1',
            'topik' => 'T', 'bidang' => 'B', 'latar_belakang' => 'L',
            'status' => 'diajukan',
        ]);

        PengajuanJudul::create([
            'mahasiswa_id' => $mhs2->id,
            'judul' => 'Judul MHS 2',
            'topik' => 'T', 'bidang' => 'B', 'latar_belakang' => 'L',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($mhs1)->withHeaders([
            'Referer' => 'http://localhost',
        ])->getJson('/api/v1/pengajuan-judul');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.judul', 'Judul MHS 1');
    }

    public function test_dpa_can_see_supervised_students_pengajuan(): void
    {
        $mhs1 = User::where('email', 'mhs1@test.com')->first();
        $dpa = User::where('email', 'dpa@test.com')->first();

        PengajuanJudul::create([
            'mahasiswa_id' => $mhs1->id,
            'judul' => 'Judul MHS 1',
            'topik' => 'T', 'bidang' => 'B', 'latar_belakang' => 'L',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($dpa)->withHeaders([
            'Referer' => 'http://localhost',
        ])->getJson('/api/v1/pengajuan-judul');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.judul', 'Judul MHS 1');
    }

    public function test_dpa_can_review_pengajuan(): void
    {
        $mhs1 = User::where('email', 'mhs1@test.com')->first();
        $dpa = User::where('email', 'dpa@test.com')->first();

        $pengajuan = PengajuanJudul::create([
            'mahasiswa_id' => $mhs1->id,
            'judul' => 'Judul MHS 1',
            'topik' => 'T', 'bidang' => 'B', 'latar_belakang' => 'L',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($dpa)->withHeaders([
            'Referer' => 'http://localhost',
        ])->patchJson("/api/v1/pengajuan-judul/{$pengajuan->id}/review", [
            'status' => 'disetujui',
            'catatan_dpa' => 'Bagus sekali',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('pengajuan_juduls', [
            'id' => $pengajuan->id,
            'status' => 'disetujui',
            'catatan_dpa' => 'Bagus sekali',
        ]);
    }

    public function test_mahasiswa_cannot_review_pengajuan(): void
    {
        $mhs1 = User::where('email', 'mhs1@test.com')->first();

        $pengajuan = PengajuanJudul::create([
            'mahasiswa_id' => $mhs1->id,
            'judul' => 'Judul MHS 1',
            'topik' => 'T', 'bidang' => 'B', 'latar_belakang' => 'L',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($mhs1)->withHeaders([
            'Referer' => 'http://localhost',
        ])->patchJson("/api/v1/pengajuan-judul/{$pengajuan->id}/review", [
            'status' => 'disetujui',
            'catatan_dpa' => 'Bagus sekali',
        ]);

        $response->assertStatus(403);
    }
}
