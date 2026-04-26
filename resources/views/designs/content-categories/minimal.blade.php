{{-- Minimal layout: clean white listing page, no gradient, compact header --}}
<div class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
  <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <a href="{{ route('public.categories.index') }}"
       class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 hover:text-indigo-600 dark:text-slate-400">
      ← All Topics
    </a>
    <h1 class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{{ $category->name }}</h1>
    @if ($category->description)
      <p class="mt-3 max-w-2xl text-base text-slate-600 dark:text-slate-400">{{ $category->description }}</p>
    @endif
  </div>
</div>

<div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
  @if ($contents->isEmpty())
    <p class="text-slate-500 dark:text-slate-400">No published content yet. Check back soon.</p>
  @else
    <div class="divide-y divide-slate-200 dark:divide-slate-800">
      @foreach ($contents as $content)
        <a href="{{ route('public.contents.show', $content) }}"
           class="flex items-start justify-between gap-6 py-6 transition hover:text-indigo-600 dark:hover:text-indigo-400">
          <div>
            <span class="text-xs font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500">
              {{ str_replace('_', ' ', $content->content_type) }}
            </span>
            <h2 class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">{{ $content->title }}</h2>
            @if ($content->summary)
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($content->summary, 130) }}</p>
            @endif
          </div>
          <svg class="mt-1 h-5 w-5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
          </svg>
        </a>
      @endforeach
    </div>

    @if ($contents->hasPages())
      <div class="mt-10">{{ $contents->withQueryString()->links() }}</div>
    @endif
  @endif
</div>
