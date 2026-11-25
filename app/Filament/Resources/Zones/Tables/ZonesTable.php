<?php

namespace App\Filament\Resources\Zones\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ZonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Blok')
                    ->searchable(),
                TextColumn::make('greenhouse.name')
                    ->label('Issiqxona')
                    ->searchable(),
                TextColumn::make('plant_spots_count')
                    ->label("O'simliklar")
                    ->counts('plantSpots')
                    ->sortable(),
                TextColumn::make('row_count')
                    ->label('Qatorlar')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('position_count')
                    ->label("O'rinlar")
                    ->numeric()
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
