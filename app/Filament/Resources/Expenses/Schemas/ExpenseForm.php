<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Models\Expense;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('greenhouse_id')
                    ->relationship('greenhouse', 'name')
                    ->required()
                    ->searchable(),
                Select::make('category')
                    ->label('Toifa')
                    ->options(Expense::CATEGORIES)
                    ->required(),
                TextInput::make('amount')
                    ->label("Summa (so'm)")
                    ->required()
                    ->numeric()
                    ->minValue(0),
                DatePicker::make('spent_on')
                    ->label('Sana')
                    ->required()
                    ->default(now()),
                TextInput::make('note')
                    ->label('Izoh')
                    ->maxLength(255),
            ]);
    }
}
