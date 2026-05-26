<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            // Path file yang diupload (PDF, Word, gambar)
            $table->string('file_path')->nullable()->after('content')
                ->comment('Path file upload (PDF/Word/gambar)');
            $table->string('file_name')->nullable()->after('file_path')
                ->comment('Nama asli file upload');
            $table->string('file_type')->nullable()->after('file_name')
                ->comment('MIME type file: pdf, doc, docx, jpg, png, dll');
            // URL video YouTube
            $table->string('video_url')->nullable()->after('file_type')
                ->comment('Link video YouTube dari siswa');
        });
    }

    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_name', 'file_type', 'video_url']);
        });
    }
};
