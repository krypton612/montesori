<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoInscripcion extends Model
{
    use HasFactory;

    protected $table = 'documento_inscripcion';

    protected $fillable = [
        'tipo_documento_id',
        'nombre_archivo',
        'codigo_inscripcion',
        'estudiante_id',
    ];


    /*
    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'inscripcion_id');
    }
    */

    public function tipo_documento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }
}
