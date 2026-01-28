<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * CAMPOS LIBERADOS PARA MASS ASSIGNMENT
     * ------------------------------------------------------------------
     * Incluímos todos os campos administrativos
     * usados no CRUD de usuários.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'job_title',
        'phone',
        'is_active',
        'department_id',
    ];

    /**
     * CAMPOS OCULTOS
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts automáticos dos atributos
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * RELACIONAMENTO
     * ------------------------------------------------------------------
     * Um usuário pertence a um departamento.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * HELPERS DE NEGÓCIO
     * ------------------------------------------------------------------
     * Facilitam leitura no código.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    // User.php
    public function supportGroups()
    {
        return $this->belongsToMany(SupportGroup::class);
    }

    
}
