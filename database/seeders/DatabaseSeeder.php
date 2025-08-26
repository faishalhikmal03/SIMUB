<?php
// File: database/seeders/DatabaseSeeder.php
// (Gunakan kode ini untuk file DatabaseSeeder utama Anda)

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Memanggil seeder pengguna yang sudah kita buat
        $this->call([
            UserSeeder::class,
            // Anda bisa menambahkan seeder lain di sini nanti,
            // contoh: KuesionerSeeder::class
        ]);
    }
}