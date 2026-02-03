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
        'description',
        'is_active',
        'sla_config', // ✅ NOVO: JSON com SLAs por prioridade
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sla_config' => 'array', // ✅ Cast automático para array
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

    /**
     * ------------------------------------------------------------------
     * MÉTODOS DE SLA
     * ------------------------------------------------------------------
     */

    /**
     * Retorna configuração de SLA para uma prioridade específica
     * 
     * @param string $priority low|medium|high|critical
     * @return array|null ['response_hours' => int, 'resolution_hours' => int]
     */
    public function getSlaForPriority(string $priority): ?array
    {
        if (!$this->sla_config) {
            return $this->getDefaultSlaForPriority($priority);
        }

        return $this->sla_config[$priority] ?? $this->getDefaultSlaForPriority($priority);
    }

    /**
     * SLA padrão quando não configurado
     */
    private function getDefaultSlaForPriority(string $priority): array
    {
        return match ($priority) {
            'critical' => ['response_hours' => 2, 'resolution_hours' => 4],
            'high' => ['response_hours' => 4, 'resolution_hours' => 12],
            'medium' => ['response_hours' => 8, 'resolution_hours' => 24],
            'low' => ['response_hours' => 24, 'resolution_hours' => 72],
            default => ['response_hours' => 24, 'resolution_hours' => 72],
        };
    }

    /**
     * Inicializa SLA config com valores padrão
     */
    public function initializeSlaConfig(): void
    {
        if ($this->sla_config) {
            return; // Já inicializado
        }

        $this->sla_config = [
            'low' => ['response_hours' => 24, 'resolution_hours' => 72],
            'medium' => ['response_hours' => 8, 'resolution_hours' => 24],
            'high' => ['response_hours' => 4, 'resolution_hours' => 12],
            'critical' => ['response_hours' => 2, 'resolution_hours' => 4],
        ];

        $this->save();
    }

    /**
     * Atualiza SLA para uma prioridade específica
     */
    public function updateSlaForPriority(
        string $priority,
        int $responseHours,
        int $resolutionHours
    ): void {
        $config = $this->sla_config ?? [];
        
        $config[$priority] = [
            'response_hours' => $responseHours,
            'resolution_hours' => $resolutionHours,
        ];

        $this->sla_config = $config;
        $this->save();
    }

    /**
     * Retorna configuração completa de SLA formatada para UI
     */
    public function getSlaConfigFormatted(): array
    {
        $config = $this->sla_config ?? [];

        return [
            'low' => $config['low'] ?? $this->getDefaultSlaForPriority('low'),
            'medium' => $config['medium'] ?? $this->getDefaultSlaForPriority('medium'),
            'high' => $config['high'] ?? $this->getDefaultSlaForPriority('high'),
            'critical' => $config['critical'] ?? $this->getDefaultSlaForPriority('critical'),
        ];
    }
}
