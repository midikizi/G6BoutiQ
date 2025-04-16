<?php

namespace App\Filament\Resources\MouvementStockResource\Pages;

use App\Filament\Resources\MouvementStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMouvementStock extends EditRecord
{
    protected static string $resource = MouvementStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
