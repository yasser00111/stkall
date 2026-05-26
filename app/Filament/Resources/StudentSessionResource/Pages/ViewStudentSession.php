<?php

namespace App\Filament\Resources\StudentSessionResource\Pages;

use App\Filament\Resources\StudentSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentSession extends ViewRecord
{
    protected static string $resource = StudentSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus Siswa Ini')
                ->modalHeading('Hapus Data Siswa')
                ->modalDescription(fn () =>
                    "Yakin ingin menghapus data siswa \"{$this->record->name}\" ({$this->record->class})? " .
                    "Semua resume, jawaban asesmen, dan token milik siswa ini akan ikut terhapus secara permanen."
                )
                ->successRedirectUrl(StudentSessionResource::getUrl('index'))
                ->successNotificationTitle('Data siswa berhasil dihapus.'),
        ];
    }
}
