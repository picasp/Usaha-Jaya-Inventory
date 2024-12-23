<?php

namespace App\Filament\Resources\TransaksiKeluarResource\Pages;

use App\Filament\Resources\TransaksiKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiKeluar extends EditRecord
{
    protected static string $resource = TransaksiKeluarResource::class;
    protected static ?string $title = 'Edit Penjualan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->label('Hapus'),
        ];
    }
}
