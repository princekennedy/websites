@props([
    'storageKey' => 'cms:list-view:default',
    'targetId' => 'cms-listing-default',
    'default' => 'table',
])

<div class="mb-4 flex items-center justify-between gap-3" data-cms-view-root data-storage-key="{{ $storageKey }}" data-target-id="{{ $targetId }}" data-default-view="{{ $default }}">
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">Listing View</p>
    <div class="inline-flex items-center rounded-full border border-slate-200/70 bg-white/70 p-1 dark:border-white/10 dark:bg-slate-950/40">
        <button type="button" data-view-btn="table" class="rounded-full px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-slate-600 transition hover:text-slate-900 dark:text-stone-300 dark:hover:text-white">Table</button>
        <button type="button" data-view-btn="card" class="rounded-full px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-slate-600 transition hover:text-slate-900 dark:text-stone-300 dark:hover:text-white">Card</button>
    </div>
</div>

@once
<script>
(() => {
    const ACTIVE_BTN = ['bg-sky-500', 'text-white', 'shadow-sm'];
    const INACTIVE_BTN = ['text-slate-600', 'dark:text-stone-300'];

    const applyView = (root, view) => {
        const target = document.getElementById(root.dataset.targetId);
        if (!target) return;

        target.querySelectorAll('[data-view-panel]').forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.viewPanel !== view);
        });

        root.querySelectorAll('[data-view-btn]').forEach((button) => {
            const active = button.dataset.viewBtn === view;
            ACTIVE_BTN.forEach((klass) => button.classList.toggle(klass, active));
            if (active) {
                INACTIVE_BTN.forEach((klass) => button.classList.remove(klass));
            } else {
                INACTIVE_BTN.forEach((klass) => button.classList.add(klass));
            }
        });
    };

    document.querySelectorAll('[data-cms-view-root]').forEach((root) => {
        const storageKey = root.dataset.storageKey;
        const defaultView = root.dataset.defaultView || 'table';
        const savedView = localStorage.getItem(storageKey);
        const initialView = savedView === 'card' || savedView === 'table' ? savedView : defaultView;

        applyView(root, initialView);

        root.querySelectorAll('[data-view-btn]').forEach((button) => {
            button.addEventListener('click', () => {
                const view = button.dataset.viewBtn;
                if (view !== 'card' && view !== 'table') return;
                localStorage.setItem(storageKey, view);
                applyView(root, view);
            });
        });
    });
})();
</script>
@endonce
