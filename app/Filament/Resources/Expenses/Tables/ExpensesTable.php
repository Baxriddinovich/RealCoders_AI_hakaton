<?php

namespace App\Filament\Resources\Expenses\Tables;

use App\Models\Expense;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('greenhouse.name')
                    ->label('Issiqxona')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Toifa')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Expense::CATEGORIES[$state] ?? $state),
                TextColumn::make('amount')
                    ->label("Summa (so'm)")
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('spent_on')
                    ->label('Sana')
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('note')
                    ->label('Izoh')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('spent_on', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->options(Expense::CATEGORIES),
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
