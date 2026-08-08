{{-- Regie · Dashboard --}}
<x-ui-page>
    <x-slot name="navbar">
        <x-ui-page-navbar title="Regie" />
    </x-slot>

    <x-slot name="actionbar">
        <x-ui-page-actionbar :breadcrumbs="[['label' => 'Regie', 'icon' => 'film']]">
            <x-nx-button variant="primary" size="sm" :href="route('tour.tours.index')" wire:navigate>
                @svg('heroicon-o-film', 'w-4 h-4') <span>Touren</span>
            </x-nx-button>
        </x-ui-page-actionbar>
    </x-slot>

    <x-ui-page-container width="contained" spacing="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-nx-stat label="Touren" :value="$total" icon="heroicon-o-film" accent="var(--nx-accent)" />
            <x-nx-stat label="Aktiv" :value="$active" icon="heroicon-o-check-circle" />
        </div>
        <x-nx-card>
            <p class="text-sm text-[color:var(--nx-muted)]">
                <span class="font-medium text-[color:var(--nx-text)]">Regie</span> — geführte Touren, die im Presenter-Overlay
                ablaufen. Ein Schritt navigiert (optional) und zeigt einen Kommentar; der Zuschauer klickt „Weiter". Schritte
                baust du per LLM-Tools (<code>tour.tours.POST</code> / <code>tour.steps.POST</code>), gestartet wird mit
                <code>tour.start</code>. Teilen per Link folgt.
            </p>
        </x-nx-card>
    </x-ui-page-container>
</x-ui-page>
