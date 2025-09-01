<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilihan_jawabans', function (Blueprint $table) {
            // Kolom ini akan menyimpan ID user dari dosen yang dinilai.
            // Boleh NULL untuk jawaban yang tidak terkait penilaian dosen.
            $table->foreignId('dosen_id')->nullable()->after('id')->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::table('pilihan_jawabans', function (Blueprint $table) {
            // 'constrained' secara otomatis membuat foreign key constraint. 
            // Kita perlu menghapusnya sebelum menghapus kolom.
            $table->dropForeign(['dosen_id']);
            $table->dropColumn('dosen_id');
        });
    }
};