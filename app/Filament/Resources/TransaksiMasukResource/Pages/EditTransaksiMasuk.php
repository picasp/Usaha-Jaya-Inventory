<?php

namespace App\Filament\Resources\TransaksiMasukResource\Pages;

use App\Filament\Resources\TransaksiMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

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
    protected function getSaveFormAction(): Action
    {
         return parent::getSaveFormAction()
             ->label('Simpan');
    }
    protected function getCancelFormAction(): Action
    {
         return parent::getCancelFormAction()
             ->label('Batal');
    }
}
