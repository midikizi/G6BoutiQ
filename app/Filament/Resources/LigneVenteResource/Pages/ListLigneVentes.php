<?php

namespace App\Filament\Resources\LigneVenteResource\Pages;

use App\Filament\Resources\LigneVenteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLigneVentes extends ListRecords
{
    protected static string $resource = LigneVenteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
