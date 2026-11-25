<?php

namespace App\Filament\Resources\Zones\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('greenhouse_id')
                    ->relationship('greenhouse', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('code')
                    ->label('Blok kodi')
                    ->helperText('Masalan: B1')
                    ->required(),
                TextInput::make('row_count')
                    ->label('Qatorlar soni')
                    ->required()
                    ->numeric()
                    ->default(10),
                TextInput::make('position_count')
                    ->label("Qatordagi o'rinlar soni")
                    ->required()
                    ->numeric()
                    ->default(10),
            ]);
    }
}
