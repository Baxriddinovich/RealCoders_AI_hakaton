<?php

namespace App\Filament\Resources\Suppliers\Tables;

use App\Models\Expense;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')
                    ->label('Toifa')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Expense::CATEGORIES[$state] ?? $state),
                TextColumn::make('name')
                    ->label('Nomi/F.I.Sh')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),
                TextColumn::make('location')
                    ->label('Joylashuvi')
                    ->searchable(),
            ])
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
