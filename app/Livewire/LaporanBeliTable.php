<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TransaksiMasukItem;
use Illuminate\Support\Facades\DB;

class LaporanBeliTable extends Component
{
    use WithPagination;

    public $totalSum;

    public function mount()
    {
        $this->calculateTotalSum();
    }

    public function calculateTotalSum()
    {
        $this->totalSum = TransaksiMasukItem::join('transaksi_masuks', 'transaksi_masuk_items.transaksi_masuk_id', '=', 'transaksi_masuks.id')
            ->sum('transaksi_masuk_items.total');
    }

    public function render()
    {
        $data = TransaksiMasukItem::query()
            ->join('transaksi_masuks', 'transaksi_masuk_items.transaksi_masuk_id', '=', 'transaksi_masuks.id')
            ->join('barangs', 'transaksi_masuk_items.barang_id', '=', 'barangs.id')
            ->join('suppliers', 'transaksi_masuks.supplier_id', '=', 'suppliers.id')
            ->select(
                'transaksi_masuks.tgl_pembelian as Tanggal',
                'barangs.nama_barang as Nama Barang',
                'suppliers.nama_supplier as Supplier',
                'barangs.satuan as Satuan',
                'transaksi_masuk_items.qty as Stok',
                DB::raw('SUM(transaksi_masuks.total) as `Total Pengeluaran`')
            )
            ->orderBy('transaksi_masuks.tgl_pembelian', 'desc')
            ->groupBy('barangs.nama_barang', 'barangs.satuan', 'transaksi_masuks.tgl_pembelian', 'suppliers.nama_supplier', 'transaksi_masuks.qty')
            ->paginate(10);

        return view('livewire.laporan-beli-table', [
            'data' => $data,
        ]);
    }
}
