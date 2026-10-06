<style>
    .app-header-controls { display:flex; align-items:center; justify-content:flex-end; gap:16px; max-width:100%; margin-left:auto; }
    .app-header-account { flex-shrink:0; }
    .app-header-controls .app-header-actions { flex-shrink:1; min-width:0; }
    .app-page-header .profile-link { background:#ffffff0d; color:#fff; border-color:#687493; }
    .app-page-header .profile-link:hover,.app-page-header .profile-link[aria-current] { background:#ffffff1a; border-color:#a8bbff; }
    .profile-link { display:grid; place-items:center; width:36px; height:36px; border:1px solid #dce1eb; border-radius:50%; background:#fff; color:#3156e8; }
    .profile-link svg { width:20px; height:20px; }
    .profile-link:hover,.profile-link[aria-current] { background:#e8eeff; border-color:#aabaf0; }
    .profile-link:focus-visible { outline:3px solid #82aaff; outline-offset:3px; }
    @media print { .profile-link { display:none !important; } }
    .app > .main { margin-left:242px; width:calc(100% - 242px); min-width:0; padding:0; }
    .app > .main > .content { max-width:none; margin:0; padding:28px 20px 48px; }
    .app-page-header { width:100%; margin:0; background:#202b4d; color:#fff; padding:30px 40px 34px; }
    .app-page-header-inner { position:relative; width:100%; max-width:none; margin:0; }
    .app-header-row { display:flex; align-items:center; justify-content:space-between; gap:24px; }
    .app-header-copy { min-width:0; }
    .app-page-header .app-header-eyebrow { display:block; color:#aebff1; font-size:10px; font-weight:700; letter-spacing:1.8px; text-transform:uppercase; margin-bottom:10px; }
    .app-page-header h1 { font-family:Georgia,serif; font-size:clamp(26px,3vw,34px); font-weight:400; line-height:1.25; letter-spacing:-.5px; margin:0; color:#fff; overflow-wrap:anywhere; }
    .app-page-header .app-header-description { color:#c1cbe2; font-size:14px; line-height:1.7; margin:10px 0 0; max-width:680px; }
    .app-header-back { margin-bottom:24px; font-size:12px; }
    .app-header-back a { color:#c1cbe2; text-decoration:none; }
    .app-header-back a:hover { color:#fff; }
    .app-header-actions { display:flex; align-items:center; flex-wrap:wrap; gap:10px; flex-shrink:0; max-width:100%; }
    .app-page-header .app-header-button { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:11px 17px; min-height:42px; border:1px solid #3156e8; border-radius:8px; background:#3156e8; color:#fff; font:600 12px Arial,sans-serif; text-decoration:none; cursor:pointer; line-height:1.5; }
    .app-page-header .app-header-button:hover { background:#2446c9; }
    .app-page-header .app-header-button.secondary { background:#ffffff0d; border-color:#687493; }
    .app-page-header .app-header-button.secondary:hover { background:#ffffff1a; }
    .app-page-header .app-header-total { padding:10px 14px; border:1px solid #56617f; border-radius:9px; color:#dce4f6; font-size:12px; line-height:1.6; }
    .app-header-extra { margin-top:24px; }
    .app-page-header a:focus-visible,.app-page-header button:focus-visible { outline:3px solid #a8bbff; outline-offset:4px; }
    .app > .main > .content .editor-body,.app > .main > .content .template-library { padding:0; max-width:none; }
    .app > .main > .content .template-editor,.app > .main > .content .templates-page { min-height:0; }
    .app > .main > .content .my-memos-page,.app > .main > .content .create-memo-page,.app > .main > .content .new-memo-page,.app > .main > .content .all-memos-page,.app > .main > .content .approvals-page { padding:0; min-height:0; }
    .app > .main > .content .memo-editor { margin-top:0; }
    @media(max-width:1000px) { .app-header-row { align-items:flex-start; flex-direction:column; gap:20px; } }
    @media(max-width:700px) { .app > .main { margin-left:70px; width:calc(100% - 70px); } .app-page-header { padding:24px 0 28px; } .app > .main > .content { padding:24px 20px 36px; } }
    @media(max-width:420px) { .app-page-header { padding-inline:0; } .app > .main > .content { padding-inline:12px; } }
    @media print { .app-page-header { display:none !important; } .app > .main { margin:0 !important; width:100% !important; } .app > .main > .content { max-width:none; margin:0; padding:0; } }
</style>
