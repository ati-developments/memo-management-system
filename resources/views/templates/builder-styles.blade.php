<style>
    .main { margin-left:242px; width:calc(100% - 242px); padding:0; min-width:0; }
    .template-editor { color:#242936; min-height:100vh; background:#f7f6f2; }
    .template-editor a { text-decoration:none; }
    .editor-header { background:#202b4d; color:#fff; padding:30px clamp(20px,4vw,64px) 34px; }
    .editor-header-inner,.editor-body { max-width:1440px; margin:0 auto; }
    .editor-back { display:inline-flex; gap:8px; color:#c1cbe2; font-size:12px; margin-bottom:24px; }
    .editor-back:hover { color:#fff; }
    .editor-eyebrow { display:block; color:#aebff1; font-size:10px; font-weight:700; letter-spacing:1.8px; text-transform:uppercase; margin-bottom:10px; }
    .editor-header h1 { font-family:Georgia,serif; font-size:clamp(26px,3vw,34px); font-weight:400; letter-spacing:-.5px; margin:0 0 10px; }
    .editor-header p { color:#c1cbe2; font-size:14px; line-height:1.7; margin:0; max-width:650px; }
    .editor-body { padding:28px clamp(20px,4vw,64px) 48px; }
    .editor-steps { display:flex; gap:24px; flex-wrap:wrap; padding:0 0 24px; margin:0; list-style:none; }
    .editor-steps li { display:flex; align-items:center; gap:9px; color:#79766e; font-size:12px; }
    .editor-steps span { display:grid; place-items:center; width:26px; height:26px; border:1px solid #dedbd5; border-radius:50%; background:#fff; font-size:11px; }
    .editor-steps .current { color:#274bce; font-weight:600; }
    .editor-steps .current span { background:#3156e8; border-color:#3156e8; color:#fff; }
    .create-layout { display:grid; grid-template-columns:minmax(0,1fr) 280px; gap:24px; align-items:start; }
    .template-editor .builder-card,.template-editor .table-builder,.editor-guide { background:#fff; border:1px solid #e1ded8; border-radius:14px; box-shadow:0 3px 12px #232f5a04; min-width:0; }
    .template-editor .builder-card { padding:28px; }
    .template-editor .section-title { font-family:Georgia,serif; font-size:21px; font-weight:400; margin:0 0 8px; color:#242936; }
    .section-description { margin:0 0 25px; color:#79756e; font-size:13px; line-height:1.6; }
    .section-number { color:#3156e8; font-family:Arial,sans-serif; font-size:11px; font-weight:700; margin-right:10px; }
    .template-editor .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:22px 20px; }
    .template-editor .form-group.full { grid-column:1 / -1; }
    .template-editor .form-label,.template-editor .field-group label { display:block; font-size:12px; font-weight:600; color:#494c58; margin-bottom:8px; }
    .optional { color:#918c83; font-size:11px; font-weight:400; margin-left:5px; }
    .template-editor .form-control { display:block; width:100%; min-width:0; min-height:43px; padding:11px 12px; border:1px solid #deddd8; border-radius:8px; color:#242936; font:13px Arial,sans-serif; background:#fff; transition:border-color .15s,box-shadow .15s; }
    .template-editor .form-control::placeholder { color:#96979f; }
    .template-editor .form-control:focus { outline:none; border-color:#6884ee; box-shadow:0 0 0 3px #3156e814; }
    .template-editor textarea.form-control { min-height:120px; resize:vertical; line-height:1.6; }
    .field-help { display:block; margin-top:7px; font-size:11px; color:#817d75; line-height:1.5; }
    .template-editor .form-actions { display:flex; align-items:center; justify-content:flex-end; flex-wrap:wrap; gap:12px; margin-top:26px; padding-top:22px; border-top:1px solid #eeece7; }
    .template-editor .btn,.template-editor .add-field-btn,.template-editor .add-column-btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; min-height:40px; padding:10px 16px; border-radius:8px; border:1px solid transparent; font:600 12px Arial,sans-serif; cursor:pointer; text-align:center; line-height:1.4; }
    .template-editor .btn-primary { background:#3156e8; color:#fff; box-shadow:0 3px 7px #3156e820; }
    .template-editor .btn-primary:hover { background:#2446c9; }
    .template-editor .btn-secondary { border-color:#dedbd5; background:#fff; color:#595951; }
    .template-editor .btn-secondary:hover { background:#f7f6f2; }
    .template-editor .btn-approval-workflow,.template-editor .add-field-btn,.template-editor .add-column-btn { background:#eef2ff; color:#3156e8; border-color:#dce4ff; }
    .template-editor .add-field-btn:hover,.template-editor .add-column-btn:hover,.template-editor .btn-approval-workflow:hover { background:#e1e8ff; }
    .template-editor a:focus-visible,.template-editor button:focus-visible,.template-editor input[type=checkbox]:focus-visible { outline:3px solid #82aaff; outline-offset:3px; }
    .editor-guide { padding:24px; }
    .guide-icon { display:grid; place-items:center; width:40px; height:44px; border-radius:9px; background:#eef2ff; color:#3156e8; margin-bottom:18px; }
    .guide-icon svg { width:22px; height:22px; }
    .editor-guide h2 { font-family:Georgia,serif; font-size:19px; font-weight:400; margin:0 0 10px; }
    .editor-guide p { font-size:12px; line-height:1.8; color:#79756e; margin:0; }
    .guide-list { list-style:none; margin:22px 0; padding:0; }
    .guide-list li { border-top:1px solid #eeece7; padding:15px 0; }
    .guide-list strong { display:block; font-size:12px; margin-bottom:5px; }
    .guide-note { background:#f7f8fc; padding:13px; border-radius:8px; }
    .template-editor .template-info { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; margin:20px 0 28px; padding-bottom:25px; border-bottom:1px solid #eeece7; }
    .template-editor .info-item { background:#f8f8fa; border:1px solid #eeedf1; border-radius:9px; padding:16px; min-width:0; }
    .template-editor .info-label { display:block; font-size:10px; font-weight:600; color:#85838d; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; }
    .template-editor .info-value { font-size:13px; font-weight:600; overflow-wrap:anywhere; }
    .template-editor .field-builder { border:1px solid #e5e5eb; border-radius:10px; overflow:hidden; margin-top:18px; }
    .template-editor .field-builder-header { background:#f7f8fc; padding:16px 18px; font-size:12px; font-weight:600; color:#626779; }
    .template-editor .field-row,.template-editor .table-column-row { display:grid; grid-template-columns:minmax(0,2fr) minmax(0,1fr) 70px 44px; gap:12px; align-items:end; padding:18px; border-top:1px solid #eeeef2; }
    .template-editor .field-group { min-width:0; }
    .template-editor .field-group label { font-size:10px; }
    .template-editor .required-group { display:flex; align-items:center; gap:7px; min-height:43px; font-size:12px; color:#626779; }
    .template-editor input[type=checkbox] { width:16px; height:16px; accent-color:#3156e8; cursor:pointer; }
    .template-editor .remove-field { display:inline-grid; place-items:center; height:43px; width:40px; border:1px solid #f1dddd; border-radius:8px; background:#fff6f5; color:#be5252; font-size:20px; cursor:pointer; }
    .template-editor .remove-field:hover { background:#fee2e2; }
    .template-editor .add-field-area { padding:15px 18px; background:#fafbfe; border-top:1px solid #eeeef2; }
    .template-editor .empty-fields { padding:32px 18px; text-align:center; color:#85818c; font-size:13px; line-height:1.7; }
    .template-editor .table-builder { margin-top:24px; padding:28px; }
    .template-editor .table-builder > .field-builder-header { display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; background:transparent; padding:0; margin-bottom:24px; }
    .template-editor .field-builder-header h2 { font:400 21px Georgia,serif; color:#242936; margin:0 0 8px; }
    .template-editor .field-builder-header p { font-size:13px; font-weight:400; margin:0; line-height:1.6; color:#79756e; }
    .template-editor .template-table-card { border:1px solid #e5e5eb; border-radius:10px; overflow:hidden; margin-bottom:18px; }
    .template-editor .table-header { display:flex; justify-content:space-between; align-items:center; padding:13px 18px; background:#f7f8fc; border-bottom:1px solid #eeeef2; font-size:13px; }
    .template-editor .table-info { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; padding:20px; }
    .template-editor .columns-section { padding:0 20px 20px; }
    .template-editor .columns-header { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:14px; font-size:12px; }
    .template-editor .table-column-row { padding:14px; background:#fafbfe; border:1px solid #eeeef2; border-radius:8px; margin-bottom:10px; }
    .template-editor .alert { padding:16px 20px; border-radius:9px; font-size:13px; line-height:1.7; margin-bottom:22px; }
    .template-editor .alert-danger { background:#fff3f2; color:#a63f3f; border:1px solid #f2d5d2; }
    .template-editor .alert-success { background:#edf8f1; color:#286944; border:1px solid #cde8d6; }
    @media(max-width:1200px) { .create-layout { grid-template-columns:minmax(0,1fr); } .template-editor .field-row,.template-editor .table-column-row { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:900px) { .template-editor .template-info { grid-template-columns:1fr; gap:10px; } .template-editor .builder-card,.template-editor .table-builder { padding:20px; } }
    @media(max-width:700px) { .main { margin-left:70px; width:calc(100% - 70px); padding:0; } .editor-steps { gap:12px; } .editor-steps li { font-size:11px; } }
    @media(max-width:520px) { .editor-body { padding:20px 12px 32px; } .editor-header { padding:24px 18px; } .template-editor .form-grid,.template-editor .field-row,.template-editor .table-column-row,.template-editor .table-info { grid-template-columns:minmax(0,1fr); } .template-editor .form-actions > * { width:100%; } .template-editor .builder-card,.template-editor .table-builder { padding:18px 14px; } .template-editor .columns-section { padding:0 12px 12px; } }
</style>
