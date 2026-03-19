@extends('layouts.app')
@section('title', 'News Feed')

@push('styles')
<style>
    .feed-layout { display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start; }
    @media (max-width: 1100px) { .feed-layout { grid-template-columns: 1fr; } .feed-sidebar { display: none; } }

    .filter-bar { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .search-field { flex: 1; min-width: 200px; background: var(--bg-card); border: 1px solid var(--border2); border-radius: 9px; display: flex; align-items: center; gap: 8px; padding: 0 12px; height: 38px; }
    .search-field:focus-within { border-color: rgba(182,224,64,0.4); }
    .search-field input { background: none; border: none; outline: none; color: var(--text); font-size: 0.82rem; font-family: inherit; flex: 1; }
    .search-field input::placeholder { color: var(--muted2); }
    .filter-select { background: var(--bg-card); border: 1px solid var(--border2); color: var(--muted); border-radius: 8px; padding: 0 10px; height: 38px; font-family: inherit; font-size: 0.8rem; outline: none; cursor: pointer; }

    .cat-strip { display: flex; gap: 6px; margin-bottom: 20px; overflow-x: auto; padding-bottom: 4px; }
    .cat-chip { padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; border: 1px solid var(--border); background: transparent; color: var(--muted); cursor: pointer; white-space: nowrap; transition: all 0.15s; font-family: inherit; }
    .cat-chip:hover { border-color: var(--border2); color: #ccc; }
    .cat-chip.active { background: var(--lime); border-color: var(--lime); color: #0f0f0f; }

    .panel { background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .panel-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); gap: 12px; flex-wrap: wrap; }
    .panel-title { display: flex; align-items: center; gap: 8px; font-size: 0.95rem; font-weight: 700; }
    .panel-title::before { content:''; width:8px; height:8px; border-radius:50%; background:var(--lime); display:inline-block; animation:liveblink 2s infinite; }
    .panel-sub { font-size: 0.72rem; color: var(--muted); margin-top: 2px; }

    .news-item { display: flex; align-items: flex-start; gap: 14px; padding: 14px 20px; border-bottom: 1px solid var(--border); transition: background 0.15s; cursor: pointer; text-decoration: none; color: inherit; }
    .news-item:last-child { border-bottom: none; }
    .news-item:hover { background: #1f1f1f; }
    .news-thumb { width: 58px; height: 58px; border-radius: 9px; object-fit: cover; flex-shrink: 0; background: #222; }
    .news-thumb-ph { width: 58px; height: 58px; border-radius: 9px; flex-shrink: 0; background: #222; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    .news-source { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--lime); margin-bottom: 3px; display: flex; align-items: center; gap: 6px; }
    .news-title { font-size: 0.875rem; font-weight: 600; color: #e0e0e0; line-height: 1.45; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .news-meta { font-size: 0.7rem; color: var(--muted); display: flex; align-items: center; gap: 8px; }
    .news-actions { margin-left: auto; display: flex; gap: 6px; align-items: center; flex-shrink: 0; }
    .action-btn { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--border); background: transparent; color: var(--muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
    .action-btn:hover { border-color: var(--lime); color: var(--lime); }
    .action-btn.liked { border-color: var(--pink); color: var(--pink); background: rgba(244,114,182,0.08); }
    .action-btn.saved { border-color: var(--amber); color: var(--amber); background: rgba(251,191,36,0.08); }

    .skeleton { background: linear-gradient(90deg, #1e1e1e 25%, #252525 50%, #1e1e1e 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 6px; }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
    .skel-item { display: flex; gap: 14px; padding: 14px 20px; border-bottom: 1px solid var(--border); }

    .pagination { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 16px 20px; border-top: 1px solid var(--border); }
    .page-btn { width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-card); color: var(--muted); font-size: 0.82rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; font-family: inherit; transition: all 0.15s; }
    .page-btn:hover { border-color: var(--lime); color: var(--lime); }
    .page-btn.active { background: var(--lime); border-color: var(--lime); color: #0f0f0f; }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    .feed-sidebar { display: flex; flex-direction: column; gap: 16px; }
    .sidebar-widget { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
    .widget-header { padding: 14px 16px; border-bottom: 1px solid var(--border); font-size: 0.8rem; font-weight: 700; display: flex; align-items: center; gap: 7px; color: var(--text); }
    .widget-body { padding: 8px 16px; }
    .trend-item { display: flex; align-items: center; gap: 10px; padding: 7px 0; border-bottom: 1px solid var(--border); }
    .trend-item:last-child { border-bottom: none; }
    .trend-num { font-size: 0.65rem; font-weight: 800; color: var(--muted2); width: 16px; }
    .trend-text { font-size: 0.8rem; font-weight: 600; color: #ccc; flex: 1; }
    .trend-badge { font-size: 0.6rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; background: var(--lime-dim); color: var(--lime); }
</style>
@endpush

@section('content')
<div x-data="newsFeed">

    <div class="page-eyebrow">Explore</div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
        <h1 class="page-title" style="margin-bottom:0;">News Feed</h1>
        <span class="live-pill">Live</span>
    </div>

    <div class="filter-bar">
        <div class="search-field">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Search articles..." x-model="searchQuery" @input.debounce.400ms="fetchArticles()" />
        </div>
        <select class="filter-select" x-model="sortBy" @change="fetchArticles()">
            <option value="publishedAt">Latest First</option>
            <option value="relevancy">Relevancy</option>
            <option value="popularity">Popularity</option>
        </select>
        <button class="btn btn-lime" @click="fetchArticles()" :disabled="loading" type="button">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" :class="loading?'spin':''"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            <span x-text="loading?'Loading…':'Refresh'"></span>
        </button>
    </div>

    <div class="cat-strip">
        <template x-for="cat in categories" :key="cat.key">
            <button class="cat-chip" :class="activeCategory===cat.key?'active':''" @click="switchCategory(cat.key)" x-text="cat.label" type="button"></button>
        </template>
    </div>

    <div class="feed-layout">
        <div>
            <div class="panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-title" x-text="activeCategoryLabel()"></div>
                        <div class="panel-sub" x-text="articles.length + ' articles · full content available'"></div>
                    </div>
                </div>

                <!-- Skeleton -->
                <template x-if="loading">
                    <div>
                        <template x-for="i in 8" :key="i">
                            <div class="skel-item">
                                <div class="skeleton" style="width:58px;height:58px;border-radius:9px;flex-shrink:0;"></div>
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
                        <div class="empty-title">Could not load articles</div>
                        <div class="empty-sub" x-text="error"></div>
                        <button class="btn btn-lime" @click="fetchArticles()" type="button">Try Again</button>
                    </div>
                </template>

                <!-- Empty -->
                <template x-if="!loading && !error && articles.length === 0">
                    <div class="empty-state">
                        <div class="empty-title" style="color:var(--muted)">No articles found</div>
                        <div class="empty-sub">Try a different category or search term</div>
                    </div>
                </template>

                <!-- Articles -->
                <template x-if="!loading && !error && articles.length > 0">
                    <div>
                        <template x-for="article in articles" :key="article.url">
                            <a class="news-item animate-fade-in" :href="articleUrl(article)" style="animation-delay: 50ms;">
                                <div class="news-image-container" style="width: 140px; height: 90px; flex-shrink: 0;">
                                    <template x-if="article.urlToImage">
                                        <img class="news-thumb" :src="article.urlToImage" :alt="article.title" 
                                             style="width: 100%; height: 100%; object-fit: cover;"
                                             x-on:error="$el.src='https://placehold.co/140x90/1a1a1a/666666?text=News'" loading="lazy" />
                                    </template>
                                    <template x-if="!article.urlToImage">
                                        <div class="news-thumb-ph" style="width: 100%; height: 100%;">📰</div>
                                    </template>
                                </div>
                                <div style="flex:1;min-width:0;padding: 4px 0;">
                                    <div class="news-source">
                                        <span x-text="article.source.name"></span>
                                        <span class="tag" :class="categoryTag(activeCategory)" x-text="activeCategory" style="padding: 2px 6px; font-size: 0.6rem;"></span>
                                    </div>
                                    <div class="news-title" style="font-size: 1rem; -webkit-line-clamp: 2;" x-text="article.title"></div>
                                    <div class="news-meta">
                                        <span x-text="formatDate(article.publishedAt)"></span>
                                    </div>
                                </div>
                                <div class="news-actions" @click.prevent.stop>
                                    <button class="action-btn hover-glow" :class="isFav(article)?'liked':''" @click.prevent.stop="toggleFav(article)" title="Favorite" type="button">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                    </button>
                                    <button class="action-btn hover-glow" :class="isSaved(article)?'saved':''" @click.prevent.stop="toggleSave(article)" title="Save" type="button">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                                    </button>
                                </div>
                            </a>
                        </template>

                        <div class="pagination">
                            <button class="page-btn" @click="prevPage()" :disabled="page===1" type="button">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </button>
                            <template x-for="p in visiblePages" :key="p">
                                <button class="page-btn" :class="page===p?'active':''" @click="goToPage(p)" x-text="p" type="button"></button>
                            </template>
                            <button class="page-btn" @click="nextPage()" :disabled="page===totalPages" type="button">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </button>
                        </div>
                    </div>
                </template>

            </div>
        </div>

        <!-- Sidebar -->
        <div class="feed-sidebar">
            <div class="sidebar-widget">
                <div class="widget-header">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--lime)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    Trending Topics
                </div>
                <div class="widget-body">
                    <template x-for="(trend, i) in trends" :key="i">
                        <div class="trend-item">
                            <span class="trend-num" x-text="i+1"></span>
                            <span class="trend-text" x-text="trend.label"></span>
                            <span class="trend-badge" x-text="trend.count"></span>
                        </div>
                    </template>
                </div>
            </div>
            <div class="sidebar-widget">
                <div class="widget-header">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--lime)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Quick Stats
                </div>
                <div class="widget-body" style="padding:12px 16px;">
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;justify-content:space-between;">
                            <span style="font-size:0.78rem;color:var(--muted);">Articles Loaded</span>
                            <span style="font-size:0.85rem;font-weight:700;color:var(--lime);" x-text="articles.length"></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="font-size:0.78rem;color:var(--muted);">Favorites</span>
                            <span style="font-size:0.85rem;font-weight:700;color:var(--pink);" x-text="favorites.length"></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="font-size:0.78rem;color:var(--muted);">Saved</span>
                            <span style="font-size:0.85rem;font-weight:700;color:var(--amber);" x-text="saved.length"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Block known hard-paywalled sources; allow everything else through
const PAYWALLED_SOURCES = new Set([
    'the new york times', 'nytimes', 'the wall street journal', 'wsj',
    'the washington post', 'financial times', 'ft', 'the economist',
    'the atlantic', 'new yorker', 'wired', 'foreign policy',
    'the information', 'seeking alpha', 'barrons', "barron's",
    'hbr', 'harvard business review', 'mit sloan management review',
]);

function isFullContentSource(sourceName) {
    // Show article unless it's a known paywall
    return !PAYWALLED_SOURCES.has((sourceName || '').toLowerCase());
}

document.addEventListener('alpine:init', () => {
    Alpine.data('newsFeed', () => ({
        loading: true, error: null, articles: [],
        activeCategory: 'top', searchQuery: '', sortBy: 'publishedAt',
        page: 1, pageSize: 10, totalPages: 1,
        favorites: JSON.parse(localStorage.getItem('cup_favs') || '[]'),
        saved:     JSON.parse(localStorage.getItem('cup_saved') || '[]'),

        categories: [
            { key: 'top',           label: '🔥 Top' },
            { key: 'technology',    label: '💻 Tech' },
            { key: 'business',      label: '📈 Business' },
            { key: 'science',       label: '🔬 Science' },
            { key: 'health',        label: '🏥 Health' },
            { key: 'politics',      label: '🌍 Politics' },
            { key: 'sports',        label: '⚽ Sports' },
            { key: 'entertainment', label: '🎬 Entertainment' },
        ],

        trends: [
            { label: 'Artificial Intelligence', count: '142' },
            { label: 'Climate Change',          count: '98'  },
            { label: 'Global Markets',          count: '87'  },
            { label: 'Space Exploration',       count: '65'  },
            { label: 'Healthcare Reform',       count: '54'  },
        ],

        get visiblePages() {
            const delta = 2;
            const range = [];
            for (let i = Math.max(1, this.page - delta); i <= Math.min(this.totalPages, this.page + delta); i++) {
                range.push(i);
            }
            return range;
        },

        init() { this.fetchArticles(); },

        async fetchArticles() {
            this.loading = true;
            this.error   = null;
            try {
                const params = new URLSearchParams({
                    category: this.activeCategory,
                    sortBy:   this.sortBy,
                    page:     this.page,
                });
                if (this.searchQuery) params.append('q', this.searchQuery);

                const res  = await fetch(`/api/news?${params}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Failed to connect to server.');
                const data = await res.json();
                if (data.error) throw new Error(data.error);

                const all = data.articles ?? [];
                this.articles = all.filter(a => isFullContentSource(a.source?.name));

                this.totalPages = Math.max(1, Math.ceil(
                    (data.totalResults ?? this.articles.length) / this.pageSize
                ));
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        switchCategory(cat) { this.activeCategory = cat; this.page = 1; this.fetchArticles(); },
        prevPage()           { if (this.page > 1)              { this.page--; this.fetchArticles(); } },
        nextPage()           { if (this.page < this.totalPages) { this.page++; this.fetchArticles(); } },
        goToPage(p)          { this.page = p; this.fetchArticles(); },

        activeCategoryLabel() {
            const cat = this.categories.find(c => c.key === this.activeCategory);
            return cat ? cat.label + ' News' : 'Latest News';
        },

        articleUrl(article) {
            const p = new URLSearchParams({
                url:         article.url,
                title:       article.title,
                source:      article.source.name,
                image:       article.urlToImage   ?? '',
                published:   article.publishedAt  ?? '',
                description: article.description  ?? '',
            });
            return `/news/article?${p.toString()}`;
        },

        isFav(article)    { return this.favorites.includes(article.url); },
        async toggleFav(article) {
            if (this.isFav(article))  this.favorites = this.favorites.filter(u => u !== article.url);
            else                      this.favorites.push(article.url);
            localStorage.setItem('cup_favs', JSON.stringify(this.favorites));

            // Sync to server
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) return;
                const payload = {
                    url: article.url,
                    title: article.title,
                    source: article.source?.name || 'Unknown',
                    description: article.description || '',
                    image: article.urlToImage || '',
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
                console.warn('Favorite sync failed:', e);
            }
        },

        isSaved(article)   { return this.saved.includes(article.url); },
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
                
                if (res.ok && data.success) {
                    // Update local state to match server
                    if (data.saved) {
                        // Article was saved
                        if (!this.isSaved(article)) {
                            this.saved.push(article.url);
                        }
                    } else {
                        // Article was removed
                        this.saved = this.saved.filter(u => u !== article.url);
                    }
                    localStorage.setItem('cup_saved', JSON.stringify(this.saved));
                    
                    // Show confirmation
                    const message = data.saved ? 'Added to library!' : 'Removed from library';
                    console.log(message);
                } else {
                    alert(data.message || 'Failed to save article');
                    console.error('Save error:', data);
                }
            } catch (e) {
                console.error('Save failed:', e);
                alert('Error saving article. Please try again.');
            }
        },

        categoryTag(cat) {
            const map = {
                technology:    'tag-tech',
                business:      'tag-biz',
                science:       'tag-sci',
                health:        'tag-health',
                politics:      'tag-world',
                top:           'tag-gen',
                sports:        'tag-gen',
                entertainment: 'tag-gen',
            };
            return map[cat] ?? 'tag-gen';
        },

        formatDate(iso) {
            if (!iso) return '';
            try {
                return new Intl.DateTimeFormat('en-US', {
                    month: 'short', day: 'numeric',
                    hour: '2-digit', minute: '2-digit',
                }).format(new Date(iso));
            } catch { return iso; }
        },
    }));
});
</script>
@endpush
@endsection