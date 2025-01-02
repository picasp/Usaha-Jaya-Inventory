<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpnameResource\Pages;
use App\Filament\Resources\OpnameResource\RelationManagers;
use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;
use App\Models\Opname;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\HtmlString;

class OpnameResource extends Resource
{
    protected static ?string $model = Opname::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Stok Opname';
    protected static ?string $breadcrumb = "Stok Opname";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    TextInput::make('nama')
                    ->label('Nama Opname')
                    ->required(),

                ]),
                Section::make()
                ->schema([
                    Repeater::make('opname_item')
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
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('qty_sistem', Barang::find($state)?->stok ?? 0)),
                            TextInput::make('qty_sistem')
                            ->label('Stok Sistem')
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->columnSpan([
                                'md' => 4,
                            ])
                            ->required(),
                            TextInput::make('qty_fisik')
                            ->label('Stok Fisik')
                            ->live(debounce: 500)
                            ->numeric()
                            ->columnSpan([
                                'md' => 3,
                            ])
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $qtySys = $get('qty_sistem');
                                $qtyPhy = $state;
                                $set('selisih', $qtyPhy - $qtySys);
                            }),
                            TextInput::make('selisih')
                            ->label('Selisih')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan([
                                'md' => 2,
                            ]),

                            Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull()
                        ])
                    ->live()
                    ->hiddenLabel()
                    ->columns([
                        'md' => 10,
                    ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Tidak ada stok opname')
            ->emptyStateDescription('Ketika Anda menambahkan stok opname, Anda akan melihat stok opname di sini.')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                ->label('Tanggal')
                ->sortable()
                ->searchable()
                ->dateTime('d-m-Y'),
                Tables\Columns\TextColumn::make('nama')
                ->label('Nama')
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\Action::make('detail')
                //     ->label('Detail')
                //     ->icon('heroicon-o-eye')
                //     ->modalHeading(fn (Model $record) => "Detail untuk opname: {$record->nama}")
                //     ->modalContent(function (Model $record) {
                //     // Map opname_item dan periksa relasi barang
                //     $rows = $record->opname_item->map(function ($item) {
                //         // Periksa jika relasi barang ada dan dapat diakses
                //         $barangNama = $item->barang ? $item->barang->nama_barang : 'Barang tidak ditemukan';
                //         return "<tr>
                //                 <td class='border px-4 py-2'>{$barangNama}</td>
                //                 <td class='border px-4 py-2'>{$item->qty_sistem}</td>
                //                 <td class='border px-4 py-2'>{$item->qty_fisik}</td>
                //                 <td class='border px-4 py-2'>{$item->selisih}</td>
                //                 <td class='border px-4 py-2'>{$item->keterangan}</td>
                //             </tr>";
                //         })->implode('');
                
                //         return new HtmlString("
                //             <table class='table-auto w-full text-left'>
                //                 <thead>
                //                     <tr>
                //                         <th class='border px-4 py-2'>Barang</th>
                //                         <th class='border px-4 py-2'>Stok Sistem</th>
                //                         <th class='border px-4 py-2'>Stok Fisik</th>
                //                         <th class='border px-4 py-2'>Selisih</th>
                //                         <th class='border px-4 py-2'>Keterangan</th>
                //                     </tr>
                //                 </thead>
                //                 <tbody>
                //                     {$rows}
                //                 </tbody>
                //             </table>
                //         ");
                //     }),                
                Tables\Actions\EditAction::make()
                ->color('warning'),
                Tables\Actions\DeleteAction::make()
                ->label('Hapus')
                ->successNotificationTitle('Stok Opname berhasil dihapus')
                ->modalHeading('Hapus Stok Opname')
                ->modalDescription('Apakah anda yakin ingin menghapus stok opname ini?')
                ->modalCancelActionLabel('Batal')
                ->modalSubmitActionLabel('Hapus')
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
            'index' => Pages\ListOpnames::route('/'),
            'create' => Pages\CreateOpname::route('/create'),
            'edit' => Pages\EditOpname::route('/{record}/edit'),
        ];
    }
}
