<?php

namespace App\Filament\Resources\PlantSpots\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PlantSpotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('zone_id')
                    ->label('Blok')
                    ->relationship('zone', 'code')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('public_code')
                    ->label('Kod')
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('row_no')
                    ->label('Qator')
                    ->required()
                    ->numeric(),
                TextInput::make('position_no')
                    ->label("O'rin")
                    ->required()
                    ->numeric(),
                DatePicker::make('planted_at')
                    ->label('Ekilgan sana'),
            ]);
    }
}
