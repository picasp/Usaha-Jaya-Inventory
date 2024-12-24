<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpnameResource\Pages;
use App\Filament\Resources\OpnameResource\RelationManagers;
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
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Filament\Tables\Filters\Filter;

class OpnameResource extends Resource
{
    protected static ?string $model = Opname::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Stok Opname';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    DatePicker::make('tgl')
                    ->label('Tanggal'),

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
            ->columns([
                Tables\Columns\TextColumn::make('tgl')
                ->label('Tanggal')
                ->sortable()
                ->searchable()
                ->dateTime('d-m-Y'),
                Tables\Columns\TextColumn::make('keterangan')
                ->label('Keterangan')
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
            'index' => Pages\ListOpnames::route('/'),
            'create' => Pages\CreateOpname::route('/create'),
            'edit' => Pages\EditOpname::route('/{record}/edit'),
        ];
    }
}
