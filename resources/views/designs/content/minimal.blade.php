{{-- Minimal layout: clean white page, no gradient hero, narrow reading column --}}
<div class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
  <div class="mx-auto max-w-3xl px-6 py-12 lg:px-8">
    @if ($content->category)
      <a href="{{ route('public.categories.show', $content->category) }}"
         class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
        {{ $content->category->name }}
      </a>
    @endif
    <h1 class="mt-3 text-3xl font-bold leading-tight text-slate-900 dark:text-white sm:text-4xl">{{ $content->title }}</h1>
    @if ($content->summary)
      <p class="mt-4 text-lg leading-7 text-slate-600 dark:text-slate-400">{{ $content->summary }}</p>
    @endif
  </div>
</div>

<div class="mx-auto max-w-3xl px-6 py-12 lg:px-8">
  @if ($content->body)
    <div class="prose prose-slate dark:prose-invert max-w-none">
      {!! $content->body !!}
    </div>
  @endif

  @if ($relatedContents->isNotEmpty())
    <div class="mt-16 border-t border-slate-200 pt-10 dark:border-slate-800">
      <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Related</p>
      <div class="mt-6 space-y-4">
        @foreach ($relatedContents as $related)
          <a href="{{ route('public.contents.show', $related) }}"
             class="flex items-start gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-4 transition hover:border-indigo-200 hover:bg-indigo-50 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-indigo-900 dark:hover:bg-indigo-950/30">
            <div>
              <h3 class="font-semibold text-slate-900 dark:text-white">{{ $related->title }}</h3>
              @if ($related->summary)
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($related->summary, 100) }}</p>
              @endif
            </div>
          </a>
        @endforeach
      </div>
    </div>
  @endif
</div>
