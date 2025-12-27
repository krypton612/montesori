<x-filament-panels::page>

    <div x-data="{ activeTab: '{{ request()->query('activeTab', 'all') }}' }">

        <x-filament::tabs>
            <x-filament::tabs.item
                alpine-active="activeTab === 'all'"
                x-on:click="activeTab = 'all'"
            >
                Todos los Grupos
            </x-filament::tabs.item>

            <x-filament::tabs.item
                alpine-active="activeTab === 'active'"
                x-on:click="activeTab = 'active'"
            >
                Grupos Activos
            </x-filament::tabs.item>

            <x-filament::tabs.item
                alpine-active="activeTab === 'inactive'"
                x-on:click="activeTab = 'inactive'"
            >
                Grupos Inactivos
            </x-filament::tabs.item>
        </x-filament::tabs>
        <br>
        <div class="mt-6">
            {{ $this->table }}
        </div>

    </div>

</x-filament-panels::page>