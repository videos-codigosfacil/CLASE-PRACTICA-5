<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'gerente_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
    ];

    public function gerente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gerente_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
