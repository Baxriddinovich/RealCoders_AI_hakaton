<?php

namespace App\Filament\Resources\Greenhouses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GreenhousesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nomi')
                    ->searchable(),
                TextColumn::make('location')
                    ->label('Manzil')
                    ->searchable(),
                TextColumn::make('crop')
                    ->label('Ekin')
                    ->searchable(),
                TextColumn::make('area_ha')
                    ->label('Maydon')
                    ->suffix(' ga')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('zones_count')
                    ->label('Bloklar')
                    ->counts('zones'),
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
