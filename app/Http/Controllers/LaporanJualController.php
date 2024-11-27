<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiKeluarItem;
use Illuminate\Support\Facades\DB;

class LaporanJualController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaksiKeluarItem::query()
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
            );

    if ($request->start_date && $request->end_date) {
        $query->whereBetween('transaksi_masuks.tgl_pembelian', [
            $request->start_date,
            $request->end_date
        ]);
    }

    $query->groupBy('transaksi_masuks.tgl_pembelian', 'barangs.nama_barang', 'suppliers.nama_supplier', 'barangs.satuan', 'transaksi_masuk_items.qty');
    $data = $query->get();

    $totalSum = $data->sum('Total Pengeluaran');
    if (!$request->start_date && !$request->end_date) {
        $totalSum = TransaksiKeluarItem::join('transaksi_masuks', 'transaksi_masuk_items.transaksi_masuk_id', '=', 'transaksi_masuks.id')
            ->sum('Total Pengeluaran');
    }

    if ($request->ajax()) {
        return response()->json(['data' => $data, 'totalSum' => $totalSum]);
    }

    return view('filament.pages.laporan-beli', [
        'data' => $data,
        'dateRange' => $request->start_date && $request->end_date 
                        ? "{$request->start_date} - {$request->end_date}" 
                        : null,
        'totalSum' => $totalSum ?? 0,
    ]);
    }
}