<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiKeluarItem;

class LaporanJual extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Penjualan';

    protected static string $view = 'filament.pages.laporan-jual';
    public $data;
    public $totalSum;

    public function mount()
    {
        $this->data = $this->sellSummary();
        $this->totalSum = $this->calculateTotalSum();
    }

    public function sellSummary()
    {
        return TransaksiKeluarItem::query()
        ->join('barangs', 'transaksi_keluar_items.barang_id', '=', 'barangs.id')
        ->join('transaksi_keluars', 'transaksi_keluar_items.transaksi_keluar_id', '=', 'transaksi_keluars.id')
        ->select(
            'transaksi_keluars.tgl_penjualan as Tanggal',
            'transaksi_keluars.nama_pembeli as Nama Pembeli',
            'transaksi_keluars.jenis_pembayaran as Transaksi',
            'barangs.nama_barang as Nama Barang',
            'barangs.satuan as Satuan',
            'transaksi_keluar_items.qty as Stok',
            'transaksi_keluar_items.harga as Harga',
            DB::raw('SUM(transaksi_keluar_items.total) as `Total Pendapatan`')
        )
            ->orderBy('transaksi_keluars.tgl_penjualan', 'desc')
            ->groupBy('barangs.nama_barang', 'barangs.satuan', 'transaksi_keluars.tgl_penjualan', 'transaksi_keluar_items.qty','transaksi_keluars.nama_pembeli', 'transaksi_keluars.jenis_pembayaran', 'transaksi_keluar_items.harga')
            ->get()
            ->toArray();
    }
    public function calculateTotalSum()
    {
        return TransaksiKeluarItem::join('transaksi_keluars', 'transaksi_keluar_items.transaksi_keluar_id', '=', 'transaksi_keluars.id')
            ->sum('transaksi_keluar_items.total');
    }
}
