@props([
    'name',
    'value',
    'options',
    'section',
    'label' => 'Layout',
])

@php
    use Illuminate\Support\Str;
    $pickerId   = 'lp-' . Str::random(10);
    $selectId   = $name;
    $currentLabel = $options[(string) $value] ?? ucfirst((string) $value);

    // Split "Default - Dark gradient hero..." into short name + description
    $labelParts = fn (string $lbl): array => array_map('trim', explode(' - ', $lbl, 2)) + ['', ''];
@endphp

{{-- ─── Visible display + trigger button ──────────────────────────────────── --}}
<div class="mt-2 space-y-3" data-picker-id="{{ $pickerId }}" data-select-id="{{ $selectId }}">
    {{-- Hidden native select (submitted with the form) --}}
    <select id="{{ $selectId }}" name="{{ $name }}" class="sr-only" aria-hidden="true" tabindex="-1">
        @foreach ($options as $optVal => $optLabel)
            <option value="{{ $optVal }}" @selected((string) $optVal === (string) $value)>{{ $optLabel }}</option>
        @endforeach
    </select>

    <div class="overflow-hidden rounded-[1.35rem] border border-slate-200 bg-white shadow-sm shadow-slate-200/50 ring-1 ring-slate-950/5 transition dark:border-white/10 dark:bg-slate-950/35 dark:shadow-none dark:ring-white/10">
        <button
            type="button"
            class="group flex w-full items-center gap-4 p-4 text-left transition hover:bg-slate-50/80 focus:outline-none focus:ring-2 focus:ring-sky-300/50 dark:hover:bg-white/[0.04] sm:p-5"
            onclick="lpOpen('{{ $pickerId }}')"
            aria-haspopup="dialog"
            aria-controls="{{ $pickerId }}-modal"
        >
            <span class="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-sky-100 text-sky-700 transition group-hover:bg-sky-200 dark:bg-sky-500/10 dark:text-sky-300 dark:group-hover:bg-sky-500/15">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z"/>
                </svg>
            </span>

            <span class="min-w-0 flex-1">
                <span class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-stone-400">Current {{ $label }}</span>
                    <span class="lp-display-value inline-flex items-center rounded-full border border-slate-200 bg-slate-100 px-2.5 py-1 text-[11px] font-medium uppercase tracking-[0.16em] text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-stone-400">{{ $value }}</span>
                </span>
                <span class="lp-display-label mt-2 block truncate text-base font-semibold text-slate-900 dark:text-white sm:text-lg">{{ $currentLabel }}</span>
                <span class="mt-1 block text-sm text-slate-500 dark:text-stone-400">Open the layout gallery to compare live previews before saving this section.</span>
            </span>

            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition group-hover:border-sky-200 group-hover:bg-sky-50 group-hover:text-sky-700 dark:border-white/10 dark:bg-white/5 dark:text-stone-200 dark:group-hover:border-sky-400/30 dark:group-hover:bg-sky-500/10 dark:group-hover:text-sky-300">
                Browse Layouts
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                </svg>
            </span>
        </button>
    </div>
    <p class="lp-selected-hint hidden rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300"></p>
</div>

{{-- ─── Modal ───────────────────────────────────────────────────────────────── --}}
<div
    id="{{ $pickerId }}-modal"
    class="fixed inset-0 z-[99999] hidden items-start justify-center overflow-y-auto bg-slate-950/82 px-3 py-4 backdrop-blur-sm sm:items-center sm:px-5 sm:py-6"
    data-picker-modal-root
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $pickerId }}-title"
    aria-hidden="true"
    tabindex="-1"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-slate-950/82"
        onclick="lpClose('{{ $pickerId }}')"
        aria-hidden="true"
    ></div>

    <div class="w-full max-w-6xl py-1 sm:py-3">
        <div class="cms-card cms-gradient-card relative mx-auto flex h-[min(96vh,58rem)] max-h-[calc(100vh-2rem)] w-full flex-col overflow-hidden rounded-[1.5rem] border border-slate-200/80 shadow-[0_30px_100px_rgba(0,0,0,0.42)] ring-1 ring-slate-900/10 dark:border-white/10 dark:ring-white/10 sm:max-h-[calc(100vh-3rem)]">

            {{-- Modal header --}}
            <div class="shrink-0 border-b border-slate-200 p-4 dark:border-white/10 sm:px-6">
                <div class="flex items-start justify-between gap-4">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.32em] text-sky-500/80 dark:text-sky-300/80">Live Preview Gallery</p>
                    <h2 id="{{ $pickerId }}-title" class="mt-2 text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">
                        Choose a {{ $label }} Layout
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-300">
                        Click <strong class="text-slate-900 dark:text-white">Select</strong> on a design to apply it.
                        Previews use real seeded data from this website.
                    </p>
                </div>
                <button
                    type="button"
                    data-lp-close
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-2xl leading-none text-slate-400 transition hover:bg-slate-100 hover:text-rose-500 dark:hover:bg-white/10"
                    onclick="lpClose('{{ $pickerId }}')"
                    aria-label="Close layout picker"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
            </div>

 

            {{-- Layout cards grid --}}
            <div class="min-h-0 flex-1 overflow-y-auto px-5 pb-6 pt-4 sm:px-6 sm:pb-6 sm:pt-5">
            <div class="grid gap-5 2xl:grid-cols-2">
                @foreach ($options as $layoutValue => $layoutLabel)
                    @php [$shortName, $shortDesc] = $labelParts($layoutLabel); @endphp

                    <div
                        class="lp-card group overflow-hidden rounded-[28px] border bg-white shadow-[0_20px_45px_rgba(15,23,42,0.12)] ring-1 ring-slate-950/5 transition duration-200 dark:bg-slate-950/85 dark:ring-white/5 {{ (string) $layoutValue === (string) $value ? 'border-sky-400/90' : 'border-slate-200 hover:border-sky-300 dark:border-white/10 dark:hover:border-sky-400/50' }}"
                        id="{{ $pickerId }}-card-{{ $layoutValue }}"
                        data-layout="{{ $layoutValue }}"
                    >
                        {{-- Scaled iframe preview --}}
                        <div
                            class="lp-preview-wrapper relative overflow-hidden border-b border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-slate-950"
                            style="height: clamp(220px, 34vh, 360px); min-height: 220px;"
                            title="Layout preview"
                        >
                            <div class="pointer-events-none absolute inset-x-0 top-0 z-[1] h-16 bg-gradient-to-b from-white/75 via-white/15 to-transparent dark:from-slate-950/60 dark:via-slate-950/10"></div>
                            <iframe
                                data-src="{{ route('cms.layout-preview', ['section' => $section, 'layout' => $layoutValue]) }}"
                                style="position: absolute; top: 0; left: 50%; width: 1200px; height: 700px; transform-origin: top center; border: none; pointer-events: none;"
                                title="{{ $shortName }} layout preview"
                            ></iframe>

                            {{-- Spinner shown until iframe loads --}}
                            <div class="lp-spinner absolute inset-0 flex items-center justify-center bg-white/88 backdrop-blur-sm dark:bg-slate-950/88">
                                <svg class="h-8 w-8 animate-spin text-slate-400 dark:text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- Card footer --}}
                        <div class="flex flex-col gap-4 bg-gradient-to-b from-white to-slate-50 p-5 dark:from-slate-900 dark:to-slate-950 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $shortName }}</p>
                                    @if ((string) $layoutValue === (string) $value)
                                        <span class="shrink-0 rounded-full bg-sky-500/15 px-2 py-0.5 text-xs font-semibold text-sky-700 dark:bg-sky-500/20 dark:text-sky-400">Current</span>
                                    @endif
                                </div>
                                @if ($shortDesc)
                                    <p class="mt-0.5 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $shortDesc }}</p>
                                @endif
                            </div>
                            <button
                                type="button"
                                class="inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-full bg-gradient-to-r from-sky-500 via-cyan-500 to-teal-400 px-5 py-2 text-sm font-semibold text-slate-950 transition hover:-translate-y-0.5 hover:from-sky-400 hover:via-cyan-400 hover:to-teal-300 sm:w-auto {{ (string) $layoutValue === (string) $value ? 'cursor-default opacity-50' : '' }}"
                                onclick="lpSelect('{{ $pickerId }}', '{{ $layoutValue }}', {{ json_encode($layoutLabel) }})"
                                {{ (string) $layoutValue === (string) $value ? 'disabled' : '' }}
                            >
                                {{ (string) $layoutValue === (string) $value ? 'Selected' : 'Select' }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Bottom spacer so last cards aren't flush with viewport bottom --}}
            <div class="h-8"></div>
            </div>
        </div>
    </div>
</div>

{{-- ─── JavaScript (output once per page, shared by all picker instances) ──── --}}
@once
<script>
(function () {
    const PREVIEW_WIDTH = 1200;
    const PREVIEW_HEIGHT = 700;
    const PREVIEW_MIN_HEIGHT = 220;
    const PREVIEW_MAX_HEIGHT = 360;
    const pickerState = {
        openId: null,
        previousActiveElement: null,
    };

    function hoistPickerModals() {
        document.querySelectorAll('[data-picker-modal-root]').forEach(function (modal) {
            if (modal.parentElement === document.body) return;
            document.body.appendChild(modal);
        });
    }

    function previewUrlWithTheme(url) {
        const themedUrl = new window.URL(url, window.location.origin);
        themedUrl.searchParams.set('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');

        return themedUrl.toString();
    }

    // Scale each iframe inside a .lp-preview-wrapper to fit its container.
    function scalePreview(wrapper) {
        const iframe = wrapper.querySelector('iframe');
        if (!iframe) return;

        const targetHeight = Math.max(Math.min(Math.round(window.innerHeight * 0.34), PREVIEW_MAX_HEIGHT), PREVIEW_MIN_HEIGHT);
        const targetWidth = wrapper.clientWidth;
        const widthScale = targetWidth / PREVIEW_WIDTH;
        const heightScale = targetHeight / PREVIEW_HEIGHT;
        const scale = Math.min(widthScale, heightScale);

        iframe.style.transform = 'translateX(-50%) scale(' + scale + ')';
        wrapper.style.height = targetHeight + 'px';
    }

    // Load the iframes inside a modal (lazy — only when modal opens).
    function loadPreviews(pickerId) {
        const modal = document.getElementById(pickerId + '-modal');
        if (!modal) return;

        const activeTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';

        modal.querySelectorAll('.lp-preview-wrapper').forEach(function (wrapper) {
            scalePreview(wrapper);

            const iframe = wrapper.querySelector('iframe[data-src]');
            if (!iframe) return;

            if (iframe.dataset.loaded === '1' && iframe.dataset.theme === activeTheme) return;

            iframe.src = previewUrlWithTheme(iframe.dataset.src);
            iframe.dataset.loaded = '1';
            iframe.dataset.theme = activeTheme;

            iframe.addEventListener('load', function () {
                const spinner = wrapper.querySelector('.lp-spinner');
                if (spinner) spinner.remove();
            }, { once: true });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hoistPickerModals, { once: true });
    } else {
        hoistPickerModals();
    }

    window.lpOpen = function (pickerId) {
        const modal = document.getElementById(pickerId + '-modal');
        if (!modal) return;

        if (pickerState.openId && pickerState.openId !== pickerId) {
            window.lpClose(pickerState.openId);
        }

        pickerState.previousActiveElement = document.activeElement && document.activeElement.nodeType === 1
            ? document.activeElement
            : null;
        pickerState.openId = pickerId;

        modal.setAttribute('aria-hidden', 'false');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        // Use rAF so the layout is painted before we measure widths for scaling.
        requestAnimationFrame(function () {
            loadPreviews(pickerId);
            const closeButton = modal.querySelector('[data-lp-close]');
            if (closeButton && typeof closeButton.focus === 'function') {
                closeButton.focus();
            } else {
                modal.focus();
            }
        });
    };

    window.lpClose = function (pickerId) {
        const modal = document.getElementById(pickerId + '-modal');
        if (!modal) return;
        modal.setAttribute('aria-hidden', 'true');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = '';

        if (pickerState.openId === pickerId) {
            pickerState.openId = null;
            if (pickerState.previousActiveElement && typeof pickerState.previousActiveElement.focus === 'function') {
                pickerState.previousActiveElement.focus();
            }
            pickerState.previousActiveElement = null;
        }
    };

    window.lpSelect = function (pickerId, value, label) {
        // Find the wrapper for this picker.
        const wrapper = document.querySelector('[data-picker-id="' + pickerId + '"]');
        if (!wrapper) return;

        const selectId  = wrapper.dataset.selectId;
        const select    = document.getElementById(selectId);
        const labelEl   = wrapper.querySelector('.lp-display-label');
        const valueEl   = wrapper.querySelector('.lp-display-value');
        const hintEl    = wrapper.querySelector('.lp-selected-hint');

        if (select)  select.value          = value;
        if (labelEl) labelEl.textContent   = label;
        if (valueEl) valueEl.textContent   = value;
        if (hintEl) {
            hintEl.textContent = 'Selected: ' + label + '. Click Save to update the database.';
            hintEl.classList.remove('hidden');
        }

        // Update card borders + button states inside this modal.
        const modal = document.getElementById(pickerId + '-modal');
        if (modal) {
            modal.querySelectorAll('.lp-card').forEach(function (card) {
                const isSelected = card.dataset.layout === value;
                card.classList.toggle('border-sky-400/90', isSelected);
                card.classList.toggle('border-slate-700/80', !isSelected);

                const btn = card.querySelector('button[onclick*="lpSelect"]');
                if (!btn) return;
                btn.disabled    = isSelected;
                btn.textContent = isSelected ? 'Selected' : 'Select';
                btn.classList.toggle('opacity-50',     isSelected);
                btn.classList.toggle('cursor-default', isSelected);

                // Update / remove "Current" badge.
                const nameEl  = card.querySelector('.font-semibold');
                const existing = card.querySelector('.lp-current-badge');
                if (isSelected && !existing && nameEl) {
                    const badge = document.createElement('span');
                    badge.className = 'lp-current-badge shrink-0 rounded-full bg-sky-500/20 px-2 py-0.5 text-xs font-semibold text-sky-400';
                    badge.textContent = 'Current';
                    nameEl.insertAdjacentElement('afterend', badge);
                } else if (!isSelected && existing) {
                    existing.remove();
                }
            });
        }

        lpClose(pickerId);
    };

    // Close on Escape key.
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        document.querySelectorAll('[id$="-modal"]:not(.hidden)').forEach(function (modal) {
            const pickerId = modal.id.replace('-modal', '');
            lpClose(pickerId);
        });
    });

    // Re-scale previews on window resize if a modal happens to be open.
    window.addEventListener('resize', function () {
        document.querySelectorAll('[id$="-modal"]:not(.hidden)').forEach(function (modal) {
            modal.querySelectorAll('.lp-preview-wrapper').forEach(scalePreview);
        });
    });
}());
</script>
@endonce
