<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación con el rol
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Proyectos administrados por el usuario
    public function managedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'gerente_id');
    }

    // Tareas asignadas al usuario
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_user_id');
    }
}
