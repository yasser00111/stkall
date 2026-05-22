<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\StudentSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Halaman landing: siswa masukkan nama & kelas untuk bergabung
     */
    public function show(string $slug)
    {
        $course = Course::where('slug', $slug)
            ->where('is_active', true)
            ->with(['materials' => fn ($q) => $q->where('is_active', true)->orderBy('order')])
            ->firstOrFail();

        return view('student.course.show', compact('course'));
    }

    /**
     * Proses join: buat StudentSession, simpan ke session browser
     */
    public function join(Request $request, string $slug)
    {
        $course = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'class' => 'required|string|max:50',
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'class.required' => 'Kelas wajib diisi.',
        ]);

        // Gunakan fill() + save() agar tidak bergantung pada mass assignment
        $student = new StudentSession();
        $student->course_id              = $course->id;
        $student->name                   = $validated['name'];
        $student->class                  = $validated['class'];
        $student->current_material_order = 0;
        $student->save();

        // Simpan ID sesi siswa di cookie browser
        $request->session()->put('student_session_id', $student->id);

        // Redirect ke materi pertama
        $firstMaterial = $course->materials()->where('is_active', true)->orderBy('order')->first();

        if (!$firstMaterial) {
            return back()->with('error', 'Belum ada materi yang tersedia.');
        }

        return redirect()->route('student.material.show', [
            'slug'       => $slug,
            'materialId' => $firstMaterial->id,
        ]);
    }

    /**
     * Lanjutkan sesi (siswa sudah punya session_token dari sebelumnya)
     */
    public function resume(Request $request, string $slug)
    {
        $course = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'session_token' => 'required|string',
        ], [
            'session_token.required' => 'Token sesi wajib diisi.',
        ]);

        $student = StudentSession::where('session_token', strtoupper($validated['session_token']))
            ->where('course_id', $course->id)
            ->first();

        if (!$student) {
            return back()->with('error', 'Token sesi tidak ditemukan atau tidak valid.');
        }

        $request->session()->put('student_session_id', $student->id);

        // Redirect ke materi sesuai progres
        $material = $course->materials()
            ->where('is_active', true)
            ->where('order', '>=', $student->current_material_order)
            ->orderBy('order')
            ->first();

        if (!$material) {
            $material = $course->materials()->where('is_active', true)->orderBy('order')->first();
        }

        return redirect()->route('student.material.show', [
            'slug'       => $slug,
            'materialId' => $material->id,
        ]);
    }
}
