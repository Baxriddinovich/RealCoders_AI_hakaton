<?php

namespace App\Filament\Resources\Observations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ObservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('observed_at', 'desc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('')
                    ->disk('public'),
                TextColumn::make('plantSpot.public_code')
                    ->label("O'simlik")
                    ->searchable(),
                TextColumn::make('category.name_uz')
                    ->label('Holat')
                    ->badge()
                    ->color(fn ($record) => match ($record->category->type) {
                        'healthy' => 'success',
                        'disease' => 'danger',
                        'pest' => 'warning',
                        'deficiency' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('health_score')
                    ->label("Sog'liq")
                    ->numeric()
                    ->sortable(),
                TextColumn::make('confidence')
                    ->label('Ishonch')
                    ->suffix('%')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('detection_count')
                    ->label('Obyektlar')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('observed_at')
                    ->label('Vaqt')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Holat')
                    ->relationship('category', 'name_uz'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
