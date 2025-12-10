@php
$label = $getLabel();
$description = $getDescription();
$qrCode = $generateQrCode();
$alignment = $getAlignment();
@endphp

<div
    {{ $attributes->merge($getExtraAttributes())->class([
    'filament-qr-code-component',
    'flex flex-col gap-2',
    match ($alignment) {
    'left', 'start' => 'items-start',
    'center' => 'items-center',
    'right', 'end' => 'items-end',
    default => 'items-start',
    }
    ]) }}
    >
    @if($label)
    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }}
    </div>
    @endif

    @if($qrCode)
    <div class="qr-code-container bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        @if($getFormat() === 'svg')
        <div class="flex flex-col items-center">
            <div id="qr-svg-wrapper">{!! $qrCode !!}</div>
            <button
                type="button"
                onclick="downloadPngQr()"
                class="mt-2 px-3 py-1 text-sm rounded bg-primary-600 text-white hover:bg-primary-700"
            >
                Descargar
            </button>
        </div>

        <script>
            function getRandomInt(min, max) {
                min = Math.ceil(min);
                max = Math.floor(max);
                return Math.floor(Math.random() * (max - min + 1)) + min;
            }
            function downloadPngQr() {
                const wrapper = document.getElementById('qr-svg-wrapper');
                if (!wrapper) return;

                const svgElement = wrapper.querySelector('svg');
                if (!svgElement) return;

                const svgData = new XMLSerializer().serializeToString(svgElement);
                const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
                const url = URL.createObjectURL(svgBlob);

                const img = new Image();

                const randomInteger = getRandomInt(10000, 20000);


                img.onload = function () {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);

                    canvas.toBlob(function (blob) {
                        const pngUrl = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = pngUrl;
                        link.download = 'code-' + randomInteger + '-qr.png';
                        link.click();
                        URL.revokeObjectURL(pngUrl);
                    }, 'image/png');

                    URL.revokeObjectURL(url);
                };

                img.src = url;
            }
        </script>
        @else
        <img src="{{ $qrCode }}" alt="QR Code" class="max-w-full h-auto">
        @endif
    </div>
    @else
    <div class="text-sm text-gray-500 dark:text-gray-400">
        No hay datos para generar el código QR
    </div>
    @endif

    @if($description)
    <div class="text-xs text-gray-500 dark:text-gray-400">
        {{ $description }}
    </div>
    @endif
</div>
