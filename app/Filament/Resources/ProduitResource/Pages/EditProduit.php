<?php

namespace App\Filament\Resources\ProduitResource\Pages;

use App\Filament\Resources\ProduitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduit extends EditRecord
{
    protected static string $resource = ProduitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $produit = $this->record;

        // Si la quantité en stock a changé
        if ($produit->wasChanged('quantite_stock')) {
            $ancienStock = $produit->getOriginal('quantite_stock');
            $nouveauStock = $produit->quantite_stock;
            $difference = $nouveauStock - $ancienStock;

            if ($difference != 0) {
                $produit->mouvements()->create([
                    'type_mouvement' => $difference > 0 ? 'entree' : 'sortie',
                    'quantite' => abs($difference),
                    'motif' => 'Ajustement manuel du stock',
                    'reference_document' => 'ADJ-' . str_pad($produit->id, 6, '0', STR_PAD_LEFT) . '-' . time(),
                    'date_mouvement' => now(),
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
