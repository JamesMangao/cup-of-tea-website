@extends('layouts.app')
@section('title', 'System Settings')

@push('styles')
<style>
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
        align-items: flex-start;
    }

    @media (max-width: 1024px) {
        .settings-grid { grid-template-columns: 1fr; }
    }

    .settings-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text);
    }

    .card-body {
        padding: 24px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-row {
        border-bottom: 1px solid var(--border);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        padding: 14px 0;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
        width: 160px;
    }

    .info-value {
        padding: 14px 0;
        font-size: 0.9rem;
        color: var(--text);
        font-family: 'JetBrains Mono', 'Courier New', monospace;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 2px 10px;
        border-radius: 100px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .status-prod { background: rgba(96, 165, 250, 0.1); color: #60a5fa; }
    .status-dev { background: rgba(251, 191, 36, 0.1); color: var(--amber); }

    .maintenance-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid var(--border2);
        background: transparent;
        color: var(--text);
        font-family: inherit;
    }

    .maintenance-btn:hover {
        background: var(--red-dim);
        border-color: var(--red);
        color: var(--red);
    }

    .maintenance-btn svg {
        transition: transform 0.3s;
    }

    .maintenance-btn:hover svg {
        transform: rotate(180deg);
    }
</style>
@endpush

@section('content')
<div x-data="systemSettings" class="page-entrance">
    <div class="page-eyebrow">Environment & Diagnostics</div>
    <h1 class="page-title">System Settings</h1>

    <div class="settings-grid">
        <div class="main-settings">
            <div class="settings-card">
                <div class="card-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    <span class="card-title">System Diagnostics</span>
                </div>
                <div class="card-body">
                    <table class="info-table">
                        <tr class="info-row">
                            <td class="info-label">Environment</td>
                            <td class="info-value">
                                <span class="status-badge {{ $systemInfo['app_env'] == 'production' ? 'status-prod' : 'status-dev' }}">
                                    {{ strtoupper($systemInfo['app_env']) }}
                                </span>
                            </td>
                        </tr>
                        <tr class="info-row">
                            <td class="info-label">Laravel Version</td>
                            <td class="info-value">{{ $systemInfo['laravel_version'] }}</td>
                        </tr>
                        <tr class="info-row">
                            <td class="info-label">PHP Version</td>
                            <td class="info-value">{{ $systemInfo['php_version'] }}</td>
                        </tr>
                        <tr class="info-row">
                            <td class="info-label">Cache Driver</td>
                            <td class="info-value">{{ $systemInfo['cache_driver'] }}</td>
                        </tr>
                        <tr class="info-row">
                            <td class="info-label">Queue Driver</td>
                            <td class="info-value">{{ $systemInfo['queue_driver'] }}</td>
                        </tr>
                        <tr class="info-row">
                            <td class="info-label">Debug Mode</td>
                            <td class="info-value">
                                <span style="color: {{ $systemInfo['debug_mode'] ? 'var(--amber)' : 'var(--lime)' }}">
                                    {{ $systemInfo['debug_mode'] ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="aside-info">
            <div class="settings-card">
                <div class="card-header">
                    <span class="card-title">Maintenance Tools</span>
                </div>
                <div class="card-body">
                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                        Use these utilities to perform routine system maintenance. 
                        <strong>Warning:</strong> Clearing cache may cause momentary performance drops.
                    </p>
                    
                    <button @click="clearCache" :disabled="clearing" class="maintenance-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/></svg>
                        <span x-show="!clearing">Clear Application Cache</span>
                        <span x-show="clearing">Clearing Systems...</span>
                    </button>
                    
                    <div class="mt-8 pt-6 border-t border-gray-800">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></div>
                            <span class="text-[0.65rem] font-bold uppercase tracking-wider text-gray-400">System Logs</span>
                        </div>
                        <a href="{{ route('system.logs') }}" class="btn btn-outline w-full text-center">View Debug Logs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('systemSettings', () => ({
        clearing: false,
        clearCache() {
            this.clearing = true;
            fetch('{{ url("/api/settings/clear-cache") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.toast.success(data.message);
                } else {
                    window.toast.error(data.message);
                }
            })
            .catch(err => {
                window.toast.error('System error occurred while clearing cache.');
            })
            .finally(() => {
                this.clearing = false;
            });
        }
    }));
});
</script>
@endsection
