<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvement_stocks';

    protected $fillable = [
        'produit_id',
        'type',
        'type_mouvement',
        'quantite',
        'motif',
        'date_mouvement',
        'description',
        'reference_document'
    ];

    protected $casts = [
        'date_mouvement' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($mouvement) {
            // Si c'est une vente, on force le type de mouvement à 'sortie'
            if ($mouvement->type === 'vente') {
                $mouvement->type_mouvement = 'sortie';
            }
            
            // Si la date de mouvement n'est pas définie, on met la date actuelle
            if (!$mouvement->date_mouvement) {
                $mouvement->date_mouvement = now();
            }
        });
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }
}
