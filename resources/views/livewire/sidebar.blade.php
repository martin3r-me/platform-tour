<div>
    <div x-show="!collapsed" class="p-3 text-sm italic text-[var(--nx-text)] uppercase border-b border-[color:var(--nx-line)] mb-2">
        Regie
    </div>

    <x-ui-sidebar-list label="Regie">
        <x-ui-sidebar-item :href="route('tour.dashboard')" :active="request()->routeIs('tour.dashboard')">
            @svg('heroicon-o-home', 'w-4 h-4 text-[var(--nx-text)]')
            <span class="ml-2 text-sm">Dashboard</span>
        </x-ui-sidebar-item>
        <x-ui-sidebar-item :href="route('tour.tours.index')" :active="request()->routeIs('tour.tours.*')">
            @svg('heroicon-o-film', 'w-4 h-4 text-[var(--nx-text)]')
            <span class="ml-2 text-sm">Touren</span>
        </x-ui-sidebar-item>
    </x-ui-sidebar-list>
</div>
