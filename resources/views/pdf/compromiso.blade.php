<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        /* Configuración de página */
        @page { margin: 2cm 2cm; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5pt; /* Fuente pequeña y formal */
            line-height: 1.4;
            color: #1a1a1a;
            margin: 0;
        }

        /* Encabezados */
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 {
            font-size: 13pt;
            text-decoration: underline;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header h2 { font-size: 11pt; margin-top: 0; font-weight: normal; }

        /* Párrafos y Listas */
        p { text-align: justify; margin-bottom: 10px; }
        .content-list { margin-left: 0; padding-left: 15px; }
        .content-list li { margin-bottom: 8px; text-align: justify; }

        .content-list-ul { margin-left: 20px; padding-left: 15px; margin-top: 10px; }
        .content-list-ul li { margin-bottom: 5px; }

        /* Tabla de Cuotas */
        .table-container { margin: 15px auto; width: 90%; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        table td, table th {
            border: 0.5pt solid #000;
            padding: 5px 10px;
        }
        .bg-gray { background-color: #f2f2f2; font-weight: bold; }

        /* Sección de Firmas corregida con tabla */
        .signature-table {
            width: 100%;
            margin-top: 60px;
            border: none;
        }
        .signature-table td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding-top: 40px;
        }
        .signature-line {
            border-top: 1pt solid black;
            width: 80%;
            margin: 0 auto 5px auto;
        }
        .fingerprint {
            border: 1pt solid #000;
            width: 100px;
            height: 100px;
            margin: 10px auto;
        }
        .watermark {
            position: fixed; /* Ojo: Dompdf soporta 'fixed' para elementos en cada página */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1000; /* Asegura que esté detrás del contenido */
            opacity: 0.15; /* AJUSTA ESTO: 0.15 es semi-transparente */
            pointer-events: none; /* Deshabilita interacciones (aunque irrelevante en PDF) */
        }

        .watermark img {
            width: 70%; /* AJUSTA ESTO: Tamaño de la imagen de fondo */
            height: auto;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* Centra la imagen */
        }

        .date-section { margin-top: 30px; }
        strong { font-weight: bold; }

    </style>
</head>
<body>
<div class="watermark">
    <img src="{{ $logo_path }}" alt="Marca de Agua">
</div>

<div class="header">
    <h1>Documento de Compromiso con {{ $empresa }}</h1>
    <h2>(Estudiantes Nuevos)</h2>
</div>

<p>
    @if($edad < 18)
    Nosotros por una parte: el Padre o Tutor <strong>{{ $tutor_nombre }}</strong>, mayor de edad, hábil por derecho, de profesión u ocupación: <strong>{{ $tutor_ocupacion }}</strong>,
    <br>con domicilio en <strong>{{ $tutor_domicilio }}</strong>; y el estudiante <strong>{{ $estudiante_nombre }}</strong>, menor de edad ({{ $edad }} años), hábil por derecho, estudiante del colegio <strong>{{ $empresa }}</strong>, de nuestra propia voluntad y sin que medie vicio alguno del consentimiento, manifestamos lo siguiente:
    @else
    Nosotros por una parte: el estudiante <strong>{{ $estudiante_nombre }}</strong>, mayor de edad ({{ $edad }} años), hábil por derecho, estudiante del colegio <strong>{{ $empresa }}</strong>, de nuestra propia voluntad y sin que medie vicio alguno del consentimiento, manifestamos lo siguiente:
    @endif
</p>


<ol class="content-list">
    <li>Es de nuestro conocimiento que el pago de cuotas se efectúa bajo la modalidad de mes anticipado.</li>
    <li>Es nuestra obligación cancelar de manera pronta y oportuna las mensualidades de acuerdo al siguiente cronograma:</li>
</ol>

<div class="table-container">
    <table>
        <thead>
        <tr class="bg-gray">
            <th>Concepto</th>
            <th>Monto</th>
            <th>Fecha Límite</th>
        </tr>
        </thead>
        <tbody>
        <tr><td>1º Cuota, Matrícula, Seguro Médico, Ext. Univ.</td><td>Bs. {{ $cuota1 }}</td><td>Hasta el {{ $fecha1 }}</td></tr>
        <tr><td>2º Cuota</td><td>Bs. {{ $cuota2 }}</td><td>Hasta el {{ $fecha2 }}</td></tr>
        <tr><td>3º Cuota</td><td>Bs. {{ $cuota3 }}</td><td>Hasta el {{ $fecha3 }}</td></tr>
        <tr><td>4º Cuota</td><td>Bs. {{ $cuota4 }}</td><td>Hasta el {{ $fecha4 }}</td></tr>
        <tr><td>5º Cuota</td><td>Bs. {{ $cuota5 }}</td><td>Hasta el {{ $fecha5 }}</td></tr>
        <tr><td>6º Cuota</td><td>Bs. {{ $cuota5 }}</td><td>Hasta el {{ $fecha5 }}</td></tr>
        <tr><td>7º Cuota</td><td>Bs. {{ $cuota5 }}</td><td>Hasta el {{ $fecha5 }}</td></tr>
        <tr><td>8º Cuota</td><td>Bs. {{ $cuota5 }}</td><td>Hasta el {{ $fecha5 }}</td></tr>
        <tr><td>9º Cuota</td><td>Bs. {{ $cuota5 }}</td><td>Hasta el {{ $fecha5 }}</td></tr>
        <tr><td>10º Cuota</td><td>Bs. {{ $cuota5 }}</td><td>Hasta el {{ $fecha5 }}</td></tr>
        <tr><td>11º Cuota</td><td>Bs. {{ $cuota5 }}</td><td>Hasta el {{ $fecha5 }}</td></tr>
        <tr><td>12º Cuota</td><td>Bs. {{ $cuota5 }}</td><td>Hasta el {{ $fecha5 }}</td></tr>
        <tr class="bg-gray">
            <td style="text-align: right;">TOTAL A CANCELAR:</td>
            <td>Bs. {{ $total }}</td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>

<ol class="content-list" start="3">
    <li>Contamos con 10 días calendario para regularizar el pago de una mensualidad adeudada. En caso de incumplimiento, aceptamos la sanción del Art. 22 del Reglamento de Cobranzas REG-10: <em>“El estudiante que no cumpla con el plazo de 10 días para regularizar sus cuotas atrasadas, perderá inmediatamente su condición de Estudiante Regular de la {{ $abreviacion }}”</em></li>
    <li>Aceptamos que las causales de separación sin derecho a reincorporación incluyen:
        <ul style="list-style-type: disc;">

            <ol class="content-list-ul" type="a">
                <li>Sustracción o hurto de bienes institucionales o personales.</li>
                <li>Fraude académico (copia en exámenes) o suplantación de identidad.</li>
                <li>Falsificación de firmas o tenencia/consumo de alcohol y sustancias controladas.</li>
                <li>Insubordinación grave o actos de indisciplina reiterados.</li>
                <li>Conducta inmoral o contraria a las buenas costumbres.</li>
                <li>Incumplimiento reiterado de obligaciones financieras.</li>
            </ol>
        </ul>
    </li>
    <li>La nota mínima de aprobación es de <strong>5.10</strong> para asignaturas técnicas y <strong>7.10</strong> para estudiantes de psicopedagogica.</li>
</ol>

<p class="date-section">
    Lugar y fecha: ______________________, _____ de _________________ de {{ $anio }}
</p>

<table class="signature-table">
    <tr>
        <td>
            <div class="signature-line"></div>
            <strong>FIRMA PADRE O TUTOR</strong><br>
            {{ $tutor_nombre }}<br>
            <div class="fingerprint"></div>
            <small>Impresión Digital</small>
        </td>
        <td>
            <div class="signature-line"></div>
            <strong>FIRMA ESTUDIANTE</strong><br>
            {{ $estudiante_nombre }}<br>
            <div class="fingerprint"></div>
            <small>Impresión Digital</small>
        </td>
    </tr>
</table>

</body>
</html>
