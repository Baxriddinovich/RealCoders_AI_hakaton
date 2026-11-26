<?php

namespace App\Filament\Resources\RobotStatuses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RobotStatusesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('zone.greenhouse.name')
                    ->label('Issiqxona')
                    ->searchable(),
                TextColumn::make('zone.code')
                    ->label('Zona')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Holati')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'idle' => 'gray',
                        'scanning' => 'success',
                        'charging' => 'warning',
                        'error' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'idle' => 'Tayyor',
                        'scanning' => 'Skanerlanmoqda',
                        'charging' => 'Zaryadlanmoqda',
                        'error' => 'Xatolik',
                        default => $state,
                    }),
                TextColumn::make('battery_pct')
                    ->label('Batareya')
                    ->suffix('%')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('last_scan_at')
                    ->label('Oxirgi skan')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('scans_completed_today')
                    ->label('Bugungi skanlar')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('error_count')
                    ->label('Xatolar')
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
