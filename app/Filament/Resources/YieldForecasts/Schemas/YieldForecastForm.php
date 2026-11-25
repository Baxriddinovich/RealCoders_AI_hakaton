<?php

namespace App\Filament\Resources\YieldForecasts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class YieldForecastForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('greenhouse_id')
                    ->label('Issiqxona')
                    ->relationship('greenhouse', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('forecast_date')
                    ->label('Prognoz sanasi')
                    ->required()
                    ->default(now()),
                TextInput::make('horizon_days')
                    ->label('Ufq (kun)')
                    ->required()
                    ->numeric(),
                TextInput::make('expected_kg')
                    ->label('Kutilgan hosil (kg)')
                    ->required()
                    ->numeric(),
                TextInput::make('grade_a_pct')
                    ->label('A nav %')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                TextInput::make('grade_b_pct')
                    ->label('B nav %')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                TextInput::make('grade_c_pct')
                    ->label('C nav %')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
            ]);
    }
}
