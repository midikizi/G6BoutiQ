<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'vente_id',
        'montant',
        'mode_paiement',
        'statut',
        'date_paiement',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($paiement) {
            if ($paiement->isDirty('statut') && $paiement->statut === 'valide') {
                $paiement->vente()->update(['statut' => 'payee']);
            }
        });

        static::created(function ($paiement) {
            if ($paiement->statut === 'valide') {
                $paiement->vente()->update(['statut' => 'payee']);
            }
        });
    }

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }
}
