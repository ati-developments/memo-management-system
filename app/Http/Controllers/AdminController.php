<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Models\UserSignature;
use App\Models\MemoStatusLabel;
use App\Models\SidebarMenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    private const MENU_ITEMS = [
        'templates' => 'Templates',
        'new_memo' => 'New Memo',
        'memos' => 'Memos',
        'approvals' => 'Approvals',
    ];

    public function users()
    {
        $users = User::with(['department', 'role'])->orderBy('name')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function createUser(Request $request)
    {
        $request->validate(['search' => ['nullable', 'string', 'max:255']]);
        $search = trim((string) $request->input('search', ''));
        $users = User::with(['department', 'role', 'signature'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('employee_id', 'like', '%'.$search.'%')
                        ->orWhere('designation', 'like', '%'.$search.'%')
                        ->orWhereHas('department', fn ($department) => $department->where('department_name', 'like', '%'.$search.'%'))
                        ->orWhereHas('role', fn ($role) => $role->where('role_name', 'like', '%'.$search.'%'));
                });
            })
            ->orderBy('name')->orderBy('id')->paginate(10)->withQueryString();
        $departments = Department::where('status', true)->orderBy('department_name')->get();
        $roles = Role::where('status', true)->orderBy('role_name')->get();
        if ($request->ajax()) {
            return view('admin.users.partials.table', compact('users'));
        }
        return view('auth.register', [
            'departments' => $departments,
            'roles' => $roles,
            'registrationRoute' => 'admin.users.store',
            'adminRegistration' => true,
            'users' => $users,
        ]);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:127'],
            'last_name' => ['required', 'string', 'max:127'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'employee_id' => ['required', 'string', 'max:255', 'unique:users,employee_id'],
            'department_id' => ['required', 'exists:departments,id'],
            'designation' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'signature' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $user = User::create([
            ...collect($validated)->only(['username', 'email', 'employee_id', 'department_id', 'designation', 'role_id'])->all(),
            'name' => trim($validated['first_name']).' '.trim($validated['last_name']),
            'password' => Hash::make($validated['password']),
        ]);

        if ($request->hasFile('signature')) {
            UserSignature::create([
                'user_id' => $user->id,
                'signature_path' => $request->file('signature')->store('signatures', 'public'),
                'is_active' => true,
                'uploaded_at' => now(),
            ]);
        }

        return to_route('admin.users.create')->with('success', 'User account created.');
    }

    public function editUser(Request $request, User $user)
    {
        return $this->createUser($request)->with([
            'editingUser' => $user->load('signature'),
            'departments' => Department::orderBy('department_name')->get(),
            'roles' => Role::orderBy('role_name')->get(),
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:127'],
            'last_name' => ['required', 'string', 'max:127'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'employee_id' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user)],
            'department_id' => ['required', 'exists:departments,id'],
            'designation' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);
        if ($user->is($request->user()) && (int) $validated['role_id'] !== (int) $user->role_id) {
            return back()->withInput($request->except('password', 'password_confirmation', 'signature'))->withErrors(['role_id' => 'Use another administrator account to change your role.']);
        }
        $attributes = collect($validated)->only(['username', 'email', 'employee_id', 'department_id', 'designation', 'role_id'])->all();
        $attributes['name'] = trim($validated['first_name']).' '.trim($validated['last_name']);
        if ($request->filled('password')) {
            $attributes['password'] = Hash::make($validated['password']);
            $attributes['remember_token'] = Str::random(60);
        }
        if ($user->email !== $attributes['email']) {
            $attributes['email_verified_at'] = null;
        }
        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $attributes, $request) {
            $user->forceFill($attributes)->save();
            if ($request->hasFile('signature')) {
                $user->signature()->updateOrCreate([], [
                    'signature_path' => $request->file('signature')->store('signatures', 'public'),
                    'is_active' => true,
                    'uploaded_at' => now(),
                ]);
            }
        });

        return to_route('admin.users.create')->with('success', 'User account updated.');
    }

    public function destroyUser(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }
        // These relationships cascade on deletion; preserve existing memo history.
        foreach (['memos' => 'created_by', 'memo_templates' => 'created_by', 'memo_approvals' => 'approver_id', 'approval_steps' => 'approver_id'] as $table => $column) {
            if (\Illuminate\Support\Facades\DB::table($table)->where($column, $user->id)->exists()) {
                return back()->withErrors(['user' => 'This user is linked to memos or approval workflows and cannot be deleted.']);
            }
        }
        $user->delete();

        return to_route('admin.users.create')->with('success', 'User account deleted.');
    }

    public function accessMenu(Request $request)
    {
        $menuItems = SidebarMenuItem::orderBy('parent_id')->orderBy('sort_order')->orderBy('id')->get();
        $activeItems = $menuItems->where('is_active', true);
        $availableItems = $menuItems->where('is_active', false)->where('is_group', false);
        $groups = $activeItems->where('is_group', true);
        $roles = Role::orderBy('role_name')->get();
        $selectedRole = $request->filled('role_id') ? Role::findOrFail($request->integer('role_id')) : null;
        $roleAccessItems = $activeItems->where('is_group', false)->sortBy('sort_order');

        return view('admin.access-menu', compact('activeItems', 'availableItems', 'groups', 'roles', 'selectedRole', 'roleAccessItems'));
    }

    public function updateRoleMenuAccess(Request $request)
    {
        $accessKeys = SidebarMenuItem::where('is_group', false)->where('is_active', true)->get()
            ->map(fn ($item) => $item->access_key ?? $item->item_key)->unique()->values()->all();
        $validated = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'access' => ['nullable', 'array'],
            'access.*' => ['string', Rule::in($accessKeys)],
        ]);

        $role = Role::findOrFail($validated['role_id']);
        if ($request->has('structure') && ($error = $this->saveSidebarStructure($request))) {
            return $error;
        }

        $role->update(['menu_access' => array_values(array_intersect($validated['access'] ?? [], $accessKeys))]);

        return to_route('admin.access-menu', ['role_id' => $role->id])->with('success', 'Dashboard access updated for the ' . $role->role_name . ' role.');
    }

    public function updateSidebarMenu(Request $request)
    {
        return $this->saveSidebarStructure($request)
            ?? to_route('admin.access-menu')->with('success', 'Sidebar items updated.');
    }

    private function saveSidebarStructure(Request $request)
    {
        $validated = $request->validate(['structure' => ['required', 'string', 'max:30000']]);
        $structure = json_decode($validated['structure'], true);
        if (!is_array($structure)) {
            return back()->withErrors(['structure' => 'The sidebar order could not be read.']);
        }

        $ids = array_column($structure, 'id');
        if (count($ids) !== count(array_unique($ids)) || collect($structure)->contains(fn ($item) => !is_array($item) || !isset($item['id']) || !is_numeric($item['id']) || !array_key_exists('parent_id', $item))) {
            return back()->withErrors(['structure' => 'The sidebar order contains invalid items.']);
        }

        $items = SidebarMenuItem::whereIn('id', $ids)->get()->keyBy('id');
        if ($items->count() !== count($ids)) {
            return back()->withErrors(['structure' => 'A sidebar item could not be found.']);
        }
        foreach ($structure as $entry) {
            $item = $items->get((int) $entry['id']);
            $parentId = $entry['parent_id'] === null ? null : (int) $entry['parent_id'];
            if ($item->is_group && $parentId !== null) {
                return back()->withErrors(['structure' => 'Sidebar groups must stay at the top level.']);
            }
            if ($parentId !== null && (!$items->has($parentId) || !$items->get($parentId)->is_group)) {
                return back()->withErrors(['structure' => 'Menu links can only be placed inside a sidebar group.']);
            }
        }

        $requiredGroups = SidebarMenuItem::where('is_group', true)->pluck('id')->map(fn ($id) => (int) $id)->all();
        if (array_diff($requiredGroups, array_map('intval', $ids))) {
            return back()->withErrors(['structure' => 'Sidebar groups cannot be removed.']);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($structure, $items) {
            SidebarMenuItem::where('is_group', false)->whereNotIn('id', $items->keys())->update(['is_active' => false]);
            foreach ($structure as $entry) {
                $item = $items->get((int) $entry['id']);
                $parentId = $entry['parent_id'] === null ? null : (int) $entry['parent_id'];
                $item->update([
                    'parent_id' => $parentId,
                    'sort_order' => (int) ($entry['position'] ?? 0),
                    'is_active' => true,
                ]);
            }
        });

        return null;
    }

    public function storeSidebarMenuItem(Request $request)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'url' => ['required', 'string', 'max:500', 'starts_with:/'],
            'parent_id' => ['nullable', 'integer', 'exists:sidebar_menu_items,id'],
            'icon' => ['required', Rule::in(['document', 'grid', 'plus', 'check', 'clock', 'review'])],
        ]);
        if (str_starts_with($validated['url'], '//') || str_contains($validated['url'], '\\')) {
            return back()->withErrors(['url' => 'Use a local path beginning with one slash.']);
        }
        if (!empty($validated['parent_id'])) {
            $parent = SidebarMenuItem::findOrFail($validated['parent_id']);
            if (!$parent->is_group || !$parent->is_active) {
                return back()->withErrors(['parent_id' => 'Choose an active sidebar group.']);
            }
        }

        SidebarMenuItem::create([
            'item_key' => 'custom_' . Str::uuid(),
            'label' => trim($validated['label']),
            'url' => $validated['url'],
            'parent_id' => $validated['parent_id'] ?? null,
            'icon' => $validated['icon'],
            'sort_order' => (int) SidebarMenuItem::where('parent_id', $validated['parent_id'] ?? null)->max('sort_order') + 10,
        ]);

        return to_route('admin.access-menu')->with('success', 'Sidebar item added.');
    }

    public function restoreSidebarMenuItem(SidebarMenuItem $item)
    {
        abort_if($item->is_group, 422);
        if ($item->parent_id && !SidebarMenuItem::whereKey($item->parent_id)->where('is_active', true)->exists()) {
            $item->update(['parent_id' => null]);
        }
        $item->update(['is_active' => true]);

        return to_route('admin.access-menu')->with('success', 'Sidebar item restored.');
    }

    public function updateAccessMenu(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'access' => ['nullable', 'array'],
            'access.*' => ['in:' . implode(',', array_keys(self::MENU_ITEMS))],
        ]);

        $user = User::with('role')->findOrFail($validated['user_id']);
        if (in_array(strtolower((string) $user->role?->role_name), ['admin', 'administrator'], true)) {
            return back()->withErrors(['user_id' => 'Admin accounts always have access to all menus.']);
        }

        $user->update(['menu_access' => array_values(array_intersect(
            $validated['access'] ?? [],
            array_keys(self::MENU_ITEMS)
        ))]);

        return to_route('admin.access-menu', [
            'username' => $user->username,
            'role_id' => $user->role_id,
            'user_id' => $user->id,
        ])->with('success', 'Menu access updated for ' . $user->username . '.');
    }

    public function roles()
    {
        $roles = Role::orderBy('role_name')->get();
        $memoStatusLabels = MemoStatusLabel::orderBy('status_key')->get()->keyBy('status_key');
        return view('admin.roles.index', compact('roles', 'memoStatusLabels'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:255', 'unique:roles,role_name'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
        ]);

        Role::create($validated);
        return to_route('admin.roles.index')->with('success', 'Role added.');
    }

    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:255', Rule::unique('roles', 'role_name')->ignore($role->id)],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
        ]);

        $role->update($validated);

        return to_route('admin.roles.index')->with('success', 'Role updated.');
    }

    public function updateMemoStatusLabels(Request $request)
    {
        $validated = $request->validate([
            'labels' => ['required', 'array'],
            'labels.draft' => ['required', 'string', 'max:50'],
            'labels.pending' => ['required', 'string', 'max:50'],
            'labels.approved' => ['required', 'string', 'max:50'],
        ]);

        foreach ($validated['labels'] as $status => $label) {
            MemoStatusLabel::updateOrCreate(['status_key' => $status], ['label' => trim($label)]);
        }

        return to_route('admin.roles.index')->with('success', 'Memo status labels updated.');
    }
}
