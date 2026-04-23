@php
    $submitLabel = $submitLabel ?? 'Save menu';
@endphp

<div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
    <section class="cms-card cms-gradient-card space-y-5 p-6">
        <div>
            <label for="name" class="text-sm font-medium text-slate-900 dark:text-stone-200">Menu name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $menu->name) }}" class="cms-input mt-2" required>
        </div>

        <div>
            <label for="slug" class="text-sm font-medium text-slate-900 dark:text-stone-200">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $menu->slug) }}" class="cms-input mt-2" placeholder="auto-generated if left blank">
        </div>

        <div>
            <label for="description" class="text-sm font-medium text-slate-900 dark:text-stone-200">Description</label>
            <textarea id="description" name="description" rows="4" class="cms-textarea mt-2">{{ old('description', $menu->description) }}</textarea>
        </div>
    </section>

    <aside class="cms-card cms-gradient-card space-y-5 p-6">
        <div>
            <label for="location" class="text-sm font-medium text-slate-900 dark:text-stone-200">Location key</label>
            <input id="location" name="location" type="text" value="{{ old('location', $menu->location) }}" class="cms-input mt-2" placeholder="home-primary">
        </div>

        <div>
            <label for="visibility" class="text-sm font-medium text-slate-900 dark:text-stone-200">Visibility</label>
            <select id="visibility" name="visibility" class="cms-input mt-2">
                @foreach (($visibilityOptions ?? []) as $option)
                    <option value="{{ $option }}" @selected(old('visibility', $menu->visibility ?: 'public') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            <p class="mt-2 text-sm text-slate-500 dark:text-stone-400">Public menus are shown before login. Private and restricted menus are shown only to logged-in users.</p>
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/70 px-4 py-3 text-sm text-slate-700 dark:border-white/10 dark:bg-slate-950/30 dark:text-stone-200">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-400 dark:border-white/20 dark:bg-slate-950" @checked(old('is_active', $menu->is_active ?? true))>
            Active menu
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">{{ $submitLabel }}</button>
    </aside>
</div>