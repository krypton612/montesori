<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoProfesor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documento_profesor';

    protected $fillable = [
        'nombre_archivo',
        'tipo_documento_id',
        'profesor_id',
    ];

    // Relación con TipoDocumento
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }

    // Relación con Profesor
    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }
}
