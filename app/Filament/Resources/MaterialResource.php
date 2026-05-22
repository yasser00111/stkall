<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Materi';
    protected static ?string $modelLabel = 'Materi';
    protected static ?string $pluralModelLabel = 'Materi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Materi')
                ->schema([
                    Forms\Components\Select::make('course_id')
                        ->label('Mata Pelajaran')
                        ->relationship('course', 'title')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Materi')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(0),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])->columns(2),

            Forms\Components\Section::make('Konten Materi')
                ->schema([
                    Forms\Components\RichEditor::make('content')
                        ->label('Isi Materi (Teks)')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('video_url')
                        ->label('URL Video YouTube')
                        ->url()
                        ->placeholder('https://www.youtube.com/watch?v=...')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Asesmen (Soal Essay)')
                ->relationship('assessment')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Asesmen')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('instructions')
                        ->label('Petunjuk / Soal')
                        ->rows(5)
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])->columns(2)
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width(50),
                Tables\Columns\TextColumn::make('course.title')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Materi')
                    ->searchable(),
                Tables\Columns\IconColumn::make('video_url')
                    ->label('Video')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->video_url)),
                Tables\Columns\IconColumn::make('assessment')
                    ->label('Punya Asesmen')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->assessment !== null),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course')
                    ->label('Mata Pelajaran')
                    ->relationship('course', 'title'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit'   => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}
