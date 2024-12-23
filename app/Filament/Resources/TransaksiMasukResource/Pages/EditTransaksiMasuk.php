<?php

namespace App\Filament\Resources\TransaksiMasukResource\Pages;

use App\Filament\Resources\TransaksiMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiMasuk extends EditRecord
{
    protected static string $resource = TransaksiMasukResource::class;
    protected static ?string $title = 'Edit Pembelian';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->label('Hapus'),
        ];
    }
}
