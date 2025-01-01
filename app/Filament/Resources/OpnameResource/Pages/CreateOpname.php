<?php

namespace App\Filament\Resources\OpnameResource\Pages;

use App\Filament\Resources\OpnameResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOpname extends CreateRecord
{
    protected static string $resource = OpnameResource::class;
    protected static ?string $title = 'Stok Opname Baru';
    protected static ?string $breadcrumb = "Tambah";
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Stok Opname berhasil ditambahkan';
    }
}
