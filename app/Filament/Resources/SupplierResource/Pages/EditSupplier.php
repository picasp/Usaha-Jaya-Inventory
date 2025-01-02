<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditSupplier extends EditRecord
{
    protected static string $resource = SupplierResource::class;
    protected static ?string $title = 'Edit Pemasok';
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
        return 'Pemasok berhasil diubah';
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
