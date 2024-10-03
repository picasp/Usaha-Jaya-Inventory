<?php

namespace App\Filament\Reports;

use App\Models\TransaksiKeluar;
use App\Models\TransaksiKeluarItem;
use EightyNine\Reports\Report;
use EightyNine\Reports\Components\Body;
use EightyNine\Reports\Components\Body\TextColumn as BodyTextColumn;
use EightyNine\Reports\Components\Footer;
use EightyNine\Reports\Components\Header;
use EightyNine\Reports\Components\Text;
use EightyNine\Reports\Components\Image;
use EightyNine\Reports\Components\VerticalSpace;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn as ColumnsTextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjualanReport extends Report
{
    public ?string $heading = "Laporan Penjualan";
    static ?string $navigationLabel = 'Laporan Penjualan';
    public ?array $filters = [];

    public function setFilters(array $filters)
    {
        Log::info('Filters set:', $filters);
        $this->filters = $filters;
    }

    public function filterForm(Form $form): Form
    {
        return $form
            ->schema([
                DateRangePicker::make("tgl_penjualan")
                ->label("Rentang Tanggal")
                ->placeholder("Pilih rentang tanggal")
                ->suffixIcon('heroicon-o-calendar')
                ->format('d/m/Y')
                ->required()
                ->reactive()  // Ensures the report reacts to filter changes
                ->afterStateUpdated(function ($state, callable $get) {
                    Log::info('Date filter updated:', [$state]);
                    $this->setFilters(['tgl_penjualan' => $state]);  // Set the filters
                    $this->emitSelf('refreshHeaderAndReport');  // Force the header to refresh after filter update
                }),
            ]);
    }

    protected function getListeners(): array
    {
        return [
            'refreshHeaderAndReport' => '$refresh',  // Add listener for refreshing header
        ];
    }

    public function refreshHeaderAndReport()
    {
        // Refresh the component (header + body)
        $this->refreshReport();
        $this->emit('$refresh');
    }

    public function header(Header $header): Header
    {
        // Retrieve the filters applied (assuming filters come through the request or passed down)
        $filters = $this->filters;
        $dateRangeText = "Periode: Semua waktu"; // Default text if no range is selected

        Log::info('Rendering header with filters:', $filters);

        // Check if filters are available and the date range is set
        if (!empty($filters['tgl_penjualan'])) {
            $dateRange = $filters['tgl_penjualan'];
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]));
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]));
                $dateRangeText = "Periode: {$startDate->format('d M Y')} - {$endDate->format('d M Y')}";
                Log::info('Date range in header:', [$startDate, $endDate]);
            }
        }

        return $header
            ->schema([
                Header\Layout\HeaderRow::make()
                ->schema([
                    Header\Layout\HeaderColumn::make()
                    ->alignCenter()
                        ->schema([
                            Text::make("Laporan Penjualan Barang UD Usaha Jaya")
                                ->title(),
                            Text::make("Dibuat pada: " . now()->format('Y-m-d H:i:s')),
                            Text::make($dateRangeText)
                        ]),
                ]),
            ]);
    }

    public function body(Body $body): Body
    {
        return $body
            ->schema([
                Body\Layout\BodyColumn::make()
                ->schema([
                    Body\Table::make()
                        ->data(
                            fn(?array $filters) => $this->salesSummary($filters)
                        )
                        ->columns([
                            BodyTextColumn::make('Nama Barang')
                                ->label('Nama Barang'),
                            BodyTextColumn::make('Total Terjual')
                                ->label('Total Terjual'),
                            BodyTextColumn::make('Satuan')
                                ->label('Satuan'),
                            BodyTextColumn::make('Total Pendapatan')
                                ->label('Total Pendapatan')
                                ->prefix('Rp. '),
                        ]),
                ]),
            ]);
    }



    public function salesSummary(?array $filters)
    {
        Log::info('Executing salesSummary with filters:', $filters);
    
        // Set the filters in the report instance
        $this->setFilters($filters);
    
        $query = TransaksiKeluarItem::query()
            ->join('barangs', 'transaksi_keluar_items.barang_id', '=', 'barangs.id')
            ->join('transaksi_keluars', 'transaksi_keluar_items.transaksi_keluar_id', '=', 'transaksi_keluars.id')
            ->select(
                'barangs.nama_barang', 'barangs.satuan',
                DB::raw('SUM(transaksi_keluar_items.qty) as total_quantity'),
                DB::raw('SUM(transaksi_keluar_items.total) as total_revenue')
            );

        if (!empty($filters['tgl_penjualan'])) {
            $dateRange = $filters['tgl_penjualan'];
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]));

                if ($startDate && $endDate) {
                    $query->whereBetween('transaksi_keluars.tgl_penjualan', [$startDate->startOfDay(), $endDate->endOfDay()]);
                }
            }
        }

        $query->groupBy('barangs.nama_barang', 'barangs.satuan');
    
        $results = $query->get()->map(function ($item) {
            return [
                'Nama Barang' => $item->nama_barang,
                'Satuan' => $item->satuan,
                'Total Terjual' => $item->total_quantity,
                'Total Pendapatan' => number_format($item->total_revenue, 0, ',', '.'),
            ];
        });
        Log::info('Query results:', $results->toArray());

        $totalRevenue = $results->sum(function ($item) {
            return str_replace('.', '', $item['Total Pendapatan']);
        });

        $results->push([
            'Nama Barang' => 'TOTAL',
            'Satuan' => '',
            'Total Terjual' => $results->sum('Total Terjual'),
            'Total Pendapatan' => number_format($totalRevenue, 0, ',', '.'),
        ]);

        return $results;
    }

    protected function refreshReport()
    {
        Log::info('Report is being refreshed');
        $this->emit('refresh');
    }
}

