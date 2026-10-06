<p>{{ $users->total() }} {{ $users->total() === 1 ? 'user' : 'users' }}</p>
<div class="registered-users-scroll">
    <table>
        <thead><tr>
            @foreach(['Name', 'Username', 'Email', 'Employee ID', 'Department', 'Designation', 'Role', 'Actions'] as $heading)
                <th scope="col">{{ $heading }}</th>
            @endforeach
        </tr></thead>
        <tbody>
            @forelse($users as $account)
                <tr>
                    <td>{{ $account->name }}</td>
                    <td>{{ $account->username }}</td>
                    <td>{{ $account->email }}</td>
                    <td>{{ $account->employee_id ?? '—' }}</td>
                    <td>{{ $account->department?->department_name ?? '—' }}</td>
                    <td>{{ $account->designation ?? '—' }}</td>
                    <td>{{ $account->role?->role_name ?? '—' }}</td>
                    <td><div class="registered-users-actions">
                        <a href="{{ route('admin.users.edit', $account) }}">Update</a>
                        <form method="POST" action="{{ route('admin.users.destroy', $account) }}" onsubmit="return confirm('Delete this user account? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" @disabled($account->id === auth()->id())>Delete</button>
                        </form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="8">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="registered-users-pagination pagination-wrap">{{ $users->links() }}</div>
