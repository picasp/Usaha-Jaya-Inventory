<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Filament\Resources\BarangResource\RelationManagers;
use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Str;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Daftar Barang';
    protected static ?string $breadcrumb = "Daftar Barang";

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
            ->schema([
                TextInput::make('kode_barang')
                ->label('Kode Barang')
                ->required()
                ->disabled()
                // Sembunyikan input pada halaman buat
                ->hidden(fn ($record) => $record === null),
                TextInput::make('nama_barang')
                ->required(),
                TextInput::make('stok')
                ->numeric()
                ->required(),
                TextInput::make('stok_minimal')
                ->numeric()
                ->required(),
                Select::make('satuan')
                ->options(['Unit' => 'Unit', 'Pcs' => 'PCS', 'Kg' => 'KG', 'Sak' => 'Sak', 'Ltr' => 'Liter'])
                ->searchable()
                ->native(false)
                ->required(),
                TextInput::make('harga_jual')
                ->numeric()
                ->prefix('Rp.')
                ->required(),
                TextInput::make('harga_beli')
                ->numeric()
                ->prefix('Rp.')
                ->required(),
                Textarea::make('keterangan')
                ->columnSpan('full'),
                ])
            ->Columns(3)
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
        ->emptyStateHeading('Tidak ada barang')
        ->emptyStateDescription('Ketika Anda menambahkan barang, Anda akan melihat barang di sini.')
        ->recordClasses(fn (Model $record) => $record->stok <= $record->stok_minimal 
        ? 'bg-low-stock' 
        : null)
            ->columns([
                Tables\Columns\TextColumn::make('kode_barang')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('nama_barang')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('stok')
                ->sortable(),
                Tables\Columns\TextColumn::make('satuan')
                ->sortable(),
                Tables\Columns\TextColumn::make('harga_jual')
                ->sortable()
                ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('harga_beli')
                ->sortable()
                ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('keterangan')
                ->markdown()
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
                Tables\Columns\TextColumn::make('status') // Menampilkan kolom status
                ->sortable(),
            ])
            ->defaultSort('status', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->color('warning'),
                Tables\Actions\DeleteAction::make()
                ->label('Hapus')
                ->successNotificationTitle('Barang berhasil dihapus')
                ->modalHeading('Hapus Barang')
                ->modalDescription('Apakah anda yakin ingin menghapus barang ini?')
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
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}
