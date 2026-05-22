<?php

namespace App\Filament\Widgets;

use App\Models\AssessmentAnswer;
use App\Models\Course;
use App\Models\Resume;
use App\Models\StudentSession;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Mata Pelajaran', Course::count())
                ->description('Total mata pelajaran aktif')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            Stat::make('Total Siswa', StudentSession::count())
                ->description('Siswa yang sedang belajar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Resume Menunggu', Resume::where('status', 'pending')->count())
                ->description('Perlu ditinjau oleh guru')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning'),

            Stat::make('Asesmen Belum Dinilai', AssessmentAnswer::where('status', 'pending')->count())
                ->description('Perlu diberi nilai')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('danger'),
        ];
    }
}
