<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Material;
use App\Models\Token;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    /**
     * Halaman input token (gate)
     */
    public function gate(Request $request, string $slug, string $type, int $materialId)
    {
        $course   = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $material = Material::where('id', $materialId)->where('course_id', $course->id)->firstOrFail();
        $student  = $this->getStudent($request);

        if (!$student) {
            return redirect()->route('student.course.show', $slug)
                ->with('error', 'Sesi tidak ditemukan. Silakan masuk ulang.');
        }

        return view('student.token.gate', compact('course', 'material', 'student', 'type'));
    }

    /**
     * Verifikasi token yang dimasukkan siswa
     */
    public function verify(Request $request, string $slug, string $type, int $materialId)
    {
        $course   = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $material = Material::where('id', $materialId)->where('course_id', $course->id)->firstOrFail();
        $student  = $this->getStudent($request);

        if (!$student) {
            return redirect()->route('student.course.show', $slug)
                ->with('error', 'Sesi tidak ditemukan. Silakan masuk ulang.');
        }

        $validated = $request->validate([
            'token' => 'required|string|size:8',
        ], [
            'token.required' => 'Token wajib diisi.',
            'token.size'     => 'Token harus 8 karakter.',
        ]);

        $token = Token::where('token', strtoupper($validated['token']))
            ->where('student_session_id', $student->id)
            ->where('type', $type)
            ->where('material_id', $materialId)
            ->where('is_used', false)
            ->first();

        if (!$token || !$token->isValid()) {
            return back()->with('error', 'Token tidak valid atau sudah digunakan. Periksa kembali token Anda.');
        }

        if ($type === 'assessment') {
            // Redirect ke halaman asesmen
            $assessment = $material->assessment;
            if (!$assessment) {
                return back()->with('error', 'Asesmen tidak ditemukan.');
            }

            // Tandai token digunakan
            $token->update(['is_used' => true]);

            return redirect()->route('student.assessment.show', [
                'slug'         => $slug,
                'assessmentId' => $assessment->id,
            ]);
        }

        if ($type === 'material') {
            // Redirect ke halaman materi (token akan dicek ulang di MaterialController)
            return redirect()->route('student.material.show', [
                'slug'       => $slug,
                'materialId' => $materialId,
            ]);
        }

        return back()->with('error', 'Tipe token tidak dikenal.');
    }
}
