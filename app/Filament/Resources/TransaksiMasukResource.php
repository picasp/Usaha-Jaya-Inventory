<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiMasukResource\Pages;
use App\Filament\Resources\TransaksiMasukResource\RelationManagers;
use App\Models\TransaksiMasuk;
use App\Models\Barang;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;


class TransaksiMasukResource extends Resource
{
    protected static ?string $model = TransaksiMasuk::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Pembelian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    Repeater::make('transaksi_masuk_item')
                    ->relationship()
                    ->required()
                    ->label(false)
                    ->addActionLabel('Tambah')
                    ->Schema([
                        Select::make('barang_id')
                            ->options(Barang::all()->pluck('nama_barang', 'id'))
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
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('harga_beli', Barang::find($state)?->harga_beli ?? 0)),
                            TextInput::make('qty')
                            ->label('Kuantitas')
                            ->live(debounce: 500)
                            ->suffix(function(Forms\Get $get)
                            {
                                $barangId = $get('barang_id');
                                if (filled($barangId)) {
                                    $barang = Barang::find($barangId);
                                    // Mengembalikan satuan barang jika ada
                                    return $barang ? $barang->satuan : '-';
                                }
                                return '-';
                            })
                            ->reactive() // Ensure this field triggers updates
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $set('total', ($state ?? 0) * ($get('harga_beli') ?? 0)); // Dynamically update total
                                self::updateTotals($get, $set);
                            })
                            ->numeric()
                            ->required()
                            ->columnSpan([
                                'md' => 3,
                            ]),
                            TextInput::make('harga_beli')
                            ->label('Harga Satuan')
                            ->dehydrated()
                            ->numeric()
                            ->prefix('Rp. ')
                            ->columnSpan([
                                'md' => 2,
                            ])
                            ->required(),

                            TextInput::make('total')
                            ->label('Total Harga')
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->prefix('Rp. ')
                            ->columnSpan([
                                'md' => 2,
                            ])
                            ->required(),
                        ])
                    ->live()
                    // After adding a new row, we need to update the totals
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                        self::updateTotals($get, $set);
                    })
                    // After deleting a row, we need to update the totals
                    ->deleteAction(
                        fn(Action $action) => $action->after(fn(Forms\Get $get, Forms\Set $set) => self::updateTotals($get, $set)),
                    )
                    ->hiddenLabel()
                    ->columns([
                        'md' => 10,
                    ]),
                    TextInput::make('total_harga_masuk')
                    ->label('Total Biaya')
                    ->numeric()
                    ->readOnly()
                    ->prefix('Rp. ')
                    ->afterStateHydrated(function (Forms\Get $get, Forms\Set $set) {
                        self::updateTotals($get, $set);
                    }),
                    ]),
                Section::make()
                ->Schema([
                    Select::make('supplier_id')
                    ->relationship('supplier', 'nama_supplier')
                    ->label('Pemasok')
                    ->reactive()
                    ->searchable()
                    ->required()
                    ->options(Supplier::all()->pluck('nama_supplier', 'id'))
                    ->disableOptionWhen(function ($value, $state, Forms\Get $get) {
                        return collect($get('../*.supplier_id'))
                            ->reject(fn($id) => $id == $state)
                            ->filter()
                            ->contains($value);
                    })
                    ->createOptionForm([
                        TextInput::make('nama_supplier'),
                        TextInput::make('no_telp')
                        ->tel(),
                        TextInput::make('alamat'),
                        Textarea::make('keterangan')
                    ])
                    ->createOptionAction(function (Action $action) {
                        return $action
                            ->modalHeading('Tambah Supplier')
                            ->modalSubmitActionLabel('Tambah')
                            ->modalWidth('lg');
                    }),
                    DatePicker::make('tgl_pembelian')
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
                Tables\Columns\TextColumn::make('tgl_pembelian')
                ->label('Tanggal')
                ->dateTime('d-m-Y')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_masuk')
                ->label('Total Harga')
                ->sortable()
                ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('supplier.nama_supplier')
                ->label('Supplier')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                ->markdown()
                ->searchable()
                ->sortable()
                ->limit(20)
                ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                    $state = $column->getState();
             
                    if (strlen($state) <= $column->getCharacterLimit()) {
                        return null;
                    }
             
                    // Only render the tooltip if the column content exceeds the length limit.
                    return $state;
                }),
            ])
            ->defaultSort('tgl_pembelian', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->color('warning'),
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
            'index' => Pages\ListTransaksiMasuks::route('/'),
            'create' => Pages\CreateTransaksiMasuk::route('/create'),
            'edit' => Pages\EditTransaksiMasuk::route('/{record}/edit'),
        ];
    }

    public static function updateTotals(Forms\Get $get, Forms\Set $set): void
{
    // Retrieve all selected products and remove empty rows
    $selectedProducts = collect($get('transaksi_masuk_item'))->filter(fn($item) => !empty($item['barang_id']) && !empty($item['qty']));
 
    // Retrieve prices for all selected products
    $prices = Barang::find($selectedProducts->pluck('barang_id'))->pluck('harga_beli', 'id');
 
    // Calculate subtotal based on the selected products and quantities
    $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
        return $subtotal + ($prices[$product['barang_id']] * $product['qty']);
    }, 0);
 
    // Update the state with the new values
    $set('total_harga_masuk', $subtotal);
}
}
