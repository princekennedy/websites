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
<div class="mt-2 space-y-2" data-picker-id="{{ $pickerId }}" data-select-id="{{ $selectId }}">
    <div class="flex items-stretch gap-2">

    {{-- Hidden native select (submitted with the form) --}}
    <select id="{{ $selectId }}" name="{{ $name }}" class="sr-only" aria-hidden="true" tabindex="-1">
        @foreach ($options as $optVal => $optLabel)
            <option value="{{ $optVal }}" @selected((string) $optVal === (string) $value)>{{ $optLabel }}</option>
        @endforeach
    </select>

    {{-- Display pill showing current selection --}}
    <div class="flex flex-1 items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/70 px-4 py-3 dark:border-white/10 dark:bg-slate-950/30">
        <svg class="h-4 w-4 shrink-0 text-slate-400 dark:text-stone-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z"/>
        </svg>
        <span class="flex-1 text-sm text-slate-700 dark:text-stone-200 lp-display-label">{{ $currentLabel }}</span>
        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-mono text-slate-400 dark:bg-white/5 dark:text-stone-500 lp-display-value">{{ $value }}</span>
    </div>

    {{-- Preview / browse button --}}
    <button
        type="button"
        class="inline-flex shrink-0 items-center gap-2 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-400 dark:hover:bg-sky-500/20"
        onclick="lpOpen('{{ $pickerId }}')"
        aria-haspopup="dialog"
    >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
        </svg>
        Preview Layouts
    </button>
    </div>
    <p class="hidden text-xs font-medium text-emerald-600 dark:text-emerald-400 lp-selected-hint"></p>
</div>

{{-- ─── Modal ───────────────────────────────────────────────────────────────── --}}
<div
    id="{{ $pickerId }}-modal"
    class="fixed inset-0 z-[220] hidden isolate"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $pickerId }}-title"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/85"
        onclick="lpClose('{{ $pickerId }}')"
        aria-hidden="true"
    ></div>

    <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-6xl overflow-hidden rounded-3xl border border-slate-800 bg-slate-950 shadow-[0_30px_80px_rgba(0,0,0,0.65)]">
            <div class="max-h-[90vh] overflow-y-auto p-5 sm:p-6 lg:p-7">

            {{-- Modal header --}}
            <div class="mb-6 flex items-start justify-between gap-4 border-b border-slate-800 pb-5">
                <div>
                    <h2 id="{{ $pickerId }}-title" class="text-2xl font-bold text-white">
                        Choose a {{ $label }} Layout
                    </h2>
                    <p class="mt-1 text-sm text-slate-400">
                        Click <strong class="text-slate-300">Select</strong> on a design to apply it.
                        Previews use real seeded data from this website.
                    </p>
                </div>
                <button
                    type="button"
                    class="mt-0.5 flex shrink-0 items-center gap-2 rounded-full bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
                    onclick="lpClose('{{ $pickerId }}')"
                    aria-label="Close layout picker"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                    Close
                </button>
            </div>

            {{-- Layout cards grid --}}
            <div class="grid gap-6 md:grid-cols-2">
                @foreach ($options as $layoutValue => $layoutLabel)
                    @php [$shortName, $shortDesc] = $labelParts($layoutLabel); @endphp

                    <div
                        class="lp-card group overflow-hidden rounded-3xl border-2 transition duration-200 {{ (string) $layoutValue === (string) $value ? 'border-sky-400' : 'border-slate-700 hover:border-sky-400/50' }}"
                        id="{{ $pickerId }}-card-{{ $layoutValue }}"
                        data-layout="{{ $layoutValue }}"
                    >
                        {{-- Scaled iframe preview --}}
                        <div
                            class="lp-preview-wrapper relative overflow-hidden bg-slate-900"
                            style="height: 280px;"
                            title="Layout preview"
                        >
                            <iframe
                                data-src="{{ route('cms.layout-preview', ['section' => $section, 'layout' => $layoutValue]) }}"
                                style="width: 1200px; height: 700px; transform-origin: top left; border: none; pointer-events: none;"
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
                        <div class="flex items-center justify-between gap-4 bg-slate-900 p-5">
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
                                class="inline-flex shrink-0 items-center gap-2 rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-2 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 {{ (string) $layoutValue === (string) $value ? 'opacity-50 cursor-default' : '' }}"
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
    // Scale each iframe inside a .lp-preview-wrapper to fit its container.
    function scalePreview(wrapper) {
        const iframe = wrapper.querySelector('iframe');
        if (!iframe) return;
        const scale = wrapper.offsetWidth / 1200;
        iframe.style.transform = 'scale(' + scale + ')';
        // Adjust wrapper height to match visible iframe area.
        wrapper.style.height = Math.round(700 * scale) + 'px';
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
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Use rAF so the layout is painted before we measure widths for scaling.
        requestAnimationFrame(function () { loadPreviews(pickerId); });
    };

    window.lpClose = function (pickerId) {
        const modal = document.getElementById(pickerId + '-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
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
                card.classList.toggle('border-sky-400',  isSelected);
                card.classList.toggle('border-white/10', !isSelected);

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
