<?php

namespace App\Filament\Resources\Alerts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AlertsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('observation.plantSpot.public_code')
                    ->label("O'simlik")
                    ->searchable(),
                TextColumn::make('observation.category.name_uz')
                    ->label('Holat'),
                TextColumn::make('severity')
                    ->label('Muhimlik')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'high' => 'Yuqori',
                        'medium' => "O'rta",
                        'low' => 'Past',
                        default => $state,
                    }),
                TextColumn::make('message')
                    ->label('Xabar')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Holati')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'danger',
                        'acknowledged' => 'warning',
                        'resolved' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Yangi',
                        'acknowledged' => 'Tekshirilmoqda',
                        'resolved' => 'Bajarildi',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('Vaqt')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new' => 'Yangi',
                        'acknowledged' => 'Tekshirilmoqda',
                        'resolved' => 'Bajarildi',
                    ]),
                SelectFilter::make('severity')
                    ->options([
                        'high' => 'Yuqori',
                        'medium' => "O'rta",
                        'low' => 'Past',
                    ]),
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
