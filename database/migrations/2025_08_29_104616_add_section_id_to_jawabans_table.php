<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jawabans', function (Blueprint $table) {
            // Menambahkan foreign key untuk section_id setelah kuesioner_id
            $table->foreignId('section_id')->nullable()->constrained()->onDelete('cascade')->after('kuesioner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jawabans', function (Blueprint $table) {
            // Hapus relasi dan kolom jika migrasi di-rollback
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });
    }
};
