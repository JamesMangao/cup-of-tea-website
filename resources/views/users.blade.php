@extends('layouts.app')
@section('title', 'Users')

@push('styles')
<style>
    .users-toolbar { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .user-search { flex: 1; min-width: 220px; background: var(--bg-card); border: 1px solid var(--border2); border-radius: 9px; display: flex; align-items: center; gap: 8px; padding: 0 12px; height: 38px; }
    .user-search:focus-within { border-color: rgba(182,224,64,0.4); }
    .user-search input { background: none; border: none; outline: none; color: var(--text); font-size: 0.82rem; font-family: inherit; flex: 1; }
    .user-search input::placeholder { color: var(--muted2); }
    .filter-sel { background: var(--bg-card); border: 1px solid var(--border2); color: var(--muted); border-radius: 8px; padding: 0 10px; height: 38px; font-family: inherit; font-size: 0.8rem; outline: none; cursor: pointer; }
    .filter-sel:focus { border-color: rgba(182,224,64,0.4); }

    /* Stats Row */
    .user-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
    @media (max-width: 700px) { .user-stats { grid-template-columns: repeat(2, 1fr); } }
    .u-stat { background: var(--bg-card); border: 1px solid var(--border); border-radius: 10px; padding: 14px 16px; }
    .u-stat-val { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.03em; line-height: 1.1; }
    .u-stat-lbl { font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); margin-top: 4px; }

    /* Table */
    .users-table-wrap { background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .users-table { width: 100%; border-collapse: collapse; }
    .users-table th { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); padding: 12px 20px; text-align: left; border-bottom: 1px solid var(--border); background: #141414; white-space: nowrap; }
    .users-table th a { color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .users-table th a:hover { color: var(--lime); }
    .users-table td { padding: 13px 20px; border-bottom: 1px solid var(--border); font-size: 0.82rem; color: #ccc; vertical-align: middle; }
    .users-table tr:last-child td { border-bottom: none; }
    .users-table tr:hover td { background: #1a1a1a; }

    .user-row { display: flex; align-items: center; gap: 10px; }
    .user-avatar { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.72rem; font-weight: 800; flex-shrink: 0; }
    .user-name  { font-weight: 600; color: #e0e0e0; font-size: 0.85rem; }
    .user-email { font-size: 0.72rem; color: var(--muted); margin-top: 1px; }

    .role-badge { padding: 3px 9px; border-radius: 6px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
    .role-admin  { background: rgba(182,224,64,0.12); color: var(--lime); border: 1px solid rgba(182,224,64,0.2); }
    .role-viewer { background: rgba(102,102,102,0.12); color: #888;       border: 1px solid #333; }

    .status-dot { display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 600; }
    .status-dot::before { content: ''; width: 7px; height: 7px; border-radius: 50%; }
    .status-active::before    { background: var(--lime); }
    .status-suspended::before { background: var(--red); }

    /* Row actions */
    .row-actions { display: flex; gap: 6px; opacity: 0; transition: opacity 0.15s; }
    .users-table tr:hover .row-actions { opacity: 1; }
    .row-btn { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--border); background: transparent; color: var(--muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; font-family: inherit; }
    .row-btn:hover        { border-color: var(--lime);  color: var(--lime); }
    .row-btn.warn:hover   { border-color: var(--amber); color: var(--amber); }
    .row-btn.danger:hover { border-color: var(--red);   color: var(--red); }

    /* Pagination */
    .table-footer { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid var(--border); flex-wrap: wrap; gap: 10px; }
    .table-info { font-size: 0.78rem; color: var(--muted); }
    .table-pages { display: flex; gap: 4px; }
    .page-btn { width: 32px; height: 32px; border-radius: 7px; border: 1px solid var(--border); background: transparent; color: var(--muted); font-size: 0.78rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; font-family: inherit; transition: all 0.15s; text-decoration: none; }
    .page-btn:hover    { border-color: var(--lime); color: var(--lime); }
    .page-btn.active   { background: var(--lime); border-color: var(--lime); color: #0f0f0f; }
    .page-btn.disabled { opacity: 0.35; pointer-events: none; }

    /* Modals */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .modal-box { background: var(--bg-card); border: 1px solid var(--border2); border-radius: 16px; width: 100%; max-width: 480px; }
    .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .modal-title { font-size: 1rem; font-weight: 700; }
    .modal-close { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: transparent; color: var(--muted); cursor: pointer; display: flex; align-items: center; justify-content: center; font-family: inherit; transition: all 0.15s; }
    .modal-close:hover { border-color: var(--red); color: var(--red); }
    .modal-body   { padding: 24px; }
    .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 8px; }
    .form-group { margin-bottom: 16px; }
    .form-label  { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); display: block; margin-bottom: 6px; }
    .form-input  { width: 100%; background: #111; border: 1px solid var(--border); border-radius: 8px; padding: 9px 12px; color: var(--text); font-family: inherit; font-size: 0.85rem; outline: none; transition: border-color 0.2s; }
    .form-input:focus  { border-color: rgba(182,224,64,0.4); }
    .form-select { width: 100%; background: #111; border: 1px solid var(--border); border-radius: 8px; padding: 9px 12px; color: var(--text); font-family: inherit; font-size: 0.85rem; outline: none; cursor: pointer; transition: border-color 0.2s; }
    .form-select:focus { border-color: rgba(182,224,64,0.4); }

    /* Flash */
    .flash { padding: 10px 16px; border-radius: 9px; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
    .flash-success { background: var(--lime-dim); color: var(--lime); border: 1px solid rgba(182,224,64,0.2); }
    .flash-error   { background: var(--red-dim);  color: var(--red);  border: 1px solid rgba(224,85,85,0.2); }

    .sort-icon { color: var(--lime); }
</style>
@endpush

@section('content')
<div x-data="usersPage">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flash flash-success">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flash flash-error">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="page-eyebrow">Management</div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
        <h1 class="page-title" style="margin-bottom:0;">Users</h1>
        <button class="btn btn-lime" @click="showInvite=true" type="button">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Invite User
        </button>
    </div>

    {{-- Stats - real counts passed from controller --}}
    <div class="user-stats">
        <div class="u-stat">
            <div class="u-stat-val">{{ $totalCount }}</div>
            <div class="u-stat-lbl">Total Users</div>
        </div>
        <div class="u-stat">
            <div class="u-stat-val" style="color:var(--lime);">{{ $activeCount }}</div>
            <div class="u-stat-lbl">Active</div>
        </div>
        <div class="u-stat">
            <div class="u-stat-val" style="color:var(--blue);">{{ $adminCount }}</div>
            <div class="u-stat-lbl">Admins</div>
        </div>
        <div class="u-stat">
            <div class="u-stat-val" style="color:var(--red);">{{ $suspendedCount }}</div>
            <div class="u-stat-lbl">Suspended</div>
        </div>
    </div>

    {{-- Toolbar - server-side search/filter via GET --}}
    <form method="GET" action="{{ route('users.index') }}" id="filterForm" class="users-toolbar">
        <div class="user-search">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" placeholder="Search name or email…"
                   value="{{ request('search') }}"
                   x-on:input.debounce.400ms="$el.closest('form').submit()">
        </div>
        <select name="role" class="filter-sel" onchange="this.form.submit()">
            <option value="all"    @selected(request('role','all') === 'all')>All Roles</option>
            <option value="admin"  @selected(request('role') === 'admin')>Admin</option>
            <option value="viewer" @selected(request('role') === 'viewer')>Viewer</option>
        </select>
        <select name="status" class="filter-sel" onchange="this.form.submit()">
            <option value="all"       @selected(request('status','all') === 'all')>All Status</option>
            <option value="active"    @selected(request('status') === 'active')>Active</option>
            <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
        </select>
        @if(request('search') || request('role') || request('status'))
            <a href="{{ route('users.index') }}" class="btn btn-outline" style="font-size:0.75rem;padding:5px 12px;">Clear</a>
        @endif
        <span style="margin-left:auto;font-size:0.75rem;color:var(--muted);">
            {{ $users->total() }} {{ Str::plural('user', $users->total()) }}
        </span>
    </form>

    {{-- Table --}}
    <div class="users-table-wrap">
        <table class="users-table">
            <thead>
                <tr>
                    <th>
                        <a href="{{ route('users.index', array_merge(request()->query(), [
                            'sort'      => 'name',
                            'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc',
                        ])) }}">
                            User
                            @if(request('sort', 'name') === 'name')
                                <span class="sort-icon">{{ request('direction','asc') === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>
                        <a href="{{ route('users.index', array_merge(request()->query(), [
                            'sort'      => 'created_at',
                            'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc',
                        ])) }}">
                            Joined
                            @if(request('sort') === 'created_at')
                                <span class="sort-icon">{{ request('direction','asc') === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                    $avatarColors = ['#b6e040','#60a5fa','#f472b6','#fbbf24','#a78bfa','#34d399'];
                    $avatarColor  = $avatarColors[abs(crc32($user->email)) % count($avatarColors)];
                    $isSuspended  = !is_null($user->suspended_at);
                    $status       = $isSuspended ? 'suspended' : 'active';
                    $role         = $user->is_admin ? 'admin' : ($user->role ?? 'viewer');
                    $nameParts    = explode(' ', $user->name);
                    $initials     = strtoupper(substr($nameParts[0], 0, 1)) . (isset($nameParts[1]) ? strtoupper(substr($nameParts[1], 0, 1)) : '');
                @endphp
                <tr>
                    <td>
                        <div class="user-row">
                            <div class="user-avatar" style="background:{{ $avatarColor }}22;color:{{ $avatarColor }};">
                                {{ $initials }}
                            </div>
                            <div>
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="role-badge role-{{ $role }}">{{ ucfirst($role) }}</span>
                    </td>
                    <td>
                        <span class="status-dot status-{{ $status }}">{{ ucfirst($status) }}</span>
                    </td>
                    <td style="color:var(--muted);font-size:0.75rem;">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        <div class="row-actions" style="justify-content:flex-end;">
                            {{-- Edit --}}
                            <button class="row-btn" title="Edit"
                                    @click="openEdit({{ json_encode(['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'role'=>$user->role ?? 'viewer']) }})"
                                    type="button">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            {{-- Suspend (not for self) --}}
                            @if($user->id !== auth()->id())
                            <button class="row-btn warn"
                                    title="{{ $isSuspended ? 'Unsuspend' : 'Suspend' }}"
                                    @click="suspend({{ $user->id }})"
                                    type="button">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                            </button>
                            {{-- Delete --}}
                            <button class="row-btn danger" title="Delete"
                                    @click="openDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    type="button">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:var(--muted);">
                        No users found matching your search.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="table-footer">
            <div class="table-info">
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
            </div>
            <div class="table-pages">
                @if($users->onFirstPage())
                    <span class="page-btn disabled"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="page-btn"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></a>
                @endif

                @foreach($users->getUrlRange(max(1,$users->currentPage()-2), min($users->lastPage(),$users->currentPage()+2)) as $page => $url)
                    @if($page == $users->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="page-btn"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
                @else
                    <span class="page-btn disabled"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ── Invite Modal ──────────────────────────────────────────────── --}}
    <div class="modal-overlay" x-show="showInvite" x-cloak @click.self="showInvite=false" style="display:none;">
        <div class="modal-box" @click.stop>
            <div class="modal-header">
                <div class="modal-title">Invite New User</div>
                <button class="modal-close" @click="showInvite=false" type="button">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input class="form-input" type="text" name="name" placeholder="Jane Doe" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input class="form-input" type="email" name="email" placeholder="jane@example.com" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="viewer">Viewer — Can read articles and summaries</option>
                            <option value="admin">Admin — Full access to all features</option>
                        </select>
                    </div>
                    <div style="background:rgba(182,224,64,0.05);border:1px solid rgba(182,224,64,0.15);border-radius:8px;padding:12px;font-size:0.78rem;color:var(--muted);">
                        📧 An invitation email will be sent to this address.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" @click="showInvite=false">Cancel</button>
                    <button type="submit" class="btn btn-lime">Send Invite</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Edit Modal ────────────────────────────────────────────────── --}}
    <div class="modal-overlay" x-show="showEdit" x-cloak @click.self="showEdit=false" style="display:none;">
        <div class="modal-box" @click.stop>
            <div class="modal-header">
                <div class="modal-title">Edit User</div>
                <button class="modal-close" @click="showEdit=false" type="button">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input class="form-input" type="text" x-model="editForm.name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input class="form-input" type="email" x-model="editForm.email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select class="form-select" x-model="editForm.role">
                        <option value="viewer">Viewer — Can read articles and summaries</option>
                        <option value="admin">Admin — Full access to all features</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" @click="showEdit=false" type="button">Cancel</button>
                <button class="btn btn-lime" @click="submitEdit()" :disabled="saving" type="button">
                    <span x-text="saving ? 'Saving…' : 'Save Changes'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Delete Confirm Modal ──────────────────────────────────────── --}}
    <div class="modal-overlay" x-show="showDelete" x-cloak @click.self="showDelete=false" style="display:none;">
        <div class="modal-box" @click.stop style="max-width:400px;">
            <div class="modal-header">
                <div class="modal-title" style="color:var(--red);">Delete User</div>
                <button class="modal-close" @click="showDelete=false" type="button">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <p style="font-size:0.84rem;color:#aaa;line-height:1.6;">
                    Are you sure you want to delete
                    <strong style="color:#e0e0e0;" x-text="deleteTarget.name"></strong>?
                    This cannot be undone.
                </p>
            </div>
            <form :action="`/users/${deleteTarget.id}`" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" @click="showDelete=false">Cancel</button>
                    <button type="submit" class="btn" style="background:var(--red);color:#fff;">Delete</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('usersPage', () => ({
        showInvite:   false,
        showEdit:     false,
        showDelete:   false,
        saving:       false,
        editForm:     { id: null, name: '', email: '', role: 'viewer' },
        deleteTarget: { id: null, name: '' },

        init() {},

        openEdit(user) {
            this.editForm = { ...user };
            this.showEdit = true;
        },

        async submitEdit() {
            if (!this.editForm.name || !this.editForm.email) return;
            this.saving = true;
            try {
                const res = await fetch(`/users/${this.editForm.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        name:  this.editForm.name,
                        email: this.editForm.email,
                        role:  this.editForm.role,
                    }),
                });
                if (res.ok) {
                    this.showEdit = false;
                    window.location.reload();
                } else {
                    const data = await res.json();
                    alert(data.message || 'Update failed. Please try again.');
                }
            } catch (e) {
                alert('Network error. Please try again.');
            } finally {
                this.saving = false;
            }
        },

        async suspend(userId) {
            try {
                const res = await fetch(`/users/${userId}/suspend`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                if (res.ok) window.location.reload();
                else alert('Could not update user status.');
            } catch (e) {
                alert('Network error. Please try again.');
            }
        },

        openDelete(id, name) {
            this.deleteTarget = { id, name };
            this.showDelete = true;
        },
    }));
});
</script>
@endpush
@endsection