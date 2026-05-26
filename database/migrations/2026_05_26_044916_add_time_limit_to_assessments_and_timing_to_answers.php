<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah batas waktu ke tabel assessments
        Schema::table('assessments', function (Blueprint $table) {
            $table->unsignedInteger('time_limit')->nullable()->after('instructions')
                ->comment('Batas waktu pengerjaan dalam menit, null = tidak terbatas');
            $table->timestamp('deadline')->nullable()->after('time_limit')
                ->comment('Deadline pengerjaan, null = tidak ada deadline');
        });

        // Tambah waktu mulai & selesai ke tabel assessment_answers
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('answer')
                ->comment('Waktu siswa mulai mengerjakan');
            $table->timestamp('submitted_at')->nullable()->after('started_at')
                ->comment('Waktu siswa submit jawaban');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn(['time_limit', 'deadline']);
        });

        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'submitted_at']);
        });
    }
};
