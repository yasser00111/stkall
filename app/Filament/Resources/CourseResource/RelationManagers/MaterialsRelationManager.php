<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';
    protected static ?string $title = 'Materi';

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul Materi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('content')
                    ->label('Isi Materi (Teks)')
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike',
                        'link', 'h2', 'h3',
                        'bulletList', 'orderedList',
                        'blockquote', 'codeBlock',
                        'undo', 'redo',
                    ]),
                Forms\Components\TextInput::make('video_url')
                    ->label('URL Video YouTube')
                    ->placeholder('https://www.youtube.com/watch?v=...')
                    ->helperText('Tempel URL YouTube di sini, contoh: https://www.youtube.com/watch?v=abc123')
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('preview_video')
                            ->icon('heroicon-o-play-circle')
                            ->label('Buka')
                            ->tooltip('Buka video di tab baru')
                            ->url(fn ($state) => $state ?: null)
                            ->openUrlInNewTab()
                            ->visible(fn ($state) => filled($state)),
                    ),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width(50),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Materi')
                    ->searchable(),
                Tables\Columns\IconColumn::make('video_url')
                    ->label('Video')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->video_url)),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->reorderable('order')
            ->defaultSort('order')
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Tambah Materi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
