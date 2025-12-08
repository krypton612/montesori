<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoInscripcion extends Model
{
    use HasFactory;

    protected $table = 'documento_inscripcion';

    protected $fillable = [
        'inscripcion_id',
        'inscripcion_id',
        'tipo_documento_id',
        'nombre_archivo',
    ];


    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'inscripcion_id');
    }

    public function tipo_documento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }
}
