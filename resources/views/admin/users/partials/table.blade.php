<p>{{ $users->total() }} {{ $users->total() === 1 ? 'user' : 'users' }}</p>
<div class="registered-users-scroll">
    <table>
        <thead><tr>
            @foreach(['Name', 'Username', 'Email', 'Department', 'Designation', 'Role', 'Actions'] as $heading)
                <th scope="col">{{ $heading }}</th>
            @endforeach
        </tr></thead>
        <tbody>
            @forelse($users as $account)
                <tr>
                    <td>{{ $account->name }}</td>
                    <td>{{ $account->username }}</td>
                    <td>{{ $account->email }}</td>
                    <td>{{ $account->department?->department_name ?? '—' }}</td>
                    <td>{{ $account->designation ?? '—' }}</td>
                    <td>{{ $account->role?->role_name ?? '—' }}</td>
                    <td><div class="registered-users-actions">
                        <a href="{{ route('admin.users.edit', $account) }}" aria-label="Edit {{ $account->name }}" title="Edit {{ $account->name }}">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.users.destroy', $account) }}" onsubmit="return confirm('Delete this user account? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="registered-users-action--delete" aria-label="Delete {{ $account->name }}" title="Delete {{ $account->name }}" @disabled($account->id === auth()->id())>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg>
                            </button>
                        </form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="registered-users-pagination pagination-wrap">{{ $users->links() }}</div>
