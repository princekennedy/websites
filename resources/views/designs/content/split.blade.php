{{-- Split layout: headline left, meta and related cards right --}}
<div class="bg-white dark:bg-slate-950">
  <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
    <div class="lg:grid lg:grid-cols-[1.4fr_0.9fr] lg:items-start lg:gap-16">
      <div>
        @if ($content->category)
          <a href="{{ route('public.categories.show', $content->category) }}" class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">{{ $content->category->name }}</a>
        @endif
        <h1 class="mt-4 text-4xl font-extrabold leading-tight text-slate-900 dark:text-white sm:text-5xl">{{ $content->title }}</h1>
        @if ($content->summary)
          <p class="mt-6 text-lg leading-8 text-slate-600 dark:text-slate-400">{{ $content->summary }}</p>
        @endif
      </div>

      <aside class="mt-10 space-y-6 rounded-3xl border border-slate-200 bg-slate-50 p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:mt-0">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Quick facts</p>
          <div class="mt-4 space-y-3 text-sm text-slate-700 dark:text-slate-300">
            <p><span class="font-semibold">Published:</span> {{ $content->published_at?->format('M j, Y') ?? 'TBC' }}</p>
            <p><span class="font-semibold">Length:</span> {{ Str::words(strip_tags($content->body ?? ''), 20, '...') }}</p>
            <p><span class="font-semibold">Type:</span> {{ ucfirst($content->content_type ?? 'Article') }}</p>
          </div>
        </div>

        @if ($relatedContents->isNotEmpty())
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Related reads</p>
            <div class="mt-4 space-y-4">
              @foreach($relatedContents->take(3) as $related)
                <a href="{{ route('public.contents.show', $related) }}" class="block rounded-3xl border border-slate-200 bg-white px-4 py-4 text-sm font-medium text-slate-900 transition hover:border-indigo-200 hover:bg-indigo-50 dark:border-slate-800 dark:bg-slate-950 dark:text-white dark:hover:border-indigo-700 dark:hover:bg-slate-900/80">{{ $related->title }}</a>
              @endforeach
            </div>
          </div>
        @endif
      </aside>
    </div>

    <div class="mt-16 lg:mt-20">
      @if ($content->body)
        <div class="prose prose-slate dark:prose-invert max-w-none">
          {!! $content->body !!}
        </div>
      @endif
    </div>
  </div>
</div>
