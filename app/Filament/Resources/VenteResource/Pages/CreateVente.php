<?php

namespace App\Filament\Resources\VenteResource\Pages;

use App\Filament\Resources\VenteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateVente extends CreateRecord
{
    protected static string $resource = VenteResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $vente = static::getModel()::create($data);
        
        // Mise à jour des stocks après la création de la vente
        $vente->updateStocks();

        return $vente;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
