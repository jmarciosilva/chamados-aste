<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportGroup extends Model
{
    /**
     * ------------------------------------------------------------------
     * CAMPOS PERMITIDOS PARA MASS ASSIGNMENT
     * ------------------------------------------------------------------
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'is_entry_point',
        'created_by',
    ];

    /**
     * ------------------------------------------------------------------
     * RELACIONAMENTOS
     * ------------------------------------------------------------------
     */

    // Usuário que criou o grupo
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Chamados atualmente atribuídos ao grupo
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'current_group_id');
    }

    /**
     * ------------------------------------------------------------------
     * SCOPES (UTILITÁRIOS)
     * ------------------------------------------------------------------
     */

    // Apenas grupos ativos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // SupportGroup.php
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
