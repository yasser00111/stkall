<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class NilaiSiswaExport implements WithMultipleSheets
{
    public function __construct(
        protected ?int $courseId = null,
        protected ?int $materialId = null
    ) {}

    public function sheets(): array
    {
        $sheets = [new NilaiSheet($this->courseId, $this->materialId)];

        // Tambah sheet rekap per materi hanya jika filter per course
        if ($this->courseId && !$this->materialId) {
            $sheets[] = new RekapPerMateriSheet($this->courseId);
        }

        return $sheets;
    }
}
