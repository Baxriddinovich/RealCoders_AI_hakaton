<?php

namespace App\Filament\Resources\RobotStatuses\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RobotStatusForm
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
                Select::make('status')
                    ->label('Holati')
                    ->options([
                        'idle' => 'Tayyor',
                        'scanning' => 'Skanerlanmoqda',
                        'charging' => 'Zaryadlanmoqda',
                        'error' => 'Xatolik',
                    ])
                    ->required()
                    ->default('idle'),
                TextInput::make('battery_pct')
                    ->label('Batareya %')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(100),
                DateTimePicker::make('last_scan_at')
                    ->label('Oxirgi skan'),
                TextInput::make('scans_completed_today')
                    ->label('Bugungi skanlar')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('error_count')
                    ->label('Xatolar soni')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
