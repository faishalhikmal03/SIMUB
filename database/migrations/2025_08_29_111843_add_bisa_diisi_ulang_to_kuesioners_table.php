<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kuesioners', function (Blueprint $table) {
            // Tambahkan kolom boolean setelah kolom 'status'
            // Default-nya adalah false (tidak bisa diisi ulang)
            $table->boolean('bisa_diisi_ulang')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('kuesioners', function (Blueprint $table) {
            $table->dropColumn('bisa_diisi_ulang');
        });
    }
};