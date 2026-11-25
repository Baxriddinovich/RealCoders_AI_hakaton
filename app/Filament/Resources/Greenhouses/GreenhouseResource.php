<?php

namespace App\Filament\Resources\Greenhouses;

use App\Filament\Resources\Greenhouses\Pages\CreateGreenhouse;
use App\Filament\Resources\Greenhouses\Pages\EditGreenhouse;
use App\Filament\Resources\Greenhouses\Pages\ListGreenhouses;
use App\Filament\Resources\Greenhouses\Schemas\GreenhouseForm;
use App\Filament\Resources\Greenhouses\Tables\GreenhousesTable;
use App\Models\Greenhouse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GreenhouseResource extends Resource
{
    protected static ?string $model = Greenhouse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static ?string $navigationLabel = 'Issiqxonalar';

    protected static string|\UnitEnum|null $navigationGroup = 'Issiqxona';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GreenhouseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GreenhousesTable::configure($table);
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
            'index' => ListGreenhouses::route('/'),
            'create' => CreateGreenhouse::route('/create'),
            'edit' => EditGreenhouse::route('/{record}/edit'),
        ];
    }
}
