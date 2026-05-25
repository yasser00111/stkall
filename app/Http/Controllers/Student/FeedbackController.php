<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAnswer;
use App\Models\Course;
use App\Models\Resume;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Halaman daftar semua feedback (nilai + feedback resume + asesmen) untuk siswa
     */
    public function index(Request $request, string $slug)
    {
        $course  = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $student = $this->getStudent($request);

        if (!$student) {
            return redirect()->route('student.course.show', $slug)
                ->with('error', 'Sesi tidak ditemukan. Silakan masuk ulang.');
        }

        // Semua resume siswa untuk course ini beserta feedback guru
        $resumes = Resume::where('student_session_id', $student->id)
            ->whereHas('material', fn ($q) => $q->where('course_id', $course->id))
            ->with('material')
            ->orderByDesc('updated_at')
            ->get();

        // Semua jawaban asesmen siswa untuk course ini beserta nilai & feedback
        $assessmentAnswers = AssessmentAnswer::where('student_session_id', $student->id)
            ->whereHas('assessment.material', fn ($q) => $q->where('course_id', $course->id))
            ->with('assessment.material')
            ->orderByDesc('updated_at')
            ->get();

        return view('student.feedback.index', compact(
            'course', 'student', 'resumes', 'assessmentAnswers'
        ));
    }

    /**
     * Siswa balas feedback resume
     */
    public function replyResume(Request $request, string $slug, int $resumeId)
    {
        $course  = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $student = $this->getStudent($request);

        if (!$student) {
            return redirect()->route('student.course.show', $slug)
                ->with('error', 'Sesi tidak ditemukan.');
        }

        $resume = Resume::where('id', $resumeId)
            ->where('student_session_id', $student->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->firstOrFail();

        $validated = $request->validate([
            'student_reply' => 'required|string|min:5|max:1000',
        ], [
            'student_reply.required' => 'Balasan wajib diisi.',
            'student_reply.min'      => 'Balasan minimal 5 karakter.',
            'student_reply.max'      => 'Balasan maksimal 1000 karakter.',
        ]);

        $resume->update([
            'student_reply'      => $validated['student_reply'],
            'student_replied_at' => now(),
        ]);

        return redirect()->route('student.feedback.index', $slug)
            ->with('success', 'Balasan berhasil dikirim!');
    }

    /**
     * Siswa balas feedback jawaban asesmen
     */
    public function replyAssessment(Request $request, string $slug, int $answerId)
    {
        $course  = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $student = $this->getStudent($request);

        if (!$student) {
            return redirect()->route('student.course.show', $slug)
                ->with('error', 'Sesi tidak ditemukan.');
        }

        $answer = AssessmentAnswer::where('id', $answerId)
            ->where('student_session_id', $student->id)
            ->where('status', 'graded')
            ->firstOrFail();

        $validated = $request->validate([
            'student_reply' => 'required|string|min:5|max:1000',
        ], [
            'student_reply.required' => 'Balasan wajib diisi.',
            'student_reply.min'      => 'Balasan minimal 5 karakter.',
            'student_reply.max'      => 'Balasan maksimal 1000 karakter.',
        ]);

        $answer->update([
            'student_reply'      => $validated['student_reply'],
            'student_replied_at' => now(),
        ]);

        return redirect()->route('student.feedback.index', $slug)
            ->with('success', 'Balasan berhasil dikirim!');
    }
}
