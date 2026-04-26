{{-- Editorial layout: two-column category page with sidebar filter --}}
<div class="bg-white dark:bg-slate-950">

  <div class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
      <a href="{{ route('public.categories.index') }}"
         class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 hover:text-indigo-600 dark:text-slate-400">
        ← All Topics
      </a>
      <h1 class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">{{ $category->name }}</h1>
      @if ($category->description)
        <p class="mt-2 max-w-2xl text-base text-slate-600 dark:text-slate-400">{{ $category->description }}</p>
      @endif
    </div>
  </div>

  <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
    <div class="lg:grid lg:grid-cols-[1fr_260px] lg:gap-12">

      {{-- Content list --}}
      <div>
        @if ($contents->isEmpty())
          <p class="text-slate-500 dark:text-slate-400">No published content in this topic yet.</p>
        @else
          <div class="space-y-0 divide-y divide-slate-200 dark:divide-slate-800">
            @foreach ($contents as $content)
              <a href="{{ route('public.contents.show', $content) }}"
                 class="group flex items-start justify-between gap-6 py-6">
                <div>
                  <span class="text-xs font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500">
                    {{ str_replace('_', ' ', $content->content_type) }}
                  </span>
                  <h2 class="mt-1 text-lg font-bold text-slate-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400 transition">
                    {{ $content->title }}
                  </h2>
                  @if ($content->summary)
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($content->summary, 140) }}</p>
                  @endif
                </div>
                <svg class="mt-2 h-5 w-5 shrink-0 text-slate-300 group-hover:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
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

      {{-- Sidebar --}}
      <aside class="mt-10 lg:mt-0">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-900">
          <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">About this topic</p>
          <p class="mt-3 font-semibold text-slate-900 dark:text-white">{{ $category->name }}</p>
          @if ($category->description)
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $category->description }}</p>
          @endif
          <div class="mt-4 border-t border-slate-200 pt-4 dark:border-slate-800">
            <p class="text-xs text-slate-400 dark:text-slate-500">
              {{ $contents->total() }} article{{ $contents->total() !== 1 ? 's' : '' }} published
            </p>
          </div>
        </div>
      </aside>

    </div>
  </div>
</div>
