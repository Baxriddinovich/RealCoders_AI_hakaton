<?php

namespace App\Filament\Resources\Greenhouses\Pages;

use App\Filament\Resources\Greenhouses\GreenhouseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGreenhouses extends ListRecords
{
    protected static string $resource = GreenhouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
