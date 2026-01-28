<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserImportLog extends Model
{
    protected $fillable = [
        'user_id',
        'total_rows',
        'imported',
        'errors',
        'error_details',
    ];

    protected $casts = [
        'error_details' => 'array',
    ];

    /**
     * Quem realizou a importação
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
