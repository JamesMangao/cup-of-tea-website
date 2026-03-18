@extends('layouts.app')
@section('title', 'Article')

@push('styles')
<style>
    /* Root Variables */
    :root {
        --primary: #b6e040;
        --text-primary: #ffffff;
        --text-secondary: #b8b8b8;
        --text-tertiary: #888888;
        --bg-base: #0f0f0f;
        --bg-card: #171717;
        --bg-hover: #1f1f1f;
        --border-light: #242424;
        --border-dark: #1a1a1a;
    }

    body {
        background: var(--bg-base);
    }

    /* Main Container */
    .article-container {
        max-width: 1440px;
        margin: 0 auto;
        padding: 0 24px;
    }

    /* Back Navigation */
    .nav-section {
        padding: 20px 0;
        border-bottom: 1px solid var(--border-light);
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-tertiary);
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 6px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .back-link:hover {
        color: var(--primary);
        background: rgba(182, 224, 64, 0.08);
    }

    /* Article Meta Section */
    .article-meta {
        padding: 40px 0 32px 0;
    }

    .meta-badge {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: var(--primary);
        padding: 6px 12px;
        border-radius: 5px;
        background: linear-gradient(135deg, rgba(182, 224, 64, 0.15) 0%, rgba(182, 224, 64, 0.05) 100%);
        border: 1px solid rgba(182, 224, 64, 0.25);
        margin-bottom: 16px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .meta-date {
        font-size: 0.8rem;
        color: var(--text-tertiary);
        font-weight: 500;
        margin-left: 12px;
    }

    /* Article Title */
    .article-title {
        font-size: clamp(1.8rem, 5vw, 2.8rem);
        font-weight: 900;
        line-height: 1.15;
        letter-spacing: -0.035em;
        color: var(--text-primary);
        margin: 20px 0 24px 0;
        word-break: break-word;
    }

    /* Article Description */
    .article-description {
        font-size: 1.15rem;
        line-height: 1.7;
        color: var(--text-secondary);
        margin-bottom: 32px;
        font-weight: 400;
        border-left: 4px solid var(--primary);
        padding-left: 20px;
    }

    /* Hero Image */
    .hero-section {
        margin-bottom: 40px;
    }

    .hero-image {
        width: 100%;
        max-height: 620px;
        height: auto;
        object-fit: cover;
        border-radius: 14px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        display: block;
        background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
    }

    /* Info Bar */
    .info-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        background: linear-gradient(90deg, rgba(182, 224, 64, 0.06), transparent);
        border: 1px solid var(--border-light);
        border-radius: 10px;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .info-group {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: var(--text-tertiary);
        font-weight: 600;
    }

    .info-item svg {
        width: 15px;
        height: 15px;
        color: var(--primary);
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .source-btn,
    .save-btn,
    .regen-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border: 1px solid var(--border-light);
        border-radius: 7px;
        color: var(--text-tertiary);
        text-decoration: none;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent;
        cursor: pointer;
        font-family: inherit;
    }

    .source-btn:hover,
    .regen-btn:hover {
        color: var(--primary);
        border-color: var(--primary);
        background: rgba(182, 224, 64, 0.08);
    }

    .save-btn {
        background: rgba(182, 224, 64, 0.15);
        border-color: var(--primary);
        color: var(--primary);
    }

    .save-btn:hover {
        background: rgba(182, 224, 64, 0.25);
    }

    .save-btn.saved {
        background: var(--primary);
        color: #0f0f0f;
    }

    .save-btn svg,
    .regen-btn svg,
    .source-btn svg {
        width: 13px;
        height: 13px;
    }

    /* Main Grid Layout */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 36px;
        align-items: start;
    }

    @media (max-width: 1120px) {
        .content-grid {
            grid-template-columns: 1fr;
            gap: 28px;
        }
    }

    .summary-column {
        position: sticky;
        top: 24px;
    }

    /* Article Content Card */
    .content-card {
        background: var(--bg-card);
        border: 1px solid var(--border-light);
        border-radius: 12px;
        overflow: hidden;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 28px;
        border-bottom: 1px solid var(--border-light);
        background: linear-gradient(90deg, rgba(182, 224, 64, 0.05), transparent);
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text-primary);
    }

    .header-title svg {
        width: 16px;
        height: 16px;
        color: var(--primary);
    }

    .header-controls {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .word-badge {
        font-size: 0.7rem;
        color: var(--text-tertiary);
        background: rgba(182, 224, 64, 0.1);
        padding: 5px 11px;
        border-radius: 5px;
        font-weight: 700;
        letter-spacing: 0.03em;
    }

    .expand-btn {
        background: none;
        border: none;
        color: var(--primary);
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        padding: 4px 8px;
        transition: all 0.2s;
    }

    .expand-btn:hover {
        opacity: 0.75;
    }

    /* Loading State */
    .loading-skeleton {
        padding: 36px 28px;
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .skeleton-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .skeleton-line {
        height: 15px;
        background: linear-gradient(90deg, #1f1f1f 0%, #242424 50%, #1f1f1f 100%);
        background-size: 200% 100%;
        animation: shimmer-animation 2s infinite;
        border-radius: 3px;
    }

    @keyframes shimmer-animation {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Content Body - ENHANCED PARAGRAPH SPACING */
    .article-body {
        padding: 48px 52px;
        max-height: 720px;
        overflow-y: auto;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .article-body.expanded {
        max-height: none;
    }

    /* PARAGRAPH STYLES WITH VISUAL SEPARATION */
    .article-paragraph {
        font-size: 1rem;
        color: var(--text-secondary);
        line-height: 1.92;
        letter-spacing: 0.25px;
        margin-bottom: 2.4rem;
        font-weight: 400;
        padding-bottom: 1.6rem;
        border-bottom: 1px solid rgba(182, 224, 64, 0.09);
    }

    .article-paragraph:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    /* First letter styling for first paragraph */
    .article-paragraph:first-child::first-letter {
        font-size: 1.35em;
        font-weight: 900;
        color: var(--primary);
        margin-right: 2px;
    }

    /* Fade Effect */
    .fade-container {
        position: relative;
    }

    .fade-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 160px;
        background: linear-gradient(to top, var(--bg-card), transparent);
        pointer-events: none;
        transition: opacity 0.3s;
    }

    .fade-overlay.hidden {
        display: none;
    }

    /* Custom Scrollbar */
    .article-body::-webkit-scrollbar {
        width: 7px;
    }

    .article-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .article-body::-webkit-scrollbar-thumb {
        background: rgba(182, 224, 64, 0.25);
        border-radius: 4px;
    }

    .article-body::-webkit-scrollbar-thumb:hover {
        background: rgba(182, 224, 64, 0.45);
    }

    /* Error State */
    .error-container {
        padding: 24px 28px;
    }

    .error-box {
        display: flex;
        gap: 14px;
        padding: 16px;
        background: rgba(224, 85, 85, 0.08);
        border: 1px solid rgba(224, 85, 85, 0.25);
        border-radius: 9px;
    }

    .error-box svg {
        width: 18px;
        height: 18px;
        color: #e05555;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .error-text {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    /* Summary Card */
    .summary-card {
        background: var(--bg-card);
        border: 1px solid var(--border-light);
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .summary-card .card-header {
        border-bottom: 1px solid var(--border-light);
    }

    /* Style Selector */
    .style-selector {
        display: flex;
        gap: 8px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--border-light);
        flex-wrap: wrap;
    }

    .style-option {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid var(--border-light);
        background: transparent;
        color: var(--text-tertiary);
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }

    .style-option:hover {
        border-color: var(--border-light);
        color: var(--text-secondary);
    }

    .style-option.active {
        background: rgba(182, 224, 64, 0.15);
        border-color: var(--primary);
        color: var(--primary);
    }

    /* Summary Body */
    .summary-body {
        padding: 32px;
        overflow-y: auto;
        flex: 1;
        max-height: 600px;
    }

    .summary-paragraph {
        font-size: 0.95rem;
        color: var(--text-secondary);
        line-height: 1.85;
        margin-bottom: 1.4rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(182, 224, 64, 0.08);
        font-weight: 400;
    }

    .summary-paragraph:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .summary-bullet {
        font-size: 0.95rem;
        color: var(--text-secondary);
        line-height: 1.8;
        margin-bottom: 1.2rem;
        padding-left: 1.6rem;
        position: relative;
    }

    .summary-bullet::before {
        content: '•';
        position: absolute;
        left: 0.2rem;
        color: var(--primary);
        font-weight: 900;
        font-size: 1.1em;
    }

    /* Summary CTA */
    .summary-cta {
        padding: 40px 32px;
        text-align: center;
        border-bottom: 1px solid var(--border-light);
    }

    .cta-text {
        font-size: 0.85rem;
        color: var(--text-tertiary);
        margin-bottom: 20px;
        font-weight: 500;
    }

    /* Summary Loading */
    .summary-loading {
        padding: 48px 32px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 18px;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid rgba(182, 224, 64, 0.2);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin-animation 0.9s linear infinite;
    }

    @keyframes spin-animation {
        to { transform: rotate(360deg); }
    }

    .loading-label {
        font-size: 0.85rem;
        color: var(--text-tertiary);
        font-weight: 500;
    }

    /* Summary Actions */
    .summary-actions {
        display: flex;
        gap: 8px;
        padding: 14px 20px;
        border-top: 1px solid var(--border-light);
        background: rgba(182, 224, 64, 0.02);
        flex-wrap: wrap;
    }

    .action-button {
        flex: 1;
        min-width: 80px;
        padding: 10px 12px;
        border-radius: 6px;
        border: 1px solid var(--border-light);
        background: transparent;
        color: var(--text-tertiary);
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
        font-family: inherit;
    }

    .action-button:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(182, 224, 64, 0.08);
    }
    .article-body { 
        max-width: 800px; 
        margin: 0 auto; 
        font-family: 'Figtree', sans-serif; 
        color: var(--text); 
        line-height: 1.8; 
        font-size: 1.1rem; 
    }
    .article-paragraph { 
        margin-bottom: 2rem; 
        opacity: 0.9;
    }
    .hero-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 20px;
        margin-bottom: 32px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        border: 1px solid var(--border-light);
    }
</style>
@endpush

@section('content')
<div x-data="articlePage" class="article-container">

    <!-- Navigation -->
    <nav class="nav-section">
        <a href="javascript:history.back()" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Feed
        </a>
    </nav>

    <!-- Article Meta & Header -->
    <header class="article-meta">
        <div>
            <span class="meta-badge" x-text="meta.source"></span>
            <span class="meta-date" x-text="formatDate(meta.published)"></span>
        </div>
        <h1 class="article-title" x-text="meta.title"></h1>
        <p class="article-description" x-show="meta.description" x-text="meta.description"></p>
    </header>

    <!-- Hero Image Section -->
    <section class="hero-section" style="margin-top: 24px;">
        <template x-if="meta.image">
            <img class="hero-image shadow-2xl" :src="meta.image" :alt="meta.title" 
                 style="width: 100%; max-height: 500px; object-fit: cover; border-radius: 16px; border: 1px solid var(--border-light);"
                 x-on:error="$el.src='/images/news-fallback.jpg'" />
        </template>
        <template x-if="!meta.image">
            <div class="h-[300px] w-full bg-gradient-to-br from-bg-card to-bg-base border border-border-light rounded-2xl flex items-center justify-center">
                <span class="text-4xl">📰</span>
            </div>
        </template>
    </section>

    <!-- Info Bar with Actions -->
    <div class="info-bar">
        <div class="info-group">
            <span class="info-item" x-show="readingTime">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span x-text="readingTime + ' min read'"></span>
            </span>
            <span class="info-item" x-show="wordCount">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <span x-text="wordCount + ' words'"></span>
            </span>
        </div>
        <div class="action-group">
            <button class="save-btn" @click="saveToLibrary()" :class="{ 'saved': isSaved }" :title="isSaved ? 'Saved to library' : 'Save to library'">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <span x-text="isSaved ? 'Saved' : 'Save'"></span>
            </button>
            <a :href="meta.url" target="_blank" rel="noopener" class="source-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                Read Original
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">

        <!-- Article Content Panel -->
        <article class="content-card">
            <!-- Card Header -->
            <div class="card-header">
                <div class="header-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Article Content
                </div>
                <div class="header-controls">
                    <span class="word-badge" x-show="!contentLoading && wordCount" x-text="wordCount + ' words'"></span>
                    <button class="expand-btn" x-show="!contentLoading && fullContent" x-on:click="expanded = !expanded" :title="expanded ? 'Collapse' : 'Expand'">
                        <span x-text="expanded ? '↑ Collapse' : '↓ Read all'"></span>
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <template x-if="contentLoading">
                <div class="loading-skeleton">
                    <template x-for="i in 4" :key="i">
                        <div class="skeleton-group">
                            <template x-for="j in [100, 95, 90, 70]" :key="j">
                                <div class="skeleton-line" :style="'width: ' + j + '%'"></div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Error State -->
            <template x-if="!contentLoading && contentError">
                <div class="error-container" style="margin-top: 24px; text-align: center; background: rgba(224, 85, 85, 0.05); border: 1px solid rgba(224, 85, 85, 0.2); border-radius: 16px; padding: 40px 24px;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--red)" stroke-width="2" style="margin: 0 auto 16px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div class="error-text font-bold text-white mb-2" style="font-size: 1.1rem;" x-text="contentError || 'Content Blocked'"></div>
                    <p class="text-xs text-muted mb-6" style="max-width: 400px; margin-left: auto; margin-right: auto; line-height: 1.6;">Some publishers block automated reading to protect their content. You can try again or read the story directly at the source.</p>
                    <div style="display: flex; gap: 12px; justify-content: center;">
                        <button class="btn btn-outline btn-sm" x-on:click="fetchFullContent()">Retry Extraction</button>
                        <a :href="meta.url" target="_blank" class="btn btn-lime btn-sm">Read at Source</a>
                    </div>
                </div>
            </template>

            <!-- Article Content -->
            <template x-if="!contentLoading && fullContent">
                <div class="fade-container">
                    <div class="article-body" :class="expanded ? 'expanded' : ''">
                        <template x-for="(para, i) in paragraphs" :key="i">
                            <p class="article-paragraph" x-text="para"></p>
                        </template>
                    </div>
                    <div class="fade-overlay" :class="expanded ? 'hidden' : ''"></div>
                </div>
            </template>
        </article>

        <!-- AI Summary Panel -->
        <aside class="summary-column">
            <div class="summary-card">
                <!-- Header -->
                <div class="card-header">
                    <div class="header-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        AI Summary
                        <span class="ai-label">GROQ</span>
                    </div>
                </div>

                <!-- Style Selector -->
                <div class="style-selector">
                    <button class="style-option" :class="summaryStyle === 'professional' ? 'active' : ''" x-on:click="summaryStyle = 'professional'">Professional</button>
                    <button class="style-option" :class="summaryStyle === 'executive' ? 'active' : ''" x-on:click="summaryStyle = 'executive'">Brief</button>
                    <button class="style-option" :class="summaryStyle === 'genz' ? 'active' : ''" x-on:click="summaryStyle = 'genz'">Casual</button>
                </div>

                <!-- CTA State -->
                <template x-if="!summaryLoading && !summary && !summaryError">
                    <div class="summary-cta">
                        <p class="cta-text">Generate an AI summary in your preferred style</p>
                        <button class="btn btn-lime" x-on:click="generateSummary()" :disabled="contentLoading" style="width: 100%;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            Generate Summary
                        </button>
                    </div>
                </template>

                <!-- Loading State -->
                <template x-if="summaryLoading">
                    <div class="summary-loading">
                        <div class="loading-spinner"></div>
                        <div class="loading-label">Generating summary…</div>
                    </div>
                </template>

                <!-- Error State -->
                <template x-if="!summaryLoading && summaryError">
                    <div style="padding: 20px;">
                        <div class="error-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            <div class="error-text" x-text="summaryError"></div>
                        </div>
                        <button class="btn btn-outline" x-on:click="generateSummary()" style="margin-top: 16px; width: 100%;">Try Again</button>
                    </div>
                </template>

                <!-- Summary Content -->
                <template x-if="!summaryLoading && summary">
                    <div class="summary-body" x-html="formattedSummary"></div>
                    <div class="summary-actions" style="display: flex; gap: 12px; padding: 16px; background: rgba(0,0,0,0.2); border-top: 1px solid var(--border-light);">
                        <button class="btn btn-outline btn-sm flex-1 font-bold" @click="regenerateSummary()" :disabled="summaryLoading">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                            Regenerate
                        </button>
                        <button class="btn btn-outline btn-sm flex-1 font-bold" @click="copySummary()" :disabled="summaryLoading">
                            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                </template>
            </div>
        </aside>

    </div>

</div>
@endsection

@push('scripts')
<script>
(function() {
    const register = () => {
        Alpine.data('articlePage', () => ({
        meta: { url: '', title: '', source: '', image: '', published: '', description: '' },
        contentLoading: true,
        contentError: null,
        fullContent: '',
        expanded: false,
        summaryStyle: 'professional',
        summaryLoading: false,
        summaryError: null,
        summary: '',
        copied: false,
        isSaved: false,
        readingTime: 0,

        get wordCount() {
            return this.fullContent ? this.fullContent.split(/\s+/).filter(Boolean).length : 0;
        },

        get paragraphs() {
            if (!this.fullContent) return [];
            return this.fullContent
                .split(/\n\n+/)
                .map(p => p.trim())
                .filter(p => p.length > 0);
        },

        get formattedSummary() {
            if (!this.summary) return '';
            const lines = this.summary.split('\n').filter(l => l.trim());
            return lines.map(line => {
                const clean = line.trim();
                if (/^[•\-\*]\s+/.test(clean)) {
                    return '<p class="summary-bullet">' + clean.replace(/^[•\-\*]\s+/, '') + '</p>';
                }
                return '<p class="summary-paragraph">' + clean + '</p>';
            }).join('');
        },

        init() {
            const p = new URLSearchParams(window.location.search);
            this.meta.url = p.get('url') ?? '';
            this.meta.title = p.get('title') ?? 'Article';
            this.meta.source = p.get('source') ?? '';
            this.meta.image = p.get('image') ?? '';
            this.meta.published = p.get('published') ?? '';
            this.meta.description = p.get('description') ?? '';
            this.checkIfSaved();
            if (this.meta.url && this.meta.url !== 'null') {
                this.fetchFullContent();
            } else {
                this.contentLoading = false;
                this.contentError = 'No article URL provided.';
            }
        },

        async fetchFullContent() {
            this.contentLoading = true;
            this.contentError = null;
            try {
                const params = new URLSearchParams({ url: this.meta.url, title: this.meta.title });
                const res = await fetch('/api/news/full?' + params.toString());
                if (!res.ok) throw new Error('Failed to fetch content.');
                const data = await res.json();
                if (data.error && !data.fullContent) throw new Error(data.error);
                this.fullContent = data.fullContent ?? '';
                this.readingTime = data.readingTime ?? 0;
                if (data.title && data.title.length > 5) this.meta.title = data.title;
                if (data.image && !this.meta.image) this.meta.image = data.image;
            } catch(e) {
                this.contentError = e.message;
            } finally {
                this.contentLoading = false;
            }
        },

        async generateSummary() {
            this.summaryLoading = true;
            this.summaryError = null;
            this.summary = '';
            const content = this.fullContent || this.meta.description || this.meta.title;
            const stylePrompts = {
                professional: 'Write a professional 3-paragraph summary of this article suitable for business audiences.',
                executive: 'Create an executive summary with exactly 5 bullet points. Keep each to one concise sentence.',
                genz: 'Summarize this casually in Gen-Z style - make it relatable and interesting. 3-4 short paragraphs.',
            };
            try {
                const res = await fetch('/api/summarize', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        content: content,
                        title: this.meta.title,
                        style: this.summaryStyle,
                        prompt: stylePrompts[this.summaryStyle],
                        sourceUrl: this.meta.url,
                    }),
                });
                if (!res.ok) throw new Error('Service unavailable.');
                const data = await res.json();
                if (data.error) throw new Error(data.error);
                this.summary = data.summary ?? data.content ?? data.text ?? '';
                if (!this.summary) throw new Error('No summary returned.');
            } catch(e) {
                this.summaryError = e.message;
            } finally {
                this.summaryLoading = false;
            }
        },

        regenerateSummary() {
            this.summary = '';
            this.summaryError = null;
            this.generateSummary();
        },

        async copySummary() {
            try {
                await navigator.clipboard.writeText(this.summary);
                this.copied = true;
                setTimeout(() => this.copied = false, 2500);
            } catch(e) {}
        },

        saveToLibrary() {
            try {
                const library = JSON.parse(localStorage.getItem('cup_library') || '[]');
                const exists = library.some(item => item.url === this.meta.url && item.type === 'article');
                
                if (!exists) {
                    library.unshift({
                        type: 'article',
                        title: this.meta.title,
                        source: this.meta.source,
                        description: this.meta.description,
                        url: this.meta.url,
                        image: this.meta.image,
                        date: new Date().toISOString(),
                    });
                    localStorage.setItem('cup_library', JSON.stringify(library));
                }
                
                this.isSaved = true;
            } catch(e) {
                console.error('Error saving to library:', e);
            }
        },

        checkIfSaved() {
            try {
                const library = JSON.parse(localStorage.getItem('cup_library') || '[]');
                this.isSaved = library.some(item => item.url === this.meta.url && item.type === 'article');
            } catch(e) {}
        },

        formatDate(iso) {
            if (!iso) return '';
            try {
                return new Intl.DateTimeFormat('en-US', {
                    year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                }).format(new Date(iso));
            } catch(e) { return iso; }
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