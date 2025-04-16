<?php

namespace App\Filament\Resources\ProduitResource\Pages;

use App\Filament\Resources\ProduitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduit extends CreateRecord
{
    protected static string $resource = ProduitResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $produit = static::getModel()::create($data);

        // Création automatique du mouvement de stock initial
        if ($produit->quantite_stock > 0) {
            $produit->mouvements()->create([
                'type_mouvement' => 'entree',
                'quantite' => $produit->quantite_stock,
                'motif' => 'Stock initial',
                'reference_document' => 'INIT-' . str_pad($produit->id, 6, '0', STR_PAD_LEFT),
                'date_mouvement' => now(),
            ]);
        }

        return $produit;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
