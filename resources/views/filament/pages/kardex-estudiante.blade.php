<x-filament-panels::page>
    <div class="space-y-8">
        <form wire:submit.prevent>
            {{ $this->form }}
        </form>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
