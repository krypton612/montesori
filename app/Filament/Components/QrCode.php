<?php

namespace App\Filament\Components;

use Filament\Schemas\Components\Component;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasExtraAttributes;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeFacade;

class QrCode extends Component
{
    use HasExtraAttributes;
    use HasAlignment;

    protected string $view = 'filament.components.qr-code';

    protected string | \Closure | null $data = null;
    protected int | \Closure $size = 200;
    protected string | \Closure $format = 'svg';
    protected string | \Closure | null $label = null;
    protected string | \Closure | null $description = null;
    protected string | \Closure $backgroundColor = '#FFFFFF';
    protected string | \Closure $color = '#000000';
    protected int | \Closure $margin = 4;
    protected string | \Closure $errorCorrection = 'H'; // L, M, Q, H
    protected string | \Closure $style = 'square'; // square, dot, round
    protected string | \Closure $eyeStyle = 'square'; // square, circle
    protected string | \Closure $encoding = 'UTF-8';

    public static function make(string | \Closure $data): static
    {
        $static = app(static::class, []);
        $static->configure();
        $static->data($data);

        return $static;
    }

    /**
     * Establece los datos que se codificarán en el código QR
     */
    public function data(string | \Closure $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Establece el tamaño del código QR
     */
    public function size(int | \Closure $size): static
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Establece el formato de salida (svg, png, eps)
     */
    public function format(string | \Closure $format): static
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Establece el color de fondo
     */
    public function backgroundColor(string | \Closure $color): static
    {
        $this->backgroundColor = $color;
        return $this;
    }

    /**
     * Establece el color del código QR
     */
    public function color(string | \Closure $color): static
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Establece el margen
     */
    public function margin(int | \Closure $margin): static
    {
        $this->margin = $margin;
        return $this;
    }

    /**
     * Establece la corrección de errores
     */
    public function errorCorrection(string | \Closure $level): static
    {
        $this->errorCorrection = $level;
        return $this;
    }

    /**
     * Establece el estilo de los puntos
     */
    public function style(string | \Closure $style): static
    {
        $this->style = $style;
        return $this;
    }

    /**
     * Establece el estilo de los ojos (esquinas)
     */
    public function eyeStyle(string | \Closure $eyeStyle): static
    {
        $this->eyeStyle = $eyeStyle;
        return $this;
    }

    /**
     * Establece la codificación
     */
    public function encoding(string | \Closure $encoding): static
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Establece una etiqueta para el componente
     */
    public function label(string | \Closure | null $label): static
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Establece una descripción para el componente
     */
    public function description(string | \Closure | null $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Obtiene los datos evaluados
     */
    public function getData(): ?string
    {
        return $this->evaluate($this->data);
    }

    /**
     * Obtiene el tamaño evaluado
     */
    public function getSize(): int
    {
        return $this->evaluate($this->size);
    }

    /**
     * Obtiene el formato evaluado
     */
    public function getFormat(): string
    {
        return $this->evaluate($this->format);
    }

    /**
     * Obtiene el color de fondo evaluado
     */
    public function getBackgroundColor(): string
    {
        return $this->evaluate($this->backgroundColor);
    }

    /**
     * Obtiene el color del código evaluado
     */
    public function getColor(): string
    {
        return $this->evaluate($this->color);
    }

    /**
     * Obtiene el margen evaluado
     */
    public function getMargin(): int
    {
        return $this->evaluate($this->margin);
    }

    /**
     * Obtiene el nivel de corrección de errores evaluado
     */
    public function getErrorCorrection(): string
    {
        return $this->evaluate($this->errorCorrection);
    }

    /**
     * Obtiene el estilo evaluado
     */
    public function getStyle(): string
    {
        return $this->evaluate($this->style);
    }

    /**
     * Obtiene el estilo de ojos evaluado
     */
    public function getEyeStyle(): string
    {
        return $this->evaluate($this->eyeStyle);
    }

    /**
     * Obtiene la codificación evaluada
     */
    public function getEncoding(): string
    {
        return $this->evaluate($this->encoding);
    }

    /**
     * Obtiene la etiqueta evaluada
     */
    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }

    /**
     * Obtiene la descripción evaluada
     */
    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }

    /**
     * Genera el código QR usando Simple QrCode
     */
    public function generateQrCode(): string
    {
        $data = $this->getData();

        if (!$data) {
            return '';
        }

        try {
            $qrCode = QrCodeFacade::encoding($this->getEncoding())
                ->size($this->getSize())
                ->margin($this->getMargin())
                ->errorCorrection($this->getErrorCorrection());

            // Aplicar estilo si está disponible
            if (method_exists($qrCode, 'style')) {
                $qrCode->style($this->getStyle());
            }

            // Aplicar estilo de ojos si está disponible
            if (method_exists($qrCode, 'eye')) {
                $qrCode->eye($this->getEyeStyle());
            }

            $format = $this->getFormat();

            if ($format === 'svg') {
                return $qrCode->format('svg')->generate($data);
            } elseif ($format === 'png') {
                $image = $qrCode->format('png')->generate($data);
                return 'data:image/png;base64,' . base64_encode($image);
            } elseif ($format === 'eps') {
                $image = $qrCode->format('eps')->generate($data);
                return 'data:application/postscript;base64,' . base64_encode($image);
            } else {
                // Por defecto SVG
                return $qrCode->format('svg')->generate($data);
            }

        } catch (\Exception $e) {
            \Log::error('Error generating QR code: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Convierte color HEX a RGB para Simple QrCode
     * Simple QrCode espera un array [R, G, B]
     */
    protected function hexToRgb(string $hex): array
    {
        // Remove #
        $hex = ltrim($hex, '#');

        // Convert to RGB
        if (strlen($hex) == 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return [$r, $g, $b];
    }

    /**
     * Método helper para generar URL de datos directamente
     */
    public function getDataUrl(): string
    {
        return $this->generateQrCode();
    }

    /**
     * Método helper para descargar el QR
     */
    public function download(string $filename = 'qrcode.png')
    {
        $data = $this->getData();
        $format = $this->getFormat();

        return QrCodeFacade::size($this->getSize())
            ->format($format)
            ->margin($this->getMargin())
            ->errorCorrection($this->getErrorCorrection())
            ->color($this->hexToRgb($this->getColor()))
            ->backgroundColor($this->hexToRgb($this->getBackgroundColor()))
            ->generate($data);
    }
}
