<?php

namespace App\Filament\Resources\Observations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ObservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('plant_spot_id')
                    ->label("O'simlik")
                    ->relationship('plantSpot', 'public_code')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('category_id')
                    ->label('Holat')
                    ->relationship('category', 'name_uz')
                    ->searchable()
                    ->preload()
                    ->required(),
                FileUpload::make('image_path')
                    ->label('Rasm')
                    ->image()
                    ->disk('public')
                    ->directory('dataset')
                    ->required(),
                TextInput::make('confidence')
                    ->label('Ishonch %')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(90),
                TextInput::make('health_score')
                    ->label("Sog'liq indeksi")
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                TextInput::make('detection_count')
                    ->label('Aniqlangan obyektlar')
                    ->required()
                    ->numeric()
                    ->default(1),
                DateTimePicker::make('observed_at')
                    ->label('Skan vaqti')
                    ->required()
                    ->default(now()),
            ]);
    }
}
