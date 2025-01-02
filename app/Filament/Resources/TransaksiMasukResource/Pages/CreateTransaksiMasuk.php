<?php

namespace App\Filament\Resources\TransaksiMasukResource\Pages;

use App\Filament\Resources\TransaksiMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateTransaksiMasuk extends CreateRecord
{
    protected static string $resource = TransaksiMasukResource::class;
    protected static ?string $title = 'Pembelian Baru';
    protected static ?string $breadcrumb = "Tambah";
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pembelian berhasil ditambahkan';
    }
    protected function getCreateAnotherFormAction(): Action
    {
         return parent::getCreateAnotherFormAction()
             ->label('Buat & Tambahkan Lagi');
    }
    protected function getCreateFormAction(): Action
    {
         return parent::getCreateFormAction()
             ->label('Buat');
    }
    protected function getCancelFormAction(): Action
    {
         return parent::getCancelFormAction()
             ->label('Batal');
    }
}
