<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    use HasFactory;

    protected $table = 'gestion';
    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'habilitado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'habilitado' => 'boolean',
    ];

    protected $with = ['cursos'];

    public function cursos()
    {
        return $this->hasMany(Curso::class, 'gestion_id');
    }

    public function grupo()
    {
        return $this->hasMany(Grupo::class, 'gestion_id');
    }
}
