<?php

namespace App\Filament\Resources\TransaksiMasukResource\Pages;

use App\Filament\Resources\TransaksiMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

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
}
