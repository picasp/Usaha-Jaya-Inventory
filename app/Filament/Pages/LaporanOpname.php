<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use App\Models\OpnameItem;

class LaporanOpname extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Stok Opname';

    protected static string $view = 'filament.pages.laporan-opname';
    public $data;

    public function mount()
    {
        $this->data = $this->opnameSummary();
    }

    public function opnameSummary()
    {
        return OpnameItem::query()
        ->join('barangs', 'opname_items.barang_id', '=', 'barangs.id')
        ->join('opnames', 'opname_items.opname_id', '=', 'opnames.id')
        ->select(
            'barangs.nama_barang as Nama Barang',
            'opnames.tgl as Tanggal',
            'opname_items.qty_sistem as Stok Sistem',
            'opname_items.qty_fisik as Stok Fisik',
            'opname_items.selisih as Selisih',
            'opname_items.keterangan as Keterangan'
        )
            ->orderBy('opnames.tgl', 'desc')
            ->groupBy('barangs.nama_barang', 'opnames.tgl', 'opname_items.qty_sistem', 'opname_items.qty_fisik', 'opname_items.selisih', 'opname_items.keterangan')
            ->get()
            ->toArray();
    }
}
