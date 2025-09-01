<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
{
    Schema::table('pilihan_jawabans', function (Blueprint $table) {
        $table->foreignId('next_section_id')->nullable()->constrained('sections')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('pilihan_jawabans', function (Blueprint $table) {
        $table->dropForeign(['next_section_id']);
        $table->dropColumn('next_section_id');
    });
}
};
