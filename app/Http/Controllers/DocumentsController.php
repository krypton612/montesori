<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Inscripcion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentsController extends Controller
{
    public function descargarCompromisoEstudiantesNuevos($id)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        // Similar al anterior, pero con stream para vista previa

        $grupo_inscripcion = $inscripcion->grupo;
        $estudiante = Estudiante::with('persona', 'apoderados.persona')->findOrFail($inscripcion->estudiante_id);

        $edad = $estudiante->persona->calcularEdad();
        // Apoderado principal
        $apoderadoPrincipal = $estudiante->apoderados->where('pivot.es_principal', true)->first();

        $gestion_inscripcion = $inscripcion->gestion;

        $logoPath = public_path('images/logo.png');

        // 2. Codificar la imagen en Base64 para incrustarla directamente
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            // En caso de que la imagen no se encuentre
            $base64 = '';
        }

        $datos = [
            'estudiante_nombre' => $estudiante->persona->getNombreCompletoAttribute(),
            'carrera' => 'Ingeniería de Sistemas', // Reemplaza con dato real si tienes modelo de matrícula
            'tutor_nombre' => $apoderadoPrincipal ? $apoderadoPrincipal->persona->getNombreCompletoAttribute() : '________________________',
            'tutor_ocupacion' => $apoderadoPrincipal ? $apoderadoPrincipal->ocupacion ?? '_____________________________' : '_____________________________',
            'tutor_domicilio' => '_________________________________________________________________________', // Puedes agregar campo si existe
            'cuota1' => '____', // Datos de cuotas - reemplaza con reales
            'fecha1' => '____',
            'cuota2' => '____',
            'fecha2' => '____',
            'cuota3' => '____',
            'fecha3' => '____',
            'cuota4' => '____',
            'fecha4' => '____',
            'cuota5' => '____',
            'fecha5' => '____',
            'total' => '____',
            'anio' => $gestion_inscripcion ? $gestion_inscripcion->nombre : '__________________',
            'empresa' => config('app.name'),
            'abreviacion' => config('app.abreviacion', 'APP'),
            'edad' => $edad,
            'logo_path' => $base64,
        ];

        $pdf = Pdf::loadView('pdf.compromiso', $datos)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $random = rand(1000, 9999);

        return $pdf->download('COMPROMISO-' . $id . '-' . $random .'.pdf');
    }

    public function previewCompromisoEstudiantesNuevos($id)
    {

        $inscripcion = Inscripcion::findOrFail($id);
        // Similar al anterior, pero con stream para vista previa

        $grupo_inscripcion = $inscripcion->grupo;
        $estudiante = Estudiante::with('persona', 'apoderados.persona')->findOrFail($inscripcion->estudiante_id);

        $edad = $estudiante->persona->calcularEdad();
        // Apoderado principal
        $apoderadoPrincipal = $estudiante->apoderados->where('pivot.es_principal', true)->first();

        $gestion_inscripcion = $inscripcion->gestion;

        $logoPath = public_path('images/logo.png');

        // 2. Codificar la imagen en Base64 para incrustarla directamente
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            // En caso de que la imagen no se encuentre
            $base64 = '';
        }

        $datos = [
            'estudiante_nombre' => $estudiante->persona->getNombreCompletoAttribute(),
            'carrera' => 'Ingeniería de Sistemas', // Reemplaza con dato real si tienes modelo de matrícula
            'tutor_nombre' => $apoderadoPrincipal ? $apoderadoPrincipal->persona->getNombreCompletoAttribute() : '________________________',
            'tutor_ocupacion' => $apoderadoPrincipal ? $apoderadoPrincipal->ocupacion ?? '_____________________________' : '_____________________________',
            'tutor_domicilio' => '_________________________________________________________________________', // Puedes agregar campo si existe
            'cuota1' => '____', // Datos de cuotas - reemplaza con reales
            'fecha1' => '____',
            'cuota2' => '____',
            'fecha2' => '____',
            'cuota3' => '____',
            'fecha3' => '____',
            'cuota4' => '____',
            'fecha4' => '____',
            'cuota5' => '____',
            'fecha5' => '____',
            'total' => '____',
            'anio' => $gestion_inscripcion ? $gestion_inscripcion->nombre : '__________________',
            'empresa' => config('app.name'),
            'abreviacion' => config('app.abreviacion', 'APP'),
            'edad' => $edad,
            'logo_path' => $base64,
        ];

        $pdf = Pdf::loadView('pdf.compromiso', $datos)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream('compromiso-emi-' . $id . '.pdf');
    }

    function generateUUIDv4() {
        // Generate 16 bytes (128 bits) of random data
        $data = random_bytes(16);

        // Set version to 0100 (version 4)
        // The 6th index byte (8th hex pair) has the 4 most significant bits set to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);

        // Set bits 6-7 of the clock_seq_hi_and_reserved to 10
        // The 8th index byte (9th hex pair) has the 2 most significant bits set to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Format the bytes into a standard UUID string
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    protected function imageToBase64($path)
    {
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            $type = pathinfo($fullPath, PATHINFO_EXTENSION);
            $data = file_get_contents($fullPath);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }

    private function imageToBase642(string $path): string
    {
        if (! is_readable($path)) {
            throw new \RuntimeException('Imagen no legible: ' . $path);
        }

        $mime = mime_content_type($path);

        $data = base64_encode(file_get_contents($path));

        return "data:$mime;base64,$data";
    }

    /**
     * Lógica común para obtener los datos de la Hoja de Inscripción.
     */
    protected function getInscripcionData($id)
    {
        $inscripcion = Inscripcion::with(['estudiante.persona', 'estudiante.apoderados.persona', 'estudiante.discapacidades', 'grupo.gestion'])->findOrFail($id);
        $estudiante = $inscripcion->estudiante;
        $persona = $estudiante->persona;
        $grupo = $inscripcion->grupo;
        $gestion = $grupo->gestion;

        // Apoderado principal
        $apoderadoPrincipalPivot = $estudiante->apoderados->where('pivot.es_principal', true)->first();
        $apoderadoPrincipal = $apoderadoPrincipalPivot ? $apoderadoPrincipalPivot->persona : null;

        // Discapacidades
        $discapacidades = $estudiante->discapacidades;
        $tiene_discapacidad = $discapacidades->isNotEmpty();
        $discapacidades_nombres = $discapacidades->pluck('nombre')->toArray();
        $observaciones_discapacidad = $discapacidades->isNotEmpty() ? $discapacidades->first()->pivot->observacion : '';

        // Asignaturas (PLACEHOLDER - DEBES REEMPLAZAR CON TU LÓGICA REAL)
        $inscripcion->load('grupo.cursos.materia');

        $asignaturas = $inscripcion->grupo->cursos
            ->pluck('materia.nombre')
            ->toArray();
        // Codificación de imágenes
        $logoBase64 = $this->imageToBase64('images/logo.png') ?? $this->imageToBase64('assets/logo.png');
        

        // Obtener la ruta relativa desde la base de datos
        $relativePath = $estudiante->foto_url;

        // Opción 1: Usar Storage directamente (RECOMENDADO)
        
        $fotoBase64 = 'data:image/png;base64,'.base64_encode(Storage::disk('public')->get($relativePath));
       


        // Generación de QR (se asume que usas un paquete como Simple QR Code)
        // Necesitarás instalarlo si no lo tienes: composer require simplesoftwareio/simple-qrcode
        // Y asegurarte de que está disponible para la vista (puedes pasarlo al arreglo $datos o usar Facades si están configurados).


        $qrCodeData = $inscripcion->codigo_inscripcion ?? 'INSCRIPCION-'.$id;

        // Datos para la vista
        $datos = [
            // Personales
            'codigo_inscripcion' => $inscripcion->codigo_inscripcion,
            'nombre' => $persona->nombre,
            'apellido_pat' => $persona->apellido_pat,
            'apellido_mat' => $persona->apellido_mat,
            'carnet_identidad' => $persona->carnet_identidad,
            'fecha_nacimiento_fmt' => $persona->fecha_nacimiento ? $persona->fecha_nacimiento->format('d/m/Y') : 'N/A',
            'edad' => $persona->calcularEdad(),
            'genero' => 'MASCULINO', // PLACEHOLDER
            'direccion' => $persona->direccion,
            'telefono_principal' => $persona->telefono_principal,
            'email_personal' => $persona->email_personal,
            'nombre_estudiante_completo' => $persona->getNombreCompletoAttribute(),

            // Apoderado
            'tutor_nombre' => $apoderadoPrincipal ? $apoderadoPrincipal->getNombreCompletoAttribute() : 'N/A',
            'tutor_ocupacion' => $apoderadoPrincipalPivot->ocupacion ?? 'N/A',

            // Académicos (PLACEHOLDERS)
            'nivel' => 'SECUNDARIA',
            'grado' => '1ro. Secundaria',
            'grupo_nombre' => $grupo->nombre ?? 'N/A',
            'gestion_nombre' => $gestion ? $gestion->nombre . ' - ' . ($gestion->nombre + 1) : 'N/A', // Asume que la gestión es el año
            'unidad_academica' => 'EMANUEL MONTESSORI A', // PLACEHOLDER
            'turno' => 'MATUTINO', // PLACEHOLDER

            // Asignaturas y Discapacidades
            'asignaturas' => $asignaturas,
            'tiene_discapacidad' => $tiene_discapacidad,
            'discapacidades_nombres' => $discapacidades_nombres,
            'observaciones_discapacidad' => $observaciones_discapacidad,

            // Metadatos
            'fecha_impresion' => Carbon::now()->format('d \d\e F \d\e Y \ H:i A'),
            'logo_path' => $logoBase64,
            'foto_url' => $fotoBase64,
        ];

        return $datos;
    }


    public function descargarInscripcionHojaDeDatos($id)
    {
        $datos = $this->getInscripcionData($id);

        $pdf = Pdf::loadView('pdf.inscripcion_hoja_datos', $datos)
            ->setPaper('letter', 'portrait') // 'letter' o 'a4'
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $codigo = $datos['codigo_inscripcion'] ?? 'INSCRIPCION-' . $id;

        return $pdf->download('HOJA-INSCRIPCION-' . $codigo . '.pdf');
    }


    public function previewInscripcionHojaDeDatos($id)
    {
        $datos = $this->getInscripcionData($id);

        $pdf = Pdf::loadView('pdf.inscripcion_hoja_datos', $datos)
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream('hoja-inscripcion-' . $datos['codigo_inscripcion'] . '.pdf');
    }
}
