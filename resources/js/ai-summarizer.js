export default function aiSummarizer() {
    return {
        mode: 'text',
        inputText: '',
        inputUrl: '',
        inputTopic: '',
        summaryStyle: 'concise',
        selectedModel: 'llama3-8b-8192',
        isLoading: false,
        showResult: false,
        error: null,
        resultText: '',
        copied: false,
        charCount: 0,
        resultStats: {},
        history: JSON.parse(localStorage.getItem('cup_summaries') || '[]'),

        init() {
            // Initialization logic can go here if needed
        },

        canSubmit() {
            if (this.mode === 'text') return this.inputText.trim().length > 50;
            if (this.mode === 'url') return this.inputUrl.trim().length > 8;
            if (this.mode === 'topic') return this.inputTopic.trim().length > 3;
            return false;
        },

        updateCharCount() {
            this.charCount = this.inputText.length;
        },

        async summarize() {
            this.isLoading = true;
            this.error = null;
            this.resultText = '';
            this.showResult = true;
            this.resultStats = {};

            let requestBody = {
                style: this.summaryStyle,
                model: this.selectedModel,
            };

            if (this.mode === 'text') {
                requestBody.content = this.inputText;
            } else if (this.mode === 'url') {
                requestBody.url = this.inputUrl;
            } else if (this.mode === 'topic') {
                requestBody.topic = this.inputTopic;
            }

            try {
                const res = await fetch('/api/summarize', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(requestBody)
                });

                if (!res.ok) {
                    const errData = await res.json();
                    throw new Error(errData.error || `HTTP error! status: ${res.status}`);
                }

                const data = await res.json();
                this.resultText = data.summary;
                this.updateStats(data.summary);
                this.addToHistory(data.summary);

            } catch (e) {
                this.error = e.message || 'An unexpected error occurred.';
            } finally {
                this.isLoading = false;
            }
        },

        formatResult(text) {
            return text.replace(/\n/g, '<br>');
        },

        updateStats(summary) {
            const originalWords = this.inputText.split(/\s+/).filter(Boolean).length;
            const summaryWords = summary.split(/\s+/).filter(Boolean).length;
            this.resultStats = {
                words: summaryWords,
                sentences: (summary.match(/[.!?]+/g) || []).length,
                readTime: Math.ceil(summaryWords / 3.5),
                compression: originalWords > 0 ? Math.round((1 - (summaryWords / originalWords)) * 100) : 0,
            };
        },

        copyResult() {
            navigator.clipboard.writeText(this.resultText);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },

        saveToLibrary() {
            // This functionality can be implemented later
            alert('Save to Library feature coming soon!');
        },

        addToHistory(summary) {
            const newItem = {
                preview: summary.substring(0, 100) + '...',
                style: this.summaryStyle,
                time: new Date().toLocaleTimeString(),
                fullSummary: summary,
                originalText: this.inputText,
            };
            this.history.unshift(newItem);
            if (this.history.length > 10) {
                this.history.pop();
            }
            localStorage.setItem('cup_summaries', JSON.stringify(this.history));
        },

        loadHistory(item) {
            this.resultText = item.fullSummary;
            this.inputText = item.originalText;
            this.summaryStyle = item.style;
            this.showResult = true;
            this.updateStats(item.fullSummary);
        }
    }
}
