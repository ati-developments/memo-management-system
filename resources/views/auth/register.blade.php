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

<body>
<div class="auth-theme-toggle">@include('layouts.theme-toggle')</div>
<main class="register-page">
    <section class="register-area">
    @include('auth.brand')

<div class="register-container">

    <div class="register-header">

        <p class="form-eyebrow">Get started with Memo</p><h1>Create your account</h1>

        <p>
            Add your details to start creating and approving memos.
        </p>

    </div>


    {{-- Success message --}}

    @if(session('success'))

        <div class="alert-success" role="status">

            {{ session('success') }}

        </div>

    @endif


    {{-- Validation errors --}}

    @if($errors->any())

        <div class="alert-error" role="alert">

            Please correct the errors below and try again.

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('register.store') }}"
        enctype="multipart/form-data"
    >

        @csrf


        <div class="register-columns">
        <section class="register-details" aria-labelledby="register-details-title">
        <div class="form-grid">


            <div class="form-section-heading" id="register-details-title"><span>01</span> Your details</div>

            <div class="form-group">

                <label for="name">
                    Name
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="name" autocomplete="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Enter full name"
                    required
                >

                @error('name')
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
                    value="{{ old('username') }}"
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
                    value="{{ old('email') }}"
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
                    value="{{ old('employee_id') }}"
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
                            {{ old('department_id') == $department->id ? 'selected' : '' }}
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
                    value="{{ old('designation') }}"
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
                            {{ old('role_id') == $role->id ? 'selected' : '' }}
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
                    required
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
                    required
                >

            </div>


            {{-- Signature --}}

            <div class="signature-section">

                <h3 id="signature-label">
                    Signature
                </h3>

                <p>
                    Upload your signature image if you want to use it
                    when signing or approving memos. This is optional.
                </p>

                <input
                    type="file"
                    name="signature" aria-labelledby="signature-label"
                    accept=".png,.jpg,.jpeg"
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

            <a
                href="{{ route('login') }}"
                class="login-link"
            >
                Already have an account? Sign in
            </a>

            <button
                type="submit"
                class="btn-register"
            >
                Create Account
            </button>

        </div>


    </form>

</div>

    <p class="auth-footer">Memo Management System &middot; Your organization, connected.</p>
    </section>
</main>
@include('layouts.theme-styles')
</body>

</html>
