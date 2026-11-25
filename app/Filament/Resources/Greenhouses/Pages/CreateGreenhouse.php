<?php

namespace App\Filament\Resources\Greenhouses\Pages;

use App\Filament\Resources\Greenhouses\GreenhouseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGreenhouse extends CreateRecord
{
    protected static string $resource = GreenhouseResource::class;
}
