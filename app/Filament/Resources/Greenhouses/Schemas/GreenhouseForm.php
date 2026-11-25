<?php

namespace App\Filament\Resources\Greenhouses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GreenhouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('location'),
                TextInput::make('area_ha')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('crop')
                    ->required()
                    ->default('Qulupnay'),
                TextInput::make('cover_type'),
            ]);
    }
}
