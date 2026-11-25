<?php

namespace App\Filament\Resources\RobotStatuses;

use App\Filament\Resources\RobotStatuses\Pages\CreateRobotStatus;
use App\Filament\Resources\RobotStatuses\Pages\EditRobotStatus;
use App\Filament\Resources\RobotStatuses\Pages\ListRobotStatuses;
use App\Filament\Resources\RobotStatuses\Schemas\RobotStatusForm;
use App\Filament\Resources\RobotStatuses\Tables\RobotStatusesTable;
use App\Models\RobotStatus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RobotStatusResource extends Resource
{
    protected static ?string $model = RobotStatus::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static ?string $navigationLabel = 'Robot holati';

    protected static string|\UnitEnum|null $navigationGroup = 'Tahlil';

    public static function form(Schema $schema): Schema
    {
        return RobotStatusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RobotStatusesTable::configure($table);
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
            'index' => ListRobotStatuses::route('/'),
            'create' => CreateRobotStatus::route('/create'),
            'edit' => EditRobotStatus::route('/{record}/edit'),
        ];
    }
}
