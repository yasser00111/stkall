<?php

namespace App\Filament\Resources\AssessmentAnswerResource\Pages;

use App\Filament\Resources\AssessmentAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAssessmentAnswer extends ViewRecord
{
    protected static string $resource = AssessmentAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
