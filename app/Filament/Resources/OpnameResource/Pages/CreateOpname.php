<?php

namespace App\Filament\Resources\OpnameResource\Pages;

use App\Filament\Resources\OpnameResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOpname extends CreateRecord
{
    protected static string $resource = OpnameResource::class;
    protected static ?string $title = 'Stok Opname Baru';
}
