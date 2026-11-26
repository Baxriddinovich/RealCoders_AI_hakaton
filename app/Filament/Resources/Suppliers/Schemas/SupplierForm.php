<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Models\Expense;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category')
                    ->label('Toifa')
                    ->options(Expense::CATEGORIES)
                    ->required(),
                TextInput::make('name')
                    ->label('Nomi/F.I.Sh')
                    ->required(),
                TextInput::make('phone')
                    ->label('Telefon')
                    ->tel(),
                TextInput::make('location')
                    ->label('Joylashuvi'),
                TextInput::make('account_number')
                    ->label('Hisob raqami')
                    ->helperText("Gaz/svet/suv kabi kommunal toifalar uchun to'lov hisob raqami"),
                TextInput::make('note')
                    ->label('Izoh')
                    ->maxLength(255),
            ]);
    }
}
