<?php

namespace App\Filament\Resources\Greenhouses\Pages;

use App\Filament\Resources\Greenhouses\GreenhouseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGreenhouse extends EditRecord
{
    protected static string $resource = GreenhouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
