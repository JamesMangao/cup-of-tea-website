@extends('layouts.app')
@section('title', 'AI Settings')

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

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
        margin-bottom: 8px;
    }

    .input-wrapper {
        position: relative;
    }

    .form-input, .form-select {
        width: 100%;
        background: #111;
        border: 1px solid var(--border2);
        border-radius: 10px;
        padding: 12px 16px;
        color: var(--text);
        font-family: inherit;
        font-size: 0.9rem;
        transition: all 0.2s;
        outline: none;
    }

    .form-input:focus, .form-select:focus {
        border-color: var(--lime);
        box-shadow: 0 0 0 4px var(--lime-dim);
    }

    .form-help {
        font-size: 0.75rem;
        color: var(--muted);
        margin-top: 6px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 100px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-ok { background: rgba(182, 224, 64, 0.1); color: var(--lime); }
    .status-err { background: rgba(224, 85, 85, 0.1); color: var(--red); }

    .pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    .feature-list {
        list-style: none;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 16px;
    }

    .feature-icon {
        width: 20px;
        height: 20px;
        background: var(--lime-dim);
        color: var(--lime);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .feature-text {
        font-size: 0.85rem;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .feature-text strong {
        display: block;
        color: var(--text);
        font-weight: 600;
        margin-bottom: 2px;
    }
</style>
@endpush

@section('content')
<div x-data="aiSettings" class="page-entrance">
    <div class="page-eyebrow">Neural Configuration</div>
    <h1 class="page-title">AI Engine Settings</h1>

    <div class="settings-grid">
        <div class="main-settings">
            <div class="settings-card">
                <div class="card-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span class="card-title">API Authentication</span>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">GROQ API Key</label>
                        <div class="input-wrapper">
                            <input type="password" value="{{ $aiStats['groq_key_set'] ? '••••••••••••••••••••' : '' }}" readonly class="form-input" placeholder="Configure in .env file">
                        </div>
                        <div class="flex items-center justify-between mt-3">
                            <span class="status-pill {{ $aiStats['groq_key_set'] ? 'status-ok' : 'status-err' }}">
                                <span class="pulse-dot"></span>
                                {{ $aiStats['groq_key_set'] ? 'Key Active' : 'Key Missing' }}
                            </span>
                            <button @click="testConnection" :disabled="testing" class="btn btn-outline-lime scale-90">
                                <span x-show="!testing">Test Connection</span>
                                <span x-show="testing">Testing...</span>
                            </button>
                        </div>
                        <p class="form-help">Keys are securely stored in the environment configuration and never exposed to the client.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">GNews API Key</label>
                        <div class="input-wrapper">
                            <input type="password" value="{{ $aiStats['gnews_key_set'] ? '••••••••••••••••••••' : '' }}" readonly class="form-input" placeholder="Configure in .env file">
                        </div>
                        <div class="mt-3">
                            <span class="status-pill {{ $aiStats['gnews_key_set'] ? 'status-ok' : 'status-err' }}">
                                <span class="pulse-dot"></span>
                                {{ $aiStats['gnews_key_set'] ? 'Intelligence Stream Active' : 'Stream Disabled' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <div class="card-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    <span class="card-title">Model Configuration</span>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Primary LLM Model (Inference)</label>
                        <select class="form-select">
                            @foreach($groqModels as $model)
                                <option value="{{ $model }}" {{ $model == $aiStats['default_model'] ? 'selected' : '' }}>{{ $model }}</option>
                            @endforeach
                        </select>
                        <p class="form-help">Current active model for summarizing news articles.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Inference Temperature ({{ $aiStats['temperature'] }})</label>
                        <input type="range" min="0" max="1" step="0.1" value="{{ $aiStats['temperature'] }}" class="w-full h-2 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-[#b6e040]">
                        <div class="flex justify-between mt-2">
                            <span class="text-[0.65rem] text-gray-500">Concise</span>
                            <span class="text-[0.65rem] text-gray-500">Creative</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="aside-info">
            <div class="settings-card">
                <div class="card-header">
                    <span class="card-title">Neural Status</span>
                </div>
                <div class="card-body">
                    <ul class="feature-list">
                        <li class="feature-item">
                            <div class="feature-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                            <div class="feature-text">
                                <strong>Real-time Summaries</strong>
                                Articles are distilled using Groq's LPUs for sub-second latency.
                            </div>
                        </li>
                        <li class="feature-item">
                            <div class="feature-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
                            <div class="feature-text">
                                <strong>Style Switching</strong>
                                Dynamic prompting enabled for Professional, Executive, and Gen-Z modes.
                            </div>
                        </li>
                    </ul>

                    <div class="mt-6 pt-6 border-t border-gray-800">
                        <div class="text-[0.65rem] text-gray-500 uppercase font-black tracking-widest mb-3">System Load</div>
                        <div class="w-full bg-gray-800 h-1 rounded-full overflow-hidden">
                            <div class="bg-[#b6e040] h-full w-[24%]" style="box-shadow: 0 0 10px var(--lime);"></div>
                        </div>
                        <div class="mt-2 text-[0.65rem] text-[#b6e040] font-bold">Stable • 24% Capacity</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('aiSettings', () => ({
        testing: false,
        testConnection() {
            this.testing = true;
            fetch('{{ url("/api/settings/test-ai") }}', {
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
                window.toast.error('System error occurred during testing.');
            })
            .finally(() => {
                this.testing = false;
            });
        }
    }));
});
</script>
@endsection
