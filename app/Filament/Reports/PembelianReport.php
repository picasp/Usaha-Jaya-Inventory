<?php

namespace App\Filament\Reports;

use App\Models\TransaksiMasuk;
use App\Models\TransaksiMasukItem;
use EightyNine\Reports\Report;
use EightyNine\Reports\Components\Body;
use EightyNine\Reports\Components\Body\TextColumn as BodyTextColumn;
use EightyNine\Reports\Components\Footer;
use EightyNine\Reports\Components\Header;
use EightyNine\Reports\Components\Text;
use EightyNine\Reports\Components\Image;
use EightyNine\Reports\Components\VerticalSpace;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn as ColumnsTextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembelianReport extends Report
{
    public ?string $heading = "Laporan Pembelian";
    static ?string $navigationLabel = 'Laporan Pembelian';
    public ?array $filters = [];

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    public function header(Header $header): Header
    {
        // Retrieve the filters applied (assuming filters come through the request or passed down)
        $filters = $this->filters;
        $dateRangeText = "Periode: Semua waktu"; // Default text if no range is selected

        // Check if filters are available and the date range is set
        if (!empty($filters['tgl_pembelian'])) {
            $dateRange = $filters['tgl_pembelian'];
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]));
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]));
                $dateRangeText = "Periode: {$startDate->format('d M Y')} - {$endDate->format('d M Y')}";
            }
        }

        return $header
            ->schema([
                Header\Layout\HeaderRow::make()
                ->schema([
                    Header\Layout\HeaderColumn::make()
                    ->alignCenter()
                        ->schema([
                            Text::make("Laporan Pembelian Barang UD Usaha Jaya")
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
                            fn(?array $filters) => $this->purchaseSummary($filters)
                        )
                        ->columns([
                            BodyTextColumn::make('Nama Barang')
                                ->label('Nama Barang'),
                            BodyTextColumn::make('Total Dibeli')
                                ->label('Total Dibeli'),
                            BodyTextColumn::make('Satuan')
                            ->label('satuan'),
                            BodyTextColumn::make('Total Pengeluaran')
                                ->label('Total Pengeluaran')
                                ->prefix('Rp. '),
                        ]),
                ]),
            ]);
    }

    public function filterForm(Form $form): Form
    {
        return $form
            ->schema([
                DateRangePicker::make("tgl_pembelian")
                ->label("Rentang Tanggal")
                ->placeholder("Pilih rentang tanggal")
                ->suffixIcon('heroicon-o-calendar')
                ->format('d/m/Y')
                ->required()
                ->reactive()  // Ensures the report reacts to filter changes
                ->afterStateUpdated(fn ($state, callable $get) => $this->refreshReport()),  // Forces refresh after state change
            ]);
    }

    public function purchaseSummary(?array $filters)
    {
        Log::info('Filters received in salesSummary:', $filters);
    
        // Set the filters in the report instance
        $this->setFilters($filters);
    
        $query = TransaksiMasukItem::query()
            ->join('barangs', 'transaksi_masuk_items.barang_id', '=', 'barangs.id')
            ->join('transaksi_masuks', 'transaksi_masuk_items.transaksi_masuk_id', '=', 'transaksi_masuks.id')
            ->select(
                'barangs.nama_barang','barangs.satuan',
                DB::raw('SUM(transaksi_masuk_items.qty) as total_quantity'),
                DB::raw('SUM(transaksi_masuk_items.total) as total_expense')
            );

        if (!empty($filters['tgl_pembelian'])) {
            $dateRange = $filters['tgl_pembelian'];
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]));

                if ($startDate && $endDate) {
                    $query->whereBetween('transaksi_masuks.tgl_pembelian', [$startDate->startOfDay(), $endDate->endOfDay()]);
                }
            }
        }

        $query->groupBy('barangs.nama_barang', 'barangs.satuan');
    
        $results = $query->get()->map(function ($item) {
            return [
                'Nama Barang' => $item->nama_barang,
                'Satuan' => $item->satuan,
                'Total Dibeli' => $item->total_quantity,
                'Total Pengeluaran' => number_format($item->total_expense, 0, ',', '.'),
            ];
        });

        $totalExpense = $results->sum(function ($item) {
            return str_replace('.', '', $item['Total Pengeluaran']);
        });

        $results->push([
            'Nama Barang' => 'TOTAL',
            'Satuan' => '',
            'Total Dibeli' => $results->sum('Total Dibeli'),
            'Total Pengeluaran' => number_format($totalExpense, 0, ',', '.'),
        ]);

        return $results;
    }

    protected function refreshReport()
    {
        $this->emit('refresh');
    }
}

