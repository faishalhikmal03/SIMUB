<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_pengisians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kuesioner_id')->constrained('kuesioners')->onDelete('cascade');
            $table->enum('status', ['belum_diisi', 'sudah_diisi'])->default('belum_diisi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_pengisians');
    }
};