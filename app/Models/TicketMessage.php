<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessage extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal_note',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal_note' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    // Mensagem pertence a um ticket
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'ticket_message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
