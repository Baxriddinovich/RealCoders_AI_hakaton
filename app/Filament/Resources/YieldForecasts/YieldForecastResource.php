<?php

namespace App\Filament\Resources\YieldForecasts;

use App\Filament\Resources\YieldForecasts\Pages\CreateYieldForecast;
use App\Filament\Resources\YieldForecasts\Pages\EditYieldForecast;
use App\Filament\Resources\YieldForecasts\Pages\ListYieldForecasts;
use App\Filament\Resources\YieldForecasts\Schemas\YieldForecastForm;
use App\Filament\Resources\YieldForecasts\Tables\YieldForecastsTable;
use App\Models\YieldForecast;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class YieldForecastResource extends Resource
{
    protected static ?string $model = YieldForecast::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Hosil prognozi';

    protected static string|\UnitEnum|null $navigationGroup = 'Tahlil';

    public static function form(Schema $schema): Schema
    {
        return YieldForecastForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return YieldForecastsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListYieldForecasts::route('/'),
            'create' => CreateYieldForecast::route('/create'),
            'edit' => EditYieldForecast::route('/{record}/edit'),
        ];
    }
}
