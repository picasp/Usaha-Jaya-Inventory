<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiKeluarResource\Pages;
use App\Filament\Resources\TransaksiKeluarResource\RelationManagers;
use App\Models\TransaksiKeluar;
use App\Models\Barang;
use App\Models\TransaksiKeluarItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class TransaksiKeluarResource extends Resource
{
    protected static ?string $model = TransaksiKeluar::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Penjualan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Keranjang Belanja')
                ->schema([
                    Repeater::make('transaksi_keluar_item')
                    ->relationship()
                    ->required()
                    ->label(false)
                    ->addActionLabel('Tambah')
                    ->Schema([
                        Select::make('barang_id')
                        ->options(function () {
                            return Barang::all()->mapWithKeys(function ($barang) {
                                return [$barang->id => $barang->nama_barang . ' (Stok: ' . $barang->stok . ' ' . $barang->satuan . ')'];
                            });
                        })
                            ->disableOptionWhen(function ($value, $state, Forms\Get $get) {
                                return collect($get('../*.barang_id'))
                                    ->reject(fn($id) => $id == $state)
                                    ->filter()
                                    ->contains($value);
                            })
                            ->label('Barang')
                            ->reactive()
                            ->searchable()
                            ->required()
                            ->columnSpan([
                                'md' => 5,
                            ])
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $barang = Barang::find($state);
                        
                                if ($barang) {
                                    // Set the price and check stock
                                    $set('harga', $barang->harga_jual);
                        
                                    // Check stock level and notify if low
                                    if ($barang->stok < 10) {
                                        Notification::make()
                                            ->title('Stok Menipis')
                                            ->danger()
                                            ->body('Stok barang "' . $barang->nama_barang . '" hanya tersisa ' . $barang->stok . ' ' .$barang->satuan . '.')
                                            ->send();
                                    }
                                }
                            }),

                            TextInput::make('qty')
                            ->label('Kuantitas')
                            ->live(debounce: 500)
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(function(Forms\Get $get, $record)
                            {
                            if (filled($get('barang_id')))
                            return Barang::where('id',$get('barang_id'))->select(['stok'])->first()->stok;
                            else return '1';
                            })
                            ->required()
                            ->reactive() // Ensure this field triggers updates
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $set('total', ($state ?? 0) * ($get('harga') ?? 0)); // Dynamically update total
                                self::updateTotals($get, $set);
                            })
                            ->columnSpan([
                                'md' => 2,
                            ]),

                            TextInput::make('harga')
                            ->label('Harga Satuan')
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->prefix('Rp. ')
                            ->columnSpan([
                                'md' => 1,
                            ])
                            ->required(),

                            TextInput::make('total')
                            ->label('Total Harga')
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->prefix('Rp. ')
                            ->columnSpan([
                                'md' => 1,
                            ])
                            ->required(),
                        ])
                    // After adding a new row, we need to update the totals
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                        self::updateTotals($get, $set);
                    })
                    // After deleting a row, we need to update the totals
                    ->deleteAction(
                        fn(Action $action) => $action->after(fn(Forms\Get $get, Forms\Set $set) => self::updateTotals($get, $set)),
                    )
                    ->hiddenLabel()
                    ->live()
                    ->columns(4),

                    TextInput::make('total_harga')
                    ->label('Subtotal')
                    ->numeric()
                    ->readOnly()
                    ->prefix('Rp. ')
                    ->afterStateHydrated(function (Forms\Get $get, Forms\Set $set) {
                        self::updateTotals($get, $set);
                    }),
                    ]),

                Section::make('Form Pembelian')
                ->Schema([
                    TextInput::make('nama_pembeli')
                    ->label('Nama Pembeli')
                    ->default('-'),

                    Select::make('jenis_pembayaran')
                    ->label('Pembayaran')
                    ->native(false)
                    ->options([
                        'tunai'=> 'Tunai',
                        'transfer'=> 'Transfer',
                    ]),

                    DatePicker::make('tgl_penjualan')
                    ->required(),

                    Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->columnSpan('full')
                ])
                ->columns(2)
                    ]); 
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tgl_penjualan')
                ->label('Tanggal')
                ->dateTime('d-m-Y')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('nama_pembeli')
                ->label('Nama Pembeli')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('total_harga')
                ->label('Subtotal')
                ->sortable()
                ->formatStateUsing(function ($state) {
                    return 'Rp ' . number_format($state, 0, ',', '.');
                }),
                Tables\Columns\TextColumn::make('keterangan')
                ->markdown()
                ->sortable(),
            ])
            ->defaultSort('tgl_penjualan', 'desc')
            ->filters([
                DateRangeFilter::make('tgl_penjualan')
                ->label('Tanggal Penjualan')
                ->placeholder('Pilih rentang tanggal'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->label('Lihat')
                ->hidden(),
                Tables\Actions\EditAction::make()
                ->color('warning'),
                Tables\Actions\Action::make('pdf') 
                ->label('Cetak')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->url(fn (TransaksiKeluar $record) => route('pdf', $record))
                ->openUrlInNewTab(), 
                Tables\Actions\DeleteAction::make()
                ->label('Hapus')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksiKeluars::route('/'),
            'create' => Pages\CreateTransaksiKeluar::route('/create'),
            'edit' => Pages\EditTransaksiKeluar::route('/{record}/edit'),
            'view' => Pages\ViewTransaksiKeluar::route('/{record}/view'),
        ];
    }

    public static function updateTotals(Forms\Get $get, Forms\Set $set): void
    {
        // Retrieve all selected products and remove empty rows
        $selectedProducts = collect($get('transaksi_keluar_item'))->filter(fn($item) => !empty($item['barang_id']) && !empty($item['qty']));
     
        // Retrieve prices for all selected products
        $prices = Barang::find($selectedProducts->pluck('barang_id'))->pluck('harga_jual', 'id');
     
        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
            return $subtotal + ($prices[$product['barang_id']] * $product['qty']);
        }, 0);
     
        // Update the state with the new values
        $set('total_harga', $subtotal);
    }
}
