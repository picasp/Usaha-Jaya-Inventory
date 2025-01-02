<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;
    protected static ?string $title = 'Pemasok Baru';
    protected static ?string $breadcrumb = "Tambah";
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pemasok berhasil ditambahkan';
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
