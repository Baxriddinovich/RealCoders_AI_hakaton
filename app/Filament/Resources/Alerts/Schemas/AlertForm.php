<?php

namespace App\Filament\Resources\Alerts\Schemas;

use App\Models\Observation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AlertForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('observation_id')
                    ->label('Kuzatuv')
                    ->relationship('observation', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn (Observation $record) => "{$record->plantSpot->public_code} — {$record->category->name_uz}"
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('severity')
                    ->label('Muhimlik darajasi')
                    ->options([
                        'low' => 'Past',
                        'medium' => "O'rta",
                        'high' => 'Yuqori',
                    ])
                    ->required(),
                Textarea::make('message')
                    ->label('Xabar')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Holati')
                    ->options([
                        'new' => 'Yangi',
                        'acknowledged' => 'Tekshirilmoqda',
                        'resolved' => 'Bajarildi',
                    ])
                    ->required()
                    ->default('new'),
            ]);
    }
}
