<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Masalan: disease_gray_mold'),
                TextInput::make('name_uz')
                    ->label("Nomi (o'zbekcha)")
                    ->required(),
                TextInput::make('name_en')
                    ->label('Name (English)')
                    ->required(),
                Select::make('type')
                    ->options([
                        'healthy' => "Sog'lom",
                        'disease' => 'Kasallik',
                        'pest' => 'Zararkunanda',
                        'deficiency' => 'Oziq yetishmovchiligi',
                    ])
                    ->required(),
                TextInput::make('severity_weight')
                    ->label('Jarima og\'irligi (0-100)')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0),
                ColorPicker::make('color')
                    ->required()
                    ->default('#2E9E44'),
            ]);
    }
}
