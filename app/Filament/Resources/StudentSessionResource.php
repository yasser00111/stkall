<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentSessionResource\Pages;
use App\Models\StudentSession;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentSessionResource extends Resource
{
    protected static ?string $model = StudentSession::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Data Siswa';
    protected static ?string $modelLabel = 'Siswa';
    protected static ?string $pluralModelLabel = 'Data Siswa';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Profil Siswa')
                ->schema([
                    Infolists\Components\TextEntry::make('name')->label('Nama'),
                    Infolists\Components\TextEntry::make('class')->label('Kelas'),
                    Infolists\Components\TextEntry::make('course.title')->label('Mata Pelajaran'),
                    Infolists\Components\TextEntry::make('current_material_order')->label('Progres Materi'),
                    Infolists\Components\TextEntry::make('session_token')->label('Token Sesi')->copyable(),
                    Infolists\Components\TextEntry::make('created_at')->label('Bergabung')->dateTime('d M Y'),
                ])->columns(3),

            Infolists\Components\Section::make('Token Aktif')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('tokens')
                        ->label('')
                        ->schema([
                            Infolists\Components\TextEntry::make('token')->label('Kode Token')->copyable(),
                            Infolists\Components\TextEntry::make('type')
                                ->label('Tipe')
                                ->formatStateUsing(fn ($state) => $state === 'assessment' ? 'Asesmen' : 'Materi'),
                            Infolists\Components\IconEntry::make('is_used')->label('Sudah Dipakai')->boolean(),
                            Infolists\Components\TextEntry::make('expires_at')->label('Kadaluarsa')->dateTime('d M Y H:i'),
                        ])->columns(4),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('class')
                    ->label('Kelas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.title')
                    ->label('Mata Pelajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_material_order')
                    ->label('Progres')
                    ->formatStateUsing(fn ($state) => "Materi #{$state}")
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('resumes_count')
                    ->label('Resume')
                    ->counts('resumes')
                    ->badge(),
                Tables\Columns\TextColumn::make('assessmentAnswers_count')
                    ->label('Asesmen')
                    ->counts('assessmentAnswers')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Bergabung')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course')
                    ->label('Mata Pelajaran')
                    ->relationship('course', 'title'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Data Siswa')
                    ->modalDescription(fn (StudentSession $record) =>
                        "Yakin ingin menghapus data siswa \"{$record->name}\" ({$record->class})? " .
                        "Semua resume, jawaban asesmen, dan token milik siswa ini akan ikut terhapus."
                    )
                    ->successNotificationTitle('Data siswa berhasil dihapus.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->modalHeading('Hapus Data Siswa Terpilih')
                        ->modalDescription('Semua resume, jawaban asesmen, dan token milik siswa yang dipilih akan ikut terhapus.')
                        ->successNotificationTitle('Data siswa berhasil dihapus.'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentSessions::route('/'),
            'view'  => Pages\ViewStudentSession::route('/{record}'),
        ];
    }
}
