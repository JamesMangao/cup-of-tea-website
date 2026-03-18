@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; margin-bottom: 24px; }
    @media (max-width: 900px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
    .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 12px; }
    .stat-card:hover { border-color: var(--border2); }
    .stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .si-lime  { background: rgba(182,224,64,0.1);  color: var(--lime); }
    .si-blue  { background: rgba(96,165,250,0.1);  color: var(--blue); }
    .si-pink  { background: rgba(244,114,182,0.1); color: var(--pink); }
    .si-amber { background: rgba(251,191,36,0.1);  color: var(--amber); }
    .stat-lbl { font-size: 0.62rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); margin-bottom: 4px; }
    .stat-val { font-size: 1.5rem; font-weight: 800; color: var(--text); letter-spacing: -0.02em; line-height: 1; }
    .stat-delta { font-size: 0.68rem; color: var(--lime); margin-top: 3px; }

    .panel { background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); gap: 12px; flex-wrap: wrap; }
    .panel-title { display: flex; align-items: center; gap: 8px; font-size: 0.95rem; font-weight: 700; }
    .panel-title::before { content:''; width:8px; height:8px; border-radius:50%; background:var(--lime); display:inline-block; animation:liveblink 2s infinite; }
    .panel-sub { font-size: 0.72rem; color: var(--muted); margin-top: 2px; }
    .panel-actions { display: flex; gap: 8px; flex-wrap: wrap; }

    .cat-tabs { display: flex; gap: 6px; padding: 12px 20px; border-bottom: 1px solid var(--border); overflow-x: auto; }
    .cat-tab { padding: 5px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; border: 1px solid var(--border); background: transparent; color: var(--muted); cursor: pointer; white-space: nowrap; transition: all 0.15s; font-family: inherit; }
    .cat-tab:hover { border-color: var(--border2); color: #ccc; }
    .cat-tab.active { background: var(--lime); border-color: var(--lime); color: #0f0f0f; }

    .news-item { display: flex; align-items: flex-start; gap: 14px; padding: 14px 20px; border-bottom: 1px solid var(--border); transition: background 0.15s; text-decoration: none; color: inherit; }
    .news-item:last-child { border-bottom: none; }
    .news-item:hover { background: #1f1f1f; }
    .news-item-link { flex: 1; display: flex; align-items: flex-start; gap: 14px; cursor: pointer; }
    .news-thumb { width: 58px; height: 58px; border-radius: 9px; object-fit: cover; flex-shrink: 0; background: #222; }
    .news-thumb-ph { width: 58px; height: 58px; border-radius: 9px; flex-shrink: 0; background: #222; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    .news-source { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--lime); margin-bottom: 3px; display: flex; align-items: center; gap: 6px; }
    .news-title { font-size: 0.875rem; font-weight: 600; color: #e0e0e0; line-height: 1.45; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .news-meta { font-size: 0.7rem; color: var(--muted); }
    .news-actions { display: flex; gap: 6px; align-items: center; flex-shrink: 0; }
    .action-btn { width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--border); background: transparent; color: var(--muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; font-family: inherit; padding: 0; }
    .action-btn:hover { border-color: var(--amber); color: var(--amber); }
    .action-btn.saved { border-color: var(--amber); color: var(--amber); background: rgba(251,191,36,0.08); }
    .action-btn.liked { border-color: var(--pink); color: var(--pink); background: rgba(244,114,182,0.08); }
    .action-btn.liked:hover { border-color: var(--pink); color: var(--pink); }

    .skeleton { background: linear-gradient(90deg, #1e1e1e 25%, #252525 50%, #1e1e1e 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 6px; }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
    .skel-item { display: flex; gap: 14px; padding: 14px 20px; border-bottom: 1px solid var(--border); }
</style>
@endpush

@section('content')
<div x-data="dashboard">

    <div class="page-eyebrow">Welcome back</div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
        <h1 class="page-title" style="margin-bottom:0;">Dashboard</h1>
        <span class="live-pill">Live updates enabled</span>
    </div>

    <!-- STAT CARDS -->
    <div class="stat-grid">
        <div class="stat-card glass-card animate-fade-in" style="animation-delay: 0ms;">
            <div class="stat-icon si-lime">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </div>
            <div>
                <div class="stat-lbl">Global Reach</div>
                <div class="stat-val" x-text="stats.articles">0</div>
                <div class="stat-delta"><span class="live-dot" style="margin-right: 4px;"></span> Articles Live</div>
            </div>
        </div>
        <div class="stat-card glass-card animate-fade-in" style="animation-delay: 100ms;">
            <div class="stat-icon si-blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div>
                <div class="stat-lbl">AI Summaries</div>
                <div class="stat-val" x-text="stats.total_summaries">0</div>
                <div class="stat-delta" style="color:var(--blue)">Real-time Total</div>
            </div>
        </div>
        <div class="stat-card glass-card animate-fade-in" style="animation-delay: 200ms;">
            <div class="stat-icon si-pink">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-lbl">Pulse Rate</div>
                <div class="stat-val" x-text="stats.active_users">0</div>
                <div class="stat-delta" style="color:var(--pink)">Active Sessions</div>
            </div>
        </div>
        <div class="stat-card glass-card animate-fade-in" style="animation-delay: 300ms;">
            <div class="stat-icon si-amber">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div>
                <div class="stat-lbl">Knowledge Hub</div>
                <div class="stat-val" x-text="stats.total_saves">0</div>
                <div class="stat-delta" style="color:var(--amber)">Saved Insights</div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT GRID -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 24px; align-items: start;">
        <!-- Left: News Panel -->
        <div class="panel glass-card">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Intelligence Stream</div>
                    <div class="panel-sub">Curated updates from global neural networks</div>
                </div>
                <div class="panel-actions">
                    <button class="btn btn-lime" @click="fetchNews()" :disabled="loading">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" :class="loading ? 'spin' : ''"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        <span x-text="loading ? 'Syncing…' : 'Sync Stream'"></span>
                    </button>
                </div>
            </div>

            <!-- Category Tabs -->
            <div class="cat-tabs">
                <template x-for="cat in categories" :key="cat.key">
                    <button class="cat-tab" :class="activeCategory === cat.key ? 'active' : ''" @click="switchCategory(cat.key)" x-text="cat.label"></button>
                </template>
            </div>

            <!-- Skeleton -->
            <template x-if="loading">
                <div>
                    <template x-for="i in 5" :key="i">
                        <div class="skel-item">
                            <div class="skeleton" style="width:140px;height:90px;border-radius:9px;flex-shrink:0;"></div>
                            <div style="flex:1;display:flex;flex-direction:column;gap:8px;padding-top:4px;">
                                <div class="skeleton" style="height:10px;width:30%;"></div>
                                <div class="skeleton" style="height:13px;width:90%;"></div>
                                <div class="skeleton" style="height:13px;width:70%;"></div>
                                <div class="skeleton" style="height:10px;width:20%;"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Error -->
            <template x-if="!loading && error">
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e05555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div class="empty-title">Stream Interrupted</div>
                    <div class="empty-sub" x-text="error"></div>
                    <button class="btn btn-lime" @click="fetchNews()">Reconnect</button>
                </div>
            </template>

            <!-- Articles -->
            <template x-if="!loading && !error">
                <div>
                    <template x-for="(article, index) in articles" :key="article.url">
                        <div class="news-item animate-fade-in" :style="'animation-delay:' + (index * 50) + 'ms'">
                            <a class="news-item-link" :href="articleUrl(article)">
                                <div class="news-image-container" style="width: 140px; height: 90px; flex-shrink: 0; position: relative; overflow: hidden; border-radius: 8px;">
                                    <template x-if="article.urlToImage">
                                        <img class="news-thumb" :src="article.urlToImage" :alt="article.title" 
                                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;"
                                             @mouseover="$el.style.transform='scale(1.1)'" @mouseout="$el.style.transform='scale(1)'"
                                             x-on:error="$el.src='https://via.placeholder.com/140x90?text=News'" loading="lazy" />
                                    </template>
                                    <template x-if="!article.urlToImage">
                                        <div class="news-thumb-ph" style="width: 100%; height: 100%; background: var(--bg-card-light);">📰</div>
                                    </template>
                                </div>
                                <div style="flex:1;min-width:0;padding: 2px 0;">
                                    <div class="news-source">
                                        <span x-text="article.source.name"></span>
                                        <span class="tag" :class="categoryTag(activeCategory)" x-text="activeCategory" style="padding: 2px 6px; font-size: 0.6rem;"></span>
                                    </div>
                                    <div class="news-title" style="font-size: 1rem; -webkit-line-clamp: 2;" x-text="article.title"></div>
                                    <div class="news-meta" x-text="formatDate(article.publishedAt)"></div>
                                </div>
                            </a>
                            <div class="news-actions">
                                <button class="action-btn hover-glow" :class="isFav(article) ? 'liked' : ''" @click.prevent.stop="toggleFav(article)" title="Favorite" type="button">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                </button>
                                <button class="action-btn hover-glow" :class="isSaved(article) ? 'saved' : ''" @click.prevent.stop="toggleSave(article)" title="Archive Insight" type="button">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>


    </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const register = () => {
        Alpine.data('dashboard', () => ({
        loading: true,
        error: null,
        articles: [],
        activeCategory: 'top',
        stats: { articles: '0', total_summaries: '0', active_users: '0', total_saves: '0', pending_tasks: '0', trending_topics: [] },
        saved: JSON.parse(localStorage.getItem('cup_saved') || '[]'),
        favorited: JSON.parse(localStorage.getItem('cup_favs') || '[]'),
        categories: [
            { key: 'top',        label: 'Trending' },
            { key: 'technology', label: 'Technology' },
            { key: 'business',   label: 'Business' },
            { key: 'science',    label: 'Science' },
            { key: 'health',     label: 'Health' },
        ],

        init() {
            this.fetchNews();
            this.fetchStats();
            setInterval(() => this.fetchStats(), 30000); // Polling every 30s
        },

        async fetchNews() {
            this.loading = true;
            this.error = null;
            try {
                const res = await fetch(`/api/news?category=${this.activeCategory}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Neural network timeout.');
                const data = await res.json();
                if (data.error) throw new Error(data.error);
                this.articles = data.articles ?? [];
                this.stats.articles = this.articles.length;
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        async fetchStats() {
            try {
                const res = await fetch('/api/dashboard/stats', {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    this.stats.total_summaries = data.total_summaries;
                    this.stats.total_saves = data.total_saves;
                    this.stats.active_users = data.active_users;
                    this.stats.pending_tasks = data.pending_tasks;
                    this.stats.trending_topics = data.trending_topics;
                }
            } catch (e) {
                console.error("Stats sync failed", e);
            }
        },

        searchTopic(topic) {
            window.location.href = `/ai-summarizer?topic=${encodeURIComponent(topic)}`;
        },

        articleUrl(article) {
            const p = new URLSearchParams({
                url:         article.url,
                title:       article.title,
                source:      article.source?.name ?? '',
                image:       article.urlToImage ?? '',
                published:   article.publishedAt ?? '',
                description: article.description ?? '',
            });
            return `/news/article?${p.toString()}`;
        },

        isSaved(article) {
            return this.saved.includes(article.url);
        },

        isFav(article) {
            return this.favorited.includes(article.url);
        },

        async toggleFav(article) {
            if (this.isFav(article)) {
                this.favorited = this.favorited.filter(u => u !== article.url);
            } else {
                this.favorited.push(article.url);
            }
            localStorage.setItem('cup_favs', JSON.stringify(this.favorited));

            // Also sync to server (reuse toggleSave endpoint with type=favorite)
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) return;
                const payload = {
                    url: article.url,
                    title: article.title,
                    source: article.source?.name || 'Unknown',
                    description: article.description || '',
                    image: article.urlToImage || '',
                    type: 'favorite',
                };
                await fetch('/api/news/toggle-favorite', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });
            } catch (e) {
                // Local state already updated; server sync is best-effort
                console.warn('Favorite server sync failed:', e);
            }
        },

        async toggleSave(article) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    alert('Security token missing. Please refresh the page.');
                    return;
                }

                const payload = {
                    url: article.url,
                    title: article.title,
                    source: article.source?.name || 'Unknown',
                    description: article.description || '',
                    image: article.urlToImage || '',
                };

                const res = await fetch('/api/news/toggle-save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                
                if (res.ok) {
                    // Update local state to match server
                    if (data.saved) {
                        if (!this.isSaved(article)) {
                            this.saved.push(article.url);
                        }
                    } else {
                        this.saved = this.saved.filter(u => u !== article.url);
                    }
                    localStorage.setItem('cup_saved', JSON.stringify(this.saved));
                    this.stats.saved = this.saved.length;
                    window.toast.success(data.message || 'Article saved');
                } else {
                    window.toast.error(data.message || 'Failed to save article');
                }
            } catch (e) {
                console.error('Save failed:', e);
                window.toast.error('Error saving article. Please try again.');
            }
        },

        categoryTag(cat) {
            const map = { technology:'tag-tech', business:'tag-biz', science:'tag-sci', health:'tag-health', politics:'tag-world', top:'tag-gen' };
            return map[cat] ?? 'tag-gen';
        },

        formatDate(iso) {
            if (!iso) return '';
            try { return new Intl.DateTimeFormat('en-US', { month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' }).format(new Date(iso)); }
            catch { return iso; }
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