<?php

namespace App\Filament\Resources\PlantSpots;

use App\Filament\Resources\PlantSpots\Pages\CreatePlantSpot;
use App\Filament\Resources\PlantSpots\Pages\EditPlantSpot;
use App\Filament\Resources\PlantSpots\Pages\ListPlantSpots;
use App\Filament\Resources\PlantSpots\Schemas\PlantSpotForm;
use App\Filament\Resources\PlantSpots\Tables\PlantSpotsTable;
use App\Models\PlantSpot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PlantSpotResource extends Resource
{
    protected static ?string $model = PlantSpot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = "O'simliklar";

    protected static string|\UnitEnum|null $navigationGroup = 'Issiqxona';

    protected static ?string $recordTitleAttribute = 'public_code';

    public static function form(Schema $schema): Schema
    {
        return PlantSpotForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlantSpotsTable::configure($table);
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
            'index' => ListPlantSpots::route('/'),
            'create' => CreatePlantSpot::route('/create'),
            'edit' => EditPlantSpot::route('/{record}/edit'),
        ];
    }
}
