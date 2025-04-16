<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vente extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_facture',
        'client_id',
        'date_vente',
        'montant_total',
        'statut',
        'mode_paiement',
        'statut_paiement',
        'date_paiement',
    ];

    protected $casts = [
        'date_vente' => 'datetime',
        'date_paiement' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vente) {
            $vente->numero_facture = 'FAC-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
        });

        static::updated(function ($vente) {
            if ($vente->isDirty('statut_paiement') && $vente->statut_paiement === 'valide') {
                $vente->update(['statut' => 'payee']);
            }
        });

        static::created(function ($vente) {
            $vente->numero_facture = 'FAC-' . str_pad($vente->id, 6, '0', STR_PAD_LEFT);
            $vente->save();
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function ligneVentes(): HasMany
    {
        return $this->hasMany(LigneVente::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function updateMontantTotal()
    {
        $this->montant_total = $this->ligneVentes()->sum('montant_total');
        $this->save();
    }

    public function updateStocks(): void
    {
        foreach ($this->ligneVentes as $ligne) {
            $ligne->produit->retirerStock(
                $ligne->quantite,
                'Vente',
                $this->numero_facture
            );
        }
    }
}
