{{-- resources/views/components/footer-simple.blade.php --}}
<footer class="bg-gray-50 border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            {{-- Información básica --}}
            <div class="text-sm text-gray-600">
                <span class="font-medium">{{ config('app.name', 'Sistema de Inscripciones') }}</span>
                &copy; {{ date('Y') }}
                @if($mostrarVersion ?? false)
                <span class="mx-2">•</span>
                <span class="text-gray-500">v{{ config('app.version', '1.0.0') }}</span>
                @endif
            </div>

            {{-- Enlaces rápidos --}}
            @if($enlacesRapidos ?? false)
            <div class="mt-2 md:mt-0 flex space-x-4">
                @foreach($enlacesRapidos as $enlace)
                <a
                    href="{{ $enlace['url'] }}"
                    class="text-xs text-gray-500 hover:text-primary-600 hover:underline transition-colors"
                    @if($enlace['nueva_ventana'] ?? false) target="_blank" @endif
                >
                {{ $enlace['texto'] }}
                </a>
                @endforeach
            </div>
            @endif

            {{-- Estado del sistema --}}
            <div class="mt-2 md:mt-0">
                <span class="inline-flex items-center text-xs text-gray-500">
                    @if($sistemaActivo ?? true)
                        <span class="flex h-2 w-2 relative mr-1">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        Sistema activo
                    @else
                        <span class="flex h-2 w-2 relative mr-1">
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                        Mantenimiento
                    @endif
                </span>
            </div>
        </div>

        {{-- Información adicional --}}
        @if($informacionAdicional ?? false)
        <div class="mt-2 pt-2 border-t border-gray-100 text-xs text-gray-500 text-center md:text-left">
            {{ $informacionAdicional }}
        </div>
        @endif
    </div>
</footer>
