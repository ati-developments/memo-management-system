<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use App\Models\UserSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    */

    public function showRegister()
    {
        $departments = Department::where('status', true)
            ->orderBy('department_name')
            ->get();

        $roles = Role::where('status', true)
            ->orderBy('role_name')
            ->get();

        return view('auth.register', compact(
            'departments',
            'roles'
        ));
    }


    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:127'],
            'last_name' => ['required', 'string', 'max:127'],

            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'employee_id' => [
                'required',
                'string',
                'max:255',
                'unique:users,employee_id'
            ],

            'department_id' => [
                'required',
                'exists:departments,id'
            ],

            'designation' => [
                'required',
                'string',
                'max:255'
            ],

            'role_id' => [
                'required',
                'exists:roles,id'
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],

            // A signature is required for registration.
            'signature' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg',
                'max:2048'
            ],
        ]);


        $user = User::create([
            'name' => trim($validated['first_name']).' '.trim($validated['last_name']),
            'username' => $validated['username'],
            'email' => $validated['email'],
            'employee_id' => $validated['employee_id'],
            'department_id' => $validated['department_id'],
            'designation' => $validated['designation'],
            'role_id' => $validated['role_id'],
            'password' => Hash::make($validated['password']),
        ]);


        /*
        |--------------------------------------------------------------------------
        | Signature
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('signature')) {

            $path = $request->file('signature')
                ->store('signatures', 'public');

            UserSignature::create([
                'user_id' => $user->id,
                'signature_path' => $path,
                'is_active' => true,
                'uploaded_at' => now(),
            ]);
        }


        return redirect()
            ->route('login')
            ->with(
                'success',
                'Registration successful. You can now log in.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => [
                'required',
                'string'
            ],

            'password' => [
                'required',
                'string'
            ],
        ]);


        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            return redirect()
                ->intended(route('dashboard'))
                ->with('success', 'Login successful.');
        }


        return back()
            ->withErrors([
                'username' => 'The username or password is incorrect.',
            ])
            ->onlyInput('username');
    }


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out.');
    }
}
