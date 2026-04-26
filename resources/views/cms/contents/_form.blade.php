@php
    $submitLabel = $submitLabel ?? 'Save content';
@endphp

<div class="grid gap-6 xl:grid-cols-[1.35fr_0.8fr]">
    <section class="cms-card cms-gradient-card space-y-5 p-6">
        <div>
            <label for="title" class="text-sm font-medium text-slate-900 dark:text-stone-200">Title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $content->title) }}" class="cms-input mt-2" required>
        </div>

        <div>
            <label for="slug" class="text-sm font-medium text-slate-900 dark:text-stone-200">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $content->slug) }}" class="cms-input mt-2" placeholder="auto-generated if left blank">
        </div>

        <div>
            <label for="layout_type" class="text-sm font-medium text-slate-900 dark:text-stone-200">Layout</label>
            <select id="layout_type" name="layout_type" class="cms-select mt-2">
                @foreach ($layoutOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('layout_type', $content->normalizedLayoutType()) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="mt-2 text-xs text-slate-500 dark:text-stone-500">Layouts are rendered from resources/views/designs/content using default as the fallback style.</p>
        </div>

        <div>
            <label for="summary" class="text-sm font-medium text-slate-900 dark:text-stone-200">Summary</label>
            <textarea id="summary" name="summary" rows="4" class="cms-textarea mt-2">{{ old('summary', $content->summary) }}</textarea>
        </div>

        <div>
            <label for="body" class="text-sm font-medium text-slate-900 dark:text-stone-200">Body</label>
            <textarea id="body" name="body" rows="14" data-ckeditor-field="content-body" class="cms-textarea mt-2">{{ old('body', $content->body) }}</textarea>
            <p class="mt-2 text-xs text-slate-500 dark:text-stone-500">Use the rich text editor to format headings, lists, links, and emphasis for the website and mobile app.</p>
        </div>
    </section>

    <aside class="cms-card cms-gradient-card space-y-5 p-6">
        <div>
            <label for="content_type" class="text-sm font-medium text-slate-900 dark:text-stone-200">Content type</label>
            <select id="content_type" name="content_type" class="cms-select mt-2">
                @foreach ($typeOptions as $option)
                    <option value="{{ $option }}" @selected(old('content_type', $content->content_type ?: 'page') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="category_id" class="text-sm font-medium text-slate-900 dark:text-stone-200">Category</label>
            <select id="category_id" name="category_id" class="cms-select mt-2">
                <option value="">Unassigned</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $content->category_id ?: request('category_id')) === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="status" class="text-sm font-medium text-slate-900 dark:text-stone-200">Status</label>
            <select id="status" name="status" class="cms-select mt-2">
                @foreach ($statusOptions as $option)
                    <option value="{{ $option }}" @selected(old('status', $content->status ?: 'draft') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="audience" class="text-sm font-medium text-slate-900 dark:text-stone-200">Audience</label>
            <select id="audience" name="audience" class="cms-select mt-2">
                @foreach ($audienceOptions as $option)
                    <option value="{{ $option }}" @selected(old('audience', $content->audience ?: 'general') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="visibility" class="text-sm font-medium text-slate-900 dark:text-stone-200">Visibility</label>
            <select id="visibility" name="visibility" class="cms-select mt-2">
                @foreach ($visibilityOptions as $option)
                    <option value="{{ $option }}" @selected(old('visibility', $content->visibility ?: 'public') === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="featured_image_upload" class="text-sm font-medium text-slate-900 dark:text-stone-200">Featured image upload</label>
            <input id="featured_image_upload" name="featured_image_upload" type="file" accept="image/*" class="cms-input mt-2 border-dashed text-sm text-slate-500 dark:text-stone-300">
            @if ($content->getFirstMediaUrl('featured_image'))
                <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200/70 bg-white/70 p-2 dark:border-white/10 dark:bg-slate-950/30">
                    <img src="{{ $content->getFirstMediaUrl('featured_image') }}" alt="{{ $content->title }} featured image" class="h-40 w-full rounded-xl object-cover">
                </div>
            @endif
        </div>

        <div>
            <label for="attachments" class="text-sm font-medium text-slate-900 dark:text-stone-200">Page files</label>
            <input id="attachments" name="attachments[]" type="file" multiple class="cms-input mt-2 border-dashed text-sm text-slate-500 dark:text-stone-300">
            @if ($content->getMedia('attachments')->isNotEmpty())
                <div class="mt-3 space-y-2 rounded-2xl border border-slate-200/70 bg-white/70 p-4 text-sm text-slate-600 dark:border-white/10 dark:bg-slate-950/30 dark:text-stone-300">
                    @foreach ($content->getMedia('attachments') as $attachment)
                        <a href="{{ $attachment->getUrl() }}" target="_blank" rel="noreferrer" class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 px-3 py-2 transition hover:border-sky-200 hover:text-slate-900 dark:border-white/10 dark:hover:text-white">
                            <span class="truncate">{{ $attachment->file_name }}</span>
                            <span class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-stone-500">{{ $attachment->mime_type }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <label for="published_at" class="text-sm font-medium text-slate-900 dark:text-stone-200">Publish at</label>
            <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $content->published_at?->format('Y-m-d\TH:i')) }}" class="cms-input mt-2">
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">{{ $submitLabel }}</button>
    </aside>
</div>

@once
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
@endonce

<script>
    (() => {
        const field = document.querySelector('[data-ckeditor-field="content-body"]');

        if (!field || field.dataset.ckeditorReady === 'true' || typeof ClassicEditor === 'undefined') {
            return;
        }

        ClassicEditor
            .create(field, {
                toolbar: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'link',
                    'bulletedList',
                    'numberedList',
                    'blockQuote',
                    '|',
                    'undo',
                    'redo',
                ],
            })
            .then((editor) => {
                field.dataset.ckeditorReady = 'true';
                window.srhrContentEditor = editor;
            })
            .catch((error) => {
                console.error('Failed to initialize CKEditor.', error);
            });
    })();
</script>