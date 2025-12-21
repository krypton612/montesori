<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */@layer theme{:root,:host{--font-sans:'Instrument Sans',ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";--spacing:.25rem;--text-sm:.875rem;--text-sm--line-height:calc(1.25/.875);--text-base:1rem;--text-base--line-height: 1.5 ;--text-lg:1.125rem;--text-lg--line-height:calc(1.75/1.125);--text-xl:1.25rem;--text-xl--line-height:calc(1.75/1.25);--text-2xl:1.5rem;--text-2xl--line-height:calc(2/1.5);--text-3xl:1.875rem;--text-3xl--line-height: 1.2 ;--text-4xl:2.25rem;--text-4xl--line-height:calc(2.5/2.25);--font-weight-normal:400;--font-weight-medium:500;--font-weight-semibold:600;--radius-sm:.25rem;--radius-lg:.5rem;--default-transition-duration:.15s;--default-transition-timing-function:cubic-bezier(.4,0,.2,1);--default-font-family:var(--font-sans)}}@layer base{*,:after,:before,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}html,:host{-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;line-height:1.5;font-family:var(--default-font-family,ui-sans-serif,system-ui,sans-serif);font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{line-height:inherit}h1,h2,h3{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}img{vertical-align:middle;display:block;max-width:100%;height:auto}}

        body {
            background: #fff;
            color: #1b1b18;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: #0a0a0a;
                color: #EDEDEC;
            }
        }

        .container {
            max-width: 900px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .header h1 {
            font-size: 3.5rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
            background: linear-gradient(135deg, #1b1b18 0%, #4a4a4a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @media (prefers-color-scheme: dark) {
            .header h1 {
                background: linear-gradient(135deg, #EDEDEC 0%, #A1A09A 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        }

        .header p {
            font-size: 1.125rem;
            color: #706f6c;
        }

        @media (prefers-color-scheme: dark) {
            .header p {
                color: #A1A09A;
            }
        }

        .modules {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .modules {
                grid-template-columns: 1fr;
            }
        }

        .module {
            position: relative;
            background: #fff;
            border: 1px solid #e3e3e0;
            border-radius: 0.75rem;
            padding: 3rem 2rem;
            text-align: center;
            transition: all 0.3s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
            text-decoration: none;
            display: block;
        }

        @media (prefers-color-scheme: dark) {
            .module {
                background: #161615;
                border-color: #3E3E3A;
            }
        }

        .module::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #F8B803, #F0ACB8, #F3BEC7);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .module:hover::before {
            opacity: 1;
        }

        .module:hover {
            transform: translateY(-8px);
            border-color: #1915014a;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        @media (prefers-color-scheme: dark) {
            .module:hover {
                border-color: #62605b;
                box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            }
        }

        .module-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s;
        }

        .module:hover .module-icon {
            transform: scale(1.1);
        }

        .module-icon svg {
            width: 32px;
            height: 32px;
        }

        .module:nth-child(1) .module-icon {
            background: linear-gradient(135deg, #F8B803 0%, #f5a623 100%);
        }

        .module:nth-child(2) .module-icon {
            background: linear-gradient(135deg, #F0ACB8 0%, #e89aaa 100%);
        }

        .module:nth-child(3) .module-icon {
            background: linear-gradient(135deg, #F3BEC7 0%, #ebacb9 100%);
        }

        @media (prefers-color-scheme: dark) {
            .module:nth-child(1) .module-icon {
                background: linear-gradient(135deg, #733000 0%, #5a2600 100%);
            }

            .module:nth-child(2) .module-icon {
                background: linear-gradient(135deg, #391800 0%, #2d1300 100%);
            }

            .module:nth-child(3) .module-icon {
                background: linear-gradient(135deg, #4B0600 0%, #3a0500 100%);
            }
        }

        .module h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1b1b18;
        }

        @media (prefers-color-scheme: dark) {
            .module h2 {
                color: #EDEDEC;
            }
        }

        .module p {
            font-size: 0.875rem;
            color: #706f6c;
            line-height: 1.5;
        }

        @media (prefers-color-scheme: dark) {
            .module p {
                color: #A1A09A;
            }
        }

        .module-arrow {
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 24px;
            height: 24px;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s;
        }

        .module:hover .module-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        .decorative-line {
            position: fixed;
            pointer-events: none;
            opacity: 0.1;
        }

        .line-1 {
            top: 10%;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, #F8B803, transparent);
        }

        .line-2 {
            top: 50%;
            right: 0;
            width: 1px;
            height: 40%;
            background: linear-gradient(180deg, transparent, #F0ACB8, transparent);
        }

        .line-3 {
            bottom: 15%;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, #F3BEC7, transparent);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            animation: fadeIn 0.8s ease-out;
        }
    </style>
</head>
<body>
<div class="decorative-line line-1"></div>
<div class="decorative-line line-2"></div>
<div class="decorative-line line-3"></div>

<div class="container">
    <div class="header">
        <h1>{{ config('app.name', 'Laravel') }}</h1>
        <p>Sistema de Gestión Institucional</p>
    </div>

    <div class="modules">
        <a href="{{ url('/inscripcion') }}" class="module">
            <div class="module-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h2>Inscripción</h2>
            <p>Gestión completa de matrículas, admisiones y registro de estudiantes</p>
            <svg class="module-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>

        <a href="{{ url('/finanzas') }}" class="module">
            <div class="module-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2>Finanzas</h2>
            <p>Control de pagos, facturación electrónica y reportes contables</p>
            <svg class="module-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>

        <a href="{{ url('/informatica') }}" class="module">
            <div class="module-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2>Informática</h2>
            <p>Administración del sistema, usuarios y configuración técnica</p>
            <svg class="module-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>
    </div>
</div>
</body>
</html>
