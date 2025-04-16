<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'prix',
        'quantite_stock',
        'seuil_alerte',
        'code_qr',
        'categorie_id',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function ligneVentes()
    {
        return $this->hasMany(LigneVente::class);
    }

    public function ajouterStock(int $quantite, string $motif = 'Approvisionnement', ?string $reference = null): void
    {
        $this->quantite_stock += $quantite;
        $this->save();

        $this->mouvements()->create([
            'type_mouvement' => 'entree',
            'quantite' => $quantite,
            'motif' => $motif,
            'reference_document' => $reference,
            'date_mouvement' => now(),
        ]);
    }

    public function retirerStock(int $quantite, string $motif = 'Vente', ?string $reference = null): void
    {
        if ($this->quantite_stock >= $quantite) {
            $this->quantite_stock -= $quantite;
            $this->save();

            $this->mouvements()->create([
                'type_mouvement' => 'sortie',
                'quantite' => $quantite,
                'motif' => $motif,
                'reference_document' => $reference,
                'date_mouvement' => now(),
            ]);
        } else {
            throw new \Exception("Stock insuffisant pour le produit {$this->nom}");
        }
    }
}
