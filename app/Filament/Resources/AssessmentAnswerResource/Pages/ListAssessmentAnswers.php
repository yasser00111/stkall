<?php

namespace App\Filament\Resources\AssessmentAnswerResource\Pages;

use App\Filament\Resources\AssessmentAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssessmentAnswers extends ListRecords
{
    protected static string $resource = AssessmentAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
