<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // User Admin
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'role' => 'Admin',
        ]);
        $admin->assignRole('Admin');

        // User TU
        $tu = User::factory()->create([
            'name' => 'TU User',
            'email' => 'tu@test.com',
            'role' => 'TU',
        ]);
        $tu->assignRole('TU');

        // User Kajur
        $kajur = User::factory()->create([
            'name' => 'Kajur User',
            'email' => 'kajur@test.com',
            'role' => 'Kajur',
        ]);
        $kajur->assignRole('Kajur');

        // User Dosen/DPA
        $dosen = User::factory()->create([
            'name' => 'Dosen DPA',
            'email' => 'dpa@test.com',
            'role' => 'DPA',
        ]);
        $dosen->assignRole('DPA');
        Dosen::create([
            'user_id' => $dosen->id,
            'nidn' => '1234567890',
            'bidang_keahlian' => 'Matematika',
        ]);

        // User Mahasiswa
        $mhs1 = User::factory()->create([
            'name' => 'Mahasiswa 1',
            'email' => 'mhs1@test.com',
            'role' => 'Mahasiswa',
        ]);
        $mhs1->assignRole('Mahasiswa');
        Mahasiswa::create([
            'user_id' => $mhs1->id,
            'nim' => 'H1A020001',
            'angkatan' => '2020',
            'program_studi' => 'Matematika',
            'dpa_id' => $dosen->id,
        ]);

        $mhs2 = User::factory()->create([
            'name' => 'Mahasiswa 2',
            'email' => 'mhs2@test.com',
            'role' => 'Mahasiswa',
        ]);
        $mhs2->assignRole('Mahasiswa');
        Mahasiswa::create([
            'user_id' => $mhs2->id,
            'nim' => 'H1A020002',
            'angkatan' => '2020',
            'program_studi' => 'Matematika',
            'dpa_id' => $dosen->id,
        ]);
    }
}
