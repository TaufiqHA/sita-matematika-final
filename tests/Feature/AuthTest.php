<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_user_can_get_csrf_cookie(): void
    {
        $response = $this->get('/sanctum/csrf-cookie');
        $response->assertNoContent();
        $response->assertCookie('XSRF-TOKEN');
    }

    public function test_user_can_login(): void
    {
        $response = $this->withHeaders([
            'Referer' => 'http://localhost',
        ])->postJson('/api/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'user' => [
                'id', 'name', 'email', 'role', 'roles', 'mahasiswa', 'dosen'
            ]
        ]);
        $response->assertJsonPath('user.email', 'admin@test.com');
        $this->assertAuthenticated();
    }

    public function test_user_can_get_me_data(): void
    {
        $user = User::where('email', 'admin@test.com')->first();
        $response = $this->actingAs($user)->getJson('/api/me');

        $response->assertOk();
        $response->assertJsonStructure([
            'user' => [
                'id', 'name', 'email', 'role', 'roles', 'mahasiswa', 'dosen'
            ]
        ]);
        $response->assertJsonPath('user.email', 'admin@test.com');
    }

    public function test_mahasiswa_has_mahasiswa_relation(): void
    {
        $user = User::where('email', 'mhs1@test.com')->first();
        $response = $this->actingAs($user)->getJson('/api/me');

        $response->assertOk();
        $response->assertJsonPath('user.email', 'mhs1@test.com');
        $response->assertJsonStructure([
            'user' => ['mahasiswa']
        ]);
        $this->assertNotNull($response->json('user.mahasiswa'));
        $this->assertEquals('H1A020001', $response->json('user.mahasiswa.nim'));
    }

    public function test_dosen_has_dosen_relation(): void
    {
        $user = User::where('email', 'dpa@test.com')->first();
        $response = $this->actingAs($user)->getJson('/api/me');

        $response->assertOk();
        $response->assertJsonPath('user.email', 'dpa@test.com');
        $response->assertJsonStructure([
            'user' => ['dosen']
        ]);
        $this->assertNotNull($response->json('user.dosen'));
        $this->assertEquals('1234567890', $response->json('user.dosen.nidn'));
    }

    public function test_user_can_logout(): void
    {
        $this->postJson('/api/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        $response = $this->withHeaders([
            'Referer' => 'http://localhost',
        ])->postJson('/api/logout');

        $response->assertOk();
        $response->assertJson(['message' => 'Successfully logged out']);
        $this->assertGuest('web');
    }
}
