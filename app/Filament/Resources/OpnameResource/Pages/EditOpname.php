<?php

namespace App\Filament\Resources\OpnameResource\Pages;

use App\Filament\Resources\OpnameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditOpname extends EditRecord
{
    protected static string $resource = OpnameResource::class;
    protected static ?string $title = 'Edit Stok Opname';
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
        return 'Stok Opname berhasil diubah';
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
