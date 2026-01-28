<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProblemCategory extends Model
{
    /**
     * ------------------------------------------------------------------
     * ATRIBUTOS
     * ------------------------------------------------------------------
     */
    protected $fillable = [
        'product_id',
        'name',
        'slug',
        'description',
        'service_type',       // incident | service_request | improvement | purchase
        'default_priority',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * ------------------------------------------------------------------
     * RELACIONAMENTOS
     * ------------------------------------------------------------------
     */

    /**
     * Produto ao qual a categoria pertence
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Chamados abertos nesta categoria
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * ------------------------------------------------------------------
     * SCOPES
     * ------------------------------------------------------------------
     */

    /**
     * Apenas categorias ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Ordenação padrão para UI
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
