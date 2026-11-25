<?php

namespace App\Filament\Resources\PlantSpots\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlantSpotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('public_code')
                    ->label('Kod')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('zone.code')
                    ->label('Blok')
                    ->searchable(),
                TextColumn::make('latestObservation.category.name_uz')
                    ->label('Holati')
                    ->badge(),
                ColorColumn::make('latestObservation.category.color')
                    ->label(''),
                TextColumn::make('latestObservation.health_score')
                    ->label("Sog'liq")
                    ->numeric()
                    ->sortable(),
                TextColumn::make('planted_at')
                    ->label('Ekilgan')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
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
