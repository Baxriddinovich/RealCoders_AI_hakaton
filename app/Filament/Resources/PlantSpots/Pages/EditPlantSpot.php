<?php

namespace App\Filament\Resources\PlantSpots\Pages;

use App\Filament\Resources\PlantSpots\PlantSpotResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlantSpot extends EditRecord
{
    protected static string $resource = PlantSpotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
