<?php

namespace App\Filament\Resources\ResumeResource\Pages;

use App\Filament\Resources\ResumeResource;
use App\Models\Course;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;

class ListResumes extends ListRecords
{
    protected static string $resource = ResumeResource::class;

    protected function getHeaderActions(): array
    {
        $courses = Course::orderBy('title')->pluck('title', 'id')->toArray();

        return [
            Action::make('export_resume_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('course_id')
                        ->label('Filter Mata Pelajaran (Opsional)')
                        ->options($courses)
                        ->placeholder('Semua Mata Pelajaran')
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    $courseId = $data['course_id'] ?? null;
                    $url = $courseId
                        ? route('export.resume.excel.course', $courseId)
                        : route('export.resume.excel');
                    $this->redirect($url);
                }),
        ];
    }
}
