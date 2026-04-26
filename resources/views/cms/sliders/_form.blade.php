@php
    $submitLabel = $submitLabel ?? 'Save slide';
@endphp

<div class="grid gap-6 xl:grid-cols-[1.35fr_0.8fr]">
    <section class="cms-card cms-gradient-card space-y-5 p-6">
        <div>
            <label for="title" class="text-sm font-medium text-slate-900 dark:text-stone-200">Title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $slider->title) }}" class="cms-input mt-2" required>
        </div>

        <div>
            <label for="slug" class="text-sm font-medium text-slate-900 dark:text-stone-200">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $slider->slug) }}" class="cms-input mt-2" placeholder="auto-generated if left blank">
        </div>

        <div>
            <label for="kicker" class="text-sm font-medium text-slate-900 dark:text-stone-200">Kicker</label>
            <input id="kicker" name="kicker" type="text" value="{{ old('kicker', $slider->kicker) }}" class="cms-input mt-2" placeholder="Simple. Elegant. Effective.">
        </div>

        <div>
            <label for="layout_type" class="text-sm font-medium text-slate-900 dark:text-stone-200">Layout</label>
            <select id="layout_type" name="layout_type" class="cms-select mt-2">
                @foreach ($layoutOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('layout_type', $slider->normalizedLayoutType()) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="caption" class="text-sm font-medium text-slate-900 dark:text-stone-200">Caption</label>
            <textarea id="caption" name="caption" rows="6" class="cms-textarea mt-2">{{ old('caption', $slider->caption) }}</textarea>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="primary_button_text" class="text-sm font-medium text-slate-900 dark:text-stone-200">Primary button text</label>
                <input id="primary_button_text" name="primary_button_text" type="text" value="{{ old('primary_button_text', $slider->primary_button_text) }}" class="cms-input mt-2">
            </div>
            <div>
                <label for="primary_button_link" class="text-sm font-medium text-slate-900 dark:text-stone-200">Primary button link</label>
                <input id="primary_button_link" name="primary_button_link" type="text" value="{{ old('primary_button_link', $slider->primary_button_link) }}" class="cms-input mt-2" placeholder="#features">
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="secondary_button_text" class="text-sm font-medium text-slate-900 dark:text-stone-200">Secondary button text</label>
                <input id="secondary_button_text" name="secondary_button_text" type="text" value="{{ old('secondary_button_text', $slider->secondary_button_text) }}" class="cms-input mt-2">
            </div>
            <div>
                <label for="secondary_button_link" class="text-sm font-medium text-slate-900 dark:text-stone-200">Secondary button link</label>
                <input id="secondary_button_link" name="secondary_button_link" type="text" value="{{ old('secondary_button_link', $slider->secondary_button_link) }}" class="cms-input mt-2" placeholder="#contact">
            </div>
        </div>
    </section>

    <aside class="cms-card cms-gradient-card space-y-5 p-6">
        <div>
            <label for="image_upload" class="text-sm font-medium text-slate-900 dark:text-stone-200">Slide image</label>
            <input id="image_upload" name="image_upload" type="file" accept="image/*" class="cms-input mt-2 border-dashed text-sm text-slate-500 dark:text-stone-300">
            @if ($slider->imageUrl())
                <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200/70 bg-white/70 p-2 dark:border-white/10 dark:bg-slate-950/30">
                    <img src="{{ $slider->imageUrl() }}" alt="{{ $slider->title }}" class="h-48 w-full rounded-xl object-cover">
                </div>
            @endif
        </div>

        <div>
            <label for="sort_order" class="text-sm font-medium text-slate-900 dark:text-stone-200">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $slider->sort_order ?? 0) }}" class="cms-input mt-2">
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/70 px-4 py-3 text-sm text-slate-700 dark:border-white/10 dark:bg-slate-950/30 dark:text-stone-200">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-400 dark:border-white/20 dark:bg-slate-950" @checked(old('is_active', $slider->is_active ?? true))>
            Active slide
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">{{ $submitLabel }}</button>
    </aside>
</div>