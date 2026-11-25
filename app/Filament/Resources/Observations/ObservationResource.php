<?php

namespace App\Filament\Resources\Observations;

use App\Filament\Resources\Observations\Pages\CreateObservation;
use App\Filament\Resources\Observations\Pages\EditObservation;
use App\Filament\Resources\Observations\Pages\ListObservations;
use App\Filament\Resources\Observations\Schemas\ObservationForm;
use App\Filament\Resources\Observations\Tables\ObservationsTable;
use App\Models\Observation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ObservationResource extends Resource
{
    protected static ?string $model = Observation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    protected static ?string $navigationLabel = 'Kuzatuvlar';

    protected static string|\UnitEnum|null $navigationGroup = 'Monitoring';

    public static function form(Schema $schema): Schema
    {
        return ObservationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ObservationsTable::configure($table);
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
            'index' => ListObservations::route('/'),
            'create' => CreateObservation::route('/create'),
            'edit' => EditObservation::route('/{record}/edit'),
        ];
    }
}
