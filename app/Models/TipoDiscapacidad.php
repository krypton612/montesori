<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDiscapacidad extends Model
{
    use HasFactory;

    protected $table = 'tipo_discapacidad';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // Relación 1 a N con Discapacidad (cuando exista esa tabla/modelo)
    public function discapacidades()
    {
        return $this->hasMany(Discapacidad::class, 'tipo_discapacidad_id');
    }
}
