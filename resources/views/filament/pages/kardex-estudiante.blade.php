<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent>
            {{ $this->form }}
        </form>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
