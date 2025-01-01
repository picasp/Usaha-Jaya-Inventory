<?php

namespace App\Filament\Resources\TransaksiMasukResource\Pages;

use App\Filament\Resources\TransaksiMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiMasuk extends EditRecord
{
    protected static string $resource = TransaksiMasukResource::class;
    protected static ?string $title = 'Edit Pembelian';
    protected static ?string $breadcrumb = "Edit";
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->label('Hapus'),
        ];
    }
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pembelian berhasil diubah';
    }
}
