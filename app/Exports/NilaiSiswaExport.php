<?php

namespace App\Exports;

use App\Models\AssessmentAnswer;
use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class NilaiSiswaExport implements WithMultipleSheets
{
    public function __construct(
        protected ?int $courseId = null,
        protected ?int $materialId = null
    ) {}

    public function sheets(): array
    {
        $sheets = [new NilaiSheet($this->courseId, $this->materialId)];

        // Tambah sheet rekap per materi jika export per course
        if ($this->courseId && !$this->materialId) {
            $sheets[] = new RekapPerMateriSheet($this->courseId);
        }

        return $sheets;
    }
}

// ─── Sheet 1: Daftar Nilai ─────────────────────────────────────────────
class NilaiSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(
        protected ?int $courseId = null,
        protected ?int $materialId = null
    ) {}

    public function title(): string
    {
        return 'Daftar Nilai';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Kelas',
            'Mata Pelajaran',
            'Materi',
            'Judul Asesmen',
            'Nilai',
            'Status',
            'Waktu Mulai',
            'Waktu Submit',
            'Durasi (menit)',
            'Feedback Guru',
            'Tanggal Dinilai',
        ];
    }

    public function collection()
    {
        $query = AssessmentAnswer::with([
            'studentSession.course',
            'assessment.material.course',
        ])->orderBy('created_at');

        if ($this->courseId) {
            $query->whereHas('assessment.material', fn ($q) =>
                $q->where('course_id', $this->courseId)
            );
        }

        if ($this->materialId) {
            $query->whereHas('assessment', fn ($q) =>
                $q->where('material_id', $this->materialId)
            );
        }

        return $query->get()->map(function ($answer, $index) {
            return [
                $index + 1,
                $answer->studentSession->name ?? '—',
                $answer->studentSession->class ?? '—',
                $answer->assessment->material->course->title ?? '—',
                $answer->assessment->material->title ?? '—',
                $answer->assessment->title ?? '—',
                $answer->score ?? '—',
                $answer->status === 'graded' ? 'Sudah Dinilai' : 'Belum Dinilai',
                $answer->started_at?->format('d/m/Y H:i') ?? '—',
                $answer->submitted_at?->format('d/m/Y H:i') ?? '—',
                $answer->duration_minutes ?? '—',
                $answer->teacher_feedback ?? '—',
                $answer->graded_at?->format('d/m/Y H:i') ?? '—',
            ];
        });
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Header row style
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1D4ED8'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        // Data rows — zebra stripe
        for ($row = 2; $row <= $lastRow; $row++) {
            $color = ($row % 2 === 0) ? 'EFF6FF' : 'FFFFFF';
            $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }

        // Warnai kolom Nilai berdasarkan angka
        for ($row = 2; $row <= $lastRow; $row++) {
            $score = $sheet->getCell("G{$row}")->getValue();
            if (is_numeric($score)) {
                $bgColor = $score >= 75 ? 'D1FAE5' : ($score >= 60 ? 'FEF3C7' : 'FEE2E2');
                $txtColor = $score >= 75 ? '065F46' : ($score >= 60 ? '92400E' : '991B1B');
                $sheet->getStyle("G{$row}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'font'      => ['bold' => true, 'color' => ['rgb' => $txtColor]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }
        }

        // Border semua sel
        $sheet->getStyle("A1:M{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Freeze header row
        $sheet->freezePane('A2');

        // Row height header
        $sheet->getRowDimension(1)->setRowHeight(30);

        return [];
    }
}

// ─── Sheet 2: Rekap Per Materi ─────────────────────────────────────────
class RekapPerMateriSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(protected int $courseId) {}

    public function title(): string
    {
        return 'Rekap Per Materi';
    }

    public function headings(): array
    {
        return [
            'Materi', 'Judul Asesmen',
            'Jml Siswa Mengerjakan', 'Sudah Dinilai', 'Belum Dinilai',
            'Nilai Tertinggi', 'Nilai Terendah', 'Rata-rata Nilai',
        ];
    }

    public function collection()
    {
        $course = Course::with(['materials.assessment.answers'])->find($this->courseId);
        if (!$course) return collect();

        return $course->materials
            ->filter(fn ($m) => $m->assessment !== null)
            ->map(function ($material) {
                $answers = $material->assessment->answers;
                $graded  = $answers->where('status', 'graded');
                return [
                    $material->title,
                    $material->assessment->title,
                    $answers->count(),
                    $graded->count(),
                    $answers->where('status', 'pending')->count(),
                    $graded->max('score') ?? '—',
                    $graded->min('score') ?? '—',
                    $graded->count() > 0 ? round($graded->avg('score'), 1) : '—',
                ];
            });
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle("A1:H{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);

        $sheet->freezePane('A2');
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }
}
