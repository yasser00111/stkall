<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Export nilai course ini ke Excel
            Action::make('export_excel')
                ->label('Export Nilai Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->url(fn () => route('export.nilai.excel.course', $this->record->id))
                ->openUrlInNewTab(),

            // Export nilai course ini ke PDF
            Action::make('export_pdf')
                ->label('Export Nilai PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->url(fn () => route('export.nilai.pdf.course', $this->record->id))
                ->openUrlInNewTab(),

            // Export resume course ini ke Excel
            Action::make('export_resume')
                ->label('Export Resume Excel')
                ->icon('heroicon-o-pencil-square')
                ->color('info')
                ->url(fn () => route('export.resume.excel.course', $this->record->id))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make(),
        ];
    }
}
