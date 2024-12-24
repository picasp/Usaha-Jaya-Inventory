<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiMasukItem;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;

class LaporanBeli extends Page implements HasTable
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Pembelian';
    protected static ?string $model = TransaksiMasukItem::class;
    use interactsWithTable;

    protected static string $view = 'filament.pages.laporan-beli';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                TransaksiMasukItem::query()
                ->join('transaksi_masuks', 'transaksi_masuk_items.transaksi_masuk_id', '=', 'transaksi_masuks.id')
                ->join('suppliers', 'transaksi_masuks.supplier_id', '=', 'suppliers.id') // Tambahkan join ke suppliers
                ->select(
                    'transaksi_masuks.tgl_pembelian as Tanggal', 
                    'suppliers.nama_supplier as Supplier', // Alias untuk nama supplier
                    'transaksi_masuk_items.id as item_id',
                    'transaksi_masuk_items.*'
                )
            )
            ->columns([
                TextColumn::make('Tanggal')
                ->sortable()
                ->dateTime('d/m/Y'),
                TextColumn::make('barang.kode_barang')->sortable()->label('Kode Barang'),
                TextColumn::make('barang.nama_barang')->sortable()->label('Nama Barang'),
                TextColumn::make('Supplier')
                    ->label('Pemasok')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->Supplier),
                TextColumn::make('qty')->sortable()->label('Stok'),
                TextColumn::make('barang.satuan')->sortable()->label('Satuan'),
                TextColumn::make('total')->sortable()->money('IDR', locale: 'id'),
                TextColumn::make('total')
                ->money('IDR', locale: 'id')
                ->summarize(Sum::make()->money('IDR', locale: 'id')),
            ])
            ->defaultSort('Tanggal', 'desc')
            ->headerActions([
                Action::make('exportPdf') 
                ->label('Cetak')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('laporan-beli.export-pdf', [
                    'start_date' => $this->getTableFilterState('Tanggal')['start'] ?? null,
                    'end_date' => $this->getTableFilterState('Tanggal')['end'] ?? null,
                    'periode' => $this->getTableFilterState('Periode Tanggal')['periode'] ?? null,
                ]))
                ->openUrlInNewTab(),
            ])
            ->filters([
                Filter::make('Periode Tanggal')
                ->form([
                    Select::make('periode')
                        ->label('Pilih Periode Tanggal')
                        ->placeholder('Pilih Periode Tanggal')
                        ->options([
                            '30_days' => '30 Hari Terakhir',
                            'this_week' => 'Minggu Ini',
                            'last_week' => 'Minggu Lalu',
                            'last_month' => 'Bulan Lalu',
                            'this_month' => 'Bulan Ini',
                            'this_year' => 'Tahun Ini',
                            'last_year' => 'Tahun Lalu',
                        ])
                ])
                ->query(function (Builder $query, $data): Builder {
                    $today = Carbon::today();
                    $value = $data['periode'] ?? null; // Mengambil nilai periode dari input

                    if (!$value) {
                        return $query; // Kembalikan query tanpa filter jika tidak ada nilai periode
                    }

                    switch ($value) {
                        case '30_days':
                            $startDate = $today->copy()->subDays(30);
                            $endDate = $today->endOfDay();
                            break;

                        case 'this_week':
                            $startDate = $today->copy()->startOfWeek();
                            $endDate = $today->copy()->endOfWeek();
                            break;

                        case 'last_week':
                            $startDate = $today->copy()->subWeek()->startOfWeek();
                            $endDate = $today->copy()->subWeek()->endOfWeek();
                            break;
                        
                        case 'last_month':
                            $startDate = $today->copy()->subMonth()->startOfMonth();
                            $endDate = $today->copy()->subMonth()->endOfMonth();
                            break;

                        case 'this_month':
                            $startDate = $today->copy()->startOfMonth();
                            $endDate = $today->copy()->endOfMonth();
                            break;

                        case 'this_year':
                            $startDate = $today->copy()->startOfYear();
                            $endDate = $today->copy()->endOfYear();
                            break;

                        case 'last_year':
                            $startDate = $today->copy()->subYear()->startOfYear();
                            $endDate = $today->copy()->subYear()->endOfYear();
                            break;

                        default:
                            return $query; // Kembalikan query tanpa filter jika nilai tidak dikenal
                    }

                    return $query->whereBetween('transaksi_masuks.tgl_pembelian', [$startDate, $endDate]);
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    $value = $data['periode'] ?? null;

                    if ($value) {
                        $today = Carbon::today();
                        switch ($value) {
                            case '30_days':
                                $startDate = $today->copy()->subDays(30)->format('d/m/Y');
                                $endDate = $today->endOfDay()->format('d/m/Y');
                                $indicators[] = "Dari {$startDate} hingga {$endDate}";
                                break;

                            case 'last_month':
                                $startDate = $today->copy()->subMonth()->startOfMonth()->format('d/m/Y');
                                $endDate = $today->copy()->subMonth()->endOfMonth()->format('d/m/Y');
                                $indicators[] = "Bulan Lalu: {$startDate} hingga {$endDate}";
                                break;

                            case 'this_month':
                                $startDate = $today->copy()->startOfMonth()->format('d/m/Y');
                                $endDate = $today->copy()->endOfMonth()->format('d/m/Y');
                                $indicators[] = "Bulan Ini: {$startDate} hingga {$endDate}";
                                break;

                            case 'this_week':
                                $startDate = $today->copy()->startOfWeek()->format('d/m/Y');
                                $endDate = $today->copy()->endOfWeek()->format('d/m/Y');
                                $indicators[] = "Minggu Ini: {$startDate} hingga {$endDate}";
                                break;

                            case 'last_week':
                                $startDate = $today->copy()->subWeek()->startOfWeek()->format('d/m/Y');
                                $endDate = $today->copy()->subWeek()->endOfWeek()->format('d/m/Y');
                                $indicators[] = "Minggu Lalu: {$startDate} hingga {$endDate}";
                                break;

                            case 'this_year':
                                $startDate = $today->copy()->startOfYear()->format('d/m/Y');
                                $endDate = $today->copy()->endOfYear()->format('d/m/Y');
                                $indicators[] = "Tahun Ini: {$startDate} hingga {$endDate}";
                                break;

                            case 'last_year':
                                $startDate = $today->copy()->subYear()->startOfYear()->format('d/m/Y');
                                $endDate = $today->copy()->subYear()->endOfYear()->format('d/m/Y');
                                $indicators[] = "Tahun Lalu: {$startDate} hingga {$endDate}";
                                break;

                            default:
                                $indicators[] = 'Periode Tidak Dikenal';
                                break;
                        }
                    }

                    return $indicators;
                }),

                Filter::make('Tanggal')
                    ->label('Tanggal pembelian')
                    ->form([
                        DatePicker::make('start')
                            ->label('Dari Tanggal')
                            ->placeholder('YYYY-MM-DD'),
                        DatePicker::make('end')
                            ->label('Sampai Tanggal')
                            ->placeholder('YYYY-MM-DD'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['start']) && isset($data['end'])) {
                            $startDate = Carbon::parse($data['start'])->startOfDay();
                            $endDate = Carbon::parse($data['end'])->endOfDay();
    
                            return $query->whereBetween('transaksi_masuks.tgl_pembelian', [$startDate, $endDate]);
                        }
    
                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
    
                        if (isset($data['start'])) {
                            $indicators['start'] = 'Mulai dari: ' . Carbon::parse($data['start'])->format('d/m/Y');
                        }
    
                        if (isset($data['end'])) {
                            $indicators['end'] = 'Hingga: ' . Carbon::parse($data['end'])->format('d/m/Y');
                        }
    
                        return $indicators;
                    }),
            ]);
    }

    public function exportPdf(Request $request)
    {
        $filterTanggal = $this->getTableFilterState('Tanggal') ?? [];
        $startDate = $request->input('start_date') ?? ($filterTanggal['start'] ?? null);
        $endDate = $request->input('end_date') ?? ($filterTanggal['end'] ?? null);
    
        $filterPeriode = $this->getTableFilterState('Periode Tanggal') ?? [];
        $periode = $request->input('periode') ?? ($filterPeriode['periode'] ?? null);

        $query = TransaksimasukItem::query()
        ->join('transaksi_masuks', 'transaksi_masuk_items.transaksi_masuk_id', '=', 'transaksi_masuks.id')
        ->join('barangs', 'transaksi_masuk_items.barang_id', '=', 'barangs.id')
        ->join('suppliers', 'transaksi_masuks.supplier_id', '=', 'suppliers.id')
        ->select(
            'transaksi_masuks.tgl_pembelian as Tanggal',
            'barangs.kode_barang as Kode Barang',
            'barangs.nama_barang as Nama Barang',
            'suppliers.nama_supplier as Supplier',
            'barangs.satuan as Satuan',
            'transaksi_masuk_items.qty as Stok',
            DB::raw('SUM(transaksi_masuk_items.total) as `Total Pengeluaran`')
        )
        ->orderBy('transaksi_masuks.tgl_pembelian', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('transaksi_masuks.tgl_pembelian', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }
    
        $periode = $request->input('periode');
        $today = Carbon::today();
    
        if ($periode) {
            switch ($periode) {
                case '30_days':
                    $startDate = $today->copy()->subDays(30);
                    $endDate = $today->endOfDay();
                    break;
    
                case 'this_week':
                    $startDate = $today->copy()->startOfWeek();
                    $endDate = $today->copy()->endOfWeek();
                    break;
    
                case 'last_week':
                    $startDate = $today->copy()->subWeek()->startOfWeek();
                    $endDate = $today->copy()->subWeek()->endOfWeek();
                    break;
    
                case 'last_month':
                    $startDate = $today->copy()->subMonth()->startOfMonth();
                    $endDate = $today->copy()->subMonth()->endOfMonth();
                    break;
    
                case 'this_month':
                    $startDate = $today->copy()->startOfMonth();
                    $endDate = $today->copy()->endOfMonth();
                    break;
    
                case 'this_year':
                    $startDate = $today->copy()->startOfYear();
                    $endDate = $today->copy()->endOfYear();
                    break;
    
                case 'last_year':
                    $startDate = $today->copy()->subYear()->startOfYear();
                    $endDate = $today->copy()->subYear()->endOfYear();
                    break;
    
                default:
                    $startDate = $today->copy()->subDays(30);  // Default to 30 days ago if no valid period is selected
                    $endDate = $today->endOfDay();
            }
    
            // Apply the periode filter
            $query->whereBetween('transaksi_masuks.tgl_pembelian', [$startDate, $endDate]);
        }
    
        $query->groupBy(
            'barangs.kode_barang',
            'barangs.nama_barang',
            'barangs.satuan',
            'transaksi_masuks.tgl_pembelian',
            'suppliers.nama_supplier',
            'transaksi_masuk_items.qty'
        );        
        $data = $query->get();
        $totalSum = $data->sum('Total Pengeluaran');
        $stampPath = public_path('img/stempel.png');
        $ttdPath = public_path('img/ttd.png');
        $stampBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($stampPath));
        $ttdBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($ttdPath));
        $pdfData = [
            'data' => $data,
            'stampBase64' => $stampBase64,
            'ttdBase64' => $ttdBase64,
            'dateRange' => $startDate && $endDate
                ? Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y')
                : 'Semua Tanggal',
            'totalSum' => $totalSum,
        ];
    
        // Load view untuk PDF
        $pdf = PDF::loadView('laporan-beli-pdf', $pdfData)
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'debugPng' => false,
        ]);
    
        // Return PDF sebagai file download atau tampilkan langsung
        return $pdf->stream('laporan-beli.pdf');
    }
}
