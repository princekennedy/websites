@php
    $submitLabel = $submitLabel ?? 'Save menu item';
@endphp

<div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
    <section class="space-y-5 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div>
            <label for="title" class="text-sm font-medium text-stone-200">Item title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $item->title) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" required>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="layout_type" class="text-sm font-medium text-stone-200">Layout</label>
                <x-cms.layout-picker
                    name="layout_type"
                    section="menu-items"
                    :options="$layoutOptions"
                    :value="old('layout_type', $item->normalizedLayoutType())"
                    label="Menu Item"
                />
            </div>

            <div>
                <label for="parent_id" class="text-sm font-medium text-stone-200">Parent item</label>
                <select id="parent_id" name="parent_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                    <option value="">None</option>
                    @foreach ($parentOptions as $parent)
                        @continue($item->exists && $item->id === $parent->id)
                        <option value="{{ $parent->id }}" @selected((string) old('parent_id', $item->parent_id) === (string) $parent->id)>{{ $parent->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="target_reference" class="text-sm font-medium text-stone-200">Target reference</label>
            <input id="target_reference" name="target_reference" type="text" value="{{ old('target_reference', $item->target_reference) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" placeholder="content:12, category:4, https://example.com">
            <div class="mt-3 grid gap-3 lg:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-stone-950/40 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400">Content shortcuts</p>
                    <ul class="mt-2 space-y-1 text-sm text-stone-300">
                        @forelse ($contentOptions as $contentOption)
                            <li>{{ $contentOption->id }} - {{ $contentOption->title }}</li>
                        @empty
                            <li>No content entries yet.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="rounded-2xl border border-white/10 bg-stone-950/40 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-400">Category shortcuts</p>
                    <ul class="mt-2 space-y-1 text-sm text-stone-300">
                        @forelse ($categoryOptions as $categoryOption)
                            <li>{{ $categoryOption->id }} - {{ $categoryOption->name }}</li>
                        @empty
                            <li>No categories yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <p class="mt-3 text-sm text-stone-400">Use `content:{id}` or `category:{id}` for CMS-linked pages. Use a full URL, `mailto:`, or `tel:` for external targets.</p>
        </div>

        <div>
            <label for="route" class="text-sm font-medium text-stone-200">Internal route</label>
            <input id="route" name="route" type="text" value="{{ old('route', $item->route) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" placeholder="/features/ask-an-expert">
        </div>
    </section>

    <aside class="space-y-5 rounded-3xl border border-white/10 bg-white/5 p-6">
        <div>
            <label for="icon" class="text-sm font-medium text-stone-200">Icon key</label>
            <input id="icon" name="icon" type="text" value="{{ old('icon', $item->icon) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none" placeholder="book-open">
        </div>

        <div>
            <label for="sort_order" class="text-sm font-medium text-stone-200">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $item->sort_order ?? 0) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
        </div>

        <div>
            <label for="visibility" class="text-sm font-medium text-stone-200">Visibility</label>
            <select id="visibility" name="visibility" class="mt-2 w-full rounded-2xl border border-white/10 bg-stone-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none">
                @foreach ($visibilityOptions as $option)
                    <option value="{{ $option }}" @selected(old('visibility', $item->visibility ?: 'public') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            <p class="mt-2 text-sm text-stone-400">Public items are shown before login. Private and restricted items are shown only to logged-in users.</p>
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-stone-950/40 px-4 py-3 text-sm text-stone-200">
            <input type="checkbox" name="open_in_webview" value="1" class="h-4 w-4 rounded border-white/20 bg-stone-950 text-emerald-400 focus:ring-emerald-400" @checked(old('open_in_webview', $item->open_in_webview ?? false))>
            Open in WebView
        </label>

        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-stone-950/40 px-4 py-3 text-sm text-stone-200">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-white/20 bg-stone-950 text-emerald-400 focus:ring-emerald-400" @checked(old('is_active', $item->is_active ?? true))>
            Active item
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">{{ $submitLabel }}</button>
    </aside>
</div>