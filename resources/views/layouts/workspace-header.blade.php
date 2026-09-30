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
        <a class="workspace-tool" href="{{ route('approvals.index') }}" aria-label="Pending approvals: {{ $needsMyAction }}" title="Pending approvals">
            @include('dashboard.icon', ['icon' => 'review'])
            <span class="workspace-notification">{{ $needsMyAction }}</span>
        </a>
        @include('layouts.theme-toggle')
        @include('layouts.profile-link')
    </nav>
</header>
