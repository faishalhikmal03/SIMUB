<?php
// File: database/seeders/UserSeeder.php
// (Ganti nama file AdminUserSeeder.php menjadi UserSeeder.php dan gunakan kode ini)

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seeder untuk Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // Kunci untuk mencari
            [
                'nama' => 'Administrator',
                'role' => 'admin',
                'password' => Hash::make('password'),
                // Kolom lain bisa null sesuai skema database
                'npm' => null,
                'nidn' => null,
                'tanggal_yudisium' => null,
            ]
        );

        // 2. Seeder untuk Mahasiswa Baru
        User::updateOrCreate(
            ['email' => 'mhsbaru@example.com'],
            [
                'nama' => 'Mahasiswa Baru Contoh',
                'npm' => '202511001', // Contoh NPM
                'role' => 'mahasiswa_baru',
                'password' => Hash::make('password'),
                'nidn' => null,
                'tanggal_yudisium' => null,
            ]
        );

        // 3. Seeder untuk Alumni
        User::updateOrCreate(
            ['email' => 'alumni@example.com'],
            [
                'nama' => 'Alumni Contoh',
                'npm' => '201811001', // Contoh NPM lama
                'role' => 'alumni',
                'tanggal_yudisium' => '2023-08-25', // Contoh tanggal yudisium
                'password' => Hash::make('password'),
                'nidn' => null,
            ]
        );
    }
}