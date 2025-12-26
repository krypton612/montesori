<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>KARDEX DEL ESTUDIANTE</title>

    <style>
        @page { margin: 1.2cm 1.2cm 5.5cm 1.2cm; }

        body {
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.3;
            color: #1a1a1a;
            margin: 0;
        }

        /* ========= MARCA DE AGUA ========= */
        .watermark {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1000;
            opacity: 0.10;
            pointer-events: none;
        }
        .watermark img {
            width: 70%;
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }

        /* ========= HEADER ========= */
        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header td { vertical-align: middle; }
        .logo { width: 25%; }
        .title { width: 50%; text-align: center; }
        .photo { width: 25%; text-align: right; }

        .title h1 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
        }

        .title h2 {
            margin: 3px 0 0 0;
            font-size: 9pt;
            font-weight: normal;
        }

        .photo-box {
            width: 95px;
            height: 115px;
            border: 1px solid #333;
            margin-left: auto;
            text-align: center;
            line-height: 115px;
            font-size: 8pt;
        }

        /* ========= SECCIONES ========= */
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            margin: 10px 0 6px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #333;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #333;
            margin-bottom: 8px;
        }

        table.data td, table.data th {
            padding: 6px 8px;
            border: 1px solid #333;
            vertical-align: top;
        }

        table.data th {
            background: #f0f0f0;
            width: 28%;
            text-align: left;
        }

        /* ========= FOOTER FIRMAS ========= */
        .footer-signatures {
            position: fixed;
            left: 1.2cm;
            right: 1.2cm;
            bottom: 0.8cm;
        }

        .sign-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sign-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding-top: 35px;
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

{{-- ===== MARCA DE AGUA ===== --}}
@if(!empty($logo_path))
<div class="watermark">
    <img src="{{ $logo_path }}" alt="Marca de Agua">
</div>
@endif

{{-- ===== HEADER ===== --}}
<table class="header">
    <tr>
        <td class="logo">
            @if(!empty($logo_path))
                <img src="{{ $logo_path }}" style="width: 95px;">
            @endif
        </td>
        <td class="title">
            <h1>{{ $institucion }}</h1>
            <h2>KARDEX DEL ESTUDIANTE</h2>
        </td>
        <td class="photo">
            <div class="photo-box">
                @if(!empty($foto_url))
                    <img src="{{ $foto_url }}" style="width: 95px; height: 115px; object-fit: cover;">
                @else
                    FOTO
                @endif
            </div>
        </td>
    </tr>
</table>

{{-- ===== DATOS PERSONALES ===== --}}
<div class="section-title">I. DATOS PERSONALES</div>

<table class="data">
    <tr><th>Nombre Completo</th><td>{{ $estudiante_nombre }}</td></tr>
    <tr><th>Cédula de Identidad</th><td>{{ $estudiante_ci }}</td></tr>
    <tr><th>Código SAGA</th><td>{{ $codigo_saga }}</td></tr>
    <tr><th>Fecha de Nacimiento</th><td>{{ $estudiante_fn }}</td></tr>
    <tr><th>Edad</th><td>{{ $estudiante_edad }} años</td></tr>
    <tr><th>Teléfono</th><td>{{ $estudiante_tel }}</td></tr>
    <tr><th>Email</th><td>{{ $estudiante_email }}</td></tr>
    <tr><th>Dirección</th><td>{{ $estudiante_dir }}</td></tr>
</table>

{{-- ===== DISCAPACIDAD ===== --}}
<div class="section-title">II. INFORMACIÓN DE DISCAPACIDAD</div>

@if(!$tiene_discapacidad)
    <table class="data">
        <tr>
            <td>No registra discapacidad.</td>
        </tr>
    </table>
@else
    <table class="data">
        <thead>
            <tr>
                <th style="width:25%;">Discapacidad</th>
                <th style="width:30%;">Descripción</th>
                <th style="width:25%;">Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($discapacidades as $d)
                <tr>
                    <td>{{ $d['nombre'] }}</td>
                    <td>{{ $d['descripcion'] ?? '—' }}</td>
                    <td>{{ $d['observacion'] ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

{{-- ===== OBSERVACIONES ===== --}}
<div class="section-title">III. OBSERVACIONES</div>
<table class="data">
    <tr><td style="height:60px;"></td></tr>
</table>

{{-- ===== FIRMAS ===== --}}
<div class="footer-signatures">
    <table class="sign-table">
        <tr>
            <td>
                <div class="sign-line"></div>
                <strong>FIRMA RESPONSABLE</strong><br>
                Unidad Administrativa
            </td>
            <td>
                <div class="stamp-box">SELLO UTIC</div>
                Firma / Sello
            </td>
            <td>
                <div class="sign-line"></div>
                <strong>FIRMA APODERADO</strong><br>

            </td>
        </tr>
    </table>

    <div class="footer-meta">
        <div class="left">Fecha de impresión: {{ $fecha_impresion }}</div>
        <div class="right">{{ $institucion }}</div>
        <div class="clear"></div>
    </div>
</div>

</body>
</html>
