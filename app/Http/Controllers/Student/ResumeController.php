<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;

class ResumeController extends Controller
{
    public function create(Request $request, string $slug, int $materialId)
    {
        $course   = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $material = Material::where('id', $materialId)->where('course_id', $course->id)->firstOrFail();
        $student  = $this->getStudent($request);

        if (!$student) {
            return redirect()->route('student.course.show', $slug)
                ->with('error', 'Sesi tidak ditemukan. Silakan masuk ulang.');
        }

        // Cek apakah sudah pernah submit resume
        $existingResume = $student->resumes()->where('material_id', $materialId)->first();

        return view('student.resume.create', compact('course', 'material', 'student', 'existingResume'));
    }

    public function store(Request $request, string $slug, int $materialId)
    {
        $course   = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $material = Material::where('id', $materialId)->where('course_id', $course->id)->firstOrFail();
        $student  = $this->getStudent($request);

        if (!$student) {
            return redirect()->route('student.course.show', $slug)
                ->with('error', 'Sesi tidak ditemukan. Silakan masuk ulang.');
        }

        // Jika sudah ada resume pending/rejected, boleh submit ulang
        $existing = $student->resumes()->where('material_id', $materialId)->first();
        if ($existing && $existing->status === 'approved') {
            return back()->with('error', 'Resume Anda sudah disetujui.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:50',
        ], [
            'content.required' => 'Isi resume wajib diisi.',
            'content.min'      => 'Resume minimal 50 karakter.',
        ]);

        if ($existing) {
            $existing->update([
                'content'          => $validated['content'],
                'status'           => 'pending',
                'teacher_feedback' => null,
                'approved_at'      => null,
            ]);
        } else {
            $student->resumes()->create([
                'material_id' => $materialId,
                'content'     => $validated['content'],
                'status'      => 'pending',
            ]);
        }

        return redirect()->route('student.material.show', ['slug' => $slug, 'materialId' => $materialId])
            ->with('success', 'Resume berhasil dikirim! Tunggu persetujuan guru untuk mendapatkan token asesmen.');
    }
}
