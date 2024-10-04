<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Filament\Resources\BarangResource\RelationManagers;
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


class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Daftar Barang';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
            ->schema([
            TextInput::make('nama_barang')
            ->required(),
            TextInput::make('stok')
            ->numeric()
            ->required(),
            Select::make('satuan')
            ->options(['unit' => 'Unit', 'pcs' => 'PCS', 'kg' => 'KG', 'sak' => 'Sak', 'liter' => 'Liter'])
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
            ->columns([
                Tables\Columns\TextColumn::make('nama_barang')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('stok')
                ->sortable(),
                Tables\Columns\TextColumn::make('satuan')
                ->sortable(),
                Tables\Columns\TextColumn::make('harga_jual')
                ->sortable()
                ->prefix('Rp. '),
                Tables\Columns\TextColumn::make('harga_beli')
                ->sortable()
                ->prefix('Rp. '),
                Tables\Columns\TextColumn::make('keterangan')
                ->markdown()
                ->sortable(),
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
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}
