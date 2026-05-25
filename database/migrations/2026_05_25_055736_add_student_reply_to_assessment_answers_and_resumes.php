<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom balasan siswa di tabel jawaban asesmen
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->text('student_reply')->nullable()->after('teacher_feedback');
            $table->timestamp('student_replied_at')->nullable()->after('student_reply');
        });

        // Tambah kolom balasan siswa di tabel resume
        Schema::table('resumes', function (Blueprint $table) {
            $table->text('student_reply')->nullable()->after('teacher_feedback');
            $table->timestamp('student_replied_at')->nullable()->after('student_reply');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->dropColumn(['student_reply', 'student_replied_at']);
        });

        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn(['student_reply', 'student_replied_at']);
        });
    }
};
