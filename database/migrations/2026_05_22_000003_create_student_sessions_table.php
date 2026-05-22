<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel siswa (tidak perlu login, hanya nama + kelas)
        Schema::create('student_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('name');           // nama siswa
            $table->string('class');          // kelas siswa
            $table->string('session_token')->unique(); // token sesi unik siswa
            $table->integer('current_material_order')->default(0); // progres materi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_sessions');
    }
};
