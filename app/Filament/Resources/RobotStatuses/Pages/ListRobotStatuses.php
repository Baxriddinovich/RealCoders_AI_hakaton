<?php

namespace App\Filament\Resources\RobotStatuses\Pages;

use App\Filament\Resources\RobotStatuses\RobotStatusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRobotStatuses extends ListRecords
{
    protected static string $resource = RobotStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
