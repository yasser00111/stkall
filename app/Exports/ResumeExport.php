<?php

namespace App\Exports;

use App\Models\Resume;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ResumeExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(protected ?int $courseId = null) {}

    public function title(): string
    {
        return 'Resume Siswa';
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Siswa', 'Kelas', 'Mata Pelajaran',
            'Materi', 'Status', 'Feedback Guru', 'Balasan Siswa',
            'Tanggal Kirim',
        ];
    }

    public function collection()
    {
        $query = Resume::with(['studentSession', 'material.course'])
            ->orderBy('created_at');

        if ($this->courseId) {
            $query->whereHas('material', fn ($q) =>
                $q->where('course_id', $this->courseId)
            );
        }

        return $query->get()->map(function ($resume, $index) {
            return [
                $index + 1,
                $resume->studentSession->name ?? '-',
                $resume->studentSession->class ?? '-',
                $resume->material->course->title ?? '-',
                $resume->material->title ?? '-',
                match ($resume->status) {
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    default    => 'Menunggu',
                },
                $resume->teacher_feedback ?? '-',
                $resume->student_reply ?? '-',
                $resume->created_at->format('d/m/Y H:i'),
            ];
        });
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Header
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->freezePane('A2');
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Data rows — hanya jika ada data
        if ($lastRow >= 2) {
            for ($row = 2; $row <= $lastRow; $row++) {
                $bgColor = ($row % 2 === 0) ? 'F5F3FF' : 'FFFFFF';
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                // Warnai kolom Status (F) berdasarkan nilai
                $status = $sheet->getCell("F{$row}")->getValue();
                $statusColors = [
                    'Disetujui' => ['bg' => 'D1FAE5', 'txt' => '065F46'],
                    'Ditolak'   => ['bg' => 'FEE2E2', 'txt' => '991B1B'],
                    'Menunggu'  => ['bg' => 'FEF3C7', 'txt' => '92400E'],
                ];
                if (isset($statusColors[$status])) {
                    $sheet->getStyle("F{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColors[$status]['bg']]],
                        'font' => ['bold' => true, 'color' => ['rgb' => $statusColors[$status]['txt']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }
            }

            $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
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
