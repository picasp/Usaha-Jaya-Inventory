<?php

namespace App\Filament\Resources\TransaksiMasukResource\Pages;

use App\Filament\Resources\TransaksiMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiMasuks extends ListRecords
{
    protected static string $resource = TransaksiMasukResource::class;
    protected static ?string $title = 'Daftar Pembelian';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Pembelian'),
        ];
    }
}
