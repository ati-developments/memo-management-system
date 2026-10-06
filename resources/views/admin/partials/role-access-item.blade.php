@php($accessKey = $accessItem->access_key ?? $accessItem->item_key)
<li class="menu-sort-item" data-menu-id="{{ $accessItem->id }}" data-is-group="false" draggable="true">
    <div class="role-access-option">
        <span class="menu-drag-handle" aria-hidden="true">&#x283F;</span>
        <label>
            <input type="checkbox" name="access[]" value="{{ $accessKey }}" @checked(($isSelectedAdminRole || !$accessItem->admin_only) && in_array($accessKey, $selectedAccess, true)) @disabled(!$isSelectedAdminRole && $accessItem->admin_only)>
            <span>{{ $accessItem->label }}{{ $accessItem->admin_only ? ' (Admin only)' : '' }}</span>
        </label>
    </div>
</li>
