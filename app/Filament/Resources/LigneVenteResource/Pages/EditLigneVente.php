<?php

namespace App\Filament\Resources\LigneVenteResource\Pages;

use App\Filament\Resources\LigneVenteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLigneVente extends EditRecord
{
    protected static string $resource = LigneVenteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
