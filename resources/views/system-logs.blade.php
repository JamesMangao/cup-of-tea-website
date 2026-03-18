@extends('layouts.app')
@section('title', 'System Logs')

@push('styles')
<style>
    .logs-toolbar { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .log-search { flex: 1; min-width: 200px; background: var(--bg-card); border: 1px solid var(--border2); border-radius: 9px; display: flex; align-items: center; gap: 8px; padding: 0 12px; height: 38px; }
    .log-search:focus-within { border-color: rgba(182,224,64,0.4); }
    .log-search input { background: none; border: none; outline: none; color: var(--text); font-size: 0.82rem; font-family: inherit; flex: 1; }
    .log-search input::placeholder { color: var(--muted2); }

    .level-tabs { display: flex; gap: 6px; margin-bottom: 20px; flex-wrap: wrap; }
    .level-tab { padding: 5px 12px; border-radius: 7px; font-size: 0.75rem; font-weight: 700; border: 1px solid var(--border); background: transparent; color: var(--muted); cursor: pointer; font-family: inherit; transition: all 0.15s; display: flex; align-items: center; gap: 6px; }
    .level-tab:hover { border-color: var(--border2); color: #ccc; }
    .level-tab.active { border-color: currentColor; }
    .level-tab.l-all.active  { color: #ccc; background: #1e1e1e; }
    .level-tab.l-info.active  { color: var(--blue); background: rgba(96,165,250,0.08); border-color: rgba(96,165,250,0.3); }
    .level-tab.l-success.active { color: var(--lime); background: rgba(182,224,64,0.08); border-color: rgba(182,224,64,0.3); }
    .level-tab.l-warning.active { color: var(--amber); background: rgba(251,191,36,0.08); border-color: rgba(251,191,36,0.3); }
    .level-tab.l-error.active { color: var(--red); background: rgba(224,85,85,0.08); border-color: rgba(224,85,85,0.3); }
    .level-count { padding: 1px 6px; border-radius: 10px; font-size: 0.62rem; background: rgba(255,255,255,0.07); }

    .log-stats { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
    .log-stat { background: var(--bg-card); border: 1px solid var(--border); border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; gap: 10px; flex: 1; min-width: 130px; }
    .log-stat-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .log-stat-val { font-size: 1.3rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1; }
    .log-stat-lbl { font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); margin-top: 2px; }

    .log-panel { background: #0a0a0a; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; font-family: 'Courier New', monospace; }
    .log-panel-header { padding: 12px 18px; background: #111; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .terminal-dots { display: flex; gap: 6px; }
    .t-dot { width: 11px; height: 11px; border-radius: 50%; }
    .log-panel-title { font-size: 0.75rem; font-weight: 600; color: var(--muted); font-family: inherit; }
    .log-stream { max-height: 600px; overflow-y: auto; padding: 0; }

    .log-entry { display: flex; align-items: baseline; gap: 0; padding: 6px 18px; transition: background 0.1s; cursor: pointer; border-bottom: 1px solid #1a1a1a; }
    .log-entry:hover { background: #111; }
    .log-time { font-size: 0.7rem; color: #444; white-space: nowrap; flex-shrink: 0; width: 88px; }
    .log-level-badge { font-size: 0.68rem; font-weight: 700; width: 64px; flex-shrink: 0; text-transform: uppercase; }
    .log-level-INFO    { color: #60a5fa; }
    .log-level-SUCCESS { color: #b6e040; }
    .log-level-WARNING { color: #fbbf24; }
    .log-level-ERROR   { color: #e05555; }
    .log-level-DEBUG   { color: #888; }
    .log-channel { font-size: 0.68rem; color: #555; width: 80px; flex-shrink: 0; }
    .log-msg { font-size: 0.78rem; color: #aaa; flex: 1; line-height: 1.5; word-break: break-all; white-space: pre-wrap; }
    .log-entry.is-error { background: rgba(224,85,85,0.04); }
    .log-entry.is-error:hover { background: rgba(224,85,85,0.07); }

    .log-detail { background: #111; border-top: 1px solid var(--border); padding: 16px 18px; font-size: 0.78rem; color: #999; }
    .log-detail-key { color: #555; }
    .log-detail-val { color: #ccc; }
    .log-detail pre { background: #0a0a0a; border: 1px solid var(--border); border-radius: 7px; padding: 10px 12px; font-size: 0.72rem; color: #aaa; overflow-x: auto; margin-top: 8px; white-space: pre; max-height: 200px; }

    .live-stream-badge { display: flex; align-items: center; gap: 6px; font-size: 0.7rem; color: var(--lime); font-family: inherit; }
    .live-stream-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--lime); animation: liveblink 2s infinite; }

    .log-filter-select { background: #111; border: 1px solid var(--border); color: var(--muted); border-radius: 7px; padding: 6px 10px; height: 36px; font-family: inherit; font-size: 0.78rem; outline: none; cursor: pointer; }

    .empty-state { padding: 48px; text-align: center; color: #444; font-size: 0.82rem; }
</style>
@endpush

@section('content')
<div x-data="systemLogs">

    <div class="page-eyebrow">System</div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
        <h1 class="page-title" style="margin-bottom:0;">System Logs</h1>
        <div style="display:flex;gap:8px;">
            <button class="btn btn-outline" @click="exportLogs()" style="font-size:0.78rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export
            </button>
            <button class="btn" :class="isLive?'btn-lime':'btn-outline'" @click="toggleLive()" style="font-size:0.78rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                <span x-text="isLive ? 'Live' : 'Paused'"></span>
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="log-stats">
        <div class="log-stat">
            <div class="log-stat-icon" style="background:rgba(96,165,250,0.1);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <div class="log-stat-val" style="color:#60a5fa;" x-text="stats.info || 0"></div>
                <div class="log-stat-lbl">Info</div>
            </div>
        </div>
        <div class="log-stat">
            <div class="log-stat-icon" style="background:rgba(182,224,64,0.1);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b6e040" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="log-stat-val" style="color:#b6e040;" x-text="stats.success || 0"></div>
                <div class="log-stat-lbl">Success</div>
            </div>
        </div>
        <div class="log-stat">
            <div class="log-stat-icon" style="background:rgba(251,191,36,0.1);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <div class="log-stat-val" style="color:#fbbf24;" x-text="stats.warning || 0"></div>
                <div class="log-stat-lbl">Warnings</div>
            </div>
        </div>
        <div class="log-stat">
            <div class="log-stat-icon" style="background:rgba(224,85,85,0.1);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e05555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <div class="log-stat-val" style="color:#e05555;" x-text="stats.error || 0"></div>
                <div class="log-stat-lbl">Errors</div>
            </div>
        </div>
        <div class="log-stat">
            <div class="log-stat-icon" style="background:#1a1a1a;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div>
                <div class="log-stat-val" x-text="stats.total || 0"></div>
                <div class="log-stat-lbl">Total</div>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="logs-toolbar">
        <div class="log-search">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Search logs..." x-model="searchQuery" @input.debounce.500ms="fetchLogs()" />
        </div>
        <select class="log-filter-select" x-model="filterChannel" @change="fetchLogs()">
            <option value="all">All Channels</option>
            <option value="api">API</option>
            <option value="auth">Auth</option>
            <option value="news">News</option>
            <option value="groq">Groq</option>
            <option value="db">Database</option>
            <option value="scheduler">Scheduler</option>
        </select>
    </div>

    <!-- Level Tabs -->
    <div class="level-tabs">
        <button class="level-tab l-all" :class="filterLevel==='all'?'active':''" @click="filterLevel='all'; fetchLogs()">
            All <span class="level-count" x-text="stats.total || 0"></span>
        </button>
        <button class="level-tab l-info" :class="filterLevel==='INFO'?'active':''" @click="filterLevel='INFO'; fetchLogs()">
            INFO <span class="level-count" x-text="stats.info || 0"></span>
        </button>
        <button class="level-tab l-success" :class="filterLevel==='SUCCESS'?'active':''" @click="filterLevel='SUCCESS'; fetchLogs()">
            SUCCESS <span class="level-count" x-text="stats.success || 0"></span>
        </button>
        <button class="level-tab l-warning" :class="filterLevel==='WARNING'?'active':''" @click="filterLevel='WARNING'; fetchLogs()">
            WARNING <span class="level-count" x-text="stats.warning || 0"></span>
        </button>
        <button class="level-tab l-error" :class="filterLevel==='ERROR'?'active':''" @click="filterLevel='ERROR'; fetchLogs()">
            ERROR <span class="level-count" x-text="stats.error || 0"></span>
        </button>
    </div>

    <!-- Terminal Panel -->
    <div class="log-panel">
        <div class="log-panel-header">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="terminal-dots">
                    <div class="t-dot" style="background:#e05555;"></div>
                    <div class="t-dot" style="background:#fbbf24;"></div>
                    <div class="t-dot" style="background:#b6e040;"></div>
                </div>
                <span class="log-panel-title">system.log — <span x-text="logs.length"></span> entries</span>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <template x-if="isLive">
                    <div class="live-stream-badge">Live</div>
                </template>
            </div>
        </div>

        <div class="log-stream">
            <template x-if="logs.length === 0">
                <div class="empty-state">No logs found</div>
            </template>

            <template x-if="logs.length > 0">
                <div>
                    <template x-for="(log, i) in logs" :key="log.id">
                        <div>
                            <div class="log-entry" :class="{'is-error': log.level==='ERROR'}" @click="toggleDetail(log)">
                                <span class="log-time" x-text="log.time"></span>
                                <span class="log-level-badge" :class="'log-level-' + log.level" x-text="log.level"></span>
                                <span class="log-channel" x-text="'[' + log.channel + ']'"></span>
                                <span class="log-msg" x-text="log.message.substring(0, 150) + (log.message.length > 150 ? '...' : '')"></span>
                            </div>
                            <template x-if="selectedLog && selectedLog.id === log.id">
                                <div class="log-detail">
                                    <div style="margin-bottom:12px;">
                                        <span class="log-detail-key">Full Message:</span>
                                        <pre x-text="log.message"></pre>
                                    </div>
                                    <template x-if="log.context">
                                        <div>
                                            <span class="log-detail-key">Context:</span>
                                            <pre x-text="JSON.stringify(log.context, null, 2)"></pre>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('systemLogs', () => ({
        logs: [],
        stats: { total: 0, info: 0, success: 0, warning: 0, error: 0, debug: 0 },
        filterLevel: 'all',
        filterChannel: 'all',
        searchQuery: '',
        selectedLog: null,
        isLive: true,
        pollInterval: null,

        init() {
            this.fetchLogs();
            this.startPolling();
        },

        async fetchLogs() {
            try {
                const params = new URLSearchParams({
                    level: this.filterLevel,
                    channel: this.filterChannel,
                    search: this.searchQuery,
                    limit: 200,
                });

                const res = await fetch(`/api/logs?${params}`);
                const data = await res.json();

                if (data.success) {
                    this.logs = data.logs || [];
                    this.stats = data.stats || { total: 0, info: 0, success: 0, warning: 0, error: 0, debug: 0 };
                }
            } catch (e) {
                console.error('Error fetching logs:', e);
            }
        },

        startPolling() {
            if (this.pollInterval) clearInterval(this.pollInterval);
            this.pollInterval = setInterval(() => {
                if (this.isLive) {
                    this.fetchLogs();
                }
            }, 5000);
        },

        toggleLive() {
            this.isLive = !this.isLive;
        },

        toggleDetail(log) {
            this.selectedLog = this.selectedLog?.id === log.id ? null : log;
        },

        exportLogs() {
            const params = new URLSearchParams({
                level: this.filterLevel,
                channel: this.filterChannel,
            });
            window.location.href = `/api/logs/export?${params}`;
        },
    }));
});
</script>
@endpush
@endsection