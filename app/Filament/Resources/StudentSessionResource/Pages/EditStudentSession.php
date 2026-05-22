<?php

namespace App\Filament\Resources\StudentSessionResource\Pages;

use App\Filament\Resources\StudentSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentSession extends EditRecord
{
    protected static string $resource = StudentSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
