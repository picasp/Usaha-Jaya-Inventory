<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarang extends EditRecord
{
    protected static string $resource = BarangResource::class;
    protected static ?string $title = 'Edit Barang';
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
        return 'Barang berhasil diubah';
    }
}
