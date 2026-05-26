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

        // ── Cek deadline ──────────────────────────────────────────────
        if (!$existingAnswer && $assessment->deadline && $assessment->deadline->isPast()) {
            return view('student.assessment.show', compact(
                'course', 'assessment', 'student', 'existingAnswer'
            ))->with('deadline_passed', true);
        }

        // ── Catat waktu mulai jika belum ada ──────────────────────────
        if (!$existingAnswer) {
            // Simpan started_at di session untuk konsistensi lintas request
            $sessionKey = "asesmen_start_{$assessmentId}_{$student->id}";
            if (!$request->session()->has($sessionKey)) {
                $request->session()->put($sessionKey, now()->timestamp);
            }
            $startedAt = $request->session()->get($sessionKey);
        } else {
            $startedAt = $existingAnswer->started_at?->timestamp;
        }

        // ── Hitung sisa waktu (detik) ─────────────────────────────────
        $remainingSeconds = null;
        $timeExpired      = false;

        if (!$existingAnswer && $assessment->time_limit) {
            $elapsed          = now()->timestamp - $startedAt;
            $totalSeconds     = $assessment->time_limit * 60;
            $remainingSeconds = max(0, $totalSeconds - $elapsed);
            $timeExpired      = $remainingSeconds <= 0;
        }

        return view('student.assessment.show', compact(
            'course', 'assessment', 'student', 'existingAnswer',
            'startedAt', 'remainingSeconds', 'timeExpired'
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

        // ── Cek deadline ──────────────────────────────────────────────
        if ($assessment->deadline && $assessment->deadline->isPast()) {
            return back()->with('error', 'Deadline asesmen sudah lewat. Anda tidak dapat mengirim jawaban.');
        }

        // ── Cek batas waktu pengerjaan ────────────────────────────────
        $sessionKey = "asesmen_start_{$assessmentId}_{$student->id}";
        $startedAt  = $request->session()->get($sessionKey);

        if ($assessment->time_limit && $startedAt) {
            $elapsed      = now()->timestamp - $startedAt;
            $totalSeconds = $assessment->time_limit * 60;
            // Beri toleransi 30 detik untuk keterlambatan jaringan
            if ($elapsed > $totalSeconds + 30) {
                return back()->with('error', 'Waktu pengerjaan sudah habis. Jawaban tidak dapat dikirim.');
            }
        }

        $validated = $request->validate([
            'answer' => 'required|string|min:10',
        ], [
            'answer.required' => 'Jawaban wajib diisi.',
            'answer.min'      => 'Jawaban minimal 10 karakter.',
        ]);

        // Ambil waktu mulai dari session
        $startDt = $startedAt ? \Carbon\Carbon::createFromTimestamp($startedAt) : now();

        $student->assessmentAnswers()->create([
            'assessment_id' => $assessmentId,
            'answer'        => $validated['answer'],
            'status'        => 'pending',
            'started_at'    => $startDt,
            'submitted_at'  => now(),
        ]);

        // Hapus session waktu mulai
        $request->session()->forget($sessionKey);

        return redirect()->route('student.material.show', [
            'slug'       => $slug,
            'materialId' => $assessment->material_id,
        ])->with('success', 'Jawaban asesmen berhasil dikirim! Tunggu penilaian guru untuk mendapatkan token materi berikutnya.');
    }
}
