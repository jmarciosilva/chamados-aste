<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /**
     * ------------------------------------------------------------------
     * ATRIBUTOS
     * ------------------------------------------------------------------
     */
    protected $fillable = [
        'name',
        'slug',
        'is_active',
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
     * Categorias de problemas vinculadas ao produto
     */
    public function problemCategories(): HasMany
    {
        return $this->hasMany(ProblemCategory::class);
    }

    /**
     * SLAs definidos para este produto
     */
    public function slas(): HasMany
    {
        return $this->hasMany(Sla::class);
    }

    /**
     * Chamados relacionados a este produto
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
     * Apenas produtos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
