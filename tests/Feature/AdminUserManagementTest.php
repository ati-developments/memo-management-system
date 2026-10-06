<?php

namespace Tests\Feature;

use App\Models\{Department, Role, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $role = Role::create(['role_name' => 'Admin', 'status' => true]);
        $admin = User::factory()->create(['username' => 'admin', 'role_id' => $role->id]);
        $this->actingAs($admin);
        return $admin;
    }

    private function fields(User $user): array
    {
        $department = Department::create(['department_name' => 'Finance', 'department_code' => 'FIN', 'status' => true]);
        $role = Role::create(['role_name' => 'Reviewer', 'status' => true]);
        return [
            'first_name' => 'Updated', 'last_name' => 'Person', 'username' => $user->username,
            'email' => $user->email, 'employee_id' => 'EMP-123', 'designation' => 'Accountant',
            'department_id' => $department->id, 'role_id' => $role->id,
        ];
    }

    public function test_update_keeps_password_when_blank_and_can_replace_password_and_signature(): void
    {
        $this->admin();
        $user = User::factory()->create(['username' => 'person']);
        $password = $user->password;
        $fields = $this->fields($user);
        $this->get(route('admin.users.edit', $user))->assertOk()->assertSee('Update User');
        $this->put(route('admin.users.update', $user), $fields)->assertSessionHasNoErrors();
        $this->assertSame('Updated Person', $user->fresh()->name);
        $this->assertSame($password, $user->fresh()->password);

        Storage::fake('public');
        $this->put(route('admin.users.update', $user), $fields + [
            'password' => 'new-password-123', 'password_confirmation' => 'new-password-123',
            'signature' => UploadedFile::fake()->image('signature.png'),
        ])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
        Storage::disk('public')->assertExists($user->fresh()->signature->signature_path);
    }

    public function test_search_returns_matching_details_without_the_registration_form(): void
    {
        $this->admin();
        User::factory()->create(['username' => 'find-me', 'employee_id' => 'SEARCH-42', 'designation' => 'Specialist']);
        $this->get(route('admin.users.create'))->assertOk()->assertSee('registered-users-search')->assertSee('Employee ID');
        $this->get(route('admin.users.create', ['search' => 'SEARCH-42']), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()->assertSee('find-me')->assertSee('Specialist')->assertSee('Update')
            ->assertDontSee('register-container');
    }

    public function test_delete_removes_an_unlinked_user_but_not_your_own_account(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create(['username' => 'delete-me']);
        $this->delete(route('admin.users.destroy', $user))->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->delete(route('admin.users.destroy', $admin))->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_non_admin_cannot_modify_users(): void
    {
        $role = Role::create(['role_name' => 'Reviewer', 'status' => true]);
        $user = User::factory()->create(['username' => 'reviewer', 'role_id' => $role->id]);
        $this->actingAs($user);
        $this->put(route('admin.users.update', $user), [])->assertForbidden();
        $this->delete(route('admin.users.destroy', $user))->assertForbidden();
    }

    public function test_delete_preserves_users_with_memo_history(): void
    {
        $this->admin();
        $user = User::factory()->create(['username' => 'memo-owner']);
        $fields = $this->fields($user);
        $memo = \App\Models\Memo::create([
            'memo_number' => 'DELETE-TEST', 'department_id' => $fields['department_id'],
            'created_by' => $user->id, 'subject' => 'Keep this memo', 'status' => 'draft',
        ]);
        $this->delete(route('admin.users.destroy', $user))->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseHas('memos', ['id' => $memo->id]);
    }
}
