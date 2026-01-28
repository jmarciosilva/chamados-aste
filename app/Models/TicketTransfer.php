<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketTransfer extends Model
{
    protected $fillable = [
        'ticket_id',
        'from_group_id',
        'to_group_id',
        'transferred_by',
        'reason',
    ];
}
