<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoDocumento extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tipo_documento';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'habilitado',
    ];

    protected $casts = [
        'habilitado' => 'boolean',
    ];
}
