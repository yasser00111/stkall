<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentResource\Pages;
use App\Models\Assessment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Asesmen';
    protected static ?string $modelLabel = 'Asesmen';
    protected static ?string $pluralModelLabel = 'Asesmen';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Asesmen')
                ->schema([
                    Forms\Components\Select::make('material_id')
                        ->label('Materi')
                        ->relationship('material', 'title')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Asesmen')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])->columns(2),

            Forms\Components\Section::make('Soal')
                ->columns(1)
                ->schema([
                    Forms\Components\Textarea::make('instructions')
                        ->label('Petunjuk / Soal Essay')
                        ->rows(5)
                        ->required(),
                ]),

            Forms\Components\Section::make('⏱ Batas Waktu')
                ->description('Atur batas waktu pengerjaan dan/atau tanggal deadline asesmen ini.')
                ->schema([
                    Forms\Components\TextInput::make('time_limit')
                        ->label('Batas Waktu Pengerjaan (menit)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(480)
                        ->placeholder('Contoh: 60 (untuk 1 jam)')
                        ->suffix('menit')
                        ->helperText('Kosongkan jika tidak ada batas waktu. Waktu dihitung sejak siswa membuka halaman asesmen.')
                        ->nullable(),
                    Forms\Components\DateTimePicker::make('deadline')
                        ->label('Deadline Pengerjaan')
                        ->placeholder('Pilih tanggal & waktu deadline')
                        ->helperText('Setelah deadline, siswa tidak bisa mengerjakan asesmen ini.')
                        ->nullable()
                        ->displayFormat('d/m/Y H:i')
                        ->timezone('Asia/Jakarta'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('material.course.title')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('material.title')
                    ->label('Materi')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Asesmen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('time_limit')
                    ->label('Batas Waktu')
                    ->formatStateUsing(fn ($state) => $state ? "{$state} menit" : 'Tidak terbatas')
                    ->badge()
                    ->color(fn ($state) => $state ? 'warning' : 'gray'),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Deadline')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Tidak ada')
                    ->color(fn ($record) => $record->deadline?->isPast() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('answers_count')
                    ->label('Dikerjakan')
                    ->counts('answers')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course')
                    ->label('Mata Pelajaran')
                    ->relationship('material.course', 'title'),
                Tables\Filters\TernaryFilter::make('time_limit')
                    ->label('Batas Waktu')
                    ->placeholder('Semua')
                    ->trueLabel('Ada batas waktu')
                    ->falseLabel('Tidak terbatas')
                    ->queries(
                        true:  fn ($q) => $q->whereNotNull('time_limit'),
                        false: fn ($q) => $q->whereNull('time_limit'),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAssessments::route('/'),
            'create' => Pages\CreateAssessment::route('/create'),
            'edit'   => Pages\EditAssessment::route('/{record}/edit'),
        ];
    }
}
