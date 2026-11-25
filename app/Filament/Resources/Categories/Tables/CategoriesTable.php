<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label(''),
                TextColumn::make('name_uz')
                    ->label('Nomi')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'healthy' => 'success',
                        'disease' => 'danger',
                        'pest' => 'warning',
                        'deficiency' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'healthy' => "Sog'lom",
                        'disease' => 'Kasallik',
                        'pest' => 'Zararkunanda',
                        'deficiency' => 'Yetishmovchilik',
                        default => $state,
                    }),
                TextColumn::make('severity_weight')
                    ->label('Jarima')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('observations_count')
                    ->label('Kuzatuvlar')
                    ->counts('observations')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'healthy' => "Sog'lom",
                        'disease' => 'Kasallik',
                        'pest' => 'Zararkunanda',
                        'deficiency' => 'Yetishmovchilik',
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
