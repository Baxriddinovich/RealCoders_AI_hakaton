<?php

namespace App\Filament\Resources\RobotStatuses\Pages;

use App\Filament\Resources\RobotStatuses\RobotStatusResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRobotStatus extends EditRecord
{
    protected static string $resource = RobotStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
