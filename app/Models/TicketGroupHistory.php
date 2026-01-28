<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketGroupHistory extends Model
{
    protected $fillable = [
        'ticket_id',
        'from_group_id',
        'to_group_id',
        'changed_by',
        'note',
    ];

    /**
     * ==========================================================
     * RELACIONAMENTOS
     * ==========================================================
     */

    // Usuário que realizou o encaminhamento (operador)
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Usuário que realizou o encaminhamento (operador)
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Grupo de origem
    public function fromGroup()
    {
        return $this->belongsTo(SupportGroup::class, 'from_group_id');
    }

    // Grupo de destino
    public function toGroup()
    {
        return $this->belongsTo(SupportGroup::class, 'to_group_id');
    }

    // Ticket relacionado
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
