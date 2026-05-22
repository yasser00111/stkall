<?php

namespace App\Filament\Resources\StudentSessionResource\Pages;

use App\Filament\Resources\StudentSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentSessions extends ListRecords
{
    protected static string $resource = StudentSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
