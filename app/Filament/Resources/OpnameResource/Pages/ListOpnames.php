<?php

namespace App\Filament\Resources\OpnameResource\Pages;

use App\Filament\Resources\OpnameResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOpnames extends ListRecords
{
    protected static string $resource = OpnameResource::class;
    protected static ?string $title = 'Daftar Stok Opname';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Stok Opname'),
        ];
    }
}
