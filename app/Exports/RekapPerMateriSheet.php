<?php

namespace App\Exports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
            'Jml Siswa', 'Sudah Dinilai', 'Belum Dinilai',
            'Nilai Tertinggi', 'Nilai Terendah', 'Rata-rata Nilai',
        ];
    }

    public function collection()
    {
        $course = Course::with(['materials.assessment.answers'])->find($this->courseId);
        if (!$course) return collect();

        return $course->materials
            ->filter(fn ($m) => $m->assessment !== null)
            ->values()
            ->map(function ($material) {
                $answers = $material->assessment->answers;
                $graded  = $answers->where('status', 'graded');
                return [
                    $material->title,
                    $material->assessment->title,
                    $answers->count(),
                    $graded->count(),
                    $answers->where('status', 'pending')->count(),
                    $graded->max('score') ?? '-',
                    $graded->min('score') ?? '-',
                    $graded->count() > 0 ? round($graded->avg('score'), 1) : '-',
                ];
            });
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Header
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->freezePane('A2');
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Data rows — hanya jika ada data
        if ($lastRow >= 2) {
            for ($row = 2; $row <= $lastRow; $row++) {
                $bgColor = ($row % 2 === 0) ? 'F0FDF4' : 'FFFFFF';
                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            $sheet->getStyle("A1:H{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);
        }

        return [];
    }
}
