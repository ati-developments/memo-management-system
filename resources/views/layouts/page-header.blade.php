<header class="app-page-header">
    <div class="app-page-header-inner">
        @hasSection('header-back')
            <nav class="app-header-back" aria-label="Back navigation">@yield('header-back')</nav>
        @endif
        <div class="app-header-row">
            <div class="app-header-copy">
                <span class="app-header-eyebrow">@yield('header-eyebrow', 'Memo workspace')</span>
                <h1>@yield('header-title', $__env->yieldContent('title', 'Memo workspace'))</h1>
                @hasSection('header-description')
                    <p class="app-header-description">@yield('header-description')</p>
                @endif
            </div>
            <div class="app-header-controls">
                @hasSection('header-actions')
                    <div class="app-header-actions">@yield('header-actions')</div>
                @endif
            </div>
        </div>
        @hasSection('header-extra')
            <div class="app-header-extra">@yield('header-extra')</div>
        @endif
    </div>
</header>
