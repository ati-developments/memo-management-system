@php
    $headerTime = now('Asia/Colombo');
    $greeting = $headerTime->hour < 12 ? 'Good Morning' : 'Good Afternoon';
    $firstName = preg_split('/\s+/u', trim($user?->name ?? ''), 2)[0] ?: 'User';
@endphp
<header class="workspace-topbar">
    <a class="workspace-identity" href="{{ route('dashboard') }}">
        <span class="workspace-symbol">@include('dashboard.icon', ['icon' => 'grid'])</span>
        <span><strong>MEMO MANAGEMENT</strong><small>OPERATIONS WORKSPACE</small></span>
    </a>
    <div class="workspace-account">
        <div class="workspace-user">
            <a href="{{ route('profile.edit') }}">{{ $greeting }}, {{ $firstName }}</a>
            <time datetime="{{ $headerTime->toIso8601String() }}">{{ $headerTime->format('d F') }} <span>&bull;</span> {{ $headerTime->format('h:i A') }}</time>
        </div>
    </div>
    <nav class="workspace-tools" aria-label="Account controls">
        <!-- <a class="workspace-tool" href="{{ route('approvals.index') }}" aria-label="Pending approvals: {{ $needsMyAction }}" title="Pending approvals">
            @include('dashboard.icon', ['icon' => 'review'])
            <span class="workspace-notification">{{ $needsMyAction }}</span>
        </a> -->
        @include('layouts.theme-toggle')
        <!-- @include('layouts.profile-link') -->
        <form method="POST" action="{{ route('logout') }}" class="workspace-logout-form" id="workspace-logout-form">
            @csrf
            <button type="submit" class="workspace-tool workspace-logout" aria-label="Log out" title="Log out">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M10 17l5-5-5-5M15 12H3" />
                    <path d="M12 3h8v18h-8" />
                </svg>
            </button>
        </form>
        <dialog class="workspace-logout-dialog" id="workspace-logout-dialog" aria-labelledby="workspace-logout-title" aria-describedby="workspace-logout-message">
            <div class="workspace-logout-dialog-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17l5-5-5-5M15 12H3"/><path d="M12 3h8v18h-8"/></svg>
            </div>
            <h2 id="workspace-logout-title">Log out?</h2>
            <p id="workspace-logout-message">Are you sure you want to log out of your workspace?</p>
            <div class="workspace-logout-dialog-actions">
                <button type="button" class="workspace-logout-cancel" data-logout-cancel>Cancel</button>
                <button type="button" class="workspace-logout-confirm" data-logout-confirm>Log out</button>
            </div>
        </dialog>
    </nav>
</header>
<script>
    (() => {
        const form = document.getElementById('workspace-logout-form');
        const dialog = document.getElementById('workspace-logout-dialog');
        if (!form || !dialog) return;

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            dialog.showModal();
        });

        dialog.querySelector('[data-logout-cancel]').addEventListener('click', () => dialog.close());
        dialog.querySelector('[data-logout-confirm]').addEventListener('click', () => {
            dialog.close();
            HTMLFormElement.prototype.submit.call(form);
        });
    })();
</script>
