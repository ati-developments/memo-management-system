<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()->load('signature', 'department', 'role')]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'employee_id' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'designation' => ['nullable', 'string', 'max:255'],
        ]);
        if ($validated['email'] !== $user->email) {
            $user->email_verified_at = null;
        }
        $user->fill($validated)->save();

        return to_route('profile.edit')->with('success', 'Profile details updated.');
    }

    public function signature(Request $request)
    {
        $request->validate(['signature' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048', 'dimensions:max_width=4096,max_height=4096']]);
        $path = $request->file('signature')->store('signatures', 'public');
        try {
            DB::transaction(function () use ($request, $path) {
                $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);
                $user->signature()->updateOrCreate([], [
                    'signature_path' => $path,
                    'is_active' => true,
                    'uploaded_at' => now(),
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }
        // Existing approvals reference the previous file, which must remain available.
        return to_route('profile.edit')->with('success', 'Signature updated. It will be used for future approvals.');
    }
}
