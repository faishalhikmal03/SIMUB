<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jawabans', function (Blueprint $table) {
            // Tambahkan kolom UUID setelah user_id untuk mengelompokkan setiap pengisian
            $table->uuid('submission_uuid')->after('user_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('jawabans', function (Blueprint $table) {
            $table->dropColumn('submission_uuid');
        });
    }
};