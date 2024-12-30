<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use App\Models\OpnameItem;
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

class LaporanOpname extends Page implements HasTable
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Stok Opname';
    protected ?string $heading = '';
    protected static ?string $model = OpnameItem::class;
    use interactsWithTable;

    protected static string $view = 'filament.pages.laporan-opname';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OpnameItem::query()
                ->join('opnames', 'opname_items.opname_id', '=', 'opnames.id')
                ->join('barangs', 'opname_items.barang_id', '=', 'barangs.id')
                ->select(
                    'opnames.created_at as Tanggal', // Alias for created_at
                    'barangs.nama_barang as NamaBarang', // Alias for nama_barang
                    'barangs.kode_barang as KodeBarang', // Alias for kode_barang
                    'opname_items.id as item_id', // Alias for opname_items id
                    'opname_items.*' // Select all columns from opname_items
                )
            )
            ->columns([
                TextColumn::make('Tanggal')
                ->sortable()
                ->dateTime('d/m/Y'),
                TextColumn::make('KodeBarang')
                    ->sortable()
                    ->label('Kode Barang')
                    ->getStateUsing(fn ($record) => $record->KodeBarang),
                TextColumn::make('NamaBarang')
                    ->sortable()
                    ->label('Nama Barang')
                    ->getStateUsing(fn ($record) => $record->NamaBarang),
                TextColumn::make('qty_sistem')->sortable()->label('Stok Sistem'),
                TextColumn::make('qty_fisik')->sortable()->label('Stok Fisik'),
                TextColumn::make('selisih')->sortable()->label('Selisih'),
                TextColumn::make('keterangan')->sortable()->label('Keterangan'),
            ])
            ->defaultSort('Tanggal', 'desc')
            ->headerActions([
                Action::make('exportPdf') 
                ->label('Cetak')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('laporan-opname.export-pdf', [
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

                    return $query->whereBetween('opnames.created_at', [$startDate, $endDate]);
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
                    ->label('Tanggal Opname')
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
    
                            return $query->whereBetween('opnames.created_at', [$startDate, $endDate]);
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
        set_time_limit(300);
        $filterTanggal = $this->getTableFilterState('Tanggal') ?? [];
        $startDate = $request->input('start_date') ?? ($filterTanggal['start'] ?? null);
        $endDate = $request->input('end_date') ?? ($filterTanggal['end'] ?? null);
    
        $filterPeriode = $this->getTableFilterState('Periode Tanggal') ?? [];
        $periode = $request->input('periode') ?? ($filterPeriode['periode'] ?? null);

        $query = OpnameItem::query()
        ->join('barangs', 'opname_items.barang_id', '=', 'barangs.id')
        ->join('opnames', 'opname_items.opname_id', '=', 'opnames.id')
        ->select(
            'barangs.kode_barang as Kode Barang',
            'barangs.nama_barang as Nama Barang',
            'opnames.created_at as Tanggal',
            'opname_items.qty_sistem as Stok Sistem',
            'opname_items.qty_fisik as Stok Fisik',
            'opname_items.selisih as Selisih',
            'opname_items.keterangan as Keterangan'
        )
        ->orderBy('opnames.created_at', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('opnames.created_at', [
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
            $query->whereBetween('opnames.created_at', [$startDate, $endDate]);
        }
    
        $query->groupBy('barangs.kode_barang', 'barangs.nama_barang', 'opnames.created_at', 'opname_items.qty_sistem', 'opname_items.qty_fisik', 'opname_items.selisih', 'opname_items.keterangan');     
        $data = $query->get();
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
        ];
    
        // Load view untuk PDF
        $pdf = PDF::loadView('laporan-opname-pdf', $pdfData)
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'debugPng' => false,
        ]);
    
        // Return PDF sebagai file download atau tampilkan langsung
        return $pdf->stream('laporan-opname.pdf');
    }
}
