@extends('layouts.app')
@section('title', 'Access Menu')
@section('header-title', 'Access Menu')
@section('header-description', 'Arrange, add, or remove the items shown in the sidebar.')
@section('styles')
<style>
    .sidebar-editor { --editor-panel:#1e2c43; --editor-border:#30405a; --editor-text:#e0e8f5; --editor-muted:#a3b1c7; width:100%; color:var(--editor-text); }
    .sidebar-editor-layout { display:grid; grid-template-columns:minmax(0,760px) minmax(280px,1fr); gap:20px; align-items:start; }
    .sidebar-editor-stack { display:grid; gap:18px; width:100%; margin:0; }
    .sidebar-editor-card { padding:20px; border:1px solid var(--editor-border); border-radius:12px; background:#19263b; }
    .sidebar-editor-card h2 { margin:0; color:var(--editor-text); font-size:16px; }
    .sidebar-editor-help { margin:6px 0 16px; color:var(--editor-muted); font-size:12px; line-height:1.5; }
    .sidebar-editor-notice,.sidebar-editor-error { padding:12px 15px; border:1px solid var(--editor-border); border-radius:9px; background:var(--editor-panel); }
    .sidebar-editor-notice { color:#b9efd7; border-color:#27614e; background:#18382f; }
    .sidebar-editor-error { color:#ffd0d5; border-color:#7c414b; background:#3a252e; }
    .menu-drop-list { display:grid; gap:8px; min-height:12px; padding:0; margin:0; list-style:none; }
    .menu-drop-list.is-over { min-height:44px; padding:5px; border:1px dashed #8fb4ff; border-radius:9px; background:#8fb4ff0d; }
    .menu-sort-item { border:1px solid var(--editor-border); border-radius:9px; background:var(--editor-panel); }
    .menu-sort-item.is-dragging { opacity:.45; }
    .menu-sort-row { display:flex; align-items:center; gap:12px; min-height:48px; padding:10px 13px; cursor:grab; }
    .menu-sort-row:active { cursor:grabbing; }
    .menu-drag-handle { color:#93a8c8; font-size:18px; user-select:none; }
    .menu-sort-name { flex:1; color:var(--editor-text); font-size:13px; font-weight:650; }
    .menu-sort-name svg { width:18px; height:18px; margin-right:6px; vertical-align:middle; }
    .menu-sort-detail { color:var(--editor-muted); font-size:11px; }
    .menu-remove-button,.menu-restore-button,.menu-save-button,.menu-add-button { min-height:36px; padding:7px 12px; border:1px solid #40516c; border-radius:7px; background: #263852; color:var(--editor-text); font:inherit; font-size:12px; cursor:pointer; }
    .menu-remove-button:hover,.menu-restore-button:hover { border-color: #a85f70; color: #ffc3c8; }
    .menu-save-button,.menu-add-button { border-color:  var(--primary); background: var(--primary); color:#fff; font-weight:650; }
    .menu-save-button:hover,.menu-add-button:hover { background: #3ba873; }
    .menu-group-children { margin:0 12px 12px 38px; padding-left:10px; border-left:1px solid #ffffff20; }
    .menu-add-grid { display:grid; grid-template-columns:1fr 1.2fr 180px 160px auto; align-items:end; gap:12px; }
    .menu-field { display:grid; gap:6px; color:#c5d2e5; font-size:11px; font-weight:650; }
    .menu-field input,.menu-field select { width:100%; min-height:39px; padding:8px 10px; border:1px solid #40516c; border-radius:7px; background:#111b2e; color:var(--editor-text); font:inherit; font-size:12px; }
    .menu-restore-list { display:grid; gap:8px; }
    .menu-restore-row { display:flex; align-items:center; gap:12px; padding:10px 12px; border:1px solid var(--editor-border); border-radius:8px; background:var(--editor-panel); }
    .menu-restore-row span:first-child { flex:1; font-size:13px; }
    .menu-empty { color:var(--editor-muted); font-size:12px; }
    .role-access-card { position:sticky; top:20px; }
    .role-access-select { display:grid; width:220px; max-width:100%; margin:14px 0 16px; }
    .role-access-select select { min-width:0; min-height:39px; padding:8px 10px; border:1px solid #40516c; border-radius:7px; background:#111b2e; color:var(--editor-text); font:inherit; font-size:12px; }
    .role-access-options { display:grid; gap:8px; margin-bottom:14px; }
    .role-access-option { display:flex; align-items:center; gap:10px; min-height:42px; padding:9px 11px; color:var(--editor-text); font-size:12px; cursor:grab; }
    .role-access-option:active { cursor:grabbing; }
    .role-access-option label { display:flex; align-items:center; gap:10px; flex:1; cursor:pointer; }
    .role-access-option input { width:16px; height:16px; accent-color:#78a3f5; }
    .role-access-note { padding:12px; border:1px solid #665431; border-radius:8px; background:#342d20; color:#f1dca9; font-size:12px; line-height:1.5; }
    @media(max-width:900px) { .menu-add-grid { grid-template-columns:1fr 1fr; }.menu-add-button { align-self:end; } }
    @media(max-width:1120px) { .sidebar-editor-layout { grid-template-columns:minmax(0,1fr); }.role-access-card { position:static; } }
    @media(max-width:540px) { .sidebar-editor-card { padding:15px; }.menu-add-grid { grid-template-columns:1fr; }.menu-sort-detail { display:none; }.menu-group-children { margin-left:16px; } }
</style>
@endsection
@section('content')
<div class="sidebar-editor">
    <div class="sidebar-editor-layout">
    <div class="sidebar-editor-stack">
        @if(session('success'))<div class="sidebar-editor-notice" role="status">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="sidebar-editor-error" role="alert">{{ $errors->first() }}</div>@endif

        <section class="sidebar-editor-card" aria-labelledby="sidebar-arrange-heading">
            <h2 id="sidebar-arrange-heading">Sidebar items</h2>
            <p class="sidebar-editor-help">Drag an item with your cursor to move it. Drop it between other items to change its order, or drop it into a group to nest it. Remove links with the Remove button, then save your changes.</p>
            <form method="POST" action="{{ route('admin.access-menu.sidebar.update') }}" id="sidebar-order-form" data-menu-editor>
                @csrf @method('PUT')
                <input type="hidden" name="structure" id="sidebar-structure-input">
                <ul class="menu-drop-list" data-drop-list data-parent-id="">
                    @foreach($activeItems->whereNull('parent_id')->sortBy('sort_order') as $item)
                        @if($item->is_group)
                            @php($children = $activeItems->where('parent_id', $item->id)->sortBy('sort_order'))
                            <li class="menu-sort-item" data-menu-id="{{ $item->id }}" data-is-group="true" draggable="true">
                                <div class="menu-sort-row"><span class="menu-drag-handle" aria-hidden="true">⠿</span><span class="menu-sort-name">@include('dashboard.icon', ['icon' => $item->icon]) {{ $item->label }}</span><span class="menu-sort-detail">Group</span></div>
                                <ul class="menu-drop-list menu-group-children" data-drop-list data-parent-id="{{ $item->id }}">
                                    @foreach($children as $child)
                                        <li class="menu-sort-item" data-menu-id="{{ $child->id }}" data-is-group="false" draggable="true">
                                            <div class="menu-sort-row"><span class="menu-drag-handle" aria-hidden="true">⠿</span><span class="menu-sort-name">@include('dashboard.icon', ['icon' => $child->icon]) {{ $child->label }}</span><span class="menu-sort-detail">{{ $child->url ?? ($child->route_name ? '' : '') }}</span><button type="button" class="menu-remove-button" data-remove-menu-item>Remove</button></div>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li class="menu-sort-item" data-menu-id="{{ $item->id }}" data-is-group="false" draggable="true">
                                <div class="menu-sort-row"><span class="menu-drag-handle" aria-hidden="true">⠿</span><span class="menu-sort-name">@include('dashboard.icon', ['icon' => $item->icon]) {{ $item->label }}</span><span class="menu-sort-detail">{{ $item->url ?? ($item->route_name ? '' : '') }}</span><button type="button" class="menu-remove-button" data-remove-menu-item>Remove</button></div>
                            </li>
                        @endif
                    @endforeach
                </ul>
                <button type="submit" class="menu-save-button" style="margin-top:16px">Save sidebar</button>
            </form>
        </section>

        <section class="sidebar-editor-card" aria-labelledby="sidebar-add-heading">
            <h2 id="sidebar-add-heading">Add a sidebar link</h2>
            <p class="sidebar-editor-help">Add a link to a page in this app. Use a local path, for example <code>/reports</code>.</p>
            <form method="POST" action="{{ route('admin.access-menu.items.store') }}" class="menu-add-grid">
                @csrf
                <label class="menu-field">Item name<input name="label" required maxlength="80" value="{{ old('label') }}" placeholder="Reports"></label>
                <label class="menu-field">Page path<input name="url" required maxlength="500" value="{{ old('url') }}" placeholder="/reports"></label>
                <label class="menu-field">Place inside
                    <select name="parent_id"><option value="">Main sidebar</option>@foreach($groups as $group)<option value="{{ $group->id }}">{{ $group->label }}</option>@endforeach</select>
                </label>
                <label class="menu-field">Icon
                    <select name="icon"><option value="document">Document</option><option value="grid">Grid</option><option value="plus">Plus</option><option value="check">Check</option><option value="clock">Clock</option><option value="review">Review</option></select>
                </label>
                <button type="submit" class="menu-add-button">Add link</button>
            </form>
        </section>

        @if($availableItems->isNotEmpty())
            <section class="sidebar-editor-card" aria-labelledby="sidebar-restore-heading">
                <h2 id="sidebar-restore-heading">Removed items</h2>
                <p class="sidebar-editor-help">Restore an item to make it available in the sidebar again.</p>
                <div class="menu-restore-list">
                    @foreach($availableItems as $item)
                        <div class="menu-restore-row"><span>{{ $item->label }}</span><form method="POST" action="{{ route('admin.access-menu.items.restore', $item) }}">@csrf<button type="submit" class="menu-restore-button">Restore</button></form></div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
    <aside class="sidebar-editor-card role-access-card" aria-labelledby="role-access-heading">
        <h2 id="role-access-heading">Dashboard access by role</h2>
        <p class="sidebar-editor-help">Choose a role to control which dashboard links everyone assigned to it can access. Nested links appear under their sidebar group.</p>
        <form method="GET" action="{{ route('admin.access-menu') }}" class="role-access-select">
            <select id="dashboard-role-select" name="role_id" aria-label="Select role" required onchange="this.form.requestSubmit()">
                <option value="" disabled @selected(!$selectedRole)>Select a role</option>
                @foreach($roles as $role)<option value="{{ $role->id }}" @selected((string) ($selectedRole?->id ?? '') === (string) $role->id)>{{ $role->role_name }}</option>@endforeach
            </select>
        </form>
        @if($selectedRole)
            @php($isSelectedAdminRole = in_array(strtolower($selectedRole->role_name), ['admin', 'administrator'], true))
            @if($isSelectedAdminRole)
                <div class="role-access-note">Admin always sees all active sidebar links. You can save selections here, but they do not hide links or restrict administrator access.</div>
            @endif
                @php($selectedAccess = $selectedRole->menu_access ?? $roleAccessItems->map(fn ($item) => $item->access_key ?? $item->item_key)->unique()->values()->all())
                <p class="sidebar-editor-help">Drag links to reorder them or move them into a group. Saving also updates the shared sidebar arrangement for all roles.</p>
                <form method="POST" action="{{ route('admin.access-menu.role-access.update') }}" data-menu-editor>
                    @csrf @method('PUT')
                    <input type="hidden" name="role_id" value="{{ $selectedRole->id }}">
                    <input type="hidden" name="structure">
                    <ul class="menu-drop-list role-access-options" data-drop-list data-parent-id="">
                        @foreach($activeItems->whereNull('parent_id')->sortBy('sort_order') as $accessItem)
                            @if($accessItem->is_group)
                                <li class="menu-sort-item" data-menu-id="{{ $accessItem->id }}" data-is-group="true" draggable="true">
                                    <div class="menu-sort-row"><span class="menu-drag-handle" aria-hidden="true">&#x283F;</span><span class="menu-sort-name">{{ $accessItem->label }}</span><span class="menu-sort-detail">Group</span></div>
                                    <ul class="menu-drop-list menu-group-children" data-drop-list data-parent-id="{{ $accessItem->id }}">
                                        @foreach($roleAccessItems->where('parent_id', $accessItem->id) as $child)
                                            @include('admin.partials.role-access-item', ['accessItem' => $child])
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                @include('admin.partials.role-access-item')
                            @endif
                        @endforeach
                    </ul>
                    <button type="submit" class="menu-save-button">Save role access</button>
                </form>
        @else
            <div class="menu-empty">Select a role to view or update its dashboard access.</div>
        @endif
    </aside>
    </div>
</div>
<script>
(() => {
    document.querySelectorAll('[data-menu-editor]').forEach((editorForm) => {
    let draggedItem = null;

    editorForm.querySelectorAll('[data-menu-id]').forEach((item) => {
        item.addEventListener('dragstart', (event) => {
            event.stopPropagation();
            draggedItem = item;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', item.dataset.menuId);
            requestAnimationFrame(() => item.classList.add('is-dragging'));
        });
        item.addEventListener('dragend', (event) => {
            event.stopPropagation();
            item.classList.remove('is-dragging');
            draggedItem = null;
            editorForm.querySelectorAll('[data-drop-list]').forEach((list) => list.classList.remove('is-over'));
        });
    });

    editorForm.querySelectorAll('[data-drop-list]').forEach((list) => {
        list.addEventListener('dragover', (event) => {
            event.stopPropagation();
            if (!draggedItem) return;
            if (draggedItem.dataset.isGroup === 'true' && list.dataset.parentId !== '') return;
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            list.classList.add('is-over');
            const after = [...list.children].find((child) => {
                if (child === draggedItem) return false;
                const box = child.getBoundingClientRect();
                return event.clientY < box.top + box.height / 2;
            });
            if (after) list.insertBefore(draggedItem, after);
            else list.appendChild(draggedItem);
        });
        list.addEventListener('dragleave', (event) => {
            if (!list.contains(event.relatedTarget)) list.classList.remove('is-over');
        });
        list.addEventListener('drop', (event) => {
            event.stopPropagation();
            event.preventDefault();
            list.classList.remove('is-over');
        });
    });

    editorForm.querySelectorAll('[data-remove-menu-item]').forEach((button) => {
        button.addEventListener('click', () => button.closest('[data-menu-id]').remove());
    });

    editorForm.addEventListener('submit', (event) => {
        const entries = [];
        editorForm.querySelectorAll('[data-drop-list]').forEach((list) => {
            [...list.children].forEach((item, position) => entries.push({
                id: Number(item.dataset.menuId),
                parent_id: list.dataset.parentId ? Number(list.dataset.parentId) : null,
                position: (position + 1) * 10,
            }));
        });
        editorForm.querySelector('input[name="structure"]').value = JSON.stringify(entries);
    });
    });
})();
</script>
@endsection
