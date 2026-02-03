<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model SupportGroup
 *
 * Representa um Grupo de Atendimento (ITIL v4)
 */
class SupportGroup extends Model
{
    use HasFactory;

    /**
     * Campos liberados para mass assignment
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
     * Técnicos/usuários pertencentes ao grupo
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    /**
     * Usuário que criou o grupo (admin)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Chamados associados ao grupo
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
