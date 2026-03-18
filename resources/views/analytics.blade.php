@extends('layouts.app')
@section('title', 'Analytics')

@push('styles')
<style>
/* ── Grid & Metric Cards ─────────────────────────────────────────── */
.analytics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
@media (max-width: 900px) {
    .analytics-grid { grid-template-columns: repeat(2, 1fr); }
}

.metric-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 18px;
    position: relative;
    overflow: hidden;
}
.metric-card::after {
    content: '';
    position: absolute;
    top: -20px; right: -20px;
    width: 80px; height: 80px;
    border-radius: 50%;
    opacity: 0.04;
}
.metric-card.mc-lime::after  { background: var(--lime); }
.metric-card.mc-blue::after  { background: var(--blue); }
.metric-card.mc-pink::after  { background: var(--pink); }
.metric-card.mc-amber::after { background: var(--amber); }

.metric-label {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--muted);
    margin-bottom: 8px;
}
.metric-value {
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1;
    margin-bottom: 6px;
}
.metric-change {
    font-size: 0.72rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}
.metric-change.up   { color: var(--lime); }
.metric-change.down { color: var(--red); }

.skeleton-line {
    background: linear-gradient(90deg, #1e1e1e 25%, #252525 50%, #1e1e1e 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    height: 24px;
    border-radius: 6px;
    margin-bottom: 8px;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* ── Chart Panels ────────────────────────────────────────────────── */
.charts-row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 16px;
    margin-bottom: 20px;
}
@media (max-width: 900px) {
    .charts-row { grid-template-columns: 1fr; }
}

.charts-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 20px;
}
@media (max-width: 900px) {
    .charts-row-2 { grid-template-columns: 1fr; }
}

.chart-panel {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
}
.chart-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.chart-title { font-size: 0.88rem; font-weight: 700; }
.chart-sub   { font-size: 0.7rem; color: var(--muted); margin-top: 2px; }
.chart-body  { padding: 20px; }

.chart-canvas-wrap {
    position: relative;
}
.chart-canvas-wrap canvas,
canvas {
    display: block !important;
    width: 100% !important;
    height: 100% !important;
}

/* ── Range Tabs ──────────────────────────────────────────────────── */
.range-tabs {
    display: flex;
    gap: 4px;
    background: #111;
    border-radius: 8px;
    padding: 3px;
}
.range-tab {
    padding: 4px 10px;
    border-radius: 6px;
    border: none;
    background: transparent;
    color: var(--muted);
    font-size: 0.72rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: all 0.15s;
}
.range-tab.active {
    background: var(--bg-card);
    color: var(--lime);
}

/* ── Category Breakdown ──────────────────────────────────────────── */
.breakdown-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.breakdown-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.breakdown-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.breakdown-label { font-size: 0.78rem; font-weight: 600; color: #ccc; }
.breakdown-count { font-size: 0.75rem; font-weight: 700; color: var(--muted); }
.breakdown-bar {
    height: 6px;
    background: var(--border);
    border-radius: 3px;
    overflow: hidden;
}
.breakdown-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 1s ease;
}

/* ── Activity Feed ───────────────────────────────────────────────── */
.activity-list {
    display: flex;
    flex-direction: column;
}
.activity-item {
    display: flex;
    gap: 12px;
    padding: 12px 20px;
    border-bottom: 1px solid var(--border);
    align-items: flex-start;
}
.activity-item:last-child { border-bottom: none; }
.activity-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 5px;
}
.activity-text {
    font-size: 0.8rem;
    color: #ccc;
    line-height: 1.4;
    flex: 1;
}
.activity-time {
    font-size: 0.68rem;
    color: var(--muted);
    white-space: nowrap;
}

/* ── Top Articles Table ──────────────────────────────────────────── */
.data-table {
    width: 100%;
    border-collapse: collapse;
}
.data-table th {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--muted);
    padding: 10px 20px;
    text-align: left;
    border-bottom: 1px solid var(--border);
}
.data-table td {
    padding: 12px 20px;
    border-bottom: 1px solid var(--border);
    font-size: 0.82rem;
    color: #ccc;
}
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: #1a1a1a; }

.rank-badge {
    width: 22px; height: 22px;
    border-radius: 6px;
    background: var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.68rem;
    font-weight: 800;
    color: var(--muted);
}
.rank-badge.top {
    background: var(--lime-dim);
    color: var(--lime);
}

.progress-mini {
    height: 4px;
    background: var(--border);
    border-radius: 2px;
    overflow: hidden;
    margin-top: 4px;
}
.progress-mini-fill {
    height: 100%;
    border-radius: 2px;
    background: var(--lime);
}
</style>
@endpush

@section('content')
<div x-data="analytics">

    <!-- Page Header -->
    <div class="page-eyebrow">Insights</div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
        <h1 class="page-title" style="margin-bottom:0;">Analytics</h1>
        <div class="range-tabs">
            <button class="range-tab" :class="range === '7d'  ? 'active' : ''" @click="setRange('7d')">7D</button>
            <button class="range-tab" :class="range === '30d' ? 'active' : ''" @click="setRange('30d')">30D</button>
            <button class="range-tab" :class="range === '90d' ? 'active' : ''" @click="setRange('90d')">90D</button>
        </div>
    </div>

    <!-- ── Metric Cards ─────────────────────────────────────────── -->
    <div class="analytics-grid">
        <template x-for="(m, i) in metrics" :key="i">
            <div class="metric-card" :class="'mc-' + m.color">
                <div class="metric-label" x-text="m.label"></div>
                <div x-show="!loading">
                    <div class="metric-value" :style="`color: var(--${m.color})`" x-text="m.value"></div>
                    <div class="metric-change" :class="m.changeDir">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <polyline :points="m.changeDir === 'up' ? '18 15 12 9 6 15' : '6 9 12 15 18 9'"/>
                        </svg>
                        <span x-text="m.change + ' vs last period'"></span>
                    </div>
                </div>
                <div x-show="loading" style="display:flex;flex-direction:column;gap:8px;">
                    <div class="skeleton-line" style="width:60%;"></div>
                    <div class="skeleton-line" style="width:40%;height:12px;"></div>
                </div>
            </div>
        </template>
    </div>

    <!-- ── Main Charts Row ──────────────────────────────────────── -->
    <div class="charts-row">

        <!-- Article Views -->
        <div class="chart-panel">
            <div class="chart-header">
                <div>
                    <div class="chart-title">Article Views Over Time</div>
                    <div class="chart-sub">Daily article engagement</div>
                </div>
            </div>
            <div class="chart-body">
                <div class="chart-canvas-wrap" style="height:220px;">
                    <canvas id="viewsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="chart-panel">
            <div class="chart-header">
                <div>
                    <div class="chart-title">Category Breakdown</div>
                    <div class="chart-sub">Articles by topic</div>
                </div>
            </div>
            <div class="chart-body">
                <div style="height:160px;position:relative;margin-bottom:16px;">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="breakdown-list">
                    <template x-for="cat in categories" :key="cat.name">
                        <div class="breakdown-item">
                            <div class="breakdown-row">
                                <span class="breakdown-label" x-text="cat.name"></span>
                                <span class="breakdown-count" x-text="cat.count + ' articles'"></span>
                            </div>
                            <div class="breakdown-bar">
                                <div class="breakdown-fill"
                                     :style="`width:${cat.pct}%; background:${cat.color}`">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>

    <!-- ── Second Row ───────────────────────────────────────────── -->
    <div class="charts-row-2">

        <!-- Summaries Generated -->
        <div class="chart-panel">
            <div class="chart-header">
                <div>
                    <div class="chart-title">Summaries Generated</div>
                    <div class="chart-sub">AI usage over time</div>
                </div>
            </div>
            <div class="chart-body">
                <div style="height:180px;position:relative;">
                    <canvas id="summariesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- User Activity -->
        <div class="chart-panel">
            <div class="chart-header">
                <div>
                    <div class="chart-title">User Activity</div>
                    <div class="chart-sub">Recent events</div>
                </div>
            </div>
            <div class="activity-list">
                <template x-for="(act, i) in activity" :key="i">
                    <div class="activity-item">
                        <div class="activity-dot" :style="`background:${act.color}`"></div>
                        <span class="activity-text" x-text="act.text"></span>
                        <span class="activity-time" x-text="act.time"></span>
                    </div>
                </template>
            </div>
        </div>

    </div>

    <!-- ── Top Articles Table ────────────────────────────────────── -->
    <div class="chart-panel">
        <div class="chart-header">
            <div>
                <div class="chart-title">Top Performing Articles</div>
                <div class="chart-sub">By views &amp; engagement</div>
            </div>
            <button class="btn btn-outline" style="font-size:0.75rem;padding:5px 12px;">
                Export CSV
            </button>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Article</th>
                        <th>Source</th>
                        <th>Views</th>
                        <th>Engagement</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(art, i) in topArticles" :key="i">
                        <tr>
                            <td>
                                <div class="rank-badge" :class="i < 3 ? 'top' : ''">
                                    <span x-text="i + 1"></span>
                                </div>
                            </td>
                            <td>
                                <div style="max-width:240px;font-weight:600;color:#e0e0e0;
                                            display:-webkit-box;-webkit-line-clamp:1;
                                            -webkit-box-orient:vertical;overflow:hidden;"
                                     x-text="art.title">
                                </div>
                            </td>
                            <td><span class="tag tag-gen" x-text="art.source"></span></td>
                            <td>
                                <span style="font-weight:700;color:var(--lime);"
                                      x-text="art.views.toLocaleString()">
                                </span>
                            </td>
                            <td>
                                <div style="font-size:0.78rem;font-weight:600;"
                                     x-text="art.engagement + '%'">
                                </div>
                                <div class="progress-mini">
                                    <div class="progress-mini-fill"
                                         :style="`width:${art.engagement}%`">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="tag" :class="art.tagClass" x-text="art.category"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
Chart.defaults.color        = '#666';
Chart.defaults.borderColor  = '#252525';
Chart.defaults.font.family  = 'Figtree, sans-serif';

document.addEventListener('alpine:init', () => {
    Alpine.data('analytics', () => ({
        range: '7d',
        loading: true,
        pollInterval: null,

        metrics: [],
        categories: [],
        activity: [],
        topArticles: [],
        chartData: { dailyViews: [], dailySummaries: [] },

        charts: {},

        init() {
            this.fetchData();
            this.startPolling();
        },

        async fetchData() {
            this.loading = true;
            try {
                const res = await fetch(`/api/analytics?range=${this.range}`);
                const data = await res.json();

                if (data.success) {
                    this.metrics = data.metrics || [];
                    this.categories = this.calculatePercentages(data.categories || []);
                    this.activity = data.activity || [];
                    this.topArticles = data.topArticles || [];
                    this.chartData = data.chartData || {};

                    this.$nextTick(() => {
                        this.drawCharts();
                    });
                }
            } catch (e) {
                console.error('Error fetching analytics:', e);
            } finally {
                this.loading = false;
            }
        },

        calculatePercentages(categories) {
            if (categories.length === 0) return [];
            
            const total = categories.reduce((sum, c) => sum + c.count, 0);
            return categories.map(c => ({
                ...c,
                pct: Math.round((c.count / total) * 100),
            }));
        },

        setRange(r) {
            this.range = r;
            this.fetchData();
        },

        startPolling() {
            if (this.pollInterval) clearInterval(this.pollInterval);
            this.pollInterval = setInterval(() => {
                this.fetchData();
            }, 30000); // Refresh every 30 seconds
        },

        drawCharts() {
            // Destroy existing charts
            Object.values(this.charts).forEach(c => c?.destroy());
            this.charts = {};

            // Views Chart
            const viewsCanvas = document.getElementById('viewsChart');
            if (viewsCanvas && this.chartData.dailyViews?.length > 0) {
                const summariesData = this.chartData.dailySummaries?.map(d => d.count) || [];
                
                this.charts.views = new Chart(viewsCanvas, {
                    type: 'line',
                    data: {
                        labels: this.chartData.dailyViews.map(d => d.date),
                        datasets: [
                            {
                                label: 'Views',
                                data: this.chartData.dailyViews.map(d => d.count),
                                borderColor: '#b6e040',
                                borderWidth: 2,
                                pointRadius: 0,
                                tension: 0.4,
                                fill: true,
                                backgroundColor: 'rgba(182,224,64,0.06)',
                            },
                            {
                                label: 'Summaries',
                                data: summariesData,
                                borderColor: '#60a5fa',
                                borderWidth: 2,
                                pointRadius: 0,
                                tension: 0.4,
                                fill: true,
                                backgroundColor: 'rgba(96,165,250,0.06)',
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    pointStyleWidth: 8,
                                    boxHeight: 5,
                                    font: { size: 11 },
                                },
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: '#1a1a1a',
                                borderColor: '#2e2e2e',
                                borderWidth: 1,
                            },
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 10 }, maxTicksLimit: 8 },
                            },
                            y: {
                                grid: { color: '#1e1e1e' },
                                ticks: { font: { size: 10 } },
                            },
                        },
                    },
                });
            }

            // Category Chart
            const catCanvas = document.getElementById('categoryChart');
            if (catCanvas && this.categories.length > 0) {
                this.charts.cat = new Chart(catCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: this.categories.map(c => c.name),
                        datasets: [{
                            data: this.categories.map(c => c.count),
                            backgroundColor: this.categories.map(c => c.color + '80'),
                            borderColor: this.categories.map(c => c.color),
                            borderWidth: 1.5,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1a1a1a',
                                borderColor: '#2e2e2e',
                                borderWidth: 1,
                            },
                        },
                    },
                });
            }

            // Summaries Chart
            const sumCanvas = document.getElementById('summariesChart');
            if (sumCanvas && this.chartData.dailySummaries?.length > 0) {
                this.charts.summaries = new Chart(sumCanvas, {
                    type: 'bar',
                    data: {
                        labels: this.chartData.dailySummaries.map(d => d.date),
                        datasets: [{
                            label: 'Summaries',
                            data: this.chartData.dailySummaries.map(d => d.count),
                            backgroundColor: 'rgba(96,165,250,0.3)',
                            borderColor: '#60a5fa',
                            borderWidth: 1,
                            borderRadius: 4,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1a1a1a',
                                borderColor: '#2e2e2e',
                                borderWidth: 1,
                            },
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 10 }, maxTicksLimit: 8 },
                            },
                            y: {
                                grid: { color: '#1e1e1e' },
                                ticks: { font: { size: 10 } },
                            },
                        },
                    },
                });
            }
        },
    }));
});
</script>
@endpush
@endsection