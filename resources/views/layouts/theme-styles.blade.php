<style>
    .theme-toggle { display:inline-grid; place-items:center; flex-shrink:0; width:34px; height:34px; padding:7px; border:1px solid #d5dfee; border-radius:50%; background:#fff; color:#48638e; cursor:pointer; vertical-align:middle; }
    .theme-toggle svg { width:18px; height:18px; }.theme-toggle .theme-sun { display:none; }
    .theme-toggle:hover { background:#e9efff; }.theme-toggle:focus-visible { outline:3px solid #82aaff; outline-offset:3px; }
    .app-header-account { display:flex; align-items:center; gap:10px; }
    .auth-theme-toggle { position:absolute; top:18px; right:20px; }
    @media screen {
        html { color-scheme:light; }
        html[data-theme=dark] { color-scheme:dark; --ui-ink:#e0e8f5; --ui-muted:#a3b1c7; --ui-line:#30405a; --ui-shadow:0 4px 16px #0002; --ink:#e0e8f5; --muted:#a3b1c7; --line:#30405a; }
        html[data-theme=dark] body { background:#101827; color:#e0e8f5; }
        html[data-theme=dark] .theme-toggle { background:#22314a; color:#f6d788; border-color:#40516c; }
        html[data-theme=dark] .theme-moon { display:none; }html[data-theme=dark] .theme-toggle .theme-sun { display:block; }
        html[data-theme=dark] .app-page-header { background:linear-gradient(115deg,#19263b,#1e304c) !important; border-color:#30405a !important; }
        html[data-theme=dark] :is(.templates-page,.dash) { --muted:#a3b1c7; --line:#30405a; }
        html[data-theme=dark] :is(.my-memos-page,.all-memos-page,.approvals-page,.template-editor,.templates-page,.new-memo-page,.create-memo-page,.memo-review) { color:#e0e8f5; }
        html[data-theme=dark] :is(.memo-table-card,.approval-register,.builder-card,.table-builder,.dash-panel,.dash-stat,.dash-shortcut,.template-selection-card,.create-template-card,.library-tools,.template-item,.template-empty,.side-card,.approval-dialog,.login-card,.register-container,.memo-editor form) { background:#19263b !important; border-color:#30405a !important; color:#e0e8f5; }
        html[data-theme=dark] :is(.field-builder-header,.table-header,.register-heading,.template-table-card,.table-info,.dash-panel-footer,.signature-section,.profile-assignment,.template-info) { background:#1e2c43; border-color:#30405a; }
        html[data-theme=dark] :is(.app-page-header h1,.dash h1,.dash h2,.template-name,.department-heading h2,.library-tools-title,.section-title,.selection-title,.create-template-title,.side-card-title,.chain-person,.subject-title,.memo-subject,.table-header h2,.register-heading h2,.auth-brand,.login-header h1,.register-header h1,.form-section-heading,.info-value) { color:#e0e8f5 !important; }
        html[data-theme=dark] :is(.app-header-description,.library-tools-hint,.template-description,.template-meta,.template-results,.section-description,.field-help,.form-label,.field-group label,.memo-editor label,.dash-stat-top,.dash-stat small,.dash-meta,.dash-subtitle,.muted,.muted-text,.memo-template,.chain-role,.detail-row,.auth-footer,.auth-brand-caption,.register-link,.login-header p,.register-header p,.form-group label,.info-label) { color:#a3b1c7 !important; }
        html[data-theme=dark] :is(.form-control,.template-search,.search-box input,.approval-search input,.memo-editor input,.memo-editor textarea,.memo-editor select,.register-container input,.register-container select,.side-card textarea) { background:#111d30 !important; color:#e0e8f5 !important; border-color:#40516c !important; }
        html[data-theme=dark] input::placeholder,html[data-theme=dark] textarea::placeholder { color:#8f9fb9 !important; }
        html[data-theme=dark] :is(.memo-table,.approval-table) th { background:#202f46; color:#b1bfd4; border-color:#30405a; }
        html[data-theme=dark] :is(.memo-table,.approval-table) td { border-color:#30405a; }
        html[data-theme=dark] :is(.memo-table,.approval-table) tbody tr:hover,html[data-theme=dark] .dash-memo:hover { background:#21324d; }
        html[data-theme=dark] :is(.department-filters,.template-card-footer,.dash-panel-header,.dash-memo,.dash-approval,.field-row,.table-column-row) { border-color:#30405a; }
        html[data-theme=dark] :is(.department-filter,.status-filter,.approval-tab,.app-header-button.secondary,.profile-link,.btn-secondary,.print-button,.dialog-cancel) { background:#22314a !important; color:#c1d2ed !important; border-color:#40516c !important; }
        html[data-theme=dark] :is(.department-filter,.status-filter,.approval-tab).active { background:#2c4576 !important; border-color:#6387d4 !important; color:#e1ebff !important; }
        html[data-theme=dark] :is(.dash-icon,.template-file,.template-badge,.department-filter span,.filter-count,.department-heading > span,.chain-step,.empty-icon) { background:#263b60 !important; color:#aec9ff !important; }
        html[data-theme=dark] :is(.use-template,.library-clear,.dash-link,.view-link,.memo-number,.login-link,.register-link a,.app-header-back a,.app-header-eyebrow) { color:#a0bfff !important; }
        html[data-theme=dark] :is(.app-header-total,.pending-total) { background:#24314a !important; border-color:#40516c !important; color:#d1dcef !important; }
        html[data-theme=dark] .pending-total :is(strong,span) { color:#f0ce87; }

        /* Page-specific surfaces and controls share the workspace dark palette. */
        html[data-theme=dark] :is(.memo-register,.form-card,.preview-card,.department-card,.department-templates,.workflow-card,.template-info-card,.editor-guide) { background:#19263b !important; color:#e0e8f5; border-color:#30405a !important; }
        html[data-theme=dark] :is(.department-template-panel,.department-card[open] summary,.department-card summary:hover,.approver-row,.guide-note,.info-item,.table-column-row,.add-field-area,.preview-header,.pagination-area,.pagination-wrap,.workflow-info) { background:#1e2c43 !important; color:#c1d2ed; border-color:#30405a !important; }
        html[data-theme=dark] :is(.department-template,.department-name,.department-template-name,.card-header h2,.form-section h3,.approval-section h3,.step-content strong,.preview-header h2,.workflow-page h2,.field-builder-header h2,.approval-dialog h2,.action-content p,.detail-row strong,.memo-author,.approval-summary strong,.empty-state h3,.all-memos-empty h2) { color:#e0e8f5 !important; }
        html[data-theme=dark] :is(.selection-description,.department-count,.department-template-results,.department-search-label,.department-template-description,.department-empty,.card-header p,.form-group small,.step-content span,.preview-header span,.template-label,.template-department,.required-checkbox,.empty-approvers-message,.editor-guide p,.optional,.required-group,.empty-fields,.field-builder-header,.field-builder-header p,.editor-steps li,.approval-dialog p,.action-content small,.memo-department,.memo-date,.approval-summary,.department-cell,.date-cell,.pagination-info,.empty-state p,.register-heading span,.record-count,.progress-label,.step-label,.waiting-label) { color:#a3b1c7 !important; }
        html[data-theme=dark] :is(.memo-search input,.department-template-search,.workflow-page select,.workflow-page input:not([type=checkbox]):not([type=radio]),.memo-editor select,.memo-editor textarea) { background:#111d30 !important; color:#e0e8f5 !important; border-color:#40516c !important; }
        html[data-theme=dark] :is(.memo-table,.approval-table) td { color:#bdcbe0; }
        html[data-theme=dark] :is(.status-tab,.view-button,.edit-button,.add-field-btn,.add-column-btn,.btn-approval-workflow,.template-reset,.memo-search button,.memo-total,.editor-steps span,.memo-editor button:not(.primary)) { background:#22314a !important; color:#c1d2ed !important; border-color:#40516c !important; }
        html[data-theme=dark] :is(.status-tab.active,.editor-steps .current span) { background:#2c4576 !important; color:#e1ebff !important; border-color:#6387d4 !important; }
        html[data-theme=dark] :is(.department-template:hover,.dash-shortcut:hover,.approver-row:hover) { background:#21324d !important; }
        html[data-theme=dark] :is(.department-icon,.department-toggle,.template-document-icon,.create-template-icon,.template-icon,.step-number,.guide-icon,.info-icon,.empty-mark,.tab-count) { background:#263b60 !important; color:#aec9ff !important; }
        html[data-theme=dark] :is(.department-template-action,.section-number,.editor-steps .current) { color:#a0bfff !important; }
        html[data-theme=dark] :is(.department-template,.department-templates,.form-actions,.card-header,.approval-section,.side-card-title,.signature-section,.form-section-heading,.field-builder,.columns-section,.empty-fields,.editor-steps) { border-color:#30405a !important; }
        html[data-theme=dark] :is(.pagination-wrap,.pagination-area) :is(a,[aria-current] > span,[aria-disabled] > span) { background:#22314a !important; color:#c1d2ed !important; border-color:#40516c !important; }
        html[data-theme=dark] :is(.pagination-wrap,.pagination-area) [aria-current] > span { background:var(--primary) !important; color:#fff !important; }
        html[data-theme=dark] :is(.pagination-wrap,.pagination-area) [aria-disabled] > span { background:#172238 !important; color:#8f9fb9 !important; }
        html[data-theme=dark] :is(.pagination-wrap,.pagination-area) p,html[data-theme=dark] :is(.pagination-wrap,.pagination-area) p span { color:#a3b1c7 !important; }
        html[data-theme=dark] :is(.status,.status-badge,.dash-status).pending { background:#483919 !important; color:#f5d18a !important; }
        html[data-theme=dark] :is(.status,.status-badge,.dash-status).approved,html[data-theme=dark] :is(.alert-success,.review-alert.success) { background:#193f32 !important; color:#a1e5bd !important; border-color:#32644e !important; }
        html[data-theme=dark] :is(.status,.status-badge,.dash-status).rejected,html[data-theme=dark] :is(.remove-field,.remove-approver,.reject-button,.alert-danger,.alert-error,.decision-error,.review-alert.error) { background:#492a35 !important; color:#ffb4bc !important; border-color:#754150 !important; }
        html[data-theme=dark] :is(.status,.status-badge,.dash-status).draft { background:#2b394f !important; color:#c5d1e4 !important; }
        html[data-theme=dark] .calculated-field { background:#25334a !important; color:#b6c6df !important; }
        html[data-theme=dark] .progress-track { background:#34445e; }

        /* Secondary UI and dynamic rows need the same palette as their parent cards. */
        html[data-theme=dark] :is(.memo-insert-bar,#memo-insert-dialog,.memo-text-editor,.memo-text-block) { background:#19263b; color:#e0e8f5; border-color:#30405a; }
        html[data-theme=dark] #memo-insert-dialog::backdrop { background:#050b16b3; }
        html[data-theme=dark] :is(.memo-insert-actions > span,#memo-insert-dialog p:not([data-insert-error]),#memo-insert-dialog label) { color:#a3b1c7; }
        html[data-theme=dark] #memo-insert-dialog :is(h2,h3) { color:#e0e8f5; }
        html[data-theme=dark] #memo-insert-dialog [data-insert-error] { color:#ffb4bc; }
        html[data-theme=dark] :is(.memo-insert-bar,#memo-insert-dialog,.memo-text-editor) .btn-secondary:not(:disabled):hover { background:#2c4576 !important; color:#e1ebff !important; }
        html[data-theme=dark] :is(.memo-insert-bar,#memo-insert-dialog,.memo-text-editor) button:disabled { opacity:.45; cursor:not-allowed; }
        html[data-theme=dark] :is(.create-memo-page,.memo-editor) :is(button,a,[contenteditable=true]):focus-visible { outline:2px solid #82aaff; outline-offset:3px; }
        html[data-theme=dark] .create-memo-page .form-control:focus { border-color:#82aaff !important; }
        html[data-theme=dark] .memo-editor .actions a { color:#a0bfff; }
        /* My Memos has a filter tray and nested pagination spans of its own. */
        html[data-theme=dark] .my-memos-page .status-filters { background:#19263b; border-color:#30405a; }
        html[data-theme=dark] .my-memos-page :is(.table-header p,.pagination-info strong,.search-icon,.clear-search) { color:#a3b1c7; }
        html[data-theme=dark] .my-memos-page .pagination-links :is(a,span) { background:#22314a; color:#c1d2ed; border-color:#40516c; }
        html[data-theme=dark] .my-memos-page .pagination-links :is([aria-current=page],[aria-current=page] > span) { background:var(--primary) !important; color:#fff !important; border-color:var(--primary) !important; }
        html[data-theme=dark] .my-memos-page .pagination-links :is([aria-disabled=true],[aria-disabled=true] > span) { background:#172238 !important; color:#8f9fb9 !important; border-color:#30405a !important; }
        html[data-theme=dark] .my-memos-page .pagination-links a:hover { background:var(--primary) !important; color:#fff !important; border-color:var(--primary) !important; }
        html[data-theme=dark] .my-memos-page :is(.status-filter:not(.active),.edit-button,.view-button):hover { background:var(--primary) !important; color:#fff !important; }
        html[data-theme=dark] .my-memos-page .clear-search:hover { color:#fff; }
        html[data-theme=dark] :is(.no-templates,.review-card) { background:#19263b; color:#e0e8f5; border-color:#30405a; }
        html[data-theme=dark] :is(.memo-table-title,.no-templates h3,.review-breadcrumb strong,.review-card .memo-field-value) { color:#e0e8f5; }
        html[data-theme=dark] :is(.create-template-description,.no-templates p,.no-templates-icon,.profile-assignment,.all-memos-empty p,.review-breadcrumb,.review-breadcrumb a,.approval-designation,.approval-date,.action-description,.review-card .memo-field-label) { color:#a3b1c7; }
        html[data-theme=dark] :is(.memo-entry-table th,.memo-entry-table td) { border-color:#40516c; }
        html[data-theme=dark] .memo-entry-table th { background:#202f46; color:#b1bfd4; }
        html[data-theme=dark] .memo-entry-table td { background:#19263b; color:#e0e8f5; }
        html[data-theme=dark] :is(.memo-table-form-section,.memo-field,.approval-history-item,.empty-approvers-message) { border-color:#30405a; }
        html[data-theme=dark] :is(.approval-line,.chain-item:not(:last-child)::after) { background:#40516c; }
        html[data-theme=dark] .approval-comment { background:#1e2c43; color:#c1d2ed; }
        html[data-theme=dark] :is(.dash-count,.approval-step-number) { background:#263b60; color:#aec9ff; }
        html[data-theme=dark] .dash-awaiting { color:#f5d18a; }
        html[data-theme=dark] .preview-header .preview-status { background:#483919; color:#f5d18a !important; }
        html[data-theme=dark] .dash-stat.amber .dash-icon { background:#483919 !important; color:#f5d18a !important; }
        html[data-theme=dark] .dash-stat.green .dash-icon { background:#193f32 !important; color:#a1e5bd !important; }
        html[data-theme=dark] .chain-item.current .chain-step { background:#483919 !important; color:#f5d18a !important; }
        html[data-theme=dark] .action-card { border-color:#997431 !important; }
        html[data-theme=dark] .action-card .side-card-title { color:#f5d18a !important; }
        html[data-theme=dark] .template-item:hover { background:#21324d !important; }
        html[data-theme=dark] input[type=file]::file-selector-button { background:#22314a; color:#c1d2ed; border:1px solid #40516c; }
        html[data-theme=dark] .toggle-password { color:#a3b1c7; }
        html[data-theme=dark] .toggle-password:hover { background:#22314a; }
        html[data-theme=dark] .document-preview { background:#fff; color:#26344a; color-scheme:light; }
        html[data-theme=dark] .document-preview .department-name { color:#808080 !important; }
        html[data-theme=dark] .document-preview .signature-section { background:#fff; color:#111; border-color:#111 !important; }
        /* Documents retain paper colors so signatures and printed output remain legible. */
        html[data-theme=dark] .memo-paper { color:#26344a; background:#fff; color-scheme:light; }
        html[data-theme=dark] .memo-paper :is(.memo-number,.muted,.muted-text) { color:#53647a !important; }
        html[data-theme=dark] .profile-signature { background:#fff; }
    }
    @media print { .theme-toggle,.auth-theme-toggle { display:none !important; } }
</style>
@include('layouts.primary-styles')
