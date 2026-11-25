<?php

namespace App\Filament\Resources\YieldForecasts\Pages;

use App\Filament\Resources\YieldForecasts\YieldForecastResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListYieldForecasts extends ListRecords
{
    protected static string $resource = YieldForecastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
