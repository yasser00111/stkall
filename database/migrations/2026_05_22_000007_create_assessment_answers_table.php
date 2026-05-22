<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_session_id')->constrained()->onDelete('cascade');
            $table->longText('answer');                                         // jawaban essay siswa
            $table->enum('status', ['pending', 'graded'])->default('pending');
            $table->integer('score')->nullable();                               // nilai 0-100
            $table->text('teacher_feedback')->nullable();                       // catatan guru
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_answers');
    }
};
