<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdmin extends Command
{
    protected $signature = 'memo:make-admin {username? : Existing username to promote}';
    protected $description = 'Promote an existing account to Admin, or create the initial Admin account';

    public function handle(): int
    {
        $username = $this->argument('username');
        $user = $username ? User::where('username', $username)->first() : null;

        if ($username && !$user) {
            $this->error("No user account was found with username '{$username}'.");
            return self::FAILURE;
        }

        if (!$user) {
            $user = $this->createAdminUser();
            if (!$user) {
                return self::FAILURE;
            }
        }

        $role = Role::firstOrCreate(
            ['role_name' => 'Admin'],
            ['description' => 'System administrator', 'status' => true]
        );
        $role->update(['status' => true, 'menu_access' => ['templates', 'new_memo', 'memos', 'approvals']]);
        $user->update(['role_id' => $role->id]);

        $this->info("{$user->username} now has the Admin role. Log in with that account.");
        return self::SUCCESS;
    }

    private function createAdminUser(): ?User
    {
        $departments = Department::where('status', true)->orderBy('department_name')->get();
        if ($departments->isEmpty()) {
            $this->error('Create an active department before creating the initial Admin account.');
            return null;
        }

        $name = trim((string) $this->ask('Admin full name'));
        $username = trim((string) $this->ask('Username'));
        $email = trim((string) $this->ask('Email'));
        $employeeId = trim((string) $this->ask('Employee ID'));
        $designation = trim((string) $this->ask('Designation', 'Administrator'));

        if ($name === '' || $username === '' || $email === '' || $employeeId === '') {
            $this->error('Name, username, email, and employee ID are required.');
            return null;
        }
        if (User::where('username', $username)->orWhere('email', $email)->orWhere('employee_id', $employeeId)->exists()) {
            $this->error('The username, email, or employee ID is already in use.');
            return null;
        }

        $this->table(['ID', 'Department'], $departments->map(fn ($department) => [$department->id, $department->department_name])->all());
        $departmentId = (int) $this->ask('Department ID');
        if (!$departments->contains('id', $departmentId)) {
            $this->error('Choose a valid active department ID.');
            return null;
        }

        $password = (string) $this->secret('Password (minimum 8 characters)');
        if (strlen($password) < 8) {
            $this->error('The password must be at least 8 characters.');
            return null;
        }

        return User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'employee_id' => $employeeId,
            'department_id' => $departmentId,
            'designation' => $designation !== '' ? $designation : 'Administrator',
            'password' => Hash::make($password),
        ]);
    }
}
