<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $username = 'reviewer'): User
    {
        return User::factory()->create(['username' => $username]);
    }

    public function test_profile_requires_login_and_renders_for_current_user(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
        $this->patch(route('profile.update'), [])->assertRedirect(route('login'));
        $this->put(route('profile.signature'), [])->assertRedirect(route('login'));
        $user = $this->user();
        $this->actingAs($user)->get(route('profile.edit'))->assertOk()->assertSee($user->email)->assertSee('No signature added yet.')->assertSee('aria-label="Profile settings"', false);
    }

    public function test_profile_updates_only_current_user_and_does_not_change_permissions(): void
    {
        $user = $this->user();
        $other = $this->user('other');
        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Updated Name', 'username' => 'updated', 'email' => 'updated@example.com',
            'employee_id' => 'EMP-42', 'designation' => 'Manager', 'user_id' => $other->id, 'role_id' => 999,
        ])->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));
        $this->assertSame('Updated Name', $user->fresh()->name);
        $this->assertSame($user->role_id, $user->fresh()->role_id);
        $this->assertSame($other->name, $other->fresh()->name);
        $this->assertNull($user->fresh()->email_verified_at);
        $this->patch(route('profile.update'), ['name' => 'Name', 'username' => $other->username, 'email' => $other->email])->assertSessionHasErrors(['username', 'email']);
    }

    public function test_signatures_can_be_added_and_replaced_without_deleting_previous_files(): void
    {
        Storage::fake('public');
        $user = $this->user();
        $this->actingAs($user)->put(route('profile.signature'), ['signature' => UploadedFile::fake()->image('signature.png', 300, 100)])
            ->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));
        $first = $user->fresh()->signature->signature_path;
        Storage::disk('public')->assertExists($first);
        $this->put(route('profile.signature'), ['signature' => UploadedFile::fake()->image('replacement.jpg', 300, 100)])->assertSessionHasNoErrors();
        $second = $user->fresh()->signature->signature_path;
        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertExists([$first, $second]);
        $this->assertDatabaseCount('user_signatures', 1);
        $this->get(route('profile.edit'))->assertOk()->assertSee('Your current signature');
        $this->put(route('profile.signature'), ['signature' => UploadedFile::fake()->create('invalid.pdf', 10, 'application/pdf')])->assertSessionHasErrors('signature');
        $this->assertSame($second, $user->fresh()->signature->signature_path);
    }
}
