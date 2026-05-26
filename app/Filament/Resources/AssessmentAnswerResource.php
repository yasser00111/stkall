<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentAnswerResource\Pages;
use App\Models\AssessmentAnswer;
use App\Models\Token;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssessmentAnswerResource extends Resource
{
    protected static ?string $model = AssessmentAnswer::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Jawaban Asesmen';
    protected static ?string $modelLabel = 'Jawaban Asesmen';
    protected static ?string $pluralModelLabel = 'Jawaban Asesmen';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationBadgeColor = 'warning';

    public static function getNavigationBadge(): ?string
    {
        $count = AssessmentAnswer::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Penilaian Jawaban')
                ->schema([
                    Forms\Components\TextInput::make('score')
                        ->label('Nilai (0-100)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending' => 'Belum Dinilai',
                            'graded'  => 'Sudah Dinilai',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('teacher_feedback')
                        ->label('Feedback / Catatan Guru')
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
                    Infolists\Components\TextEntry::make('assessment.material.course.title')->label('Mata Pelajaran'),
                    Infolists\Components\TextEntry::make('assessment.material.title')->label('Materi'),
                    Infolists\Components\TextEntry::make('assessment.title')->label('Judul Asesmen'),
                    Infolists\Components\TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending' => 'warning',
                            'graded'  => 'success',
                            default   => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => $state === 'pending' ? 'Belum Dinilai' : 'Sudah Dinilai'),
                    Infolists\Components\TextEntry::make('score')
                        ->label('Nilai')
                        ->placeholder('Belum dinilai'),
                    Infolists\Components\TextEntry::make('created_at')->label('Dikirim')->dateTime('d M Y H:i'),
                ])->columns(3),

            Infolists\Components\Section::make('Soal Asesmen')
                ->schema([
                    Infolists\Components\TextEntry::make('assessment.instructions')
                        ->label('')
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Jawaban Siswa')
                ->schema([
                    Infolists\Components\TextEntry::make('answer')
                        ->label('')
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Feedback Guru')
                ->schema([
                    Infolists\Components\TextEntry::make('teacher_feedback')
                        ->label('Catatan')
                        ->placeholder('Belum ada feedback'),
                ])
                ->hidden(fn (AssessmentAnswer $record) => $record->status === 'pending'),

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
                ->hidden(fn (AssessmentAnswer $record) => $record->status === 'pending'),
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
                Tables\Columns\TextColumn::make('assessment.material.course.title')
                    ->label('Mata Pelajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('assessment.title')
                    ->label('Asesmen'),
                Tables\Columns\TextColumn::make('score')
                    ->label('Nilai')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'graded',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'pending' ? 'Belum Dinilai' : 'Sudah Dinilai'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->getStateUsing(fn ($record) => $record->duration_minutes)
                    ->formatStateUsing(fn ($state) => $state ? "{$state} mnt" : '—')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('student_reply')
                    ->label('Dibalas Siswa')
                    ->boolean()
                    ->getStateUsing(fn (AssessmentAnswer $record) => !empty($record->student_reply))
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Belum Dinilai',
                        'graded'  => 'Sudah Dinilai',
                    ]),
                Tables\Filters\TernaryFilter::make('student_reply')
                    ->label('Balasan Siswa')
                    ->placeholder('Semua')
                    ->trueLabel('Ada balasan')
                    ->falseLabel('Belum dibalas')
                    ->queries(
                        true:  fn ($q) => $q->whereNotNull('student_reply'),
                        false: fn ($q) => $q->whereNull('student_reply'),
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('grade')
                    ->label('Beri Nilai')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(fn (AssessmentAnswer $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\TextInput::make('score')
                            ->label('Nilai (0-100)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                        Forms\Components\Textarea::make('teacher_feedback')
                            ->label('Feedback / Catatan Guru')
                            ->rows(3),
                    ])
                    ->action(function (AssessmentAnswer $record, array $data) {
                        $record->update([
                            'score'            => $data['score'],
                            'teacher_feedback' => $data['teacher_feedback'] ?? null,
                            'status'           => 'graded',
                            'graded_at'        => now(),
                        ]);

                        // Generate token untuk materi berikutnya
                        $currentMaterial = $record->assessment->material;
                        $nextMaterial = $currentMaterial->course->materials()
                            ->where('order', '>', $currentMaterial->order)
                            ->where('is_active', true)
                            ->orderBy('order')
                            ->first();

                        if ($nextMaterial) {
                            Token::create([
                                'student_session_id' => $record->student_session_id,
                                'type'               => 'material',
                                'material_id'        => $nextMaterial->id,
                                'expires_at'         => now()->addDays(7),
                            ]);
                        }

                        Notification::make()
                            ->title($nextMaterial
                                ? 'Nilai disimpan! Token materi berikutnya telah digenerate.'
                                : 'Nilai disimpan! Ini adalah materi terakhir.')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssessmentAnswers::route('/'),
            'view'  => Pages\ViewAssessmentAnswer::route('/{record}'),
            'edit'  => Pages\EditAssessmentAnswer::route('/{record}/edit'),
        ];
    }
}
