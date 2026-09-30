<div class="workspace-page-heading">
    <div class="workspace-page-heading-copy">
        @unless(request()->routeIs('dashboard'))
            <nav class="workspace-page-back" aria-label="Back navigation">
                @hasSection('header-back')
                    @yield('header-back')
                @else
                    <x-back-link :fallback="route('dashboard')" />
                @endif
            </nav>
        @endunless
        <h1>@yield('header-title', $__env->yieldContent('title', 'Memo workspace'))</h1>
    </div>
    @hasSection('header-actions')
        <div class="workspace-page-actions app-header-actions">@yield('header-actions')</div>
    @endif
</div>
