<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>KARDEX DEL ESTUDIANTE</title>

    <style>
        /* Reservamos espacio abajo para firmas */
        @page { margin: 1.2cm 1.2cm 5.8cm 1.2cm; }

        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.25;
            color: #1a1a1a;
            margin: 0;
        }

        /* Marca de agua */
        .watermark {
            position: fixed;
            inset: 0;
            z-index: -1000;
            opacity: 0.10;
            pointer-events: none;
        }
        .watermark img {
            width: 70%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Header */
        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header td { vertical-align: middle; }
        .logo { width: 22%; }
        .title { width: 56%; text-align: center; }
        .qr { width: 22%; text-align: right; }

        .title h1 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
        }
        .title h2 {
            margin: 3px 0 0 0;
            font-size: 9pt;
            font-weight: normal;
        }

        .section-title {
            font-size: 10pt;
            font-weight: bold;
            margin: 10px 0 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #333;
        }

        /* Bloque estudiante */
        .student-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #333;
            margin-bottom: 8px;
        }
        .student-table td {
            padding: 6px 8px;
            vertical-align: top;
        }
        .student-photo {
            width: 18%;
            border-right: 1px solid #333;
            text-align: center;
        }
        .photo-box {
            width: 95px;
            height: 115px;
            border: 1px solid #333;
            margin: 0 auto 6px auto;
            line-height: 115px;
            font-size: 8pt;
        }

        .kv {
            width: 100%;
            border-collapse: collapse;
        }
        .kv td { padding: 2px 0; }
        .label { font-weight: bold; }

        /* Tabla simple */
        table.simple {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        table.simple th,
        table.simple td {
            border: 1px solid #333;
            padding: 5px;
        }
        table.simple th {
            background: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }

        /* Firmas UTIC (igual estilo del ejemplo) */
        .footer-signatures {
            position: fixed;
            left: 1.2cm;
            right: 1.2cm;
            bottom: 0.9cm;
        }
        .sign-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sign-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            border: none;
            padding-top: 55px; /* espacio para firmar (como tu ejemplo) */
        }
        .sign-line {
            border-top: 1px solid #111;
            width: 80%;
            margin: 0 auto 5px auto;
        }
        .stamp-box {
            height: 50px;
            line-height: 50px;
            border: 1px dashed #111;
            width: 80%;
            margin: 0 auto 6px auto;
            font-size: 8pt;
        }
        .footer-meta {
            margin-top: 8px;
            font-size: 7.5pt;
            width: 100%;
        }
        .footer-meta .left { float: left; }
        .footer-meta .right { float: right; }
        .clear { clear: both; }
    </style>
</head>

<body>

@if(!empty($logo_path))
<div class="watermark">
    <img src="{{ $logo_path }}" alt="Marca de Agua">
</div>
@endif

<table class="header">
    <tr>
        <td class="logo">
            @if(!empty($logo_path))
                <img src="{{ $logo_path }}" alt="Logo" style="width: 95px; height: auto;">
            @endif
        </td>

        <td class="title">
            <h1>KARDEX DEL ESTUDIANTE</h1>
            <h2>Datos personales y condición de discapacidad</h2>
        </td>

        <td class="qr">
            <strong>{{ $codigo_saga ?? '—' }}</strong>
        </td>
    </tr>
</table>

<div class="section-title">DATOS DEL ESTUDIANTE</div>

<table class="student-table">
    <tr>
        <td class="student-photo">
            <div class="photo-box">
                @if(!empty($foto_url))
                    <img src="{{ $foto_url }}" alt="Foto" style="width: 95px; height: 115px; object-fit: cover;">
                @else
                    FOTO
                @endif
            </div>
            <div style="font-weight: bold;">ESTUDIANTE</div>
        </td>

        <td class="student-data">
            <table class="kv">
                <tr>
                    <td colspan="2"><span class="label">Nombre:</span> {{ $estudiante_nombre ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="width: 50%;"><span class="label">CI:</span> {{ $estudiante_ci ?? '—' }}</td>
                    <td style="width: 50%;"><span class="label">Código SAGA:</span> {{ $codigo_saga ?? '—' }}</td>
                </tr>
                <tr>
                    <td><span class="label">Edad:</span> {{ $estudiante_edad ?? '—' }}</td>
                    <td><span class="label">Fecha Nac.:</span> {{ $estudiante_fn ?? '—' }}</td>
                </tr>
                <tr>
                    <td><span class="label">Teléfono:</span> {{ $estudiante_tel ?? '—' }}</td>
                    <td><span class="label">Email:</span> {{ $estudiante_email ?? '—' }}</td>
                </tr>
                <tr>
                    <td colspan="2"><span class="label">Dirección:</span> {{ $estudiante_dir ?? '—' }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="section-title">DISCAPACIDAD</div>

<table class="simple">
    <thead>
        <tr>
            <th>Registro</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align:center;">
                {{ $discapacidades ?? 'No registra discapacidad' }}
            </td>
        </tr>
    </tbody>
</table>

{{-- FIRMAS SIEMPRE ABAJO --}}
<div class="footer-signatures">
    <table class="sign-table">
        <tr>
            <td>
                <div class="sign-line"></div>
                <strong>FIRMA RESPONSABLE</strong><br>
                (Encargado/a)
            </td>

            <td>
                <div class="sign-line"></div>
                <strong>FIRMA UTIC</strong><br>
                (Unidad de Tecnologías)
                <div class="stamp-box" style="margin-top:8px;">SELLO UTIC</div>
            </td>

            <td>
                <div class="sign-line"></div>
                <strong>FIRMA</strong><br>
                (Conformidad)
            </td>
        </tr>
    </table>

    <div class="footer-meta">
        <div class="left">Fecha de impresión: {{ $fecha_impresion ?? '—' }}</div>
        <div class="right">{{ $institucion ?? 'EMANUEL MONTESSORI' }}</div>
        <div class="clear"></div>
    </div>
</div>

</body>
</html>
