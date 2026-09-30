<style>
    :root {
        --primary: #81bd43;
        --primary-hover: #72aa39;
        --primary-ink: #203014;
        --primary-button-text: #fff;
        --primary-link: #456b22;
        --primary-soft: #edf6e3;
        --primary-line: #c0dda4;
        --ui-blue: var(--primary);
    }
    html[data-theme=dark] { --primary-link: #a8d77a; --primary-soft: #26391e; --primary-line: #527b32; }
    ::selection { background: var(--primary-line); color: var(--primary-ink); }
    input[type=checkbox], input[type=radio] { accent-color: var(--primary); }
    .app :is(a,button,input,select,textarea):focus-visible, .theme-toggle:focus-visible { outline-color: var(--primary); }
    .app :is(input,select,textarea):focus { border-color: var(--primary) !important; box-shadow: 0 0 0 3px #81bd4326; }
    .app :is(.btn-primary,.dash-button,.review-button,.app-header-button:not(.secondary),.memo-editor .primary),
    .login-button, .btn-register,
    .app .nav-item.new-memo, .app .brand-logo {
        background: var(--primary); border-color: var(--primary); color: var(--primary-button-text); box-shadow: 0 3px 8px #81bd4326;
    }
    .app :is(.btn-primary,.dash-button,.review-button,.app-header-button:not(.secondary),.memo-editor .primary):hover,
    .login-button:hover, .btn-register:hover, .app .nav-item.new-memo:hover { background: var(--primary-hover); color: var(--primary-button-text); }
    .app .nav-item.active:not(.new-memo) { background: #81bd4321; border-color: #81bd434d; color: #c0e59e; box-shadow: inset 3px 0 var(--primary); }
    .app :is(.status-filter,.status-tab,.approval-tab,.department-filter).active,
    .app .pagination-wrap nav [aria-current=page] > span,
    .app .pagination-wrap nav a:hover {
        background: var(--primary) !important; border-color: var(--primary) !important; color: var(--primary-button-text) !important;
    }
    .app :is(.status-filter,.status-tab,.approval-tab,.department-filter):hover { border-color: var(--primary); }
    .app :is(.status-filter,.status-tab,.approval-tab,.department-filter).active :is(.filter-count,.tab-count) { background: #ffffff40 !important; color: var(--primary-button-text) !important; }
    .app .my-memos-page .status-filters { flex-wrap:wrap; }
    .app .my-memos-page .status-filter { min-height:36px; border-radius:6px; }
    .app .my-memos-page .filter-count { display:inline-flex; align-items:center; justify-content:center; min-width:20px; height:20px; padding:0 5px; border-radius:5px; line-height:1; }
    html .app .my-memos-page .status-filter.active {  color:var(--primary-button-text) !important; border-color:var(--primary) !important; box-shadow:none; }
    html .app .my-memos-page .status-filter.active .filter-count { background:#ffffff40 !important; color:var(--primary-button-text) !important; }
    html .app .my-memos-page .status-filter:not(.active):hover { background:var(--primary-soft) !important; color:var(--primary-link) !important; }
    .app :is(.use-template,.library-clear,.dash-link,.view-link,.department-template-action,.section-number,.app-header-back a,.app-header-eyebrow),
    .login-link, .register-link a { color: var(--primary-link) !important; }
    .app :is(.template-file,.template-badge,.department-filter span,.department-heading > span,.empty-icon,.empty-mark,.tab-count,.filter-count,.dash-count,.department-icon,.department-toggle,.template-document-icon,.create-template-icon,.template-icon,.step-number,.guide-icon,.info-icon,.approval-step-number),
    .app .dash-stat:not(.amber):not(.green) .dash-icon,
    .app .memo-search button { background: var(--primary-soft) !important; color: var(--primary-link) !important; }
    .app .progress-track span { background: var(--primary); }
    .app :is(.dash-stat,.dash-shortcut,.create-template-card):hover { border-color: var(--primary-line); }
    .app .template-item:hover, .app .profile-link:hover, .theme-toggle:hover { background: var(--primary-soft); }
    html:not([data-theme=dark]) .app .app-page-header { background: linear-gradient(120deg,#fff 45%,var(--primary-soft)); }
</style>
