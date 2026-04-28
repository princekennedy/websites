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
    $supportedTargetKeys = ['content_id', 'category_id', 'slider_id', 'menu_item_id', 'menu_id'];
    $persistTargetKey = collect($supportedTargetKeys)->first(fn (string $key): bool => filled($params[$key] ?? null));
    $persistTargetPermission = match ($persistTargetKey) {
        'content_id' => 'cms.manage.contents',
        'category_id' => 'cms.manage.categories',
        'slider_id' => 'cms.manage.sliders',
        'menu_item_id', 'menu_id' => 'cms.manage.menus',
        default => null,
    };
    $persistTargetLabel = match ($persistTargetKey) {
        'content_id' => 'content entry',
        'category_id' => 'category',
        'slider_id' => 'slide',
        'menu_item_id' => 'menu item',
        'menu_id' => 'menu',
        default => 'component',
    };
    $canSetDefault = $persistTargetKey !== null
        && ($persistTargetPermission === null || auth()->user()?->hasCmsPermission($persistTargetPermission));
    $currentLayoutLabel = $previewOptions[$safeLayout] ?? ucfirst($safeLayout);

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
    class="fixed inset-0 z-[99999] hidden items-start justify-center overflow-y-auto bg-slate-950/82 px-3 py-4 backdrop-blur-sm sm:items-center sm:px-5 sm:py-6"
    data-preview-modal-root
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $previewId }}-title"
    aria-hidden="true"
    tabindex="-1"
    onclick="cmsPreviewClose('{{ $previewId }}')"
>
    <div class="w-full max-w-6xl py-1 sm:py-3">
        <div
            class="cms-card cms-gradient-card relative mx-auto flex h-[min(96vh,58rem)] max-h-[calc(100vh-2rem)] w-full flex-col overflow-hidden rounded-[1.5rem] border border-slate-200/80 shadow-[0_30px_100px_rgba(0,0,0,0.42)] ring-1 ring-slate-900/10 dark:border-white/10 dark:ring-white/10 sm:max-h-[calc(100vh-3rem)]"
            onclick="event.stopPropagation()"
        >
            <!-- Header -->
            <div class="shrink-0 border-b border-slate-200 px-5 py-4 dark:border-white/10 sm:px-6">
                <div class="flex items-center justify-between gap-4">
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
            </div>

            <!-- Preview Frame -->
            <div class="relative min-h-0 flex-1 overflow-hidden border-y border-slate-200 bg-slate-100 dark:border-white/10 dark:bg-slate-900">
                <div data-preview-loader class="absolute inset-0 z-10 flex items-center justify-center bg-white/88 backdrop-blur-sm dark:bg-slate-950/88">
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-8 w-8 animate-spin rounded-full border-4 border-slate-200 border-t-sky-500 dark:border-slate-700 dark:border-t-sky-400"></div>
                        <span class="text-sm text-slate-500 dark:text-slate-300">Loading preview...</span>
                    </div>
                </div>
                <iframe id="{{ $previewId }}-frame" class="relative block h-full min-h-[55vh] w-full bg-white dark:bg-slate-950" src="about:blank" title="{{ $title }}"></iframe>
            </div>

            <!-- Layout Options -->
            <div class="shrink-0 overflow-y-auto px-5 py-4 sm:px-6">
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

            @if ($canSetDefault)
                <div class="shrink-0 border-t border-slate-200 px-5 py-4 dark:border-white/10 sm:px-6">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Set Previewed Layout as Default</p>
                            <p class="text-sm text-slate-500 dark:text-slate-300">Save the selected preview as the default layout for this {{ $persistTargetLabel }}.</p>
                        </div>

                        <form method="POST" action="{{ route('cms.layout-preview.set-default') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            @csrf
                            <input type="hidden" name="section" value="{{ $section }}">
                            @foreach ($supportedTargetKeys as $targetKey)
                                @if (filled($params[$targetKey] ?? null))
                                    <input type="hidden" name="{{ $targetKey }}" value="{{ $params[$targetKey] }}">
                                @endif
                            @endforeach
                            <input type="hidden" name="layout" value="{{ $safeLayout }}" data-preview-default-input="{{ $previewId }}">

                            <p data-preview-default-state="{{ $previewId }}" class="text-sm text-slate-500 dark:text-slate-300">
                                Current default: {{ $currentLayoutLabel }}
                            </p>

                            <button
                                type="submit"
                                data-preview-default-button="{{ $previewId }}"
                                data-current-layout="{{ $safeLayout }}"
                                class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/40 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 disabled:cursor-default disabled:opacity-50 disabled:hover:translate-y-0 dark:shadow-none"
                                disabled
                            >
                                Current default
                            </button>
                        </form>
                    </div>
                </div>
            @endif
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

    const previewUrlWithTheme = (url) => {
        const themedUrl = new window.URL(url, window.location.origin);
        themedUrl.searchParams.set('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');

        return themedUrl.toString();
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

    const syncDefaultAction = (id, value) => {
        const input = document.querySelector('[data-preview-default-input="' + id + '"]');
        const button = document.querySelector('[data-preview-default-button="' + id + '"]');
        const state = document.querySelector('[data-preview-default-state="' + id + '"]');

        if (input) {
            input.value = value;
        }

        if (!button && !state) {
            return;
        }

        const selectedButton = document.querySelector('[data-layout-button="' + id + '"][data-layout-value="' + value + '"]');
        const selectedLabel = selectedButton ? selectedButton.textContent.trim() : value;
        const currentLayout = button?.dataset.currentLayout ?? value;
        const isCurrentDefault = currentLayout === value;

        if (state) {
            state.textContent = isCurrentDefault
                ? 'Current default: ' + selectedLabel
                : 'Selected for default: ' + selectedLabel;
        }

        if (button) {
            button.disabled = isCurrentDefault;
            button.textContent = isCurrentDefault ? 'Current default' : 'Set as default';
        }
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
        frame.src = previewUrlWithTheme(url);
        setActiveButtons(id, layout || 'default');
        syncDefaultAction(id, layout || 'default');
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
        frame.src = previewUrlWithTheme(btn.dataset.layoutUrl);
        setActiveButtons(id, value);
        syncDefaultAction(id, value);
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
