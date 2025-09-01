<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception; // Diperlukan untuk menangani error

class ImportDosenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:dosen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data dosen dari file CSV menggunakan fungsi bawaan PHP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses impor data dosen dari file CSV...');

        $filePath = storage_path('app/data/Dosen.csv');

        if (!file_exists($filePath)) {
            $this->error('File tidak ditemukan di: ' . $filePath);
            return 1;
        }

        try {
            // Membuka file CSV untuk dibaca
            $fileHandle = fopen($filePath, 'r');
            if ($fileHandle === false) {
                throw new Exception("Gagal membuka file CSV.");
            }

            // Membaca baris header untuk dilewati
            $header = fgetcsv($fileHandle);
            
            // Mencari tahu indeks kolom berdasarkan nama header
            // array_search bersifat case-sensitive, pastikan nama kolom di file CSV sama persis
            $namaIndex = array_search('Nama Dosen', $header);
            $nidnIndex = array_search('NIDN/NIDK', $header);

            if ($namaIndex === false || $nidnIndex === false) {
                 $this->error('Header kolom "Nama Dosen" atau "NIDN/NIDK" tidak ditemukan di file CSV.');
                 fclose($fileHandle);
                 return 1;
            }

            $this->info('Membaca data baris per baris...');
            
            // Looping untuk membaca sisa baris di file
            while (($row = fgetcsv($fileHandle)) !== false) {
                $nama = trim($row[$namaIndex]);
                $nidn = trim($row[$nidnIndex]);
                
                // Lewati baris jika data tidak valid
                if (empty($nidn) || empty($nama) || !is_numeric($nidn)) {
                    continue;
                }

                User::updateOrCreate(
                    ['nidn' => $nidn],
                    [
                        'nama' => $nama,
                        'password' => Hash::make($nidn),
                        'role' => 'dosen',
                        'email' => $nidn . '@example.com',
                        'npm' => null,
                        'tanggal_yudisium' => null
                    ]
                );
            }

            // Menutup file setelah selesai
            fclose($fileHandle);

            $this->info("\nProses impor data dosen telah berhasil diselesaikan!");

        } catch (Exception $e) {
            $this->error('Terjadi error: ' . $e->getMessage());
            // Pastikan file ditutup jika terjadi error
            if (isset($fileHandle) && is_resource($fileHandle)) {
                fclose($fileHandle);
            }
            return 1;
        }

        return 0;
    }
}