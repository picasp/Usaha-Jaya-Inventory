<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateBarang extends CreateRecord
{
    protected static string $resource = BarangResource::class;
    protected static ?string $title = 'Barang Baru';
    protected static ?string $breadcrumb = "Tambah";
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Barang berhasil ditambahkan';
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
