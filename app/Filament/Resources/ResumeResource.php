<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResumeResource\Pages;
use App\Models\Resume;
use App\Models\Token;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ResumeResource extends Resource
{
    protected static ?string $model = Resume::class;
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Resume Siswa';
    protected static ?string $modelLabel = 'Resume';
    protected static ?string $pluralModelLabel = 'Resume Siswa';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationBadgeColor = 'warning';

    public static function getNavigationBadge(): ?string
    {
        $count = Resume::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Resume')
                ->schema([
                    Forms\Components\Placeholder::make('student_name')
                        ->label('Nama Siswa')
                        ->content(fn (Resume $record) => $record->studentSession->name . ' - ' . $record->studentSession->class),
                    Forms\Components\Placeholder::make('material_title')
                        ->label('Materi')
                        ->content(fn (Resume $record) => $record->material->title),
                    Forms\Components\Textarea::make('content')
                        ->label('Isi Resume')
                        ->rows(10)
                        ->disabled()
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Penilaian Guru')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending'  => 'Menunggu',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('teacher_feedback')
                        ->label('Catatan / Feedback')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Informasi Siswa')
                ->schema([
                    Infolists\Components\TextEntry::make('studentSession.name')->label('Nama Siswa'),
                    Infolists\Components\TextEntry::make('studentSession.class')->label('Kelas'),
                    Infolists\Components\TextEntry::make('material.course.title')->label('Mata Pelajaran'),
                    Infolists\Components\TextEntry::make('material.title')->label('Materi'),
                    Infolists\Components\TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending'  => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                        }),
                    Infolists\Components\TextEntry::make('created_at')->label('Dikirim')->dateTime('d M Y H:i'),
                ])->columns(3),

            Infolists\Components\Section::make('Isi Resume')
                ->schema([
                    Infolists\Components\TextEntry::make('content')
                        ->label('Teks Resume')
                        ->placeholder('(tidak ada teks)')
                        ->columnSpanFull(),

                    // File upload
                    Infolists\Components\TextEntry::make('file_name')
                        ->label('File Upload')
                        ->placeholder('Tidak ada file')
                        ->formatStateUsing(fn ($state, Resume $record) => $state
                            ? "📎 {$state}"
                            : null)
                        ->suffixAction(
                            Infolists\Components\Actions\Action::make('download_file')
                                ->label('Unduh / Lihat')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->url(fn (Resume $record) => $record->file_url)
                                ->openUrlInNewTab()
                                ->visible(fn (Resume $record) => $record->hasFile()),
                        )
                        ->visible(fn (Resume $record) => $record->hasFile()),

                    // Video URL
                    Infolists\Components\TextEntry::make('video_url')
                        ->label('Video YouTube')
                        ->placeholder('Tidak ada video')
                        ->formatStateUsing(fn ($state) => $state ? "🎬 {$state}" : null)
                        ->suffixAction(
                            Infolists\Components\Actions\Action::make('open_video')
                                ->label('Tonton')
                                ->icon('heroicon-o-play-circle')
                                ->url(fn (Resume $record) => $record->video_url)
                                ->openUrlInNewTab()
                                ->visible(fn (Resume $record) => !empty($record->video_url)),
                        )
                        ->visible(fn (Resume $record) => !empty($record->video_url)),
                ]),

            Infolists\Components\Section::make('Feedback Guru')
                ->schema([
                    Infolists\Components\TextEntry::make('teacher_feedback')
                        ->label('Catatan')
                        ->placeholder('Belum ada feedback')
                        ->columnSpanFull(),
                ])
                ->hidden(fn (Resume $record) => $record->status === 'pending'),

            Infolists\Components\Section::make('💬 Percakapan Feedback')
                ->schema([
                    Infolists\Components\TextEntry::make('teacher_feedback')
                        ->label('Guru menulis:')
                        ->placeholder('—')
                        ->columnSpanFull(),
                    Infolists\Components\TextEntry::make('student_reply')
                        ->label('Balasan Siswa:')
                        ->placeholder('Siswa belum membalas.')
                        ->columnSpanFull(),
                    Infolists\Components\TextEntry::make('student_replied_at')
                        ->label('Waktu Balas')
                        ->dateTime('d M Y H:i')
                        ->placeholder('—'),
                ])->columns(2)
                ->hidden(fn (Resume $record) => $record->status === 'pending'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('studentSession.name')
                    ->label('Nama Siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('studentSession.class')
                    ->label('Kelas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('material.course.title')
                    ->label('Mata Pelajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('material.title')
                    ->label('Materi')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'  => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default    => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('file_path')
                    ->label('File')
                    ->boolean()
                    ->getStateUsing(fn (Resume $record) => !empty($record->file_path))
                    ->trueIcon('heroicon-o-paper-clip')
                    ->trueColor('purple')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('video_url')
                    ->label('Video')
                    ->boolean()
                    ->getStateUsing(fn (Resume $record) => !empty($record->video_url))
                    ->trueIcon('heroicon-o-play-circle')
                    ->trueColor('danger')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('student_reply')
                    ->label('Dibalas Siswa')
                    ->boolean()
                    ->getStateUsing(fn (Resume $record) => !empty($record->student_reply))
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('course')
                    ->label('Mata Pelajaran')
                    ->relationship('material.course', 'title'),
                Tables\Filters\TernaryFilter::make('student_reply')
                    ->label('Balasan Siswa')
                    ->placeholder('Semua')
                    ->trueLabel('Ada balasan')
                    ->falseLabel('Belum dibalas')
                    ->queries(
                        true:  fn ($q) => $q->whereNotNull('student_reply'),
                        false: fn ($q) => $q->whereNull('student_reply'),
                    ),
                Tables\Filters\TernaryFilter::make('file_path')
                    ->label('File Upload')
                    ->placeholder('Semua')
                    ->trueLabel('Ada file')
                    ->falseLabel('Tanpa file')
                    ->queries(
                        true:  fn ($q) => $q->whereNotNull('file_path'),
                        false: fn ($q) => $q->whereNull('file_path'),
                    ),
                Tables\Filters\TernaryFilter::make('video_url')
                    ->label('Video YouTube')
                    ->placeholder('Semua')
                    ->trueLabel('Ada video')
                    ->falseLabel('Tanpa video')
                    ->queries(
                        true:  fn ($q) => $q->whereNotNull('video_url'),
                        false: fn ($q) => $q->whereNull('video_url'),
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Resume $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Resume $record) {
                        $record->update([
                            'status'      => 'approved',
                            'approved_at' => now(),
                        ]);

                        // Generate token untuk asesmen
                        $assessment = $record->material->assessment;
                        if ($assessment) {
                            Token::create([
                                'student_session_id' => $record->student_session_id,
                                'type'               => 'assessment',
                                'material_id'        => $record->material_id,
                                'expires_at'         => now()->addDays(7),
                            ]);
                        }

                        Notification::make()
                            ->title('Resume disetujui! Token asesmen telah dikirim.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Resume $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('teacher_feedback')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Resume $record, array $data) {
                        $record->update([
                            'status'           => 'rejected',
                            'teacher_feedback' => $data['teacher_feedback'],
                        ]);

                        Notification::make()
                            ->title('Resume ditolak.')
                            ->warning()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListResumes::route('/'),
            'view'   => Pages\ViewResume::route('/{record}'),
            'edit'   => Pages\EditResume::route('/{record}/edit'),
        ];
    }
}
