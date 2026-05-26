<?php

namespace App\Filament\Resources\AssessmentAnswerResource\Pages;

use App\Filament\Resources\AssessmentAnswerResource;
use App\Models\Course;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;

class ListAssessmentAnswers extends ListRecords
{
    protected static string $resource = AssessmentAnswerResource::class;

    protected function getHeaderActions(): array
    {
        $courses = Course::orderBy('title')->pluck('title', 'id')->toArray();

        return [
            // Export Excel
            Action::make('export_excel')
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
                        ? route('export.nilai.excel.course', $courseId)
                        : route('export.nilai.excel');
                    $this->redirect($url);
                }),

            // Export PDF
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
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
                        ? route('export.nilai.pdf.course', $courseId)
                        : route('export.nilai.pdf');
                    $this->redirect($url);
                }),
        ];
    }
}
