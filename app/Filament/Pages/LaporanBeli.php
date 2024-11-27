<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiMasukItem;

class LaporanBeli extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Pembelian';

    protected static string $view = 'filament.pages.laporan-beli';
    public $data;

    public function mount()
    {
        $this->data = $this->purchaseSummary();
    }

    public function purchaseSummary()
    {
        return TransaksiMasukItem::query()
        ->join('transaksi_masuks', 'transaksi_masuk_items.transaksi_masuk_id', '=', 'transaksi_masuks.id')
        ->join('barangs', 'transaksi_masuk_items.barang_id', '=', 'barangs.id')
        ->join('suppliers', 'transaksi_masuks.supplier_id', '=', 'suppliers.id')
        ->select(
            'transaksi_masuks.tgl_pembelian as Tanggal',
            'barangs.nama_barang as Nama Barang',
            'suppliers.nama_supplier as Supplier',
            'barangs.satuan as Satuan',
            'transaksi_masuk_items.qty as Stok',
            DB::raw('SUM(transaksi_masuk_items.total) as `Total Pengeluaran`')
        )
            ->groupBy('barangs.nama_barang', 'barangs.satuan', 'transaksi_masuks.tgl_pembelian', 'suppliers.nama_supplier', 'transaksi_masuk_items.qty')
            ->get()
            ->toArray();
    }
}
