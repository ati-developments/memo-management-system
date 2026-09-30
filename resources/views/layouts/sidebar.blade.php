    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="brand">

            <a href="{{ route('dashboard') }}" class="sidebar-brand-link" aria-label="Amana Takaful Insurance - Dashboard">
                <img src="{{ asset('storage/images/logo.png') }}" alt="Amana Takaful Insurance" class="sidebar-brand-image" width="608" height="239">
            </a>

        </div>


        <nav class="nav" aria-label="Main navigation">

            <a aria-label="Dashboard" title="Dashboard" href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                <span class="nav-icon">@include('dashboard.icon', ['icon' => 'grid'])</span>
                <span>Dashboard</span>

            </a>


            <a aria-label="Templates" title="Templates" href="{{ route('templates.index') }}" class="nav-item {{ request()->routeIs('templates.*') ? 'active' : '' }}">

                <span class="nav-icon">@include('dashboard.icon', ['icon' => 'document'])</span>
                <span>Templates</span>

            </a>


            <a aria-label="New memo" title="New memo" href="{{ route('memos.new') }}" class="nav-item new-memo {{ request()->routeIs('memos.new', 'memos.create', 'memos.create.*') ? 'active' : '' }}">

                <span class="nav-icon">@include('dashboard.icon', ['icon' => 'plus'])</span>
                <span>New Memo</span>

            </a>


            <a aria-label="My memos" title="My memos" href="{{ route('memos.my') }}" class="nav-item {{ request()->routeIs('memos.my') ? 'active' : '' }}">

                <span class="nav-icon">@include('dashboard.icon', ['icon' => 'document'])</span>
                <span>My Memos</span>

            </a>


            <a aria-label="Approvals" title="Approvals" href="{{ route('approvals.index') }}" class="nav-item {{ request()->routeIs('approvals.*') ? 'active' : '' }}">

                <span class="nav-icon">@include('dashboard.icon', ['icon' => 'check'])</span>
                <span>Approvals</span>

                @if(($needsMyAction ?? 0) > 0)

                    <span class="badge">
                        {{ $needsMyAction }}
                    </span>

                @endif

            </a>


            <a aria-label="All memos" title="All memos" href="{{ route('memos.all') }}" class="nav-item {{ request()->routeIs('memos.all') ? 'active' : '' }}">

                <span class="nav-icon">@include('dashboard.icon', ['icon' => 'grid'])</span>
                <span>All Memos</span>

            </a>

        </nav>


        <!-- USER -->

        <div class="user-area">

            <div class="user-avatar">

                {{ strtoupper(substr($user?->name ?? 'User', 0, 2)) }}

            </div>

            <div>

                <div class="user-name">

                    {{ $user?->name ?? 'User' }}

                </div>

                <div class="user-role">

                    {{ $user?->designation ?? 'User' }}

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
