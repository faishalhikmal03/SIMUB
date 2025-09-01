<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jawabans', function (Blueprint $table) {
            // Kolom ini akan menyimpan ID user dari dosen yang sedang dievaluasi dalam satu sesi
            $table->foreignId('dosen_id')->nullable()->after('user_id')->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::table('jawabans', function (Blueprint $table) {
            $table->dropForeign(['dosen_id']);
            $table->dropColumn('dosen_id');
        });
    }
};