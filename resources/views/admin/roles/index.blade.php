@extends('layouts.app')
@section('title', 'Update Roles and Memo Status Labels')
@section('header-title', 'Update')
@section('header-description', 'Manage the roles in user registration and the words shown for memo statuses.')
@section('content')
<style>
    .role-edit-page { --edit-bg:#19263b; --edit-panel:#1e2c43; --edit-border:#30405a; --edit-text:#e0e8f5; --edit-muted:#a3b1c7; --edit-accent:#8fb4ff; width:100%; max-width:1280px; margin:0; color:var(--edit-text); }
    .role-edit-list { display:grid; gap:14px; }
    .settings-edit-layout { display:grid; grid-template-columns:220px minmax(0,1fr); gap:20px; align-items:start; }
    .settings-edit-nav { display:grid; gap:7px; padding:12px; border:1px solid var(--edit-border); border-radius:12px; background:var(--edit-bg); position:sticky; top:18px; }
    .settings-edit-nav button { padding:11px 12px; border:1px solid transparent; border-radius:8px; background:transparent; color:var(--edit-muted); text-align:left; font:inherit; cursor:pointer; }
    .settings-edit-nav button[aria-selected="true"] { background:#2c4576; color:#e1ebff; border-color:#405985; }
    .settings-edit-panel[hidden] { display:none; }
    .role-edit-card { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1.5fr) 130px auto; gap:14px; align-items:end; padding:18px; background:var(--edit-panel); border:1px solid var(--edit-border); border-radius:10px; }
    .role-edit-card label { min-width:0; }
    .role-edit-card label { display:grid; gap:7px; color:var(--edit-muted); font-size:12px; }
    .role-edit-card input,.role-edit-card select { width:100%; min-height:40px; padding:9px 11px; border:1px solid #40516c; border-radius:8px; background:#17243a; color:var(--edit-text); }
    .role-edit-card input:focus,.role-edit-card select:focus { border-color:var(--edit-accent); outline:2px solid #8fb4ff40; }
    .role-edit-card button { min-height:40px; padding:9px 16px; border:1px solid #7296ff50; border-radius:8px; background: var(--primary); color:#fff; font:inherit; font-weight:400; cursor:pointer; }
    .role-edit-card button:hover { background:var(--primary-hover); }
    .role-edit-notice,.role-edit-error,.role-edit-empty { padding:14px 16px; border:1px solid var(--edit-border); border-radius:9px; background:var(--edit-panel); }
    .role-edit-notice { color:#b8e7cb; border-color:#37634d; background:#203b34; }
    .role-edit-error { color:#ffc3c8; border-color:#70414b; background:#3d2833; }
    .role-edit-empty { color:var(--edit-muted); }
    .role-edit-section { margin:0; color:var(--edit-text); font-size:16px; }
    .role-edit-help { margin:6px 0 0; color:var(--edit-muted); font-size:13px; line-height:1.6; }
    .edit-section-panel { display:grid; gap:16px; padding:24px; background:var(--edit-bg); border:1px solid var(--edit-border); border-radius:16px; }
    .edit-section-heading { display:flex; align-items:center; justify-content:space-between; gap:16px; }
    .edit-count { flex-shrink:0; padding:6px 10px; background:var(--primary-soft); color:var(--primary-link); border-radius:20px; font-size:12px; }
    .edit-add-role { border-style:dashed; }
    .edit-form-heading { grid-column:1 / -1; margin:0; font-size:13px; font-weight:650; color:var(--edit-text); }
    .edit-status-card { grid-template-columns:repeat(3,minmax(0,1fr)); }
    .edit-status-card button { grid-column:1 / -1; justify-self:end; }
    .edit-shortcuts { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; }
    .edit-shortcut { display:block; padding:18px; border:1px solid var(--edit-border); border-radius:10px; background:var(--edit-panel); text-decoration:none; color:var(--edit-text); }
    .edit-shortcut strong { display:block; margin-bottom:6px; font-size:14px; }
    .edit-shortcut span { color:var(--edit-muted); font-size:12px; line-height:1.6; }
    .edit-shortcut:hover { border-color:var(--primary); }
    .edit-shortcut:focus-visible { outline:2px solid var(--primary); outline-offset:3px; }
    @media(max-width:1000px) { .role-edit-card { grid-template-columns:1fr 1fr; }.edit-shortcuts { grid-template-columns:1fr; } }
    @media(max-width:600px) { .role-edit-card { grid-template-columns:1fr; }.edit-section-panel { padding:16px; }.edit-section-heading { align-items:flex-start; }.role-edit-card button { width:100%; } }
    @media(max-width:760px) { .settings-edit-layout { grid-template-columns:1fr; }.settings-edit-nav { position:static; grid-template-columns:repeat(2,minmax(0,1fr)); }.settings-edit-nav button { text-align:center; } }
</style>
<div class="role-edit-page">
<div class="settings-edit-layout">
<nav class="settings-edit-nav" role="tablist" aria-label="Settings sections">
    <button type="button" role="tab" aria-controls="settings-roles" aria-selected="true" data-settings-tab="roles">Roles &amp; registration</button>
    <button type="button" role="tab" aria-controls="settings-status" aria-selected="false" data-settings-tab="status">Memo status labels</button>
    <button type="button" role="tab" aria-controls="settings-more" aria-selected="false" data-settings-tab="more">More settings</button>
</nav>
<div class="role-edit-list">
    @if(session('success'))<div class="role-edit-notice" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="role-edit-error" role="alert">{{ $errors->first() }}</div>@endif
    <section class="edit-section-panel settings-edit-panel" id="settings-roles" data-settings-panel="roles" aria-labelledby="edit-roles-heading">
    <div class="edit-section-heading">
        <div><h2 class="role-edit-section" id="edit-roles-heading">Roles &amp; registration</h2>
        <p class="role-edit-help">Manage role names, descriptions, and availability in registration.</p></div>
        <span class="edit-count">{{ $roles->count() }} roles · {{ $roles->where('status', true)->count() }} active</span>
    </div>
    <form method="POST" action="{{ route('admin.roles.store') }}" class="role-edit-card edit-add-role">
        <h3 class="edit-form-heading">Add a new role</h3>
        @csrf
        <label>New role name<input name="role_name" required maxlength="255" value="{{ old('role_name') }}"></label>
        <label>Description<input name="description" value="{{ old('description') }}"></label>
        <label>Status<select name="status"><option value="1" @selected(old('status', '1') === '1')>Active</option><option value="0" @selected(old('status') === '0')>Inactive</option></select></label>
        <button type="submit">Add role</button>
    </form>
    @foreach($roles as $role)
        <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="role-edit-card">
            @csrf @method('PUT')
            <label>Role name
                <input name="role_name" value="{{ old('role_name', $role->role_name) }}" required maxlength="255">
            </label>
            <label>Description
                <input name="description" value="{{ old('description', $role->description) }}">
            </label>
            <label>Status
                <select name="status">
                    <option value="1" @selected((string) old('status', (int) $role->status) === '1')>Active</option>
                    <option value="0" @selected((string) old('status', (int) $role->status) === '0')>Inactive</option>
                </select>
            </label>
            <button type="submit" aria-label="Save {{ $role->role_name }} role">Save role</button>
        </form>
    @endforeach
    @if($roles->isEmpty())<p class="role-edit-empty">No roles are available.</p>@endif
    </section>
    <section class="edit-section-panel settings-edit-panel" id="settings-status" data-settings-panel="status" aria-labelledby="edit-status-heading" hidden>
    <div><h2 class="role-edit-section" id="edit-status-heading">Memo status labels</h2>
    <p class="role-edit-help">Choose the words people see for each status. Changing a label does not change the approval workflow.</p></div>
    <form method="POST" action="{{ route('admin.memo-status-labels.update') }}" class="role-edit-card edit-status-card">
        @csrf @method('PUT')
        @foreach(['draft' => 'Draft', 'pending' => 'Pending', 'approved' => 'Approved'] as $statusKey => $defaultLabel)
            <label>{{ $defaultLabel }} label
                <input name="labels[{{ $statusKey }}]" required maxlength="50" value="{{ old('labels.'.$statusKey, $memoStatusLabels[$statusKey]->label ?? $defaultLabel) }}">
            </label>
        @endforeach
        <button type="submit">Save labels</button>
    </form>
    </section>
    <section class="edit-section-panel settings-edit-panel" id="settings-more" data-settings-panel="more" aria-labelledby="edit-more-heading" hidden>
        <div><h2 class="role-edit-section" id="edit-more-heading">More settings</h2>
        <p class="role-edit-help">Open the other editing tools available in the system.</p></div>
        <div class="edit-shortcuts">
            <a class="edit-shortcut" href="{{ route('admin.users.create') }}"><strong>User accounts &rarr;</strong><span>Update personal details, roles, passwords, and signatures.</span></a>
            <a class="edit-shortcut" href="{{ route('admin.access-menu') }}"><strong>Access &amp; navigation &rarr;</strong><span>Arrange sidebar links and manage access by role.</span></a>
            <a class="edit-shortcut" href="{{ route('templates.index') }}"><strong>Templates &amp; approvals &rarr;</strong><span>Edit template fields, tables, and approval workflows.</span></a>
        </div>
    </section>
</div>
</div>
<script>
document.querySelectorAll('[data-settings-tab]').forEach(tab => tab.addEventListener('click', () => {
    document.querySelectorAll('[data-settings-tab]').forEach(item => item.setAttribute('aria-selected', String(item === tab)));
    document.querySelectorAll('[data-settings-panel]').forEach(panel => panel.hidden = panel.dataset.settingsPanel !== tab.dataset.settingsTab);
}));
</script>
@endsection
