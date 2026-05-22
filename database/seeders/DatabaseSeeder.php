<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun guru
        $teacher = User::create([
            'name'     => 'Guru Demo',
            'email'    => 'guru@stkall.id',
            'password' => Hash::make('password'),
        ]);

        // Mata pelajaran contoh
        $course = Course::create([
            'user_id'     => $teacher->id,
            'title'       => 'Pengenalan Pemrograman Web',
            'description' => 'Mempelajari dasar-dasar pemrograman web menggunakan HTML, CSS, dan JavaScript.',
            'slug'        => 'pengenalan-web-abc123',
            'is_active'   => true,
        ]);

        // Materi 1
        $m1 = Material::create([
            'course_id' => $course->id,
            'title'     => 'Materi 1: HTML Dasar',
            'content'   => '<h2>Pengenalan HTML</h2><p>HTML (HyperText Markup Language) adalah bahasa markup yang digunakan untuk membuat halaman web. HTML menggunakan tag-tag untuk mendefinisikan struktur konten.</p><h3>Tag Dasar HTML</h3><ul><li><strong>&lt;html&gt;</strong> - elemen root</li><li><strong>&lt;head&gt;</strong> - informasi meta dokumen</li><li><strong>&lt;body&gt;</strong> - konten halaman</li><li><strong>&lt;h1&gt; - &lt;h6&gt;</strong> - heading</li><li><strong>&lt;p&gt;</strong> - paragraf</li></ul>',
            'video_url' => 'https://www.youtube.com/watch?v=qz0aGYrrlhU',
            'order'     => 1,
            'is_active' => true,
        ]);

        Assessment::create([
            'material_id'  => $m1->id,
            'title'        => 'Asesmen HTML Dasar',
            'instructions' => 'Jelaskan apa yang dimaksud dengan HTML dan sebutkan minimal 5 tag HTML beserta fungsinya masing-masing!',
            'is_active'    => true,
        ]);

        // Materi 2
        $m2 = Material::create([
            'course_id' => $course->id,
            'title'     => 'Materi 2: CSS Dasar',
            'content'   => '<h2>Pengenalan CSS</h2><p>CSS (Cascading Style Sheets) digunakan untuk mengatur tampilan dan layout halaman web. Dengan CSS, kita dapat mengubah warna, font, ukuran, dan posisi elemen HTML.</p><h3>Cara Penulisan CSS</h3><p>CSS dapat ditulis secara <em>inline</em>, <em>internal</em>, maupun <em>external</em>.</p>',
            'video_url' => 'https://www.youtube.com/watch?v=1PnVor36_40',
            'order'     => 2,
            'is_active' => true,
        ]);

        Assessment::create([
            'material_id'  => $m2->id,
            'title'        => 'Asesmen CSS Dasar',
            'instructions' => 'Jelaskan perbedaan antara penulisan CSS secara inline, internal, dan external. Kapan sebaiknya menggunakan masing-masing metode tersebut?',
            'is_active'    => true,
        ]);

        // Materi 3
        Material::create([
            'course_id' => $course->id,
            'title'     => 'Materi 3: JavaScript Dasar',
            'content'   => '<h2>Pengenalan JavaScript</h2><p>JavaScript adalah bahasa pemrograman yang membuat halaman web menjadi interaktif. JavaScript dapat memanipulasi elemen HTML, merespons event, dan berkomunikasi dengan server.</p>',
            'video_url' => null,
            'order'     => 3,
            'is_active' => true,
        ]);

        $this->command->info('✅ Seeder berhasil!');
        $this->command->info('👤 Login Admin: guru@stkall.id / password');
        $this->command->info('🔗 Link Belajar: /belajar/pengenalan-web-abc123');
    }
}
