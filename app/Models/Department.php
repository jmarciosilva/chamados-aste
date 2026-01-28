<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    /**
     * CAMPOS PERMITIDOS PARA MASS ASSIGNMENT
     */
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    /**
     * CASTS
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * RELACIONAMENTO
     * ------------------------------------------------------------------
     * Um departamento pode ter vários usuários.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
