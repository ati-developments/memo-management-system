<style>
    .app { --workspace-gutter:20px; }
    @media screen {
        .app > .main > .content { width:100%; max-width:none; margin-inline:0; padding-inline:var(--workspace-gutter); }
        .app > .main > .content :is(.dash,.access-menu-page,.my-memos-page,.new-memo-page,.memo-editor,.memo-review,.memo-details-page) { max-width:none; margin-inline:0; }
        .app .workspace-topbar { margin-inline:var(--workspace-gutter); }
    }
    @media screen and (max-width:700px) { .app { --workspace-gutter:8px; } }
    .workspace-page-heading { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:24px; }
    .workspace-page-heading-copy { display:flex; align-items:center; gap:16px; flex-wrap:wrap; min-width:0; }
    .workspace-page-heading h1 { margin:0; font-size:22px; font-weight:650; line-height:1.3; letter-spacing:0; color:var(--ui-ink); overflow-wrap:anywhere; }
    .workspace-page-back { flex-shrink:0; font-size:12px; }
    .workspace-page-back a { color:var(--primary-link); text-decoration:none; }
    .workspace-page-back a:hover { text-decoration:underline; }
    .workspace-page-actions { justify-content:flex-end; margin-left:auto; }
    .workspace-page-actions .app-header-button { display:inline-flex; align-items:center; padding:8px 13px; border:1px solid var(--primary); border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; }
    .workspace-page-actions .app-header-button.secondary { background:var(--primary-soft); color:var(--primary-link); }
    .workspace-tools .profile-link { width:36px; height:36px; border-radius:12px; background:#182231; border-color:#303c4d; color:#dce5f2; }
    .workspace-tools .profile-link:hover { background:#2b3c4c; }
    .workspace-topbar { display:flex; align-items:center; gap:12px; min-height:68px; margin:8px 20px; padding:10px 12px; border:1px solid #293444; border-radius:22px; background:linear-gradient(110deg,#293a32 0%,#0c1523 36%); color:#f1f5f9; }
    .workspace-identity { display:flex; align-items:center; gap:10px; color:inherit; text-decoration:none; min-width:0; margin-right:auto; }
    .workspace-symbol { display:grid; place-items:center; flex:0 0 42px; height:42px; border:1px solid #81bd434d; border-radius:12px; background:#81bd4321; color:#a8d77a; }
    .workspace-symbol svg { width:20px; height:20px; }
    .workspace-identity strong { display:block; font-size:13px; line-height:1.4; letter-spacing:0; }
    .workspace-identity small { display:block; margin-top:3px; font-size:9px; color:#93a4bb; }
    .workspace-account { display:flex; align-items:center; gap:14px; padding:5px 6px 5px 12px; border:1px solid #263142; border-radius:16px; }
    .workspace-user { max-width:240px; min-width:0; }
    .workspace-user a { display:block; color:#f1f5f9; text-decoration:none; font-size:11px; font-weight:700; overflow-wrap:anywhere; }
    .workspace-user time { display:block; margin-top:4px; color:#9caec4; font-size:9px; font-weight:600; }
    .workspace-user time span { color:#81bd43; padding:0 3px; }
    .workspace-department { display:flex; align-items:center; min-height:36px; max-width:210px; padding:6px 12px; border:1px solid #303c4d; border-radius:12px; background:#182231; font-size:10px; font-weight:700; text-transform:uppercase; overflow-wrap:anywhere; }
    .workspace-tools { display:flex; flex-shrink:0; align-items:center; gap:7px; padding:4px; border:1px solid #263142; border-radius:16px; }
    .workspace-tools form { margin:0; }
    .workspace-tool, .workspace-tools .theme-toggle { position:relative; display:grid; place-items:center; width:36px; height:36px; padding:8px; border:1px solid #303c4d; border-radius:12px; background:#182231; color:#dce5f2; text-decoration:none; cursor:pointer; }
    .workspace-tool svg { width:18px; height:18px; }
    .workspace-tools .theme-toggle { width:52px; border-radius:20px; background:#1c3158; }
    .workspace-tool:hover { background:#2b3c4c; }
    .workspace-notification { position:absolute; top:-6px; right:-3px; min-width:17px; padding:2px 4px; border-radius:10px; background:#ef4444; color:white; font-size:9px; font-weight:700; text-align:center; }
    .workspace-tool.workspace-logout { background:#2c1e2c; border-color:#603348; color:#ff9fac; font-size:23px; }
    .app .sidebar .brand { min-height:84px; height:84px; }
    .app .app-page-header { padding:18px 28px; }
    .app .app-page-header h1 { font-size:23px; letter-spacing:0; }
    .app .app-page-header .app-header-description { margin-top:5px; font-size:12px; }
    .app .app-header-back { margin-bottom:12px; }
    @media(max-width:1100px) { .workspace-topbar { flex-wrap:wrap; }.workspace-identity { flex:1 1 240px; }.workspace-account { margin-left:auto; } }
    @media(max-width:700px) { .workspace-topbar { margin:8px; padding:10px; gap:10px; border-radius:16px; }.workspace-identity { flex-basis:100%; }.workspace-account { flex:1 1 100%; margin:0; flex-wrap:wrap; }.workspace-user { max-width:100%; }.workspace-department { max-width:100%; }.workspace-tools { margin-left:auto; }.app .app-page-header { padding:16px 20px; } }
    html:not([data-theme=dark]) .workspace-topbar { background:linear-gradient(110deg,#edf6e3 0%,#fff 36%); border-color:#dce5d5; color:#253321; }
    html:not([data-theme=dark]) .workspace-symbol { background:#e4f1d7; border-color:#c0dda4; color:#456b22; }
    html:not([data-theme=dark]) .workspace-identity small,
    html:not([data-theme=dark]) .workspace-user time { color:#657260; }
    html:not([data-theme=dark]) .workspace-user a { color:#253321; }
    html:not([data-theme=dark]) .workspace-account,
    html:not([data-theme=dark]) .workspace-tools { background:#fff; border-color:#e0e6dc; }
    html:not([data-theme=dark]) .workspace-tools :is(.workspace-tool,.theme-toggle,.profile-link) { background:#f5f8f2; border-color:#dce5d5; color:#456b22; }
    html:not([data-theme=dark]) .workspace-tools :is(.workspace-tool,.theme-toggle,.profile-link):hover { background:#e4f1d7; border-color:#81bd43; }
    @media print { .workspace-topbar { display:none; } }
</style>
