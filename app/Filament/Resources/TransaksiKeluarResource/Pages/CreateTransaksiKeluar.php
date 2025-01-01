<?php

namespace App\Filament\Resources\TransaksiKeluarResource\Pages;

use App\Filament\Resources\TransaksiKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\TransaksiKeluar;
use App\Models\Barang;
use App\Models\TransaksiKeluarItem;
use Illuminate\Support\Facades\DB;

class CreateTransaksiKeluar extends CreateRecord
{
    protected static string $resource = TransaksiKeluarResource::class;
    protected static ?string $title = 'Penjualan Baru';
    protected static ?string $breadcrumb = "Tambah";
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Penjualan berhasil ditambahkan';
    }
}
