<?php

namespace App\Exports;

use App\Models\AssessmentAnswer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
            'No', 'Nama Siswa', 'Kelas', 'Mata Pelajaran',
            'Materi', 'Judul Asesmen', 'Nilai', 'Status',
            'Waktu Mulai', 'Waktu Submit', 'Durasi (menit)',
            'Feedback Guru', 'Tanggal Dinilai',
        ];
    }

    public function collection()
    {
        $query = AssessmentAnswer::with([
            'studentSession',
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
                $answer->studentSession->name ?? '-',
                $answer->studentSession->class ?? '-',
                $answer->assessment->material->course->title ?? '-',
                $answer->assessment->material->title ?? '-',
                $answer->assessment->title ?? '-',
                $answer->score ?? '-',
                $answer->status === 'graded' ? 'Sudah Dinilai' : 'Belum Dinilai',
                $answer->started_at?->format('d/m/Y H:i') ?? '-',
                $answer->submitted_at?->format('d/m/Y H:i') ?? '-',
                $answer->duration_minutes ?? '-',
                $answer->teacher_feedback ?? '-',
                $answer->graded_at?->format('d/m/Y H:i') ?? '-',
            ];
        });
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Selalu style header row 1
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

        // Freeze header
        $sheet->freezePane('A2');
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Data rows — hanya jika ada data
        if ($lastRow >= 2) {
            for ($row = 2; $row <= $lastRow; $row++) {
                $bgColor = ($row % 2 === 0) ? 'EFF6FF' : 'FFFFFF';
                $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Warnai kolom Nilai (G)
                $score = $sheet->getCell("G{$row}")->getValue();
                if (is_numeric($score)) {
                    $bgScore  = $score >= 75 ? 'D1FAE5' : ($score >= 60 ? 'FEF3C7' : 'FEE2E2');
                    $txtScore = $score >= 75 ? '065F46' : ($score >= 60 ? '92400E' : '991B1B');
                    $sheet->getStyle("G{$row}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgScore]],
                        'font'      => ['bold' => true, 'color' => ['rgb' => $txtScore]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }
            }

            // Border seluruh tabel
            $sheet->getStyle("A1:M{$lastRow}")->applyFromArray([
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
