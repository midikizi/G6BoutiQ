<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneVente extends Model
{
    use HasFactory;

    protected $fillable = [
        'vente_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'montant_total',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($ligneVente) {
            $ligneVente->produit->retirerStock(
                $ligneVente->quantite,
                'Vente',
                $ligneVente->vente->numero_facture
            );

            $ligneVente->montant_total = $ligneVente->quantite * $ligneVente->prix_unitaire;
            $ligneVente->save();

            $ligneVente->vente->updateMontantTotal();
        });

        static::updated(function ($ligneVente) {
            if ($ligneVente->wasChanged('quantite')) {
                $ancienneQuantite = $ligneVente->getOriginal('quantite');
                $nouvelleQuantite = $ligneVente->quantite;
                $difference = $nouvelleQuantite - $ancienneQuantite;

                if ($difference > 0) {
                    $ligneVente->produit->retirerStock(
                        $difference,
                        'Ajustement vente',
                        $ligneVente->vente->numero_facture
                    );
                } elseif ($difference < 0) {
                    $ligneVente->produit->ajouterStock(
                        abs($difference),
                        'Ajustement vente',
                        $ligneVente->vente->numero_facture
                    );
                }
            }

            $ligneVente->montant_total = $ligneVente->quantite * $ligneVente->prix_unitaire;
            $ligneVente->save();

            $ligneVente->vente->updateMontantTotal();
        });

        static::deleted(function ($ligneVente) {
            $ligneVente->produit->ajouterStock(
                $ligneVente->quantite,
                'Annulation vente',
                $ligneVente->vente->numero_facture
            );

            $ligneVente->vente->updateMontantTotal();
        });
    }

    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class);
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }
}
