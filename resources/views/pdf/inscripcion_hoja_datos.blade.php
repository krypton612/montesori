<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hoja de Datos de Inscripción</title>
    <style>
        /* Configuración de página */
        @page { margin: 1.5cm 1.5cm 1.5cm 1.5cm; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #1a1a1a;
            margin: 0;
        }

        /* Estilos de encabezado */
        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header .logo { width: 25%; text-align: left; }
        .header .title { width: 50%; text-align: center; font-size: 14pt; font-weight: bold; }
        .header .qr { width: 25%; text-align: right; }
        .header h1 { margin: 0; font-size: 14pt; }

        /* Contenedor principal de datos (uso de tablas anidadas para estructura) */
        .data-section {
            width: 100%;
            border-collapse: collapse;
            border: 1pt solid #1a1a1a;
            margin-bottom: 20px;
        }
        .data-section td {
            padding: 8px 10px;
            border: 0; /* Quitamos bordes internos del data-section principal */
            vertical-align: top;
        }

        .data-section .photo-cell {
            width: 20%;
            text-align: center;
        }

        .photo-box {
            width: 100px;
            height: 120px;
            border: 1px solid #1a1a1a;
            margin: 0 auto 5px auto;
            text-align: center;
            line-height: 120px;
            font-size: 8pt;
        }

        /* Tabla de detalles personales */
        .personal-data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .personal-data-table td {
            padding: 3px 0;
        }
        .personal-data-table strong { font-weight: bold; }
        .personal-data-table span { font-weight: normal; }

        /* Secciones de título */
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            padding: 5px 0;
            margin-top: 10px;
            border-bottom: 1pt solid #1a1a1a;
            margin-bottom: 10px;
        }

        /* Tablas académicas y de asignaturas */
        .academic-table, .subject-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .academic-table th, .academic-table td,
        .subject-table th, .subject-table td {
            border: 1px solid #1a1a1a;
            padding: 5px;
            text-align: center;
            font-size: 8.5pt;
        }
        .academic-table th, .subject-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .subject-table .subject-name { text-align: left; }

        /* Firmas y sellos */
        .signatures-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }
        .signatures-section td {
            width: 33.33%;
            text-align: center;
            padding-top: 20px;
            border: none;
        }
        .signature-line {
            border-top: 1px solid #1a1a1a;
            width: 80%;
            margin: 0 auto 5px auto;
        }
        .stamp-box {
            height: 50px;
            line-height: 50px;
            border: 1px dashed #1a1a1a; /* Simulación del área del sello */
            width: 80%;
            margin: 5px auto;
        }

        /* Pie de página */
        .footer-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            font-size: 8pt;
        }
        .footer-info td {
            width: 50%;
            border: none;
            padding: 2px 0;
        }
        .footer-info .left { text-align: left; }
        .footer-info .right { text-align: right; }

    </style>
</head>
<body>

<table class="header">
    <tr>
        <td class="logo">
            <img src="{{ $logo_path }}" alt="Logo" style="width: 80px; height: auto;">
        </td>
        <td class="title">
            HOJA DE DATOS PERSONALES
        </td>
        <td class="qr">
            <img src="data:image/png;base64,{{ base64_encode(QrCode::format('png')->size(80)->generate($codigo_inscripcion)) }}" alt="QR Code" style="width: 80px; height: 80px;">
            <div style="font-size: 7pt; margin-top: 2px;">Cód. Inscripción: {{ $codigo_inscripcion }}</div>
        </td>
    </tr>
</table>

<div class="section-title">DATOS PERSONALES DEL ESTUDIANTE</div>

<table class="data-section" style="border: 1pt solid #1a1a1a;">
    <tr>
        <td class="photo-cell" style="border-right: 1pt solid #1a1a1a;">
            <div class="photo-box">
                @if($foto_url)
                <img src="{{ $foto_url }}" alt="Foto" style="width: 100px; height: 120px; object-fit: cover;">
                @else
                FOTO
                @endif
            </div>
            <strong>ESTUDIANTE</strong>
        </td>
        <td style="width: 80%;">
            <table class="personal-data-table">
                <tr>
                    <td colspan="3"><strong>NOMBRES:</strong> {{ $nombre }}</td>
                </tr>
                <tr>
                    <td style="width: 50%;"><strong>APELLIDO PATERNO:</strong> {{ $apellido_pat }}</td>
                    <td style="width: 50%;"><strong>APELLIDO MATERNO:</strong> {{ $apellido_mat }}</td>
                </tr>
                <tr>
                    <td><strong>Doc. de Identidad:</strong> {{ $carnet_identidad }}</td>
                    <td><strong>Edad:</strong> {{ $edad }} AÑOS</td>
                </tr>
                <tr>
                    <td><strong>Fecha de Nacimiento:</strong> {{ $fecha_nacimiento_fmt }}</td>
                    <td><strong>Género:</strong> {{ $genero }}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Dirección Domiciliaria:</strong> {{ $direccion }}</td>
                </tr>
                <tr>
                    <td><strong>Teléfono/Celular:</strong> {{ $telefono_principal }}</td>
                    <td><strong>Correo Electrónico:</strong> {{ $email_personal }}</td>
                </tr>
                @if($tutor_nombre)
                <tr>
                    <td colspan="2">
                        <div style="margin-top: 10px;">
                            <span style="font-weight: bold; font-size: 10pt;">DATOS DEL APODERADO PRINCIPAL</span>
                            <hr style="border: 0; border-top: 1px dotted #ccc; margin: 3px 0;">
                            <span><strong>Nombre:</strong> {{ $tutor_nombre }}</span><br>
                            <span><strong>Ocupación:</strong> {{ $tutor_ocupacion }}</span>
                        </div>
                    </td>
                </tr>
                @endif
            </table>
        </td>
    </tr>
</table>

<div class="section-title">DATOS ACADÉMICOS</div>

<table class="academic-table">
    <thead>
    <tr>
        <th>Nivel</th>
        <th>Grado</th>
        <th>Grupo</th>
        <th>Periodo Académico</th>
        <th>Unidad Académica</th>
        <th>Turno</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $nivel }}</td>
        <td>{{ $grado }}</td>
        <td>{{ $grupo_nombre }}</td>
        <td>{{ $gestion_nombre }}</td>
        <td>{{ $unidad_academica }}</td>
        <td>{{ $turno }}</td>
    </tr>
    </tbody>
</table>

<div class="section-title">ASIGNATURAS QUE CURSARÁ</div>

<table class="subject-table">
    <thead>
    <tr>
        <th style="width: 5%;">#</th>
        <th style="width: 20%;">Grado</th>
        <th style="width: 75%;">Asignatura que Cursará</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($asignaturas as $index => $asignatura)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $grado }}</td>
        <td class="subject-name">{{ $asignatura }}</td>
    </tr>
    @endforeach
    @for ($i = count($asignaturas); $i < count($asignaturas); $i++)
    <tr>
        <td>{{ $i + 1 }}</td>
        <td></td>
        <td class="subject-name"></td>
    </tr>
    @endfor
    </tbody>
</table>

@if ($tiene_discapacidad)
<div class="section-title" style="margin-top: 30px;">REGISTRO DE DISCAPACIDAD</div>
<p style="font-size: 8pt; margin-bottom: 5px;">
    El estudiante presenta la(s) siguiente(s) discapacidad(es):
    <span style="font-weight: bold;">
        {{ implode(', ', $discapacidades_nombres) }}
    </span>
    . Observaciones: {{ $observaciones_discapacidad }}
</p>
@endif

<table class="signatures-section">
    <tr>
        <td>
            <div class="signature-line"></div>
            <strong>FIRMA ESTUDIANTE</strong><br>
            {{ $nombre_estudiante_completo }}
        </td>
        <td>
            <div class="stamp-box">SELLO UTIC</div>
            Firma Jefe de UTIC
        </td>
        <td>
            <div class="stamp-box">SELLO FINANZAS</div>
            Firma de Recepción
        </td>
    </tr>
</table>

<table class="footer-info">
    <tr>
        <td class="left">
            Fecha de impresión: {{ $fecha_impresion }}
        </td>
        <td class="right">
            Instituto Psicopedagógico EMANUEL MONTESSORI
        </td>
    </tr>
</table>

</body>
</html>
