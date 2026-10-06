<style>
    :root { --ui-ink:#18263f; --ui-muted:#64748b; --ui-line:#e2e8f1; --ui-blue:#315fe9; --ui-shadow:0 6px 24px #172b4d06,0 1px 3px #172b4d04; }
    body { font-family:'Segoe UI',system-ui,-apple-system,sans-serif; background:#f4f7fb; color:var(--ui-ink); -webkit-font-smoothing:antialiased; }
    ::selection { background:#dbe7ff; color:#18263f; }
    .app button,.app input,.app select,.app textarea { font-family:inherit; }
    .app a,.app button,.app input,.app select,.app textarea { transition:background-color .18s,border-color .18s,box-shadow .18s,transform .18s; }
    .app :is(a,button,input,select,textarea):focus-visible { outline:3px solid #82aaff; outline-offset:3px; }
    .skip-link { position:fixed; top:12px; left:260px; z-index:100; padding:12px 18px; border-radius:10px; background:#fff; color:#204dc6; transform:translateY(-160%); }
    .skip-link:focus { transform:translateY(0); }
    .app .sidebar { background:linear-gradient(170deg,#17253f,#101b31); border-right:1px solid #ffffff08; z-index:20; overflow-y:auto; scrollbar-width:thin; scrollbar-color:#43516a transparent; }
    .app .brand { min-height:100px; padding:0 24px; border-color:#ffffff0c; }
    .sidebar-brand-link { display:block; width:100%; }
    .sidebar-brand-image { display:block; width:100%; height:auto; }
    .app .brand-logo { flex-shrink:0; width:40px; height:40px; border-radius:13px; background:linear-gradient(145deg,#6b91ff,#315fe9); box-shadow:0 5px 18px #0003,inset 0 1px 0 #ffffff50; font-family:inherit; font-weight:750; }
    .app .brand-name { font-family:inherit; font-size:20px; letter-spacing:-.6px; }
    .app .brand-subtitle { font-size:10px; letter-spacing:.3px; color:#9eacc5; margin-top:5px; }
    .app .nav { padding:24px 14px; }
    .app .nav-group { margin:8px 0 12px; }
    .app .nav-subitems { margin-left:10px; padding-left:8px; border-left:1px solid #ffffff18; }
    .app .nav-group-toggle { width:100%; background:transparent; text-align:left; cursor:pointer; font-family:inherit; }
    .app .nav-group-toggle:hover { background:#ffffff09; color:#fff; }
    .app .nav-expand-arrow { margin-left:auto; font-size:18px; line-height:1; }
    .app .nav-item { min-height:46px; padding:12px 14px; gap:12px; font-size:13px; font-weight:550; border:1px solid transparent; border-radius:10px; color:#b6c2d8; }
    .app .nav-item:hover { background:#ffffff09; color:#fff; transform:translateX(2px); }
    .app .nav-item.active { background:#315fe921; border-color:#7296ff30; color:#dce7ff; box-shadow:inset 3px 0 #7296ff; }
    .app .nav-item.new-memo { background:linear-gradient(110deg,#416fed,#315fe9); color:#fff; box-shadow:0 4px 14px #0002; margin-block:12px 20px; }
    .app .nav-icon { display:grid; place-items:center; flex:0 0 20px; width:20px; }
    .app .nav-icon svg { width:19px; height:19px; }
    .app .badge { width:auto; min-width:21px; height:21px; padding:0 5px; background:#fbbf24; color:#33270d; font-size:10px; }
    .app .user-area { padding:20px; gap:10px; border-top:1px solid #ffffff0b; }
    .app .user-avatar { flex-shrink:0; margin:0; border:1px solid #ffffff20; background:#344a75; width:36px; height:36px; border-radius:12px; }
    .app .user-area > div:last-child { min-width:0; }
    .app .user-name,.app .user-role { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:150px; }
    .app .user-name { font-size:12px; }.app .user-role { font-size:10px; }
    .app .logout-form { border:0; }.app .logout-button { font-size:12px; }
    .app .app-page-header { background:linear-gradient(120deg,#fff 45%,#edf3ff); color:var(--ui-ink); padding:20px clamp(24px,4vw,56px); border-bottom:1px solid var(--ui-line); }
    .app .app-page-header h1 { font-family:inherit; font-weight:650; color:var(--ui-ink); font-size:clamp(22px,2vw,27px); letter-spacing:-.6px; }
    .app .app-page-header .app-header-eyebrow { color:#4c6baa; font-size:9px; letter-spacing:1.4px; margin-bottom:6px; }
    .app .app-page-header .app-header-description { color:var(--ui-muted); font-size:12px; max-width:620px; margin-top:6px; line-height:1.5; }
    .app .app-header-back { margin-bottom:10px; }
    .app .app-header-extra { margin-top:14px; }.app .app-header-back a { color:#526b94; }
    .app .app-header-back a:hover { color:var(--ui-blue); }
    .app .app-page-header .profile-link { width:34px; height:34px; background:white; color:#46608b; border-color:var(--ui-line); box-shadow:0 2px 5px #172b4d05; }
    .app .app-page-header .profile-link:hover { background:#edf3ff; border-color:#a7bcef; }
    .app .app-page-header .app-header-button { min-height:36px; padding:8px 13px; border-radius:8px; font-family:inherit; background:var(--ui-blue); box-shadow:0 4px 10px #315fe918; }
    .app .app-page-header .app-header-button.secondary { background:#fff; border-color:#d5ddeb; color:#405777; box-shadow:none; }
    .app .app-page-header .app-header-total { background:#fff; border-color:var(--ui-line); color:#4b6080; }
    .app > .main > .content { padding:30px clamp(24px,4vw,56px) 52px; }
    .app :is(.my-memos-page,.all-memos-page,.approvals-page,.template-editor,.templates-page,.new-memo-page,.create-memo-page) { background:transparent; font-family:inherit; }
    .app :is(.memo-table-card,.approval-register,.builder-card,.table-builder,.dash-panel,.template-selection-card,.memo-editor form) { background:#fff; border:1px solid var(--ui-line); border-radius:16px; box-shadow:var(--ui-shadow); }
    .app :is(.dash-stat,.dash-shortcut,.create-template-card) { border-radius:14px; border-color:var(--ui-line); box-shadow:var(--ui-shadow); }
    .app :is(.dash-stat,.dash-shortcut,.create-template-card):hover { transform:translateY(-3px); border-color:#adc1f4; box-shadow:0 10px 24px #244a9610; }
    .app .dash-header { padding:0; margin-bottom:20px; background:transparent; border:0; border-radius:0; box-shadow:none; }
    .app .dash-stat { padding:14px 16px; }.app .dash-stat strong { font-size:26px; margin:8px 0 3px; }
    .app .dash h1 { font-size:clamp(22px,2vw,26px); }
    .app .dash-topline { margin-bottom:12px; }
    .app .dash-shortcut { padding:12px 14px; }
    .app .dash-stats,.app .dash-grid { gap:12px; margin-bottom:12px; }
    .app .dash-icon { width:28px; height:28px; border-radius:8px; }.app .dash-icon svg { width:15px; height:15px; }
    .app .dash-panel-header { padding:14px 16px; }.app .dash-memo,.app .dash-approval { padding:12px 16px; }
    .app :is(.status-filters,.approval-tabs,.department-filters) { gap:6px; }
    .app :is(.status-filter,.approval-tab,.department-filter) { border-radius:9px; font-family:inherit; }
    .app :is(.status-filter,.approval-tab,.department-filter).active { background:#e9efff; border-color:#c7d5fa; color:#294fbd; box-shadow:0 1px 3px #315fe908; }
    .app .status-filter.active .filter-count { background:#d7e2ff; color:#294fbd; }
    .app :is(.memo-table,.approval-table) th { background:#f8fafd; color:#66768e; font-size:10px; font-weight:650; letter-spacing:.7px; text-transform:uppercase; padding-block:16px; border-bottom:1px solid var(--ui-line); }
    .app :is(.memo-table,.approval-table) td { padding-block:19px; border-color:#edf1f6; }
    .app :is(.memo-table,.approval-table) tbody tr { transition:background-color .18s; }
    .app :is(.memo-table,.approval-table) tbody tr:hover { background:#f6f9ff; }
    .app :is(.status-badge,.dash-status,.approval-table .status) { border-radius:100px; }
    .app :is(.table-header,.register-heading) h2 { font-family:inherit; font-size:15px; font-weight:650; }
    .app :is(.form-control,.memo-editor input,.memo-editor textarea,.memo-editor select) { border-radius:9px; border-color:#d9e1ed; background:#fcfdff; min-height:42px; }
    .app :is(.form-control,.memo-editor input,.memo-editor textarea,.memo-editor select):focus { border-color:#6f93ef; box-shadow:0 0 0 3px #315fe912; background:#fff; }
    .app input[type=checkbox],.app input[type=radio] { accent-color:var(--ui-blue); }
    .app :is(.btn,.dash-button,.review-button,.view-button,.edit-button,.add-field-btn,.add-column-btn) { border-radius:9px; }
    .app :is(.btn-primary,.dash-button,.review-button) { background:var(--ui-blue); border-color:var(--ui-blue); box-shadow:0 3px 8px #315fe918; }
    .app :is(.btn-primary,.dash-button,.review-button,.app-header-button):hover { filter:brightness(.96); transform:translateY(-1px); }
    .app button:disabled { cursor:not-allowed; opacity:.6; transform:none; }
    .app .template-item { border-radius:12px; transition:background-color .18s,box-shadow .18s,transform .18s; }
    .app .template-item:hover { background:#f5f8ff; transform:translateX(3px); }
    .app .template-file { border-radius:12px; background:#edf2ff; }
    .app .template-search { background:#fff; color:var(--ui-ink); border-color:#d5dfee; box-shadow:0 3px 12px #172b4d04; }
    .app .template-search::placeholder { color:#71819a; }.app .template-search-wrap svg { color:#71819a; }
    .app .template-table-card { border-radius:12px; border-color:var(--ui-line); }
    .app .field-builder-header { background:#f8fafd; }
    .app .empty-state,.app .template-empty { padding:48px 24px; }
    .app .empty-icon { border-radius:18px; background:#edf3ff; color:#4869b3; }
    @keyframes workspace-enter { from { opacity:0; transform:translateY(7px); } to { opacity:1; transform:translateY(0); } }
    @media(prefers-reduced-motion:no-preference) { .app > .main > .content { animation:workspace-enter .35s ease-out both; } }
    @media(max-width:1000px) { .app .app-header-controls { margin-left:0; width:100%; justify-content:space-between; } }
    @media(max-width:700px) {
        .app .sidebar { width:70px; }.app .brand { min-height:78px; padding:0 5px; }.app .brand-logo { margin:0; }
        .app .nav { padding:18px 8px; }.app .nav-item { justify-content:center; padding:12px; }.app .nav-item:hover { transform:none; }
        .app .nav-subitems { margin-left:0; padding-left:0; border-left:0; }
        .app .user-area { padding:16px; }.app .user-area > div:last-child { display:none; }.app .logout-form { padding:0 8px 16px; }
        .app .app-page-header { padding:18px 20px; }
        .app .app-header-row { gap:12px; }.app > .main > .content { padding:20px 16px 36px; }
        .app .dash-header { padding:0; }.app .dash-stat { padding:12px; }.app .dash-stats { gap:10px; }
        .skip-link { left:80px; }.app .app-header-controls { gap:12px; }
    }
    @media(prefers-reduced-motion:reduce) { .app *,.app *::before,.app *::after { animation:none !important; transition:none !important; scroll-behavior:auto !important; } }
    @media print { .app .sidebar,.skip-link { display:none !important; } body { background:#fff; }.app > .main > .content { animation:none; padding:0; }.app :is(.memo-table-card,.approval-register,.builder-card,.dash-panel) { box-shadow:none; } }
</style>
