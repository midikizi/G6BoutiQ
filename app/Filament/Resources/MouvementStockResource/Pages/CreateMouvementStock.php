<?php

namespace App\Filament\Resources\MouvementStockResource\Pages;

use App\Filament\Resources\MouvementStockResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Produit;

class CreateMouvementStock extends CreateRecord
{
    protected static string $resource = MouvementStockResource::class;

    protected function afterCreate(): void
    {
        $mouvement = $this->record;
        $produit = Produit::find($mouvement->produit_id);

        if ($mouvement->type_mouvement === 'entree') {
            $produit->quantite_stock += $mouvement->quantite;
        } else {
            if ($produit->quantite_stock >= $mouvement->quantite) {
                $produit->quantite_stock -= $mouvement->quantite;
            } else {
                $this->halt();
                $this->notify('danger', 'Stock insuffisant pour ce produit');
                return;
            }
        }

        $produit->save();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
