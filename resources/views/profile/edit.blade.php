@extends('layouts.app')
@section('title', 'Profile settings')
@section('header-title', 'Profile settings')
@section('header-eyebrow', 'Your account')
@section('header-description', 'Manage your personal details and the signature used for memo approvals.')
@section('styles')
@include('templates.builder-styles')
<style>
    .profile-layout { display:grid; grid-template-columns:minmax(0,1.4fr) minmax(280px,1fr); gap:24px; align-items:start; }
    .profile-signature { display:flex; align-items:center; justify-content:center; min-height:150px; padding:22px; margin:20px 0; border:1px dashed #d6d9e2; border-radius:10px; background:#fafbfe; }
    .profile-signature img { max-width:100%; max-height:120px; object-fit:contain; }
    .profile-signature p { color:#79756e; font-size:13px; }
    .profile-assignment { font-size:12px; color:#79756e; line-height:1.8; margin-bottom:24px; }
    @media(max-width:1100px) { .profile-layout { grid-template-columns:1fr; } }
</style>
@endsection
@section('content')
<div class="template-editor">
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert"><strong>Please check the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="profile-layout">
        <section class="builder-card" aria-labelledby="details-title">
            <h2 class="section-title" id="details-title">Personal information</h2>
            <p class="section-description">Keep your contact details and professional information up to date.</p>
            <div class="profile-assignment">Department: <strong>{{ $user->department?->department_name ?? 'Not assigned' }}</strong><br>Role: <strong>{{ $user->role?->role_name ?? 'Not assigned' }}</strong></div>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')
                <div class="form-grid">
                    @foreach(['name' => 'Full name', 'username' => 'Username', 'email' => 'Email address', 'employee_id' => 'Employee ID', 'designation' => 'Designation'] as $field => $label)
                        <div class="form-group {{ $field === 'name' ? 'full' : '' }}">
                            <label class="form-label" for="{{ $field }}">{{ $label }} @if(in_array($field, ['employee_id', 'designation']))<span class="optional">Optional</span>@endif</label>
                            <input class="form-control" id="{{ $field }}" name="{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}" value="{{ old($field, $user->$field) }}" maxlength="255" @required(in_array($field, ['name', 'username', 'email']))>
                        </div>
                    @endforeach
                </div>
                <div class="form-actions"><button class="btn btn-primary" type="submit">Save profile</button></div>
            </form>
        </section>
        <section class="builder-card" aria-labelledby="signature-title">
            <h2 class="section-title" id="signature-title">Your signature</h2>
            <p class="section-description">Upload a clear signature on a white or transparent background.</p>
            <div class="profile-signature">
                @if($user->signature?->is_active)
                    <img src="{{ asset('storage/' . $user->signature->signature_path) }}" alt="Your current signature">
                @else
                    <p>No signature added yet.</p>
                @endif
            </div>
            <form method="POST" action="{{ route('profile.signature') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <label class="form-label" for="signature">{{ $user->signature ? 'Replace signature' : 'Add signature' }}</label>
                <input class="form-control" type="file" id="signature" name="signature" accept="image/png,image/jpeg" required aria-describedby="signature-help">
                <span class="field-help" id="signature-help">PNG or JPG, up to 2 MB and 4096 × 4096 pixels. Changes apply to future approvals; previously signed memos retain their original signature.</span>
                <div class="form-actions"><button class="btn btn-primary" type="submit">Save signature</button></div>
            </form>
        </section>
    </div>
</div>
@endsection
