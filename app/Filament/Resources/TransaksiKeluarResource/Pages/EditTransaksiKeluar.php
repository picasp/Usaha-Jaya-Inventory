<?php

namespace App\Filament\Resources\TransaksiKeluarResource\Pages;

use App\Filament\Resources\TransaksiKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiKeluar extends EditRecord
{
    protected static string $resource = TransaksiKeluarResource::class;
    protected static ?string $title = 'Edit Penjualan';
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
        return 'Penjualan berhasil diubah';
    }
}
