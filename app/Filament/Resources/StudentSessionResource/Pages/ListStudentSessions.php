<?php

namespace App\Filament\Resources\StudentSessionResource\Pages;

use App\Filament\Resources\StudentSessionResource;
use Filament\Resources\Pages\ListRecords;

class ListStudentSessions extends ListRecords
{
    protected static string $resource = StudentSessionResource::class;

    // Tidak ada tombol Create — siswa masuk lewat link guru, bukan dari admin
    protected function getHeaderActions(): array
    {
        return [];
    }
}
