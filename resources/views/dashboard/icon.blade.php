<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($icon)
        @case('plus')
            <path d="M12 5v14M5 12h14"/>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
            @break
        @case('check')
            <circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>
            @break
        @case('review')
            <path d="M9 4H5v17h14v-7M9 3h6v4H9zM10 14l7-7 3 3-7 7-4 1z"/>
            @break
        @case('grid')
            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            @break
        @default
            <path d="M6 3h9l4 4v14H6zM14 3v5h5M9 12h7M9 16h5"/>
    @endswitch
</svg>
