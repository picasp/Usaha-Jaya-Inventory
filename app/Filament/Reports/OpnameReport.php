<?php

namespace App\Filament\Reports;

use App\Models\Opname;
use App\Models\OpnameItem;
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

class OpnameReport extends Report
{
    public ?string $heading = "Laporan Stok Opname";
    static ?string $navigationLabel = 'Laporan Stok Opname';
    public ?array $filters = [];

    public function filterForm(Form $form): Form
    {
        return $form
            ->schema([
                DateRangePicker::make('tgl')
                    ->label('Rentang Tanggal Opname')
                    ->placeholder('Pilih rentang tanggal')
                    ->suffixIcon('heroicon-o-calendar')
                    ->format('d/m/Y')
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->setFilters(['tgl' => $state]);
                        $this->refreshReport();
                    }),
            ]);
    }

    protected function getListeners(): array
    {
        return [
            'refreshReport' => '$refresh',
        ];
    }

    public function header(Header $header): Header
    {
        $filters = $this->filters;
        $dateRangeText = "Periode: Semua waktu";

        if (!empty($filters['tgl'])) {
            $dateRange = $filters['tgl'];
            $dates = explode(' - ', $dateRange);

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]));
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
                                Text::make("Laporan Stok Opname")
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
                                fn (?array $filters) => $this->opnameSummary($filters)
                            )
                            ->columns([
                                BodyTextColumn::make('Tgl Opname')
                                    ->label('Tanggal')
                                    ->dateTime('d M Y'),
                                BodyTextColumn::make('Nama Barang')
                                    ->label('Nama Barang'),
                                BodyTextColumn::make('Qty Sistem')
                                    ->label('Qty Sistem'),
                                BodyTextColumn::make('Qty Fisik')
                                    ->label('Qty Fisik'),
                                BodyTextColumn::make('Selisih')
                                    ->label('Selisih'),
                                BodyTextColumn::make('Keterangan')
                                    ->label('Keterangan'),
                            ]),
                    ]),
            ]);
    }

    public function opnameSummary(?array $filters)
    {
        $query = OpnameItem::query()
            ->join('barangs', 'opname_items.barang_id', '=', 'barangs.id')
            ->join('opnames', 'opname_items.opname_id', '=', 'opnames.id')
            ->select(
                'barangs.nama_barang',
                'opnames.tgl',
                'opname_items.qty_sistem',
                'opname_items.qty_fisik',
                'opname_items.selisih',
                'opname_items.keterangan'
            );

        if (!empty($filters['tgl'])) {
            $dateRange = $filters['tgl'];
            $dates = explode(' - ', $dateRange);

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]));
                $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]));

                $query->whereBetween('opnames.tgl', [$startDate->startOfDay(), $endDate->endOfDay()]);
            }
        }

        return $query->get()->map(function ($item) {
            return [
                'Nama Barang' => $item->nama_barang,
                'Tgl Opname' => $item->tgl,
                'Qty Sistem' => $item->qty_sistem,
                'Qty Fisik' => $item->qty_fisik,
                'Selisih' => $item->selisih,
                'Keterangan' => $item->keterangan,
            ];
        });
    }

    protected function refreshReport()
    {
        $this->emit('refreshReport');
    }
}
