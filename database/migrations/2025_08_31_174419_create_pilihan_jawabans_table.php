<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilihan_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertanyaan_id')->constrained('pertanyaans')->onDelete('cascade');
            $table->string('pilihan');
            $table->string('value')->nullable();
            $table->foreignId('next_section_id')->nullable()->constrained('sections')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilihan_jawabans');
    }
};