@props([
    'section',
    'layout' => 'default',
    'options' => [],
    'params' => [],
    'title' => 'Layout Preview',
    'buttonLabel' => 'Preview',
    'buttonClass' => 'cms-action-btn cms-action-btn-sm cms-action-btn--preview',
])

@php
    $previewId = 'lpv-'.\Illuminate\Support\Str::random(10);
    $safeLayout = preg_replace('/[^a-z0-9\-]/', '', (string) $layout) ?: 'default';
    $previewOptions = count($options) ? $options : ['default' => 'Default'];

    $buildUrl = fn (string $layoutValue): string => route('cms.layout-preview', array_merge(
        ['section' => $section, 'layout' => $layoutValue],
        $params,
    ));

    $initialUrl = $buildUrl($safeLayout);
@endphp

<button
    type="button"
    class="{{ $buttonClass }}"
    title="{{ $buttonLabel }}"
    onclick="cmsPreviewOpen('{{ $previewId }}', '{{ $initialUrl }}', '{{ $safeLayout }}')"
>
    {{ $slot->isEmpty() ? $buttonLabel : $slot }}
</button>

<div
    id="{{ $previewId }}"
    class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-950/82 backdrop-blur-sm px-3 py-4 sm:px-5 sm:py-6"
    data-preview-modal-root
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $previewId }}-title"
    aria-hidden="true"
    tabindex="-1"
    onclick="cmsPreviewClose('{{ $previewId }}')"
>
    <div class="w-full max-w-6xl">
        <div
            class="cms-card cms-gradient-card relative mx-auto flex h-[96vh] max-h-[96vh] w-full flex-col overflow-hidden rounded-[1.5rem] border border-slate-200/80 shadow-[0_30px_100px_rgba(0,0,0,0.42)] ring-1 ring-slate-900/10 dark:border-white/10 dark:ring-white/10"
            onclick="event.stopPropagation()"
        >
            <!-- Header -->
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-white/10 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 id="{{ $previewId }}-title" class="text-lg font-bold text-slate-900 dark:text-white">{{ $title }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-300">Switch layouts below to compare how this section renders.</p>
                    </div>
                </div>
                <button
                    type="button"
                    onclick="cmsPreviewClose('{{ $previewId }}')"
                    data-preview-close
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-2xl leading-none text-slate-400 transition hover:bg-slate-100 hover:text-rose-500 dark:hover:bg-white/10"
                    aria-label="Close modal"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Preview Frame -->
            <div class="relative flex-1 overflow-hidden border-y border-slate-200 bg-slate-100 min-h-[50vh] dark:border-white/10 dark:bg-slate-900">
                <div data-preview-loader class="absolute inset-0 z-10 flex items-center justify-center bg-white/88 backdrop-blur-sm dark:bg-slate-950/88">
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-8 w-8 animate-spin rounded-full border-4 border-slate-200 border-t-sky-500 dark:border-slate-700 dark:border-t-sky-400"></div>
                        <span class="text-sm text-slate-500 dark:text-slate-300">Loading preview...</span>
                    </div>
                </div>
                <iframe id="{{ $previewId }}-frame" class="relative block h-[50vh] min-h-[50vh] w-full bg-white dark:bg-slate-950" src="about:blank" title="{{ $title }}"></iframe>
            </div>

            <!-- Layout Options -->
            <div class="px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-300">Select Layout</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($previewOptions as $layoutValue => $layoutLabel)
                            <button
                                type="button"
                                data-layout-button="{{ $previewId }}"
                                data-layout-value="{{ $layoutValue }}"
                                data-layout-url="{{ $buildUrl($layoutValue) }}"
                                class="layout-btn rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition-all hover:-translate-y-0.5 hover:border-sky-300 hover:bg-sky-50 hover:text-sky-700 dark:border-white/10 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-sky-400/50 dark:hover:bg-sky-500/10 dark:hover:text-sky-300"
                                onclick="cmsPreviewSetLayout('{{ $previewId }}', '{{ $layoutValue }}')"
                            >
                                {{ $layoutLabel }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .layout-btn.active {
        @apply border-sky-500 bg-sky-500 text-white shadow-lg shadow-sky-500/25;
    }

    html.dark .layout-btn.active {
        @apply border-sky-400 bg-sky-400/20 text-sky-100 shadow-none;
    }
</style>

@once
<script>
(() => {
    const previewState = {
        openId: null,
        previousActiveElement: null,
    };

    const hoistPreviewModals = () => {
        document.querySelectorAll('[data-preview-modal-root]').forEach((modal) => {
            if (modal.parentElement === document.body) {
                return;
            }

            document.body.appendChild(modal);
        });
    };

    const showLoader = (id) => {
        const modal = document.getElementById(id);
        const loader = modal?.querySelector('[data-preview-loader]');
        if (loader) {
            loader.classList.remove('hidden');
        }
    };

    const hideLoader = (id) => {
        const modal = document.getElementById(id);
        const loader = modal?.querySelector('[data-preview-loader]');
        if (loader) {
            loader.classList.add('hidden');
        }
    };

    const setActiveButtons = (id, value) => {
        document.querySelectorAll('[data-layout-button="' + id + '"]').forEach((button) => {
            const isActive = button.dataset.layoutValue === value;
            button.classList.toggle('active', isActive);
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hoistPreviewModals, { once: true });
    } else {
        hoistPreviewModals();
    }

    window.cmsPreviewOpen = (id, url, layout) => {
        const modal = document.getElementById(id);
        const frame = document.getElementById(id + '-frame');
        if (!modal || !frame) return;

        if (previewState.openId && previewState.openId !== id) {
            window.cmsPreviewClose(previewState.openId);
        }

        previewState.previousActiveElement = document.activeElement && document.activeElement.nodeType === 1
            ? document.activeElement
            : null;
        previewState.openId = id;

        modal.setAttribute('aria-hidden', 'false');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        showLoader(id);
        frame.src = url;
        setActiveButtons(id, layout || 'default');
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(() => {
            const closeButton = modal.querySelector('[data-preview-close]');
            if (closeButton && closeButton.nodeType === 1 && typeof closeButton.focus === 'function') {
                closeButton.focus();
            } else {
                modal.focus();
            }
        });
    };

    window.cmsPreviewClose = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;

        modal.setAttribute('aria-hidden', 'true');
    modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = '';

        if (previewState.openId === id) {
            previewState.openId = null;

            if (previewState.previousActiveElement && typeof previewState.previousActiveElement.focus === 'function') {
                previewState.previousActiveElement.focus();
            }

            previewState.previousActiveElement = null;
        }
    };

    window.cmsPreviewSetLayout = (id, value) => {
        const frame = document.getElementById(id + '-frame');
        if (!frame) return;

        const btn = document.querySelector('[data-layout-button="' + id + '"][data-layout-value="' + value + '"]');
        if (!btn) return;

        showLoader(id);
        frame.src = btn.dataset.layoutUrl;
        setActiveButtons(id, value);
    };

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;

        document.querySelectorAll('[id^="lpv-"]:not(.hidden)').forEach((modal) => {
            window.cmsPreviewClose(modal.id);
        });
    });

    document.addEventListener('load', (event) => {
        const frame = event.target;
        if (!frame || frame.tagName !== 'IFRAME' || !frame.id.endsWith('-frame')) return;

        hideLoader(frame.id.replace(/-frame$/, ''));
    }, true);
})();
</script>
@endonce
