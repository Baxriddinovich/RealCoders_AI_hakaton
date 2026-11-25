<?php

namespace App\Filament\Resources\YieldForecasts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class YieldForecastsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('greenhouse.name')
                    ->label('Issiqxona')
                    ->searchable(),
                TextColumn::make('horizon_days')
                    ->label('Ufq')
                    ->suffix(' kun')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('expected_kg')
                    ->label('Kutilgan hosil')
                    ->suffix(' kg')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('grade_a_pct')
                    ->label('A nav')
                    ->suffix('%')
                    ->numeric(),
                TextColumn::make('grade_b_pct')
                    ->label('B nav')
                    ->suffix('%')
                    ->numeric(),
                TextColumn::make('grade_c_pct')
                    ->label('C nav')
                    ->suffix('%')
                    ->numeric(),
                TextColumn::make('forecast_date')
                    ->label('Sana')
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
