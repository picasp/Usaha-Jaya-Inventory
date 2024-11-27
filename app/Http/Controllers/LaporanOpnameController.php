<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OpnameItem;
use Illuminate\Support\Facades\DB;

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
            );

            if ($request->start_date && $request->end_date) {
                $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d');
                $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d');
            
                $query->whereBetween('opnames.tgl', [$startDate, $endDate]);
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
}
