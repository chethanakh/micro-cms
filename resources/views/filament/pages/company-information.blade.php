<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div>
            <x-filament::button type="submit">
                Save Changes
            </x-filament::button>
        </div>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
