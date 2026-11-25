<?php

namespace App\Filament\Resources\YieldForecasts\Pages;

use App\Filament\Resources\YieldForecasts\YieldForecastResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditYieldForecast extends EditRecord
{
    protected static string $resource = YieldForecastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
