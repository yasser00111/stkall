<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Course;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function show(Request $request, string $slug, int $assessmentId)
    {
        $course     = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $assessment = Assessment::where('id', $assessmentId)
            ->whereHas('material', fn ($q) => $q->where('course_id', $course->id))
            ->with('material')
            ->firstOrFail();

        $student = $this->getStudent($request);
        if (!$student) {
            return redirect()->route('student.course.show', $slug)
                ->with('error', 'Sesi tidak ditemukan. Silakan masuk ulang.');
        }

        // Cek apakah sudah pernah jawab
        $existingAnswer = $student->assessmentAnswers()
            ->where('assessment_id', $assessmentId)
            ->first();

        return view('student.assessment.show', compact(
            'course', 'assessment', 'student', 'existingAnswer'
        ));
    }

    public function store(Request $request, string $slug, int $assessmentId)
    {
        $course     = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $assessment = Assessment::where('id', $assessmentId)
            ->whereHas('material', fn ($q) => $q->where('course_id', $course->id))
            ->firstOrFail();

        $student = $this->getStudent($request);
        if (!$student) {
            return redirect()->route('student.course.show', $slug)
                ->with('error', 'Sesi tidak ditemukan. Silakan masuk ulang.');
        }

        // Jika sudah pernah jawab, tidak bisa submit ulang
        $existing = $student->assessmentAnswers()->where('assessment_id', $assessmentId)->first();
        if ($existing) {
            return back()->with('error', 'Anda sudah mengerjakan asesmen ini.');
        }

        $validated = $request->validate([
            'answer' => 'required|string|min:30',
        ], [
            'answer.required' => 'Jawaban wajib diisi.',
            'answer.min'      => 'Jawaban minimal 30 karakter.',
        ]);

        $student->assessmentAnswers()->create([
            'assessment_id' => $assessmentId,
            'answer'        => $validated['answer'],
            'status'        => 'pending',
        ]);

        return redirect()->route('student.material.show', [
            'slug'       => $slug,
            'materialId' => $assessment->material_id,
        ])->with('success', 'Jawaban asesmen berhasil dikirim! Tunggu penilaian guru untuk mendapatkan token materi berikutnya.');
    }
}
