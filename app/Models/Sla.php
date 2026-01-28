<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sla extends Model
{
    /**
     * ------------------------------------------------------------------
     * ATRIBUTOS
     * ------------------------------------------------------------------
     */
    protected $fillable = [
        'product_id',
        'service_type',          // enum ServiceType
        'priority',              // enum Priority
        'response_time_hours',
        'resolution_time_hours',
        'is_default',            // fallback do produto
        'is_active',
    ];

    protected $casts = [
        'service_type' => ServiceType::class,
        'priority'     => Priority::class,
        'is_default'   => 'boolean',
        'is_active'    => 'boolean',
    ];

    /**
     * ------------------------------------------------------------------
     * RELACIONAMENTOS
     * ------------------------------------------------------------------
     */

    /**
     * Produto ao qual o SLA pertence
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * ------------------------------------------------------------------
     * SCOPES
     * ------------------------------------------------------------------
     */

    /**
     * Apenas SLAs ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * SLA específico por produto + tipo + prioridade
     */
    public function scopeMatchRule(
        $query,
        int $productId,
        ServiceType $serviceType,
        Priority $priority
    ) {
        return $query->active()
            ->where('product_id', $productId)
            ->where('service_type', $serviceType)
            ->where('priority', $priority);
    }

    /**
     * SLA padrão (fallback) do produto
     */
    public function scopeDefaultForProduct($query, int $productId)
    {
        return $query->active()
            ->where('product_id', $productId)
            ->where('is_default', true);
    }
}
