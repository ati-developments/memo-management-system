@extends('layouts.app')
@section('title', 'User Registration')
@section('header-title', 'User Registration')
@section('header-description', 'Create and manage system user accounts.')
@section('header-actions')<a class="app-header-button" href="{{ route('admin.users.create') }}">+ Register user</a>@endsection
@section('content')
<section style="background:#fff;border:1px solid #e2e7ef;border-radius:10px;padding:20px">
    @if(session('success'))<p role="status">{{ session('success') }}</p>@endif
    <div style="overflow-x:auto"><table style="width:100%;border-collapse:collapse;text-align:left">
        <thead>
            <tr>
                <th style="padding:12px">Name</th>
                <th style="padding:12px">Username</th>
                <th style="padding:12px">Department</th>
                <th style="padding:12px">Role</th>
            </tr>
        </thead>
        <tbody>@forelse($users as $account)
            <tr style="border-top:1px solid #e5e9ef">
                <td style="padding:12px">{{ $account->name }}</td>
                <td style="padding:12px">{{ $account->username }}</td>
                <td style="padding:12px">{{ $account->department?->department_name ?? '—' }}</td>
                <td style="padding:12px">{{ $account->role?->role_name ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="padding:20px">No users found.</td>
        
            </tr>
            @endforelse
        </tbody>
    </table></div>
    <div style="margin-top:16px">{{ $users->links() }}</div>
</section>
@endsection
