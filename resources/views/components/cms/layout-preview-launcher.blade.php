@props([
    'section',
    'layout' => 'default',
    'options' => [],
    'params' => [],
    'title' => 'Layout Preview',
    'buttonLabel' => 'Preview',
    'buttonClass' => 'inline-flex h-8 w-8 items-center justify-center rounded-full bg-sky-50 text-sky-600 transition hover:bg-sky-100 hover:text-sky-700 dark:bg-sky-500/10 dark:text-sky-400 dark:hover:bg-sky-500/20',
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

<div id="{{ $previewId }}" class="fixed inset-0 z-[220] hidden isolate" role="dialog" aria-modal="true" aria-labelledby="{{ $previewId }}-title">
    <div class="absolute inset-0 bg-black/85" onclick="cmsPreviewClose('{{ $previewId }}')"></div>

    <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-7xl overflow-hidden rounded-3xl border border-slate-800 bg-slate-950 shadow-[0_30px_80px_rgba(0,0,0,0.65)]">
            <div class="max-h-[90vh] overflow-y-auto p-5 sm:p-6">
                <div class="mb-4 flex items-start justify-between gap-4 border-b border-slate-800 pb-4">
                    <div>
                        <h3 id="{{ $previewId }}-title" class="text-xl font-semibold text-white">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-slate-400">Switch layouts below to compare how this section renders.</p>
                    </div>
                    <button type="button" class="rounded-full bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700" onclick="cmsPreviewClose('{{ $previewId }}')">Close</button>
                </div>

                <div class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900">
                    <iframe id="{{ $previewId }}-frame" class="h-[65vh] w-full bg-white" src="about:blank" title="{{ $title }}"></iframe>
                </div>

                <div class="mt-4 rounded-2xl border border-slate-800 bg-slate-900 p-4">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Other Layouts</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($previewOptions as $layoutValue => $layoutLabel)
                            <button
                                type="button"
                                data-layout-button="{{ $previewId }}"
                                data-layout-value="{{ $layoutValue }}"
                                data-layout-url="{{ $buildUrl($layoutValue) }}"
                                class="rounded-full border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-300 transition hover:border-sky-500 hover:text-white"
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

@once
<script>
(() => {
    const setActiveButtons = (id, value) => {
        document.querySelectorAll('[data-layout-button="' + id + '"]').forEach((button) => {
            const active = button.dataset.layoutValue === value;
            button.classList.toggle('border-sky-500', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('bg-sky-500/15', active);
            if (!active) {
                button.classList.remove('bg-sky-500/15');
            }
        });
    };

    window.cmsPreviewOpen = (id, url, layout) => {
        const modal = document.getElementById(id);
        const frame = document.getElementById(id + '-frame');
        if (!modal || !frame) return;
        frame.src = url;
        setActiveButtons(id, layout || 'default');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    window.cmsPreviewClose = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };

    window.cmsPreviewSetLayout = (id, value) => {
        const frame = document.getElementById(id + '-frame');
        if (!frame) return;

        const btn = document.querySelector('[data-layout-button="' + id + '"][data-layout-value="' + value + '"]');
        if (!btn) return;

        frame.src = btn.dataset.layoutUrl;
        setActiveButtons(id, value);
    };

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        document.querySelectorAll('[id^="lpv-"]:not(.hidden)').forEach((modal) => {
            window.cmsPreviewClose(modal.id);
        });
    });
})();
</script>
@endonce
