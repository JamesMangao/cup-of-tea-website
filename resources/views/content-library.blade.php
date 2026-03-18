@extends('layouts.app')
@section('title', 'Content Library')

@push('styles')
<style>
    .library-toolbar { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .lib-search { flex: 1; min-width: 200px; background: var(--bg-card); border: 1px solid var(--border2); border-radius: 9px; display: flex; align-items: center; gap: 8px; padding: 0 12px; height: 38px; }
    .lib-search:focus-within { border-color: rgba(182,224,64,0.4); }
    .lib-search input { background: none; border: none; outline: none; color: var(--text); font-size: 0.82rem; font-family: inherit; flex: 1; }
    .lib-search input::placeholder { color: var(--muted2); }

    .type-tabs { display: flex; gap: 6px; margin-bottom: 20px; flex-wrap: wrap; }
    .type-tab { padding: 6px 14px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; border: 1px solid var(--border); background: transparent; color: var(--muted); cursor: pointer; font-family: inherit; transition: all 0.15s; display: flex; align-items: center; gap: 6px; }
    .type-tab:hover { border-color: var(--border2); color: #ccc; }
    .type-tab.active { background: var(--lime); border-color: var(--lime); color: #0f0f0f; }
    .type-tab .count { background: rgba(0,0,0,0.2); padding: 1px 6px; border-radius: 10px; font-size: 0.65rem; }
    .type-tab.active .count { background: rgba(0,0,0,0.25); }

    .library-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    @media (max-width: 1100px) { .library-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .library-grid { grid-template-columns: 1fr; } }

    .lib-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; transition: all 0.2s; display: flex; flex-direction: column; }
    .lib-card:hover { border-color: var(--border2); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.35); }
    .lib-card.deleting { opacity: 0.5; pointer-events: none; }
    .lib-card-thumb { width: 100%; height: 110px; object-fit: cover; background: linear-gradient(135deg, #1a1a1a, #222); display: block; flex-shrink: 0; }
    .lib-card-thumb-ph { width: 100%; height: 110px; background: linear-gradient(135deg, #1a1a1a, #222); display: flex; align-items: center; justify-content: center; font-size: 2.2rem; flex-shrink: 0; }
    .lib-card-body { padding: 14px; flex: 1; display: flex; flex-direction: column; }
    .lib-card-type { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
    .lib-card-title { font-size: 0.875rem; font-weight: 700; color: #e0e0e0; line-height: 1.4; margin-bottom: 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; flex: 1; }
    .lib-card-desc { font-size: 0.78rem; color: var(--muted); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 10px; }
    .lib-card-footer { display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 10px; border-top: 1px solid var(--border); }
    .lib-card-meta { font-size: 0.68rem; color: var(--muted2); }
    .lib-card-actions { display: flex; gap: 6px; }
    .lib-action { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--border); background: transparent; color: var(--muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; text-decoration: none; padding: 0; }
    .lib-action:hover { border-color: var(--lime); color: var(--lime); }
    .lib-action.danger:hover { border-color: var(--red); color: var(--red); }

    /* Empty state */
    .lib-empty { text-align: center; padding: 80px 20px; }
    .lib-empty-icon { font-size: 3rem; margin-bottom: 16px; }
    .lib-empty-title { font-size: 1rem; font-weight: 700; color: var(--muted); margin-bottom: 6px; }
    .lib-empty-sub { font-size: 0.82rem; color: var(--muted2); margin-bottom: 20px; }

    /* Modal */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .modal-box { background: var(--bg-card); border: 1px solid var(--border2); border-radius: 16px; width: 100%; max-width: 640px; max-height: 80vh; display: flex; flex-direction: column; }
    .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
    .modal-title { font-size: 1rem; font-weight: 700; }
    .modal-close { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: transparent; color: var(--muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
    .modal-close:hover { border-color: var(--red); color: var(--red); }
    .modal-body { padding: 24px; overflow-y: auto; flex: 1; }
    .modal-text { font-size: 0.88rem; line-height: 1.75; color: #d8d8d8; white-space: pre-wrap; }

    /* Stats bar */
    .stats-bar { display: flex; gap: 20px; margin-bottom: 24px; padding: 16px 20px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; flex-wrap: wrap; }
    .stats-bar-item { display: flex; flex-direction: column; gap: 2px; }
    .stats-bar-val { font-size: 1.4rem; font-weight: 800; color: var(--text); letter-spacing: -0.02em; }
    .stats-bar-lbl { font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); }
</style>
@endpush

@section('content')
<div x-data="contentLibrary">
    <!-- Header -->
    <div class="page-eyebrow">Collection</div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px;">
        <h1 class="page-title" style="margin-bottom:0;">Content Library</h1>
        <button class="btn btn-outline" @click="clearAll()" style="font-size:0.78rem;flex-shrink:0;" x-show="items.length > 0" type="button">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            Clear All
        </button>
    </div>

    <!-- Empty State -->
    <template x-if="items.length === 0">
        <div class="lib-empty">
            <div class="lib-empty-icon">📚</div>
            <div class="lib-empty-title">Your library is empty</div>
            <div class="lib-empty-sub">Save articles from News Feed to see them here.</div>
            <a href="{{ route('news.feed') }}" class="btn btn-lime" style="display:inline-block;">Browse News</a>
        </div>
    </template>

    <!-- Content (only show if items exist) -->
    <template x-if="items.length > 0">
        <div>
            <!-- Stats Bar -->
            <div class="stats-bar">
                <div class="stats-bar-item">
                    <span class="stats-bar-val" x-text="items.length"></span>
                    <span class="stats-bar-lbl">Total Items</span>
                </div>
                <div class="stats-bar-item">
                    <span class="stats-bar-val" style="color:var(--blue);" x-text="countByType('article')"></span>
                    <span class="stats-bar-lbl">Articles</span>
                </div>
                <div class="stats-bar-item">
                    <span class="stats-bar-val" style="color:var(--lime);" x-text="countByType('summary')"></span>
                    <span class="stats-bar-lbl">Summaries</span>
                </div>
                <div class="stats-bar-item">
                    <span class="stats-bar-val" style="color:var(--pink);" x-text="countByType('favorite')"></span>
                    <span class="stats-bar-lbl">Favorites</span>
                </div>
                <div class="stats-bar-item">
                    <span class="stats-bar-val" style="color:var(--amber);" x-text="countByType('bookmark')"></span>
                    <span class="stats-bar-lbl">Bookmarks</span>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="library-toolbar">
                <div class="lib-search">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" placeholder="Search saved content..." x-model="searchQuery" />
                </div>
                <select style="background:var(--bg-card);border:1px solid var(--border2);color:var(--muted);border-radius:8px;padding:0 10px;height:38px;font-family:inherit;font-size:0.8rem;outline:none;" x-model="sortBy">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="az">A–Z</option>
                </select>
            </div>

            <!-- Type Tabs -->
            <div class="type-tabs">
                <button class="type-tab" :class="activeType==='all'?'active':''" @click="activeType='all'" type="button">
                    All <span class="count" x-text="items.length"></span>
                </button>
                <button class="type-tab" :class="activeType==='article'?'active':''" @click="activeType='article'" type="button">
                    📰 Articles <span class="count" x-text="countByType('article')"></span>
                </button>
                <button class="type-tab" :class="activeType==='summary'?'active':''" @click="activeType='summary'" type="button">
                    ✨ Summaries <span class="count" x-text="countByType('summary')"></span>
                </button>
                <button class="type-tab" :class="activeType==='favorite'?'active':''" @click="activeType='favorite'" type="button">
                    ❤️ Favorites <span class="count" x-text="countByType('favorite')"></span>
                </button>
                <button class="type-tab" :class="activeType==='bookmark'?'active':''" @click="activeType='bookmark'" type="button">
                    🔖 Bookmarks <span class="count" x-text="countByType('bookmark')"></span>
                </button>
            </div>

            <!-- Grid or Empty Filter State -->
            <template x-if="filteredItems.length === 0">
                <div class="lib-empty" style="padding:60px 20px;">
                    <div class="lib-empty-icon">🔍</div>
                    <div class="lib-empty-title">No items found</div>
                    <div class="lib-empty-sub">Try adjusting your filters or search query.</div>
                </div>
            </template>

            <div class="library-grid" x-show="filteredItems.length > 0">
                <template x-for="item in filteredItems" :key="`item-${item.id}`">
                    <div class="lib-card" :class="{ 'deleting': item._deleting }">
                        <template x-if="item.image">
                            <img class="lib-card-thumb" :src="item.image" :alt="item.title" loading="lazy" />
                        </template>
                        <template x-if="!item.image">
                            <div class="lib-card-thumb-ph" x-text="typeEmoji(item.type)"></div>
                        </template>
                        <div class="lib-card-body">
                            <div class="lib-card-type">
                                <span class="tag" :class="typeTagClass(item.type)" x-text="item.type"></span>
                                <template x-if="item.source">
                                    <span style="font-size:0.65rem;color:var(--muted);font-weight:600;" x-text="item.source"></span>
                                </template>
                            </div>
                            <div class="lib-card-title" x-text="item.title"></div>
                            <div class="lib-card-desc" x-text="item.description"></div>
                            <div class="lib-card-footer">
                                <span class="lib-card-meta" x-text="formatDate(item.created_at)"></span>
                                <div class="lib-card-actions">
                                    <button class="lib-action" @click="viewItem(item)" title="View" type="button">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <template x-if="item.url">
                                        <a :href="item.url" target="_blank" class="lib-action" title="Open original">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                        </a>
                                    </template>
                                    <button class="lib-action danger" @click="removeItem(item.id)" title="Remove" type="button" :disabled="item._deleting">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <!-- View Modal -->
    <template x-if="selectedItem">
        <div class="modal-overlay" @click.self="selectedItem = null">
            <div class="modal-box">
                <div class="modal-header">
                    <div>
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                            <span class="tag" :class="typeTagClass(selectedItem.type)" x-text="selectedItem.type"></span>
                            <template x-if="selectedItem.source">
                                <span style="font-size:0.72rem;color:var(--muted);" x-text="selectedItem.source"></span>
                            </template>
                        </div>
                        <div class="modal-title" x-text="selectedItem.title"></div>
                    </div>
                    <button class="modal-close" @click="selectedItem = null" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-text" x-text="selectedItem.description || 'No content available'"></div>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('contentLibrary', () => ({
        items: @json($items ?? []),
        activeType: 'all',
        searchQuery: '',
        sortBy: 'newest',
        selectedItem: null,

        init() {
            // Data loaded from server
        },

        get filteredItems() {
            let list = this.items;
            
            if (this.activeType !== 'all') {
                list = list.filter(i => i.type === this.activeType);
            }
            
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(i => 
                    (i.title || '').toLowerCase().includes(q) || 
                    (i.description || '').toLowerCase().includes(q) || 
                    (i.source || '').toLowerCase().includes(q)
                );
            }
            
            if (this.sortBy === 'oldest') {
                list = [...list].sort((a,b) => new Date(a.created_at) - new Date(b.created_at));
            } else if (this.sortBy === 'newest') {
                list = [...list].sort((a,b) => new Date(b.created_at) - new Date(a.created_at));
            } else if (this.sortBy === 'az') {
                list = [...list].sort((a,b) => (a.title||'').localeCompare(b.title||''));
            }
            
            return list;
        },

        countByType(type) { 
            return this.items.filter(i => i.type === type).length; 
        },

        viewItem(item) { 
            this.selectedItem = JSON.parse(JSON.stringify(item));
        },

        async removeItem(id) {
            if (!confirm('Remove from library?')) return;
            
            const item = this.items.find(i => i.id === id);
            if (!item) return;
            
            item._deleting = true;
            
            try {
                const response = await fetch(`/content-library/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    this.items = this.items.filter(i => i.id !== id);
                    this.selectedItem = null;
                } else {
                    alert('Error removing item');
                    item._deleting = false;
                }
            } catch (error) {
                console.error(error);
                alert('Error removing item');
                item._deleting = false;
            }
        },

        async clearAll() {
            if (!confirm('Clear all saved content? This cannot be undone.')) return;
            
            try {
                const response = await fetch('{{ route("content-library.clear-all") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    this.items = [];
                }
            } catch (error) {
                console.error(error);
            }
        },

        typeEmoji(type) { 
            return { article:'📰', summary:'✨', favorite:'❤️', bookmark:'🔖' }[type] ?? '📄'; 
        },

        typeTagClass(type) { 
            return { article:'tag-tech', summary:'tag-biz', favorite:'tag-health', bookmark:'tag-world' }[type] ?? 'tag-gen'; 
        },

        formatDate(iso) {
            if (!iso) return '';
            try {
                const d = new Date(iso);
                const now = new Date();
                const diff = (now - d) / 1000;
                if (diff < 60) return 'Just now';
                if (diff < 3600) return Math.floor(diff/60) + 'm ago';
                if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
                return Math.floor(diff/86400) + 'd ago';
            } catch { 
                return iso; 
            }
        },
    }));
});
</script>
@endpush
@endsection