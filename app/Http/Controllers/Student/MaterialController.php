<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Material;
use App\Models\StudentSession;
use App\Models\Token;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function show(Request $request, string $slug, int $materialId)
    {
        $course   = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $material = Material::where('id', $materialId)
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->firstOrFail();

        // Ambil student session dari browser session
        $student = $this->getStudent($request);
        if (!$student) {
            return redirect()->route('student.course.show', $slug)
                ->with('error', 'Silakan masukkan nama dan kelas Anda terlebih dahulu.');
        }

        // Cek apakah materi ini adalah materi pertama atau bukan
        $firstMaterial = $course->materials()->where('is_active', true)->orderBy('order')->first();
        $isFirstMaterial = $firstMaterial && $firstMaterial->id === $material->id;

        // Jika bukan materi pertama, cek token
        if (!$isFirstMaterial) {
            $token = Token::where('student_session_id', $student->id)
                ->where('type', 'material')
                ->where('material_id', $material->id)
                ->where('is_used', false)
                ->first();

            if (!$token || !$token->isValid()) {
                return redirect()->route('student.token.gate', [
                    'slug'       => $slug,
                    'type'       => 'material',
                    'materialId' => $material->id,
                ])->with('info', 'Masukkan token untuk mengakses materi ini.');
            }

            // Tandai token sudah digunakan
            $token->update(['is_used' => true]);

            // Update progres siswa
            if ($material->order > $student->current_material_order) {
                $student->update(['current_material_order' => $material->order]);
            }
        }

        // Cek apakah sudah ada resume untuk materi ini
        $hasResume     = $student->hasResumeFor($material->id);
        $resumeStatus  = null;
        if ($hasResume) {
            $resumeStatus = $student->resumes()
                ->where('material_id', $material->id)
                ->value('status');
        }

        // Semua materi dalam course (untuk navigasi)
        $allMaterials = $course->materials()->where('is_active', true)->orderBy('order')->get();

        return view('student.material.show', compact(
            'course', 'material', 'student', 'hasResume', 'resumeStatus', 'allMaterials', 'isFirstMaterial'
        ));
    }
}
