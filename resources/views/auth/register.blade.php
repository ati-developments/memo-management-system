@if($adminRegistration ?? false)
@extends('layouts.app')
@section('title', 'User Registration')
@section('header-title', 'User Registration')
@section('header-description', 'Create user accounts and assign roles.')
@section('styles')
    @include('auth.styles')
    <style>
        body { height:auto; min-height:100vh; overflow:auto; background:#f7f6f2; }
        .admin-registration { display:grid; grid-template-columns:minmax(0,380px) minmax(0,1fr); gap:24px; align-items:start; width:100%; margin:0; }
        .admin-registration .register-container { width:100%; max-width:380px; min-height:0; padding:20px; border-radius:12px; }
        .admin-registration .form-grid { grid-template-columns:minmax(0,1fr); }
        .admin-registration .register-header { text-align:left; margin-bottom:18px; }
        .admin-registration .register-header h1 { margin:0 0 6px; font-size:25px; }
        .admin-registration .register-header p { margin:0; }
        .admin-registration .register-columns { display:grid; grid-template-columns:1fr; gap:24px; align-items:start; }
        .admin-registration .register-account { padding-top:24px; border-top:1px solid var(--line); }
        .registered-users { min-width:0; padding:24px; border:1px solid var(--line); border-radius:12px; background:var(--ui-surface,#fff); color:var(--ui-ink,#111827); }
        .registered-users h2 { margin:0 0 8px; font-size:20px; }
        .registered-users-scroll { overflow-x:auto; }
        .registered-users table { width:100%; border-collapse:collapse; text-align:left; font-size:13px; }
        .registered-users th,.registered-users td { padding:12px; border-bottom:1px solid var(--line); overflow-wrap:anywhere; }
        .registered-users-pagination { margin-top:20px; }
        .registered-users-search { display:flex; flex-wrap:wrap; align-items:center; gap:10px; margin:18px 0; }
        .registered-users-search label { flex-basis:100%; margin:0; }
        .registered-users-search input { flex:0 1 260px; min-width:0; width:260px; max-width:100%; }
        .registered-users table { min-width:850px; }
        .registered-users-actions { display:flex; gap:8px; align-items:center; }
        .registered-users-actions button,.registered-users-actions a { padding:7px 10px; border:1px solid var(--line); border-radius:6px; background:var(--ui-surface,#fff); color:var(--primary-link); font:inherit; cursor:pointer; text-decoration:none; }
        .registered-users-actions form { margin:0; }
        .registered-users-actions button:disabled { opacity:.45; cursor:not-allowed; }
        .registered-users-search button { padding:12px 16px; border:0; border-radius:8px; background:var(--primary); color:var(--primary-button-text); cursor:pointer; }
        .registered-users-search a { color:var(--primary-link); }
        @media(max-width:1100px) { .admin-registration { grid-template-columns:1fr; } }
        .admin-registration .form-actions { margin-top:16px; padding-top:14px; }
        @media(max-width:700px) { .admin-registration .register-columns { grid-template-columns:1fr; gap:20px; } .admin-registration .register-account { padding:20px 0 0; border:0; border-top:1px solid var(--line); } }
    </style>
@endsection
@section('content')
<div class="admin-registration">
@else
<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.theme-init')

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Register - Memo Management System</title>

    @include('auth.styles')
    <style>
        body { height:100vh; height:100dvh; overflow:hidden; }
        .register-page { height:100%; min-height:0; padding:16px 24px; }
        .register-area { min-height:0; justify-content:center; }
        .register-area > .auth-brand { flex-shrink:0; margin-bottom:12px; }
        .register-area .auth-brand-image { width:180px; }
        .register-container { max-width:1060px; min-height:0; overflow-y:auto; overscroll-behavior:contain; padding:20px 28px; border-radius:16px; }
        .register-header { margin-bottom:16px; }
        .register-header h1 { font-size:27px; margin-bottom:6px; }
        .register-header .form-eyebrow { margin-bottom:6px; }
        .register-header p { font-size:12px; }
        .register-columns { display:grid; grid-template-columns:minmax(0,1.2fr) minmax(0,1fr); gap:28px; align-items:start; }
        .register-details,.register-account { min-width:0; }
        .register-account { padding-left:28px; border-left:1px solid var(--line); }
        .register-container .form-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px 18px; }
        .register-container .register-account .form-grid { grid-template-columns:1fr; }
        .register-container .full-width { grid-column:1 / -1; }
        .register-container label { margin-bottom:5px; }
        .register-container input,.register-container select { min-height:40px; padding:8px 12px; }
        .register-container .form-section-heading { margin-top:0; }
        .register-container .signature-section { padding:14px 16px; margin-top:0; }
        .register-container .signature-section h3 { margin:0; }
        .register-container .signature-section p { margin:6px 0 10px; font-size:11px; }
        .register-container .signature-section .help-text { margin-top:6px; }
        .register-container .signature-section .error { grid-column:1 / -1; }
        .register-container .form-actions { margin-top:16px; padding-top:14px; }
        .register-container .btn-register { min-height:40px; padding:9px 20px; }
        .register-area > .auth-footer { flex-shrink:0; margin-top:10px; }
        @media(max-width:700px) {
            .register-columns { grid-template-columns:1fr; gap:24px; }
            .register-account { padding-left:0; padding-top:20px; border-left:0; border-top:1px solid var(--line); }
            .register-page { padding:52px 12px 12px; }
            .register-container { padding:18px; }
            .register-container .form-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
            .register-container .signature-section { display:block; }
            .register-container .signature-section p { margin:6px 0 10px; }
            .register-container .signature-section .help-text { margin-top:6px; }
        }
        @media(max-width:440px) {
            .register-container .form-grid { grid-template-columns:1fr; }
            .register-header h1 { font-size:24px; }
        }
    </style>

</head>
@endif

@if(!($adminRegistration ?? false))
<body>
<div class="auth-theme-toggle">@include('layouts.theme-toggle')</div>
<main class="register-page">
    <section class="register-area">
    @include('auth.brand')
@endif

<div class="register-container">





    @if(isset($editingUser))
        <h2>Update {{ $editingUser->name }}</h2>
        <p>Leave password and signature empty to keep their current values.</p>
        <a href="{{ route('admin.users.create') }}">Cancel update</a>
    @endif
    {{-- Success message --}}

    @if(session('success'))

        <div class="alert-success" role="status">

            {{ session('success') }}

        </div>

    @endif


    {{-- Validation errors --}}

    @if($errors->any())

        <div class="alert-error" role="alert">

            {{ $errors->first() }}

        </div>

    @endif


    <form
        method="POST"
        action="{{ isset($editingUser) ? route('admin.users.update', $editingUser) : route($registrationRoute ?? 'register.store') }}"
        enctype="multipart/form-data"
    >

        @csrf
        @if(isset($editingUser)) @method('PUT') @endif


        <div class="register-columns">
        <section class="register-details" aria-labelledby="register-details-title">
        <div class="form-grid">


            <div class="form-section-heading" id="register-details-title"><span>01</span> Personal Details</div>

            <div class="form-group">

                <label for="first_name">
                    First name
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="first_name" autocomplete="given-name"
                    name="first_name" maxlength="127"
                    value="{{ old('first_name', isset($editingUser) ? explode(' ', $editingUser->name, 2)[0] : '') }}"
                    placeholder="Enter first name"
                    required
                >

                @error('first_name')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>


            <div class="form-group">
                <label for="last_name">Last name <span class="required">*</span></label>
                <input type="text" id="last_name" name="last_name" autocomplete="family-name"
                    maxlength="127" value="{{ old('last_name', isset($editingUser) ? (explode(' ', $editingUser->name, 2)[1] ?? '') : '') }}" placeholder="Enter last name" required>
                @error('last_name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Username --}}

            <div class="form-group">

                <label for="username">
                    Username
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="username" autocomplete="username"
                    name="username"
                    value="{{ old('username', $editingUser->username ?? '') }}"
                    placeholder="Enter username"
                    required
                >

                @error('username')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>


            {{-- Email --}}

            <div class="form-group">

                <label for="email">
                    Email
                    <span class="required">*</span>
                </label>

                <input
                    type="email"
                    id="email" autocomplete="email"
                    name="email"
                    value="{{ old('email', $editingUser->email ?? '') }}"
                    placeholder="Enter email address"
                    required
                >

                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>


            {{-- Employee ID --}}

            <div class="form-group">

                <label for="employee_id">
                    Employee ID
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="employee_id"
                    name="employee_id"
                    value="{{ old('employee_id', $editingUser->employee_id ?? '') }}"
                    placeholder="Enter employee ID"
                    required
                >

                @error('employee_id')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>


            {{-- Department --}}

            <div class="form-group">

                <label for="department_id">
                    Department
                    <span class="required">*</span>
                </label>

                <select
                    id="department_id"
                    name="department_id"
                    required
                >

                    <option value="">
                        Select Department
                    </option>

                    @foreach($departments as $department)

                        <option
                            value="{{ $department->id }}"
                            {{ old('department_id', $editingUser->department_id ?? '') == $department->id ? 'selected' : '' }}
                        >
                            {{ $department->department_name }}
                        </option>

                    @endforeach

                </select>

                @error('department_id')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>


            {{-- Designation --}}

            <div class="form-group">

                <label for="designation">
                    Designation
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="designation"
                    name="designation"
                    value="{{ old('designation', $editingUser->designation ?? '') }}"
                    placeholder="e.g. IT Executive, Head of IT"
                    required
                >

                @error('designation')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>


            {{-- Role --}}

            <div class="form-group full-width">

                <label for="role_id">
                    Role
                    <span class="required">*</span>
                </label>

                <select
                    id="role_id"
                    name="role_id"
                    required
                >

                    <option value="">
                        Select Role
                    </option>

                    @foreach($roles as $role)

                        <option
                            value="{{ $role->id }}"
                            {{ old('role_id', $editingUser->role_id ?? '') == $role->id ? 'selected' : '' }}
                        >
                            {{ $role->role_name }}
                        </option>

                    @endforeach

                </select>

                @error('role_id')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>


        </div>
        </section>
        <section class="register-account" aria-labelledby="register-account-title">
        <div class="form-grid">
            <div class="form-section-heading" id="register-account-title"><span>02</span> Account password</div>

            <div class="form-group">

                <label for="password">
                    Password
                    <span class="required">*</span>
                </label>

                <input
                    type="password"
                    id="password" autocomplete="new-password"
                    name="password"
                    placeholder="Enter password"
                    @required(!isset($editingUser))
                >

                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>


            {{-- Confirm Password --}}

            <div class="form-group">

                <label for="password_confirmation">
                    Confirm Password
                    <span class="required">*</span>
                </label>

                <input
                    type="password"
                    id="password_confirmation" autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Confirm password"
                    @required(!isset($editingUser))
                >

            </div>


            {{-- Signature --}}

            <div class="signature-section">

                <h3 id="signature-label">
                    Signature <span class="required">*</span>
                </h3>

                <p>
                    {{ isset($editingUser) ? 'Upload a replacement signature, or leave empty to keep the current signature.' : 'Upload a signature image for signing or approving memos. A signature is required.' }}
                </p>

                <input
                    type="file"
                    name="signature" aria-labelledby="signature-label"
                    accept=".png,.jpg,.jpeg"
                    @required(!isset($editingUser))
                >

                @error('signature')
                    <div class="error">{{ $message }}</div>
                @enderror

                <div class="help-text">
                    Accepted formats: PNG, JPG, JPEG. Maximum size: 2 MB.
                </div>

            </div>


        </div>


        </section>
        </div>

        <div class="form-actions">

            @unless($adminRegistration ?? false)<a
                href="{{ route('login') }}"
                class="login-link"
            >
                Already have an account? Sign in
            </a>@endunless

            <button
                type="submit"
                class="btn-register"
            >
                {{ isset($editingUser) ? 'Update User' : (($adminRegistration ?? false) ? 'Register User' : 'Create Account') }}
            </button>

        </div>


    </form>

</div>
@if($adminRegistration ?? false)
<section class="registered-users" aria-labelledby="registered-users-title">
    <h2 id="registered-users-title">Registered users</h2>
    <form method="GET" action="{{ route('admin.users.create') }}" class="registered-users-search" role="search">
        <label for="registered-users-search">Search registered users</label>
        <input class="form-control" type="search" id="registered-users-search" name="search"
            value="{{ request('search') }}" maxlength="255" placeholder="Search registered users">
        <a href="{{ route('admin.users.create') }}" id="clear-users-search">Clear</a>
    </form>
    <p role="status" id="users-search-status"></p>
    <div id="registered-users-results">
        @include('admin.users.partials.table')
    </div>
</section>
</div>
@include('admin.users.partials.search-script')
@endsection
@else
    <p class="auth-footer">Memo Management System &middot; Your organization, connected.</p>
    </section>
</main>
@include('layouts.theme-styles')
</body>

</html>
@endif
