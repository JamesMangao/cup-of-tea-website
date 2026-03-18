<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Cup of Tea</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen p-8 font-inter" x-data="adminApp()">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-teal-400 to-purple-500 bg-clip-text text-transparent">Admin Dashboard ☕</h1>
            <div class="flex space-x-4">
                <button @click="toggleTheme()" class="p-2 rounded-lg hover:bg-slate-700">🌙</button>
                <button onclick="logout()" class="px-6 py-2 bg-teal-500 hover:bg-teal-600 rounded-lg transition">Logout</button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="grid grid-cols-5 gap-4 mb-8 text-center">
            <button x-on:click="currentTab = 'dashboard'" :class="currentTab === 'dashboard' ? 'bg-teal-500 text-black font-bold' : 'hover:bg-slate-700'" class="p-4 rounded-xl transition">Dashboard</button>
            <button x-on:click="currentTab = 'ai'" :class="currentTab === 'ai' ? 'bg-teal-500 text-black font-bold' : 'hover:bg-slate-700'" class="p-4 rounded-xl transition">AI</button>
            <button x-on:click="currentTab = 'analytics'" :class="currentTab === 'analytics' ? 'bg-teal-500 text-black font-bold' : 'hover:bg-slate-700'" class="p-4 rounded-xl transition">Analytics</button>
            <button x-on:click="currentTab = 'users'" :class="currentTab === 'users' ? 'bg-teal-500 text-black font-bold' : 'hover:bg-slate-700'" class="p-4 rounded-xl transition">Users</button>
            <button x-on:click="currentTab = 'news'" :class="currentTab === 'news' ? 'bg-teal-500 text-black font-bold' : 'hover:bg-slate-700'" class="p-4 rounded-xl transition">News</button>
        </div>

        <!-- Dashboard Tab -->
        <template x-if="currentTab === 'dashboard'">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-slate-800/50 backdrop-blur-md p-6 rounded-2xl border border-slate-700/50">
                    <div class="text-3xl font-bold text-teal-400" x-text="stats.totalSummaries || 0"></div>
                    <div class="text-slate-400">Total Summaries</div>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-md p-6 rounded-2xl border border-slate-700/50">
                    <div class="text-3xl font-bold text-purple-400" x-text="stats.totalUsers || 0"></div>
                    <div class="text-slate-400">Active Users</div>
                </div>
            </div>

            <!-- AI Summarizer -->
            <div class="bg-slate-800/50 backdrop-blur-md p-8 rounded-3xl border border-slate-700/50">
                <h2 class="text-2xl font-bold mb-6">AI Summarizer</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-slate-400 mb-2">Text to Summarize</label>
                        <textarea x-model="inputText" rows="4" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-4 focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-vertical" placeholder="Paste article text or URL..."></textarea>
                        <div class="text-sm text-slate-500 mt-1" x-text="'Chars: ' + inputText.length"></div>
                    </div>
                    <div class="flex space-x-4">
                        <select x-model="selectedStyle" class="flex-1 bg-slate-900 border border-slate-700 rounded-xl p-3 focus:ring-2 focus:ring-teal-500">
                            <option value="default">Professional Summary</option>
                            <option value="genz">Gen-Z Style</option>
                            <option value="executive">Executive Brief</option>
                            <option value="keytakeaways">Key Takeaways</option>
                            <option value="eli5">ELI5</option>
                        </select>
                        <button @click="generateSummary()" :disabled="!inputText.trim() || isLoading" class="px-8 py-3 bg-gradient-to-r from-teal-500 to-purple-500 hover:from-teal-600 hover:to-purple-600 rounded-xl font-bold transition disabled:opacity-50" x-text="isLoading ? 'Generating...' : 'Generate Summary'">
                        </button>
                    </div>
                    <div x-show="summaryResult" class="mt-6 p-6 bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl border border-teal-500/30">
                        <div x-html="summaryResult.replace(/\n/g, '<br>')"></div>
                        <button @click="copySummary()" class="mt-4 px-4 py-2 bg-teal-500 hover:bg-teal-600 rounded-lg text-sm transition">Copy Summary</button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Analytics Tab (Mock data) -->
        <template x-if="currentTab === 'analytics'">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-slate-800/50 p-8 rounded-3xl border border-slate-700/50">
                    <h3 class="text-xl font-bold mb-6">User Engagement (30 days)</h3>
                    <div class="space-y-4">
                        <template x-for="day in userEngagement" :key="day.date">
                            <div class="flex justify-between">
                                <span x-text="day.date"></span>
                                <span class="font-bold" x-text="day.activeUsers"></span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="bg-slate-800/50 p-8 rounded-3xl border border-slate-700/50">
                    <h3 class="text-xl font-bold mb-6">Popular Topics</h3>
                    <div class="space-y-3">
                        <template x-for="topic in popularTopics" :key="topic.topic">
                            <div class="flex justify-between">
                                <span x-text="topic.topic"></span>
                                <span class="font-bold text-teal-400" x-text="topic.count"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- Recent News -->
        <template x-if="currentTab === 'news'">
            <div class="bg-slate-800/50 p-8 rounded-3xl border border-slate-700/50">
                <h3 class="text-xl font-bold mb-6">Recent News</h3>
                <div x-data="{ newsCategory: 'top', news: [], loading: false }" class="space-y-4">
                    <div class="flex space-x-4 mb-6">
                        <button @click="loadNews('top')" :class="newsCategory === 'top' ? 'bg-teal-500' : ''" class="px-4 py-2 rounded-lg">Top</button>
                        <button @click="loadNews('technology')" :class="newsCategory === 'technology' ? 'bg-teal-500' : ''" class="px-4 py-2 rounded-lg">Tech</button>
                        <button @click="loadNews('business')" :class="newsCategory === 'business' ? 'bg-teal-500' : ''" class="px-4 py-2 rounded-lg">Business</button>
                    </div>
                    <template x-if="loading">
                        <div class="animate-pulse space-y-4">
                            <div class="h-24 bg-slate-700 rounded-xl"></div>
                            <div class="h-24 bg-slate-700 rounded-xl"></div>
                        </template>
                    </template>
                    <template x-if="news.length">
                        <template x-for="article in news" :key="article.url">
                            <div class="group bg-slate-800 p-6 rounded-2xl hover:bg-slate-700 transition border border-slate-700 cursor-pointer" @click="viewArticle(article)">
                                <div class="flex space-x-4">
                                    <div class="w-24 h-16 bg-gradient-to-br from-purple-500 to-teal-500 rounded-lg animate-pulse-slow"></div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold line-clamp-1 group-hover:text-teal-400 transition" x-text="article.title"></h4>
                                        <p class="text-sm text-slate-400 mt-1 line-clamp-2" x-text="article.description"></p>
                                        <div class="text-xs text-slate-500 mt-2" x-text="article.source?.name"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>
            </div>
        </template>

    </div>

    <script>
        function adminApp() {
            return {
                currentTab: 'dashboard',
                inputText: '',
                summaryResult: '',
                selectedStyle: 'default',
                isLoading: false,
                copyFeedback: '',
                stats: {},
                userEngagement: [],
                popularTopics: [],
                newsCategory: 'top',
                news: [],
                loading: false,
                async generateSummary() {
                    if (!this.inputText.trim()) return;
                    this.isLoading = true;
                    try {
                        const response = await fetch('/api/summarize', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ text: this.inputText, style: this.selectedStyle })
                        });
                        this.summaryResult = '';
                        const reader = response.body.getReader();
                        const decoder = new TextDecoder();
                        while (true) {
                            const { done, value } = await reader.read();
                            if (done) break;
                            const chunk = decoder.decode(value);
                            const lines = chunk.split('\n');
                            for (let line of lines) {
                                if (line.startsWith('data: ')) {
                                    const data = line.slice(6);
                                    if (data === '[DONE]') break;
                                    try {
                                        const parsed = JSON.parse(data);
                                        if (parsed.token) this.summaryResult += parsed.token;
                                    } catch (e) {}
                                }
                            }
                        }
                    } catch (e) {
                        alert('Summary failed: ' + e.message);
                    } finally {
                        this.isLoading = false;
                    }
                },
                copySummary() {
                    navigator.clipboard.writeText(this.summaryResult);
                    this.copyFeedback = 'Copied!';
                    setTimeout(() => this.copyFeedback = '', 2000);
                },
                async loadNews(category) {
                    this.newsCategory = category;
                    this.loading = true;
                    this.news = [];
                    try {
                        const response = await fetch(`/api/news?category=${category}`);
                        const data = await response.json();
                        this.news = data.articles || [];
                    } catch (e) {
                        console.error('News load failed', e);
                    } finally {
                        this.loading = false;
                    }
                },
                viewArticle(article) {
                    window.location.href = `/news/${btoa(article.url)}`;
                },
                toggleTheme() {
                    document.documentElement.classList.toggle('dark');
                },
                async loadStats() {
                    // Mock stats for demo
                    this.stats = { totalSummaries: 247, totalUsers: 42 };
                    this.userEngagement = Array.from({length: 7}, (_, i) => ({
                        date: new Date(Date.now() - i*86400000).toLocaleDateString(),
                        activeUsers: 5 + Math.random()*10
                    }));
                    this.popularTopics = [
                        { topic: 'Technology', count: 89 },
                        { topic: 'Business', count: 67 },
                        { topic: 'Politics', count: 45 },
                    ];
                }
            }
        }

        function logout() {
            if (confirm('Logout?')) window.location.href = '/logout';
        }
    </script>
</body>
</html>
