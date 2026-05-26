<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        $existing = $student->resumes()->where('material_id', $materialId)->first();
        if ($existing && $existing->status === 'approved') {
            return back()->with('error', 'Resume Anda sudah disetujui.');
        }

        // ── Validasi ─────────────────────────────────────────────────────────
        $request->validate([
            'content'   => 'nullable|string|max:10000',
            'video_url' => 'nullable|string|max:500',
            'file'      => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,webp|max:10240',
        ], [
            'file.mimes' => 'File harus berformat: PDF, Word (doc/docx), atau gambar (jpg, png, gif, webp).',
            'file.max'   => 'Ukuran file maksimal 10 MB.',
        ]);

        // Minimal salah satu harus diisi
        $hasContent  = !empty(trim($request->input('content', '')));
        $hasVideo    = !empty(trim($request->input('video_url', '')));
        $hasFile     = $request->hasFile('file') && $request->file('file')->isValid();

        if (!$hasContent && !$hasVideo && !$hasFile) {
            return back()->withErrors([
                'content' => 'Wajib mengisi minimal salah satu: teks resume, file upload, atau link video.'
            ])->withInput();
        }

        // ── Handle upload file ────────────────────────────────────────────────
        $filePath = null;
        $fileName = null;
        $fileType = null;

        if ($hasFile) {
            // Hapus file lama jika ada
            if ($existing && $existing->file_path) {
                Storage::disk('public')->delete($existing->file_path);
            }

            $uploadedFile = $request->file('file');
            $extension    = strtolower($uploadedFile->getClientOriginalExtension());
            $fileName     = $uploadedFile->getClientOriginalName();
            $fileType     = $extension;
            $storageName  = Str::random(20) . '_' . time() . '.' . $extension;
            $filePath     = $uploadedFile->storeAs(
                'resumes/' . $student->id,
                $storageName,
                'public'
            );
        } elseif ($existing && $existing->file_path) {
            // Pertahankan file lama jika tidak upload baru
            $filePath = $existing->file_path;
            $fileName = $existing->file_name;
            $fileType = $existing->file_type;
        }

        // ── Simpan data ───────────────────────────────────────────────────────
        $data = [
            'content'          => trim($request->input('content', '')),
            'video_url'        => trim($request->input('video_url', '')) ?: null,
            'file_path'        => $filePath,
            'file_name'        => $fileName,
            'file_type'        => $fileType,
            'status'           => 'pending',
            'teacher_feedback' => null,
            'approved_at'      => null,
            'student_reply'    => null,
            'student_replied_at' => null,
        ];

        if ($existing) {
            $existing->update($data);
        } else {
            $student->resumes()->create(array_merge($data, ['material_id' => $materialId]));
        }

        return redirect()->route('student.material.show', ['slug' => $slug, 'materialId' => $materialId])
            ->with('success', 'Resume berhasil dikirim! Tunggu persetujuan guru untuk mendapatkan token asesmen.');
    }
}
