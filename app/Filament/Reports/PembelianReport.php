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
    protected ?string $dateRange = null;

    public function header(Header $header): Header
    {
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
                            Text::make("Rentang Tanggal: " . ($this->dateRange ?? '-')),
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
                            BodyTextColumn::make('Tanggal')
                            ->label('Tanggal')
                            ->dateTime('d/m/Y'),
                            BodyTextColumn::make('Nama Barang')
                            ->label('Nama Barang')
                            ->extraAttributes(['style' => 'width: 150px;']),
                            BodyTextColumn::make('Supplier')
                            ->label('Pemasok')
                            ->extraAttributes(['style' => 'width: 150px;']),
                            BodyTextColumn::make('Stok')
                            ->label('Stok Diterima')
                            ->extraAttributes(['style' => 'width: 120px;']),
                            BodyTextColumn::make('Satuan')
                            ->label('Satuan'),
                            BodyTextColumn::make('Total Pengeluaran')
                            ->label('Total Pembelian')
                            ->prefix('Rp. ')
                            ->extraAttributes(['style' => 'width: 150px;']),
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
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    // Update rentang tanggal saat filter diubah
                    $this->dateRange = $state; // Menyimpan rentang tanggal ke dalam properti
                }),
            ]);
    }

    public function submit()
    {
        // Ambil nilai dari filter dan simpan rentang tanggal ke dalam properti
        $this->dateRange = request()->input('tgl_pembelian'); // Mengambil rentang tanggal dari request
    }

    public function purchaseSummary(?array $filters)
    {
    
        $query = TransaksiMasukItem::query()
            ->join('transaksi_masuks', 'transaksi_masuk_items.transaksi_masuk_id', '=', 'transaksi_masuks.id')
            ->join('barangs', 'transaksi_masuk_items.barang_id', '=', 'barangs.id')
            ->join('suppliers', 'transaksi_masuks.supplier_id', '=', 'suppliers.id')
            ->select(
                'transaksi_masuks.tgl_pembelian',
                'barangs.nama_barang',
                'suppliers.nama_supplier',
                'barangs.satuan',
                DB::raw('SUM(transaksi_masuk_items.qty) as total_quantity'),
                DB::raw('SUM(transaksi_masuk_items.total) as total_expense')
            );

            if (!empty($filters['tgl_pembelian'])) {
                $dateRange = $filters['tgl_pembelian'];
                $dates = explode(' - ', $dateRange);
        
                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]));
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]));
                    $query->whereBetween('transaksi_masuks.tgl_pembelian', [$startDate->startOfDay(), $endDate->endOfDay()]);
                }
            }

        $query->groupBy('barangs.nama_barang', 'barangs.satuan', 'transaksi_masuks.tgl_pembelian', 'suppliers.nama_supplier');
    
        $results = $query->get()->map(function ($item) {
            return [
                'Tanggal' => $item->tgl_pembelian,
                'Nama Barang' => $item->nama_barang,
                'Supplier' => $item->nama_supplier,
                'Stok' => $item->total_quantity,
                'Satuan' => $item->satuan,
                // 'Total Dibeli' => $item->total_quantity,
                'Total Pengeluaran' => number_format($item->total_expense, 0, ',', '.'),
            ];
        });

        $totalExpense = $results->sum(function ($item) {
            return str_replace('.', '', $item['Total Pengeluaran']);
        });

        $results->push([
            'Tanggal' => '',
            'Nama Barang' => 'TOTAL',
            'Supplier' => '',
            'Stok' => '',
            'Satuan' => '',
            // 'Total Dibeli' => $results->sum('Total Dibeli'),
            'Total Pengeluaran' => number_format($totalExpense, 0, ',', '.'),
        ]);

        return $results;
    }
}

