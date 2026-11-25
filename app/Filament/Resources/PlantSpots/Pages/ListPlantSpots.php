<?php

namespace App\Filament\Resources\PlantSpots\Pages;

use App\Filament\Resources\PlantSpots\PlantSpotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlantSpots extends ListRecords
{
    protected static string $resource = PlantSpotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
