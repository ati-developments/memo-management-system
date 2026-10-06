    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="brand">

            <a href="{{ route('dashboard') }}" class="sidebar-brand-link" aria-label="Amana Takaful Insurance - Dashboard">
                <img src="{{ asset('images/logo.png') }}" alt="Amana Takaful Insurance" class="sidebar-brand-image" width="608" height="239">
            </a>

        </div>


        <nav class="nav" aria-label="Main navigation">

            @foreach($sidebarMenuItems ?? [] as $item)
                @php
                    $itemAccessKey = $item->access_key ?? $item->item_key;
                    $adminFullMenu = $isAdmin ?? false;
                    $itemAllowed = $adminFullMenu || ((($isAdmin ?? false) || !$item->admin_only) && ($item->is_group || in_array($itemAccessKey, $menuAccess ?? [], true)));
                    $children = $item->children->filter(fn ($child) => $adminFullMenu || ((($isAdmin ?? false) || !$child->admin_only) && in_array($child->access_key ?? $child->item_key, $menuAccess ?? [], true)));
                    $groupOpen = $children->contains(fn ($child) => $child->route_name && request()->routeIs($child->route_name, $child->route_name . '.*'));
                    $active = $item->route_name && request()->routeIs($item->route_name, $item->route_name . '.*');
                @endphp
                @if($itemAllowed && ($item->is_group ? $children->isNotEmpty() : true))
                    @if($item->is_group)
                        <div class="nav-group">
                            <button type="button" class="nav-item nav-group-toggle" aria-expanded="{{ $groupOpen ? 'true' : 'false' }}" aria-controls="sidebar-menu-{{ $item->id }}" data-nav-toggle>
                                <span class="nav-icon">@include('dashboard.icon', ['icon' => $item->icon])</span><span>{{ $item->label }}</span><span class="nav-expand-arrow" aria-hidden="true">{{ $groupOpen ? '⌄' : '›' }}</span>
                            </button>
                            <div class="nav-subitems" id="sidebar-menu-{{ $item->id }}" @if(!$groupOpen) hidden @endif>
                                @foreach($children as $child)
                                    <a aria-label="{{ $child->label }}" title="{{ $child->label }}" href="{{ $child->route_name ? route($child->route_name) : $child->url }}" class="nav-item {{ $child->item_key === 'new_memo' ? 'new-memo' : '' }} {{ $child->route_name && request()->routeIs($child->route_name, $child->route_name . '.*') ? 'active' : '' }}">
                                        <span class="nav-icon">@include('dashboard.icon', ['icon' => $child->icon])</span><span>{{ $child->label }}</span>
                                        @if($child->item_key === 'approvals' && ($needsMyAction ?? 0) > 0)<span class="badge">{{ $needsMyAction }}</span>@endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a aria-label="{{ $item->label }}" title="{{ $item->label }}" href="{{ $item->route_name ? route($item->route_name) : $item->url }}" class="nav-item {{ $item->item_key === 'new_memo' ? 'new-memo' : '' }} {{ $active ? 'active' : '' }}">
                            <span class="nav-icon">@include('dashboard.icon', ['icon' => $item->icon])</span><span>{{ $item->label }}</span>
                            @if($item->item_key === 'approvals' && ($needsMyAction ?? 0) > 0)<span class="badge">{{ $needsMyAction }}</span>@endif
                        </a>
                    @endif
                @endif
            @endforeach

        </nav>


        <!-- USER -->

        <div class="user-area">


            <div>

                <div class="user-name">


                </div>

                <div class="user-role">


                </div>

            </div>

        </div>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="logout-button" aria-label="Logout" title="Logout">
                <span class="logout-icon" aria-hidden="true">&#x21AA;</span>
                <span>Logout</span>
            </button>
        </form>

    </aside>

    <script>
        document.querySelectorAll('[data-nav-toggle]').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const expanded = toggle.getAttribute('aria-expanded') === 'true';
                const submenu = document.getElementById(toggle.getAttribute('aria-controls'));
                toggle.setAttribute('aria-expanded', String(!expanded));
                submenu.hidden = expanded;
                toggle.querySelector('.nav-expand-arrow').textContent = expanded ? '›' : '⌄';
            });
        });
    </script>
