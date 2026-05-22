<?php

namespace App\Filament\Resources\AssessmentAnswerResource\Pages;

use App\Filament\Resources\AssessmentAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssessmentAnswer extends EditRecord
{
    protected static string $resource = AssessmentAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
