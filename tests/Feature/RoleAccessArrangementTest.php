<?php

namespace Tests\Feature;

use App\Models\{Role, SidebarMenuItem, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessArrangementTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_access_saves_the_arrangement_and_permissions(): void
    {
        $role = $this->signInAdmin();
        $group = SidebarMenuItem::where('item_key', 'memos_group')->firstOrFail();
        $link = SidebarMenuItem::where('item_key', 'approvals')->firstOrFail();
        $structure = $this->structure();
        foreach ($structure as &$entry) {
            if ($entry['id'] === $link->id) {
                $entry['parent_id'] = $group->id;
                $entry['position'] = 5;
            }
        }
        unset($entry);

        $this->put(route('admin.access-menu.role-access.update'), [
            'role_id' => $role->id,
            'access' => ['approvals'],
            'structure' => json_encode($structure),
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.access-menu', ['role_id' => $role->id]));

        $this->assertSame(['approvals'], $role->fresh()->menu_access);
        $this->assertDatabaseHas('sidebar_menu_items', ['id' => $link->id, 'parent_id' => $group->id, 'sort_order' => 5, 'is_active' => true]);
        $this->get(route('admin.access-menu', ['role_id' => $role->id]))
            ->assertOk()->assertSee('data-menu-editor', false)->assertSee('Save role access');
    }

    public function test_invalid_nesting_does_not_change_permissions(): void
    {
        $role = $this->signInAdmin();
        $structure = $this->structure();
        $group = SidebarMenuItem::where('is_group', true)->firstOrFail();
        foreach ($structure as &$entry) {
            if ($entry['id'] === $group->id) {
                $entry['parent_id'] = $group->id;
            }
        }
        unset($entry);

        $this->put(route('admin.access-menu.role-access.update'), [
            'role_id' => $role->id, 'access' => ['approvals'], 'structure' => json_encode($structure),
        ])->assertSessionHasErrors('structure');

        $this->assertSame(['dashboard'], $role->fresh()->menu_access);
        $this->assertNull($group->fresh()->parent_id);
    }

    public function test_sidebar_editor_still_saves_independently(): void
    {
        $this->signInAdmin();
        $this->put(route('admin.access-menu.sidebar.update'), ['structure' => json_encode($this->structure())])
            ->assertSessionHasNoErrors()->assertRedirect(route('admin.access-menu'));
    }

    public function test_admin_can_save_selections_without_losing_sidebar_links(): void
    {
        $this->signInAdmin();
        $admin = Role::where('role_name', 'Admin')->firstOrFail();
        $this->get(route('admin.access-menu', ['role_id' => $admin->id]))
            ->assertOk()->assertSee('Save role access')
            ->assertSee('name="access[]" value="approvals" checked', false)
            ->assertDontSee('name="access[]" value="approvals" checked disabled', false);

        $this->put(route('admin.access-menu.role-access.update'), [
            'role_id' => $admin->id, 'access' => ['dashboard', 'access_menu'], 'structure' => json_encode($this->structure()),
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.access-menu', ['role_id' => $admin->id]));

        $this->assertSame(['dashboard', 'access_menu'], $admin->fresh()->menu_access);
        $this->actingAs(auth()->user()->fresh());
        $this->get(route('admin.access-menu', ['role_id' => $admin->id]))
            ->assertOk()->assertSee('aria-label="Approvals"', false)
            ->assertSee('aria-label="Access Menu"', false)
            ->assertSee('aria-label="User Registration"', false)
            ->assertSee('aria-label="Templates"', false);
    }

    private function signInAdmin(): Role
    {
        $admin = Role::create(['role_name' => 'Admin', 'status' => true]);
        $this->actingAs(User::factory()->create(['username' => 'arrangement-admin', 'role_id' => $admin->id]));

        return Role::create(['role_name' => 'Reviewer', 'status' => true, 'menu_access' => ['dashboard']]);
    }

    private function structure(): array
    {
        return SidebarMenuItem::all()->map(fn ($item) => [
            'id' => $item->id, 'parent_id' => $item->parent_id, 'position' => $item->sort_order,
        ])->all();
    }
}
