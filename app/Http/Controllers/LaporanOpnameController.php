<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OpnameItem;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanOpnameController extends Controller
{
    public function index(Request $request)
    {
        $query = OpnameItem::query()
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
            ->orderBy('opnames.tgl', 'desc');

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('opnames.tgl', [
                    $request->start_date,
                    $request->end_date
                ]);
            }
            

    $query->groupBy('barangs.nama_barang', 'opnames.tgl', 'opname_items.qty_sistem', 'opname_items.qty_fisik', 'opname_items.selisih', 'opname_items.keterangan');
    $data = $query->get();

    if ($request->ajax()) {
        return response()->json(['data' => $data]);
    }

    return view('filament.pages.laporan-opname', [
        'data' => $data,
        'dateRange' => $request->start_date && $request->end_date 
                        ? "{$request->start_date} - {$request->end_date}" 
                        : null,
    ]);
    }

    public function exportPdf(Request $request)
    {
        $query = OpnameItem::query()
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
            ->orderBy('opnames.tgl', 'desc');
    
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('opnames.tgl', [
                $request->start_date,
                $request->end_date
            ]);
        }
    
        $query->groupBy('barangs.nama_barang', 'opnames.tgl', 'opname_items.qty_sistem', 'opname_items.qty_fisik', 'opname_items.selisih', 'opname_items.keterangan');        
        $data = $query->get();
        $pdfData = [
            'data' => $data,
            'dateRange' => $request->start_date && $request->end_date 
                            ? "{$request->start_date} - {$request->end_date}" 
                            : 'Semua Tanggal',
        ];
    
        // Load view untuk PDF
        $pdf = PDF::loadView('laporan-opname-pdf', $pdfData);
    
        // Return PDF sebagai file download atau tampilkan langsung
        return $pdf->stream('laporan-opname.pdf');
    }
}
