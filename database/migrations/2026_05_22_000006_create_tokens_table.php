<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Token yang digenerate sistem untuk unlock assessment atau materi berikutnya
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_session_id')->constrained()->onDelete('cascade');
            $table->string('token', 8)->unique();               // token 8 karakter
            $table->enum('type', ['assessment', 'material']);   // untuk apa token ini
            $table->foreignId('material_id')->nullable()->constrained()->onDelete('cascade'); // materi target
            $table->boolean('is_used')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
