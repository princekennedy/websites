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
    class="fixed inset-0 z-[220] hidden items-center justify-center overflow-y-auto px-4 py-6 sm:px-6 sm:py-8 lg:px-10"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $pickerId }}-title"
    aria-hidden="true"
    tabindex="-1"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-slate-950/80 backdrop-blur-md"
        onclick="lpClose('{{ $pickerId }}')"
        aria-hidden="true"
    ></div>

    <div class="relative w-full max-w-[min(96vw,1480px)] overflow-hidden rounded-[32px] border border-white/10 bg-slate-950/95 shadow-[0_40px_120px_rgba(2,6,23,0.72)] ring-1 ring-white/10">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(56,189,248,0.18),_transparent_38%),radial-gradient(circle_at_bottom_right,_rgba(34,211,238,0.12),_transparent_32%)]"></div>
            <div class="relative max-h-[92vh] overflow-y-auto px-5 pb-6 pt-5 sm:px-7 sm:pb-7 sm:pt-6 lg:px-8 lg:pb-8">

            {{-- Modal header --}}
            <div class="sticky top-0 z-10 mb-6 flex items-start justify-between gap-4 border-b border-white/10 bg-slate-950/90 pb-5 pt-1 backdrop-blur-xl">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.32em] text-sky-300/80">Live Preview Gallery</p>
                    <h2 id="{{ $pickerId }}-title" class="mt-2 text-2xl font-bold text-white sm:text-3xl">
                        Choose a {{ $label }} Layout
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300/80">
                        Click <strong class="text-slate-300">Select</strong> on a design to apply it.
                        Previews use real seeded data from this website.
                    </p>
                </div>
                <button
                    type="button"
                    data-lp-close
                    class="mt-0.5 inline-flex shrink-0 items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/10"
                    onclick="lpClose('{{ $pickerId }}')"
                    aria-label="Close layout picker"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                    Close
                </button>
            </div>

            <div class="mb-6 rounded-[28px] border border-white/10 bg-white/[0.03] p-4 shadow-[0_20px_40px_rgba(2,6,23,0.18)] sm:p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-white">Current selection</p>
                        <p class="mt-1 text-sm text-slate-300/75">{{ $currentLabel }}</p>
                    </div>
                    <div class="inline-flex items-center gap-2 self-start rounded-full border border-sky-400/20 bg-sky-400/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-sky-200">
                        <span>Code</span>
                        <span class="rounded-full bg-slate-950/60 px-2 py-0.5 font-mono text-[11px] text-sky-100">{{ $value }}</span>
                    </div>
                </div>
            </div>

            {{-- Layout cards grid --}}
            <div class="grid gap-6 xl:grid-cols-2">
                @foreach ($options as $layoutValue => $layoutLabel)
                    @php [$shortName, $shortDesc] = $labelParts($layoutLabel); @endphp

                    <div
                        class="lp-card group overflow-hidden rounded-[28px] border bg-slate-900/85 shadow-[0_20px_45px_rgba(15,23,42,0.28)] ring-1 ring-white/5 transition duration-200 {{ (string) $layoutValue === (string) $value ? 'border-sky-400/90' : 'border-slate-700/80 hover:border-sky-400/50' }}"
                        id="{{ $pickerId }}-card-{{ $layoutValue }}"
                        data-layout="{{ $layoutValue }}"
                    >
                        {{-- Scaled iframe preview --}}
                        <div
                            class="lp-preview-wrapper relative overflow-hidden border-b border-white/10 bg-slate-950"
                            style="height: clamp(320px, 50vh, 560px); min-height: 50vh;"
                            title="Layout preview"
                        >
                            <div class="pointer-events-none absolute inset-x-0 top-0 z-[1] h-16 bg-gradient-to-b from-slate-950/60 via-slate-950/10 to-transparent"></div>
                            <iframe
                                data-src="{{ route('cms.layout-preview', ['section' => $section, 'layout' => $layoutValue]) }}"
                                style="position: absolute; top: 0; left: 50%; width: 1200px; height: 700px; transform-origin: top center; border: none; pointer-events: none;"
                                title="{{ $shortName }} layout preview"
                            ></iframe>

                            {{-- Spinner shown until iframe loads --}}
                            <div class="lp-spinner absolute inset-0 flex items-center justify-center bg-slate-900/80">
                                <svg class="h-8 w-8 animate-spin text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- Card footer --}}
                        <div class="flex items-center justify-between gap-4 bg-gradient-to-b from-slate-900 to-slate-950 p-5">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-white truncate">{{ $shortName }}</p>
                                    @if ((string) $layoutValue === (string) $value)
                                        <span class="shrink-0 rounded-full bg-sky-500/20 px-2 py-0.5 text-xs font-semibold text-sky-400">Current</span>
                                    @endif
                                </div>
                                @if ($shortDesc)
                                    <p class="mt-0.5 truncate text-sm text-slate-400">{{ $shortDesc }}</p>
                                @endif
                            </div>
                            <button
                                type="button"
                                class="inline-flex shrink-0 items-center gap-2 rounded-full bg-gradient-to-r from-sky-500 via-cyan-500 to-teal-400 px-5 py-2 text-sm font-semibold text-slate-950 transition hover:-translate-y-0.5 hover:from-sky-400 hover:via-cyan-400 hover:to-teal-300 {{ (string) $layoutValue === (string) $value ? 'cursor-default opacity-50' : '' }}"
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

{{-- ─── JavaScript (output once per page, shared by all picker instances) ──── --}}
@once
<script>
(function () {
    const PREVIEW_WIDTH = 1200;
    const PREVIEW_HEIGHT = 700;
    const PREVIEW_MIN_HEIGHT = 320;
    const pickerState = {
        openId: null,
        previousActiveElement: null,
    };

    // Scale each iframe inside a .lp-preview-wrapper to fit its container.
    function scalePreview(wrapper) {
        const iframe = wrapper.querySelector('iframe');
        if (!iframe) return;

        const targetHeight = Math.max(Math.round(window.innerHeight * 0.5), PREVIEW_MIN_HEIGHT);
        const targetWidth = Math.min(wrapper.offsetWidth, Math.round(window.innerWidth * 0.5));
        const widthScale = targetWidth / PREVIEW_WIDTH;
        const heightScale = targetHeight / PREVIEW_HEIGHT;
        const scale = Math.max(widthScale, heightScale);

        iframe.style.transform = 'translateX(-50%) scale(' + scale + ')';
        wrapper.style.height = targetHeight + 'px';
    }

    // Load the iframes inside a modal (lazy — only when modal opens).
    function loadPreviews(pickerId) {
        const modal = document.getElementById(pickerId + '-modal');
        if (!modal) return;

        modal.querySelectorAll('.lp-preview-wrapper').forEach(function (wrapper) {
            scalePreview(wrapper);

            const iframe = wrapper.querySelector('iframe[data-src]');
            if (!iframe) return;

            // Already loaded.
            if (iframe.dataset.loaded === '1') return;

            iframe.src = iframe.dataset.src;
            iframe.dataset.loaded = '1';

            iframe.addEventListener('load', function () {
                const spinner = wrapper.querySelector('.lp-spinner');
                if (spinner) spinner.remove();
            }, { once: true });
        });
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
