<?php

namespace App\Filament\Resources\TransaksiKeluarResource\Pages;

use App\Filament\Resources\TransaksiKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiKeluars extends ListRecords
{
    protected static string $resource = TransaksiKeluarResource::class;
    protected static ?string $title = 'Riwayat Penjualan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Penjualan'),
        ];
    }
}
