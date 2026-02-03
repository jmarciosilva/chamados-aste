<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\ServiceType;
use App\Enums\TicketStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    /**
     * ==============================================================
     * CAMPOS PREENCHÍVEIS
     * ==============================================================
     */
    protected $fillable = [
        'code',
        'subject',
        'description',

        // Contexto do chamado
        'product_id',
        'problem_category_id',
        'service_type',

        // Status e prioridade
        'priority',
        'status',

        // SLA (SNAPSHOT)
        'sla_id',
        'sla_response_hours',
        'sla_resolution_hours',
        'sla_started_at',
        'sla_paused_at',
        'sla_paused_seconds',
        'sla_status',

        // Fechamento
        'resolved_at',

        // Relacionamentos
        'requester_id',
        'department_id',
        'current_group_id',
        'assigned_to',
    ];

    /**
     * ==============================================================
     * CASTS
     * ==============================================================
     */
    protected $casts = [
        'service_type' => ServiceType::class,
        'status' => TicketStatus::class,
        'priority' => Priority::class,

        'closed_at' => 'datetime',
        'resolved_at' => 'datetime',
        'sla_started_at' => 'datetime',
        'sla_paused_at' => 'datetime',
    ];

    /**
     * ==============================================================
     * RELACIONAMENTOS
     * ==============================================================
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function problemCategory()
    {
        return $this->belongsTo(ProblemCategory::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function currentGroup(): BelongsTo
    {
        return $this->belongsTo(SupportGroup::class, 'current_group_id');
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function sla(): BelongsTo
    {
        return $this->belongsTo(Sla::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function groupHistories()
    {
        return $this->hasMany(TicketGroupHistory::class);
    }

    /**
     * ==============================================================
     * SLA — CÁLCULO REAL (COM PAUSA)
     * ==============================================================
     */

    /**
     * Deadline final do SLA considerando pausas
     */
    public function slaDeadline(): ?Carbon
    {
        if (! $this->sla_started_at || ! $this->sla_resolution_hours) {
            return null;
        }

        return $this->sla_started_at
            ->copy()
            ->addHours($this->sla_resolution_hours)
            ->addSeconds($this->sla_paused_seconds ?? 0);
    }

    /**
     * Indicador de status do SLA (UI / Dashboard)
     */
    public function slaIndicator(): array
    {
        // --------------------------------------------------
        // NÃO TEM SLA → NÃO APLICA INDICADOR
        // --------------------------------------------------
        if (
            ! $this->sla_started_at ||
            ! $this->sla_resolution_hours
        ) {
            return [
                'status' => 'not_applicable',
                'label' => 'SLA não definido',
            ];
        }

        $now = now();

        // --------------------------------------------------
        // PRAZO FINAL DO SLA
        // --------------------------------------------------
        $deadline = Carbon::parse($this->sla_started_at)
            ->addHours($this->sla_resolution_hours);

        // --------------------------------------------------
        // CHAMADO ENCERRADO
        // --------------------------------------------------
        if ($this->closed_at) {
            return [
                'status' => $this->closed_at->lte($deadline)
                    ? 'ok'
                    : 'breached',
                'label' => $this->closed_at->lte($deadline)
                    ? 'Resolvido dentro do SLA'
                    : 'Resolvido fora do SLA',
            ];
        }

        // --------------------------------------------------
        // CHAMADO EM ANDAMENTO
        // --------------------------------------------------
        return $now->gt($deadline)
            ? ['status' => 'breached', 'label' => 'SLA estourado']
            : ['status' => 'running', 'label' => 'SLA em andamento'];
    }

    /**
     * ==============================================================
     * CONTROLE DE PAUSA / RETOMADA DO SLA
     * ==============================================================
     */
    public function pauseSla(): void
    {
        if ($this->sla_status !== 'running') {
            return;
        }

        $this->update([
            'sla_status' => 'paused',
            'sla_paused_at' => now(),
        ]);
    }

    public function resumeSla(): void
    {
        if ($this->sla_status !== 'paused' || ! $this->sla_paused_at) {
            return;
        }

        $pausedSeconds = $this->sla_paused_at->diffInSeconds(now());

        $this->update([
            'sla_paused_seconds' => ($this->sla_paused_seconds ?? 0) + $pausedSeconds,
            'sla_paused_at' => null,
            'sla_status' => 'running',
        ]);
    }

    /**
     * ==============================================================
     * MENSAGEM DE SISTEMA (VISÍVEL AO USUÁRIO)
     * ==============================================================
     */
    public function addSystemMessage(string $message): void
    {
        $this->messages()->create([
            'user_id' => auth()->id() ?? $this->assigned_to,
            'message' => $message,
            'is_internal_note' => false,
        ]);
    }

    /**
     * ==============================================================
     * ACCESSOR — STATUS LEGÍVEL (UI)
     * ==============================================================
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            TicketStatus::OPEN => 'Aberto',
            TicketStatus::IN_PROGRESS => 'Em Atendimento',
            TicketStatus::WAITING_USER => 'Aguardando Usuário',
            TicketStatus::RESOLVED => 'Resolvido',
            TicketStatus::CLOSED => 'Fechado',
        };
    }

    public function statusBadge(): array
    {
        return match ($this->status) {
            TicketStatus::OPEN => [
                'label' => 'Aberto',
                'color' => 'bg-blue-100 text-blue-700',
            ],
            TicketStatus::IN_PROGRESS => [
                'label' => 'Em Atendimento',
                'color' => 'bg-yellow-100 text-yellow-700',
            ],
            TicketStatus::WAITING_USER => [
                'label' => 'Aguardando Usuário',
                'color' => 'bg-orange-100 text-orange-700 border border-orange-300',
            ],
            TicketStatus::RESOLVED => [
                'label' => 'Resolvido',
                'color' => 'bg-green-100 text-green-700',
            ],
            TicketStatus::CLOSED => [
                'label' => 'Fechado',
                'color' => 'bg-slate-200 text-slate-700',
            ],
        };
    }

    public function priorityBadge(): array
    {
        return [
            'label' => $this->priority->label(),
            'color' => $this->priority->color(),
        ];
    }

    public function slaBadge(): array
    {
        $sla = $this->slaIndicator();

        return [
            'label' => $sla['label'],
            'color' => match ($sla['status']) {
                'running' => 'bg-blue-100 text-blue-700',
                'paused' => 'bg-yellow-100 text-yellow-700',
                'breached' => 'bg-red-100 text-red-700',
                'completed' => 'bg-green-100 text-green-700',
                default => 'bg-slate-100 text-slate-600',
            },
        ];
    }
}
