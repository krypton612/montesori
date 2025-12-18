<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Kardex + Apoderados (Detallado)</title>
    <style>
        /* Reservamos bastante espacio abajo para que las firmas queden más abajo (sin chocar con contenido) */
        @page { margin: 1.2cm 1.2cm 4.2cm 1.2cm; }

        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.25;
            color: #1a1a1a;
            margin: 0;
        }

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
            height: auto;
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Header */
        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header td { vertical-align: middle; }
        .header .logo { width: 22%; text-align: left; }
        .header .title { width: 56%; text-align: center; }
        .header .qr { width: 22%; text-align: right; }

        .title h1 { margin: 0; font-size: 12pt; font-weight: bold; }
        .title h2 { margin: 3px 0 0 0; font-size: 9pt; font-weight: normal; }

        .section-title {
            font-size: 10pt;
            font-weight: bold;
            margin: 10px 0 6px 0;
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
            border: 0;
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
            text-align: center;
            line-height: 115px;
            font-size: 8pt;
        }
        .student-data {
            width: 82%;
        }
        .kv {
            width: 100%;
            border-collapse: collapse;
        }
        .kv td {
            padding: 2px 0;
        }
        .label { font-weight: bold; }

        /* Tabla apoderados */
        table.apoderados {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.4pt;
        }
        table.apoderados th, table.apoderados td {
            border: 1px solid #333;
            padding: 4px 4px;
            vertical-align: top;
        }
        table.apoderados th {
            background: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }
        .t-left { text-align: left; }
        .t-center { text-align: center; }
        .wrap { word-wrap: break-word; white-space: normal; }

        /* Footer firmas - fijo abajo */
        .footer-signatures {
            position: fixed;
            left: 1.2cm;
            right: 1.2cm;
            bottom: 0.9cm; /* MÁS ABAJO */
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
            padding-top: 32px; /* espacio para firmar */
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
            <h1>KARDEX + APODERADOS (DETALLADO)</h1>
            <h2>Registro de apoderados, relación y datos de contacto</h2>
        </td>
        <td class="qr">
            @php
                // Si ya tienes el paquete de QR como en tu hoja de inscripción
                $qr = $qr_value ?? ($codigo_saga ?? ('EST-' . ($estudiante_id ?? '')));
            @endphp

            @if(class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class))
                <img
                    src="data:image/png;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(85)->generate($qr)) }}"
                    alt="QR"
                    style="width: 85px; height: 85px;"
                >
                <div style="font-size: 7pt; margin-top: 2px;">
                    QR: {{ $qr }}
                </div>
            @else
                <div style="font-size: 8pt;">
                    QR no disponible
                </div>
            @endif
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
                    <td><span class="label">Edad:</span> {{ isset($estudiante_edad) ? $estudiante_edad : '—' }}</td>
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

<div class="section-title">APODERADOS REGISTRADOS</div>

<table class="apoderados">
    <thead>
        <tr>
            <th style="width: 3%;">#</th>
            <th style="width: 14%;">Apoderado</th>
            <th style="width: 7%;">CI</th>
            <th style="width: 7%;">Parentesco</th>
            <th style="width: 4%;">Principal</th>
            <th style="width: 4%;">Vive</th>
            <th style="width: 7%;">Tel. 1</th>
            <th style="width: 7%;">Tel. 2</th>
            <th style="width: 10%;">Email</th>
            <th style="width: 15%;">Dirección</th>
            <th style="width: 8%;">Ocupación</th>
            <th style="width: 8%;">Empresa / Cargo</th>
            <th style="width: 8%;">Niv. Edu.</th>
            <th style="width: 8%;">Estado Civil</th>
        </tr>
    </thead>
    <tbody>
        @forelse(($apoderados ?? []) as $i => $ap)
            @php
                $p = $ap->persona;
                $nombreAp = $p ? trim(($p->nombre ?? '').' '.($p->apellido_pat ?? '').' '.($p->apellido_mat ?? '')) : 'Sin persona';
                $ciAp = $p->carnet_identidad ?? '—';
                $tel1 = $p->telefono_principal ?? '—';
                $tel2 = $p->telefono_secundario ?? '—';
                $email = $p->email_personal ?? '—';
                $dir = $p->direccion ?? '—';

                $pivot = $ap->pivot ?? null;
                $parentesco = $pivot->parentestco ?? '—';
                $vive = isset($pivot->vive_con_el) ? ($pivot->vive_con_el ? 'Sí' : 'No') : '—';
                $principal = isset($pivot->es_principal) ? ($pivot->es_principal ? 'Sí' : 'No') : '—';

                $ocup = $ap->ocupacion ?? '—';
                $empresaCargo = trim(($ap->empresa ?? '—') . ' / ' . ($ap->cargo_empresa ?? '—'));
                $nivelEdu = $ap->nivel_educacion ?? '—';
                $estadoCivil = $ap->estado_civil ?? '—';
            @endphp
            <tr>
                <td class="t-center">{{ $i + 1 }}</td>
                <td class="t-left wrap">{{ $nombreAp }}</td>
                <td class="t-center">{{ $ciAp }}</td>
                <td class="t-center wrap">{{ $parentesco }}</td>
                <td class="t-center">{{ $principal }}</td>
                <td class="t-center">{{ $vive }}</td>
                <td class="t-center">{{ $tel1 }}</td>
                <td class="t-center">{{ $tel2 }}</td>
                <td class="t-left wrap">{{ $email }}</td>
                <td class="t-left wrap">{{ $dir }}</td>
                <td class="t-left wrap">{{ $ocup }}</td>
                <td class="t-left wrap">{{ $empresaCargo }}</td>
                <td class="t-left wrap">{{ $nivelEdu }}</td>
                <td class="t-left wrap">{{ $estadoCivil }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="14" class="t-center">El estudiante no tiene apoderados registrados.</td>
            </tr>
        @endforelse
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
                <div class="stamp-box">SELLO UTIC</div>
                Firma / Sello
            </td>
            <td>
                <div class="sign-line"></div>
                <strong>FIRMA APODERADO PRINCIPAL</strong><br>
                {{ $apoderado_principal_nombre ?? '____________________________' }}
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
