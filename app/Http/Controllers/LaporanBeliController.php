<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiMasukItem;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanBeliController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaksiMasukItem::query()
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
            ->orderBy('transaksi_masuks.tgl_pembelian', 'desc');

        // Filter berdasarkan rentang tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('transaksi_masuks.tgl_pembelian', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // Kelompokkan data
        $query->groupBy(
            'transaksi_masuks.tgl_pembelian', 
            'barangs.nama_barang', 
            'suppliers.nama_supplier', 
            'barangs.satuan', 
            'transaksi_masuk_items.qty'
        );

        // Ambil data terpaginasi
        $paginatedData = $query->paginate(10);

        // Simpan hanya data dan informasi pagination yang diperlukan
        $data = $paginatedData->items(); // Hanya ambil item dari paginator
        $pagination = [
            'from' => $paginatedData->firstItem(),
            'to' => $paginatedData->lastItem(),
            'current_page' => $paginatedData->currentPage(),
            'last_page' => $paginatedData->lastPage(),
            'per_page' => $paginatedData->perPage(),
            'total' => $paginatedData->total(),
            'links' => $paginatedData->links('pagination::tailwind')->render(),
        ];

        // Hitung total sum
        $totalSum = collect($data)->sum('Total Pengeluaran');
        if (!$request->start_date && !$request->end_date) {
            $totalSum = TransaksiMasukItem::join('transaksi_masuks', 'transaksi_masuk_items.transaksi_masuk_id', '=', 'transaksi_masuks.id')
                ->sum('transaksi_masuk_items.total');
        }

        // Respon Ajax
        if ($request->ajax()) {
            return response()->json([
                'data' => $data,
                'pagination' => $pagination,
                'totalSum' => $totalSum
            ]);
        }

        // Respon ke View
        return view('filament.pages.laporan-beli', [
            'data' => $data,
            'pagination' => $pagination,
            'dateRange' => $request->start_date && $request->end_date ? "{$request->start_date} - {$request->end_date}" : null,
            'totalSum' => $totalSum,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $query = TransaksiMasukItem::query()
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
            ->orderBy('transaksi_masuks.tgl_pembelian', 'desc');
    
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('transaksi_masuks.tgl_pembelian', [
                $request->start_date,
                $request->end_date
            ]);
        }
    
        $query->groupBy('transaksi_masuks.tgl_pembelian', 'barangs.nama_barang', 'suppliers.nama_supplier', 'barangs.satuan', 'transaksi_masuk_items.qty');
        $data = $query->get();
    
        $totalSum = $data->sum('Total Pengeluaran');
    
        $pdfData = [
            'data' => $data,
            'dateRange' => $request->start_date && $request->end_date 
                            ? "{$request->start_date} - {$request->end_date}" 
                            : 'Semua Tanggal',
            'totalSum' => $totalSum,
        ];
    
        // Load view untuk PDF
        $pdf = PDF::loadView('laporan-beli-pdf', $pdfData);
    
        // Return PDF sebagai file download atau tampilkan langsung
        return $pdf->stream('laporan-beli.pdf');
    }
    
}
