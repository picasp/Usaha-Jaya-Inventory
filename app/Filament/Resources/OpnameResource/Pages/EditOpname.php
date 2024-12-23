<?php

namespace App\Filament\Resources\OpnameResource\Pages;

use App\Filament\Resources\OpnameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOpname extends EditRecord
{
    protected static string $resource = OpnameResource::class;
    protected static ?string $title = 'Edit Stok Opname';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->label('Hapus'),
        ];
    }
}
