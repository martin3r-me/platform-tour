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
                    <x-nx-list-item
                        icon="heroicon-o-film"
                        :title="$t->name"
                        :subtitle="$t->description"
                        :meta="$t->steps_count.' Schritte · '.$t->status" />
                @endforeach
            </x-nx-card>
        @endif
    </x-ui-page-container>
</x-ui-page>
