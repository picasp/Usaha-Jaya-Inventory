<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
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
use Filament\Forms\Components\Textarea;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Daftar Pemasok';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
            ->schema([
            TextInput::make('nama_supplier'),
            TextInput::make('no_telp')
            ->numeric(),
            TextInput::make('alamat')
            ->columnSpan('full'),
            Textarea::make('keterangan')
            ->columnSpan('full')
            ])
            ->Columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('nama_supplier')
            ->searchable()
            ->sortable(),
            Tables\Columns\TextColumn::make('no_telp')
            ->sortable(),
            Tables\Columns\TextColumn::make('alamat')
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
