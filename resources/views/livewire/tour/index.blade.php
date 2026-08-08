{{-- Regie · Touren-Liste --}}
<x-ui-page>
    <x-slot name="navbar">
        <x-ui-page-navbar title="Touren" />
    </x-slot>

    <x-slot name="actionbar">
        <x-ui-page-actionbar :breadcrumbs="[
            ['label' => 'Regie', 'icon' => 'film', 'route' => 'tour.dashboard'],
            ['label' => 'Touren'],
        ]" />
    </x-slot>

    <x-ui-page-container width="contained" spacing="space-y-6">
        @if($tours->isEmpty())
            <x-nx-card>
                <x-nx-empty icon="heroicon-o-film">
                    Noch keine Touren. Bau eine per LLM-Tool (<code>tour.tours.POST</code> + <code>tour.steps.POST</code>) und starte sie mit <code>tour.start</code>.
                </x-nx-empty>
            </x-nx-card>
        @else
            <x-nx-card flush class="divide-y divide-[color:var(--nx-line)]">
                @foreach($tours as $t)
                    <div class="flex items-center justify-between gap-3 p-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                @svg('heroicon-o-film', 'w-4 h-4 text-[color:var(--nx-muted)] shrink-0')
                                <span class="text-sm font-medium text-[color:var(--nx-text)]">{{ $t->name }}</span>
                                <span class="text-xs text-[color:var(--nx-faint)] tabular-nums">{{ $t->steps_count }} Schritte · {{ $t->status }}</span>
                            </div>
                            @if($t->description)
                                <div class="mt-0.5 text-xs text-[color:var(--nx-muted)]">{{ $t->description }}</div>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            @if($t->share_token)
                                <button type="button"
                                        x-data="{ copied: false }"
                                        @click="navigator.clipboard.writeText('{{ url('/tour/s/'.$t->share_token) }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                        class="text-xs text-[color:var(--nx-accent)] hover:underline">
                                    <span x-show="!copied">Link kopieren</span>
                                    <span x-show="copied" x-cloak>Kopiert ✓</span>
                                </button>
                            @endif
                            @if($t->steps_count > 0)
                                <x-nx-button variant="primary" size="sm" wire:click="start({{ $t->id }})">
                                    @svg('heroicon-o-play', 'w-4 h-4') <span>Starten</span>
                                </x-nx-button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </x-nx-card>
        @endif
    </x-ui-page-container>
</x-ui-page>
