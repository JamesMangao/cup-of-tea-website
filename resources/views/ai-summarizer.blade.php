
@extends('layouts.app')
@section('title', 'AI Summarizer')

@push('styles')
<style>
    .summarizer-layout { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; }
    @media (max-width: 1000px) { .summarizer-layout { grid-template-columns: 1fr; } }

    .input-panel { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .input-panel:hover { transform: translateY(-2px); }

    .mode-tabs { display: flex; gap: 6px; margin-bottom: 20px; background: rgba(0,0,0,0.3); border-radius: 12px; padding: 5px; border: 1px solid var(--border-light); }
    .mode-tab { flex: 1; padding: 10px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-size: 0.8rem; font-weight: 700; font-family: inherit; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .mode-tab.active { background: var(--bg-card); color: var(--primary); box-shadow: 0 4px 15px rgba(0,0,0,0.3); border: 1px solid var(--primary-glow); }
    .mode-tab:hover:not(.active) { color: #fff; background: rgba(255,255,255,0.03); }

    .form-label { font-size: 0.65rem; font-weight: 900; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; display: block; }
    .form-input, .form-textarea, .option-select { 
        width: 100%; background: rgba(0,0,0,0.4); border: 1px solid var(--border-light); 
        border-radius: 10px; padding: 12px 16px; color: var(--text); font-size: 0.9rem; 
        font-family: inherit; outline: none; transition: all 0.2s;
    }
    .form-input:focus, .form-textarea:focus, .option-select:focus { 
        border-color: var(--primary); background: rgba(0,0,0,0.6); box-shadow: 0 0 0 4px var(--primary-glow);
    }
    .form-textarea { min-height: 180px; line-height: 1.6; }

    .options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }

    .result-panel { margin-top: 24px; animation: slide-up 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .result-text { font-size: 1rem; line-height: 1.8; color: var(--text-secondary); letter-spacing: 0.2px; }
    .result-text b, .result-text strong { color: var(--primary); }
    
    .typing-cursor { display: inline-block; width: 6px; height: 1.2em; background: var(--primary); margin-left: 4px; animation: blink-cursor 0.7s infinite; vertical-align: middle; }

    .history-item { cursor: pointer; transition: all 0.2s; border-left: 3px solid transparent; }
    .history-item:hover { background: rgba(255,255,255,0.03); border-left-color: var(--primary); transform: translateX(4px); }
    
    .ai-loader { border: 3px solid var(--border-light); border-top-color: var(--primary); }
</style>
@endpush

@section('content')
<div x-data="aiSummarizer" x-cloak class="animate-fade-in" x-init="init()">

    <div class="page-eyebrow">Intellectual Hub</div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:32px;">
        <div>
            <h1 class="page-title" style="margin-bottom:8px;">AI Summarizer Pro</h1>
            <p style="font-size:0.9rem;color:var(--muted);margin:0;">Distill complex information using <span class="text-primary font-bold">Llama 3</span> via Groq API</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="live-pill">Ultra Fast</span>
            <div class="avatar-small" style="background: var(--primary-glow); border: 1px solid var(--primary);">AI</div>
        </div>
    </div>

    <div class="summarizer-layout">
        <!-- Left: Input + Result -->
        <div>
            <div class="glass-card input-panel">
                <div class="p-8">
                    <!-- Mode Tabs -->
                    <div class="mode-tabs">
                        <button class="mode-tab" :class="mode==='text'?'active':''" @click="mode='text'" title="Summarize copied text">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Text
                        </button>
                        <button class="mode-tab" :class="mode==='url'?'active':''" @click="mode='url'" title="Summarize from a web link">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            URL
                        </button>
                        <button class="mode-tab" :class="mode==='topic'?'active':''" @click="mode='topic'" title="Summarize a general topic">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Topic
                        </button>
                    </div>

                    <!-- Text Mode -->
                    <div x-show="mode==='text'" class="animate-fade-in">
                        <div class="mb-6">
                            <label class="form-label">Article Text</label>
                            <textarea class="form-textarea" x-model="inputText" @input="updateCharCount()" placeholder="Paste the content you want to analyze here... (min 50 chars)"></textarea>
                            <div class="flex justify-between mt-2">
                                <span class="text-[10px] text-muted font-bold tracking-widest uppercase" x-text="inputText.split(/\s+/).filter(Boolean).length + ' words'"></span>
                                <div class="char-count" :class="charCount > 8000 ? 'error' : charCount > 5000 ? 'warn' : ''" x-text="charCount + ' / 8000 chars'"></div>
                            </div>
                        </div>
                    </div>

                    <!-- URL Mode -->
                    <div x-show="mode==='url'" class="animate-fade-in">
                        <div class="mb-6">
                            <label class="form-label">Web Address</label>
                            <input type="url" class="form-input" x-model="inputUrl" placeholder="https://www.theverge.com/article-path..." />
                        </div>
                    </div>

                    <!-- Topic Mode -->
                    <div x-show="mode==='topic'" class="animate-fade-in">
                        <div class="mb-6">
                            <label class="form-label">Topic Query</label>
                            <input type="text" class="form-input" x-model="inputTopic" placeholder="e.g. Latest advancements in Quantum Computing" />
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="options-grid">
                        <div>
                            <label class="form-label">Output Style</label>
                            <select class="option-select" x-model="summaryStyle">
                                <option value="concise">Concise Overview</option>
                                <option value="detailed">Comprehensive Analysis</option>
                                <option value="bullets">Key Bullet Points</option>
                                <option value="eli5">Explain Like I'm 5</option>
                                <option value="executive">Executive Brief</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Intelligence Level</label>
                            <select class="option-select" x-model="selectedModel">
                                <option value="llama3-8b-8192">Llama 3 8B (Speed)</option>
                                <option value="llama3-70b-8192">Llama 3 70B (Power)</option>
                                <option value="mixtral-8x7b-32768">Mixtral 8x7B (Niche)</option>
                            </select>
                        </div>
                    </div>

                    <button class="btn btn-lime btn-lg w-full hover-glow shadow-xl" @click="summarize()" :disabled="isLoading || !canSubmit()">
                        <template x-if="!isLoading">
                            <span class="flex items-center gap-3">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                Generate AI Summary
                            </span>
                        </template>
                        <template x-if="isLoading">
                            <span class="flex items-center gap-3">
                                <div class="ai-loader w-5 h-5 rounded-full animate-spin"></div>
                                Processing with Groq...
                            </span>
                        </template>
                    </button>
                    
                    <div class="mt-4 text-center">
                        <p class="text-[10px] text-muted font-bold tracking-widest uppercase">Result ready in < 1.0 second typically </p>
                    </div>
                </div>
            </div>

            <!-- Result -->
            <div x-show="showResult || isLoading" class="glass-card result-panel overflow-hidden">
                <div class="px-8 py-6 border-b border-border-light flex items-center justify-between bg-primary-glow/5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary-glow flex items-center justify-center text-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <span class="font-bold text-white uppercase tracking-wider text-xs">Summary Result</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="btn btn-outline px-4 py-2 text-xs" @click="copyResult()" :disabled="isLoading">
                            <span x-text="copied?'COPIED':'COPY'"></span>
                        </button>
                        <button class="btn btn-outline px-4 py-2 text-xs border-primary/30 text-primary" @click="saveToLibrary()" :disabled="isLoading">
                            SAVE TO LIBRARY
                        </button>
                    </div>
                </div>
                
                <div class="p-8">
                    <template x-if="isLoading && !resultText">
                        <div class="flex flex-col items-center justify-center py-20 gap-6">
                            <div class="loading-spinner w-12 h-12"></div>
                            <div class="text-center">
                                <div class="text-white font-bold mb-1">Synthesizing Information</div>
                                <div class="text-xs text-muted" x-text="'Using ' + selectedModel"></div>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="resultText" class="result-text" x-html="formatResult(resultText)">
                        
                    </div>
                    <span x-show="isLoading" class="typing-cursor"></span>

                    <div x-show="!isLoading && resultStats.words" class="mt-10 pt-8 border-t border-border-light grid grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-xl font-black text-primary mb-1" x-text="resultStats.words"></div>
                            <div class="text-[9px] font-black text-muted uppercase tracking-widest">Words</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-black text-blue mb-1" x-text="resultStats.sentences"></div>
                            <div class="text-[9px] font-bold text-muted uppercase tracking-widest">Sentences</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-black text-pink mb-1" x-text="resultStats.readTime + 's'"></div>
                            <div class="text-[9px] font-bold text-muted uppercase tracking-widest">Read Time</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-black text-lime mb-1" x-text="resultStats.compression + '%'"></div>
                            <div class="text-[9px] font-bold text-muted uppercase tracking-widest">Compression</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error -->
            <div x-show="error" class="glass-card mt-6 p-6 border-red-900/30 bg-red-950/10 flex gap-4 animate-shake">
                <div class="text-red-500 mt-1">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <div class="font-bold text-red-500 mb-1">Neural Network Disturbance</div>
                    <div class="text-sm text-red-400/80" x-text="error"></div>
                </div>
            </div>
        </div>

        <!-- Right: History + Tips -->
        <div class="space-y-6">
            <div class="glass-card overflow-hidden">
                <div class="p-5 border-b border-border-light bg-black/20 flex items-center justify-between">
                    <span class="text-xs font-black text-white uppercase tracking-widest">Recent Activity</span>
                    <span class="text-[10px] text-muted font-bold" x-text="history.length"></span>
                </div>
                <div class="history-list max-h-[400px] overflow-y-auto">
                    <template x-if="history.length === 0">
                        <div class="p-10 text-center text-muted italic text-xs">
                            No history yet. Start summarizing to build your timeline.
                        </div>
                    </template>
                    <template x-for="(item, i) in history" :key="i">
                        <div class="history-item p-5 border-b border-border-light/50" @click="loadHistory(item)">
                            <div class="text-xs font-bold text-secondary line-clamp-2 mb-2" x-text="item.preview"></div>
                            <div class="flex items-center gap-3">
                                <span class="tag-gen text-[9px] px-2 py-0.5" x-text="item.style"></span>
                                <span class="text-[9px] text-muted font-bold" x-text="item.time"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="glass-card p-6 bg-gradient-to-br from-primary-glow/5 to-transparent">
                <h3 class="text-xs font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5"><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                    Pro Efficiency
                </h3>
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <span class="text-lg">🤖</span>
                        <p class="text-[11px] text-muted leading-relaxed">
                            <span class="text-primary font-black">Llama 3 70B</span> is recommended for complex legal or scientific documents.
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <span class="text-lg">⚡</span>
                        <p class="text-[11px] text-muted leading-relaxed">
                            Summarize by <span class="text-blue font-bold">URL</span> to bypass article clutter and ads automatically.
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <span class="text-lg">🌍</span>
                        <p class="text-[11px] text-muted leading-relaxed">
                            Need a translation? Change the <span class="text-pink font-bold">Language</span> setting before clicking generate.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const register = () => {
        Alpine.data('aiSummarizer', () => ({
        mode: 'text',
        inputText: '', inputUrl: '', inputTopic: '',
        summaryStyle: 'concise', outputLang: 'english',
        selectedModel: 'llama3-8b-8192', tone: 'neutral',
        isLoading: false, showResult: false, error: null,
        resultText: '', copied: false,
        charCount: 0,
        resultStats: {},
        history: JSON.parse(localStorage.getItem('cup_summaries') || '[]'),

        init() {},

        canSubmit() {
            if (this.mode === 'text') return this.inputText.trim().length > 50;
            if (this.mode === 'url') return this.inputUrl.trim().length > 8;
            if (this.mode === 'topic') return this.inputTopic.trim().length > 3;
            return false;
        },

        updateCharCount() {
            this.charCount = this.inputText.length;
        },

        buildPrompt() {
            const styleInstructions = {
                concise: 'Write a concise 2-3 sentence summary capturing the absolute core essence.',
                detailed: 'Provide a comprehensive 2-3 paragraph breakdown with all crucial facts and context.',
                bullets: 'Extract 5 high-impact bullet points, each starting with • and focused on a unique insight.',
                eli5: 'Explain this in ultra-simple terms as if talking to a bright 10-year-old. No jargon.',
                executive: 'Format as an Executive Brief: [SUMMARY], [KEY IMPLICATIONS], and [NEXT STEPS/ACTION ITEMS].',
            };
            const langNote = this.outputLang !== 'english' ? `Crucially, respond entirely in ${this.outputLang}.` : '';
            
            let sourceContext = '';
            if (this.mode === 'text') sourceContext = `Content to analyze:\n"""\n${this.inputText}\n"""`;
            else if (this.mode === 'url') sourceContext = `I need you to summarize the article located at this URL: ${this.inputUrl}`;
            else sourceContext = `Research and synthesize a state-of-the-art summary about the following topic: ${this.inputTopic}`;

            return `You are a world-class strategic analyst. ${styleInstructions[this.summaryStyle]} ${toneInstructions[this.tone]} ${langNote}\n\n${sourceContext}`;
        },

        async summarize() {
            this.isLoading = true;
            this.error = null;
            this.resultText = '';
            this.showResult = true;
            this.resultStats = {};

            try {
                const res = await fetch('/api/summarize', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: this.mode === 'text' ? this.inputText : '',
                        url: this.mode === 'url' ? this.inputUrl : '',
                        topic: this.mode === 'topic' ? this.inputTopic : '',
                        prompt: this.buildPrompt(),
                        model: this.selectedModel,
                        style: this.summaryStyle,
                        language: this.outputLang,
                        tone: this.tone
                    })
                });
                
                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.error || `Server error: ${res.status}`);
                }
                
                const data = await res.json();
                if (data.error) throw new Error(data.error);
                
                this.resultText = data.summary ?? data.text ?? data.content ?? '';
                if (!this.resultText) throw new Error("Received empty response from neural engine.");
                
                this.computeStats();
                this.addToHistory();
            } catch (e) {
                console.error("Summarization error:", e);
                this.error = e.message;
                this.showResult = false;
            } finally {
                this.isLoading = false;
            }
        },

        computeStats() {
            const words = this.resultText.split(/\s+/).filter(Boolean).length;
            const sentences = this.resultText.split(/[.!?]+/).filter(Boolean).length;
            const inputLen = this.inputText.length || 1500; // fallback if URL mode
            const outputLen = this.resultText.length;
            this.resultStats = {
                words,
                sentences,
                readTime: Math.max(5, Math.ceil(words / 3.5)),
                compression: Math.max(20, Math.min(95, Math.round((1 - outputLen / inputLen) * 100))),
            };
        },

        addToHistory() {
            const item = {
                preview: this.resultText.substring(0, 120),
                result: this.resultText,
                style: this.summaryStyle,
                model: this.selectedModel,
                time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
            };
            this.history.unshift(item);
            if (this.history.length > 30) this.history.pop();
            localStorage.setItem('cup_summaries', JSON.stringify(this.history));
        },

        loadHistory(item) {
            this.resultText = item.result;
            this.showResult = true;
            this.summaryStyle = item.style;
            this.computeStats();
            window.scrollTo({ top: document.querySelector('.result-panel')?.offsetTop - 100, behavior: 'smooth' });
        },

        async copyResult() {
            try {
                await navigator.clipboard.writeText(this.resultText);
                this.copied = true;
                setTimeout(() => this.copied = false, 2500);
            } catch (e) {
                console.error("Copy failed", e);
            }
        },

        async saveToLibrary() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('/api/store-summary', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        original_text: this.inputText || this.inputUrl || this.inputTopic || "No Source Text",
                        summary: this.resultText
                    })
                });
                
                if (res.ok) {
                    alert('Synchronized to Cloud Library successfully!');
                } else {
                    // Fallback to local
                    const saved = JSON.parse(localStorage.getItem('cup_library') || '[]');
                    saved.unshift({ type: 'summary', content: this.resultText, style: this.summaryStyle, date: new Date().toISOString() });
                    localStorage.setItem('cup_library', JSON.stringify(saved));
                    alert('Saved to Local Device Library.');
                }
            } catch (e) {
                const saved = JSON.parse(localStorage.getItem('cup_library') || '[]');
                saved.unshift({ type: 'summary', content: this.resultText, style: this.summaryStyle, date: new Date().toISOString() });
                localStorage.setItem('cup_library', JSON.stringify(saved));
                alert('Saved to Local Device Library.');
            }
        },

        formatResult(text) {
            if (!text) return '';
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/•/g, '<br>•')
                .replace(/\n\n/g, '<br><br>')
                .replace(/\n/g, '<br>');
        },
    }));
    };

    if (window.Alpine) {
        register();
    } else {
        document.addEventListener('alpine:init', register);
    }
})();
</script>
@endpush
@endsection

