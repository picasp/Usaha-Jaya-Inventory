<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiKeluarItem;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanJualController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaksiKeluarItem::query()
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
        ->orderBy('transaksi_keluars.tgl_penjualan', 'desc');

    if ($request->start_date && $request->end_date) {
        $query->whereBetween('transaksi_keluars.tgl_penjualan', [
            $request->start_date,
            $request->end_date
        ]);
    }

    $query->groupBy(
        'transaksi_keluars.tgl_penjualan',
        'transaksi_keluars.nama_pembeli',
        'transaksi_keluars.jenis_pembayaran',
        'barangs.nama_barang',
        'barangs.satuan',
        'transaksi_keluar_items.qty',
        'transaksi_keluar_items.harga'
    );
    $data = $query->get();

    $totalSum = $data->sum('Total Pendapatan');
    if (!$request->start_date && !$request->end_date) {
        $totalSum = TransaksiKeluarItem::join('transaksi_keluars', 'transaksi_keluar_items.transaksi_keluar_id', '=', 'transaksi_keluars.id')
            ->sum('transaksi_keluar_items.total');
    }

    if ($request->ajax()) {
        return response()->json(['data' => $data, 'totalSum' => $totalSum]);
    }

    return view('filament.pages.laporan-jual', [
        'data' => $data,
        'totalSum' => $totalSum,
        'dateRange' => $request->start_date && $request->end_date 
                        ? "{$request->start_date} - {$request->end_date}" 
                        : null,
    ]);
    }

    public function exportPdf(Request $request)
    {
        $query = TransaksiKeluarItem::query()
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
        ->orderBy('transaksi_keluars.tgl_penjualan', 'desc');
    
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('transaksi_keluars.tgl_penjualan', [
                $request->start_date,
                $request->end_date
            ]);
        }
    
        $query->groupBy(
            'transaksi_keluars.tgl_penjualan',
            'transaksi_keluars.nama_pembeli',
            'transaksi_keluars.jenis_pembayaran',
            'barangs.nama_barang',
            'barangs.satuan',
            'transaksi_keluar_items.qty',
            'transaksi_keluar_items.harga'
        );        
        $data = $query->get();
        $totalSum = $data->sum('Total Pendapatan');
        $pdfData = [
            'data' => $data,
            'dateRange' => $request->start_date && $request->end_date 
                            ? "{$request->start_date} - {$request->end_date}" 
                            : 'Semua Tanggal',
            'totalSum' => $totalSum,
        ];
    
        // Load view untuk PDF
        $pdf = PDF::loadView('laporan-jual-pdf', $pdfData);
    
        // Return PDF sebagai file download atau tampilkan langsung
        return $pdf->stream('laporan-jual.pdf');
    }
}
