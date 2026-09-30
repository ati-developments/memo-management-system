<style>
    :root { --ink:#182438; --muted:#66758b; --line:#dce2eb; --accent:#2563eb; }
    * { box-sizing:border-box; }
    body { margin:0; background:radial-gradient(ellipse at 15% 0%,#e4edff 0,transparent 48%),radial-gradient(ellipse at 95% 95%,#e6eef5 0,transparent 45%),#f5f7fb; color:var(--ink); font-family:'Segoe UI',Arial,sans-serif; font-size:14px; -webkit-font-smoothing:antialiased; }
    a { color:var(--accent); } button,input,select,textarea { font:inherit; }
    .login-page,.register-page { display:flex; justify-content:center; min-height:100vh; min-height:100svh; padding:48px 24px 28px; }
    .login-page { align-items:center; }
    .login-area,.register-area { display:flex; flex-direction:column; align-items:center; width:100%; min-width:0; }
    .auth-brand { display:flex; align-items:center; gap:14px; margin-bottom:28px; padding:4px; border-radius:16px; color:var(--ink); text-decoration:none; line-height:1; }
    .auth-brand { max-width:100%; }
    .auth-brand-image { display:block; width:260px; max-width:100%; height:auto; }
    .login-card,.register-container { width:100%; background:#fff; border:1px solid #e1e7f0; border-radius:22px; box-shadow:0 16px 50px #233e6810; }
    .login-card { max-width:460px; padding:38px; }
    .register-container { max-width:760px; padding:36px 40px; }
    .form-eyebrow { color:var(--accent); font-size:10px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; margin:0 0 13px; }
    .login-header,.register-header { margin-bottom:30px; }
    .login-card > .form-eyebrow,.login-header,.register-header { text-align:center; }
    .login-header h1,.register-header h1 { margin:0 0 12px; font-size:34px; line-height:1.2; font-weight:650; letter-spacing:-1.2px; }
    .login-header p,.register-header p { margin:0; color:var(--muted); font-size:14px; line-height:1.6; }.register-header .form-eyebrow { color:var(--accent); margin-bottom:13px; font-size:10px; }
    .form-group { min-width:0; margin-bottom:24px; } label { display:block; margin-bottom:9px; color:#334155; font-weight:600; font-size:12px; }
    .input-wrap { position:relative; }.input-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); width:18px; height:18px; color:#7c8ba1; pointer-events:none; }
    .form-control,.register-container input,.register-container select { width:100%; min-height:48px; padding:12px 14px; border:1px solid var(--line); border-radius:10px; background:#fbfcfe; color:var(--ink); outline:none; font-size:14px; transition:border-color .15s,box-shadow .15s; }
    .form-control { padding-left:43px; }.password-input { padding-right:46px; } input::placeholder { color:#8693a6; }
    .form-control:hover,.register-container input:hover,.register-container select:hover { border-color:#a8b6cb; }
    .form-control:focus,.register-container input:focus,.register-container select:focus { border-color:var(--accent); box-shadow:0 0 0 3px #2563eb16; }
    .toggle-password { position:absolute; right:9px; top:50%; transform:translateY(-50%); display:grid; place-items:center; width:32px; height:32px; border:0; border-radius:5px; background:transparent; color:#718198; cursor:pointer; }.toggle-password svg { width:18px; height:18px; }.toggle-password:hover { background:#edf3fc; }
    a:focus-visible,button:focus-visible { outline:3px solid #79a9ff; outline-offset:4px; }
    .login-button,.btn-register { display:flex; align-items:center; justify-content:center; gap:12px; min-height:48px; padding:12px 24px; border:1px solid #1e56d3; border-radius:10px; background:var(--accent); color:#fff; font-weight:600; font-size:14px; cursor:pointer; transition:background .15s; box-shadow:0 4px 10px #2563eb18; }
    .login-button { width:100%; margin-top:8px; }.login-button:hover,.btn-register:hover { background:#1d4ed8; }.login-button svg { width:18px; height:18px; }
    .register-link { margin:26px 0 0; text-align:center; color:var(--muted); font-size:12px; line-height:1.8; }.register-link a,.login-link { font-weight:600; text-decoration:none; }.register-link a:hover,.login-link:hover { text-decoration:underline; }
    .auth-footer { margin:28px 0 0; color:#718097; font-size:11px; text-align:center; line-height:1.6; }
    .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:21px 24px; }.form-grid .form-group { margin:0; }.full-width,.signature-section,.form-section-heading { grid-column:1 / -1; }
    .form-section-heading { display:flex; align-items:center; gap:10px; margin:5px 0 0; font-size:13px; font-weight:600; }.form-section-heading::after { content:''; flex:1; height:1px; margin-left:4px; background:#e7ebf1; }.form-section-heading span { color:#73839b; font-size:11px; font-weight:400; }
    .required { color:#be3548; }.help-text { margin-top:8px; font-size:11px; line-height:1.5; color:var(--muted); }.error { color:#b42338; font-size:11px; margin-top:6px; }
    .alert { display:flex; align-items:center; gap:10px; }.alert svg { width:18px; height:18px; flex-shrink:0; }.alert-error,.alert-success { padding:13px; border-radius:6px; margin-bottom:22px; line-height:1.5; font-size:12px; }.alert-error { color:#a52b3c; background:#fff1f3; border:1px solid #f5d0d7; }.alert-success { color:#1c509f; background:#edf5ff; border:1px solid #ccdef8; }
    .signature-section { background:#f8fafc; border:1px solid #e5eaf1; padding:20px; border-radius:8px; margin-top:4px; }.signature-section h3 { font-size:13px; margin:0 0 7px; }.signature-section p { font-size:12px; line-height:1.6; color:var(--muted); margin:0 0 14px; }.signature-section input { background:#fff; font-size:11px; }.signature-section input::file-selector-button { background:#edf2fa; color:#344d73; border:0; border-radius:4px; padding:7px 10px; margin-right:12px; cursor:pointer; }
    .form-actions { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-top:28px; padding-top:24px; border-top:1px solid #e8edf3; }.login-link { font-size:12px; }
    @media(max-width:600px) {
        .login-page,.register-page { padding:28px 16px 22px; }
        .login-card,.register-container { padding:28px 22px; border-radius:18px; }
        .auth-brand { margin-bottom:24px; }
        .form-grid { grid-template-columns:1fr; gap:20px; }
        .form-actions { flex-direction:column-reverse; align-items:stretch; }
        .login-link { text-align:center; }
        .login-header h1,.register-header h1 { font-size:28px; }
        .signature-section { padding:16px; }
        .register-container input,.register-container select,.form-control { font-size:16px; }
    }
    @media(prefers-reduced-motion:reduce) { * { transition:none !important; } }
</style>
