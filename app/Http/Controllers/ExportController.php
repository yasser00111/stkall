<?php

namespace App\Http\Controllers;

use App\Exports\NilaiSiswaExport;
use App\Exports\ResumeExport;
use App\Models\AssessmentAnswer;
use App\Models\Course;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Export nilai ke Excel
     */
    public function nilaiExcel(?int $courseId = null, ?int $materialId = null)
    {
        $course    = $courseId ? Course::find($courseId) : null;
        $fileName  = 'nilai-siswa';
        if ($course) $fileName .= '-' . \Illuminate\Support\Str::slug($course->title);
        $fileName .= '-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new NilaiSiswaExport($courseId, $materialId),
            $fileName
        );
    }

    /**
     * Export nilai ke PDF
     */
    public function nilaiPdf(?int $courseId = null, ?int $materialId = null)
    {
        $course  = $courseId ? Course::with(['materials.assessment'])->find($courseId) : null;

        $query = AssessmentAnswer::with([
            'studentSession',
            'assessment.material.course',
        ])->orderBy('created_at');

        if ($courseId) {
            $query->whereHas('assessment.material', fn ($q) =>
                $q->where('course_id', $courseId)
            );
        }
        if ($materialId) {
            $query->whereHas('assessment', fn ($q) =>
                $q->where('material_id', $materialId)
            );
        }

        $answers = $query->get();

        // Rekap per materi
        $rekapMateri = collect();
        if ($course) {
            $rekapMateri = $course->materials
                ->filter(fn ($m) => $m->assessment !== null)
                ->map(function ($material) {
                    $answers = $material->assessment->answers;
                    $graded  = $answers->where('status', 'graded');
                    return [
                        'materi'    => $material->title,
                        'asesmen'   => $material->assessment->title,
                        'total'     => $answers->count(),
                        'dinilai'   => $graded->count(),
                        'tertinggi' => $graded->max('score') ?? '—',
                        'terendah'  => $graded->min('score') ?? '—',
                        'rata_rata' => $graded->count() ? round($graded->avg('score'), 1) : '—',
                        'lulus'     => $graded->where('score', '>=', 75)->count(),
                    ];
                });
        }

        $pdf = Pdf::loadView('exports.nilai-pdf', compact('answers', 'course', 'rekapMateri'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
            ]);

        $fileName = 'nilai-siswa';
        if ($course) $fileName .= '-' . \Illuminate\Support\Str::slug($course->title);
        $fileName .= '-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export resume ke Excel
     */
    public function resumeExcel(?int $courseId = null)
    {
        $course   = $courseId ? Course::find($courseId) : null;
        $fileName = 'resume-siswa';
        if ($course) $fileName .= '-' . \Illuminate\Support\Str::slug($course->title);
        $fileName .= '-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new ResumeExport($courseId), $fileName);
    }
}
