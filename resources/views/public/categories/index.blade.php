<x-layouts.site title="Topics | SRHR Connect">
  {{-- Hero --}}
  <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(99,102,241,0.25),transparent_35%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,0.2),transparent_30%)]"></div>
    <div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-24">
      <div class="max-w-3xl">
        <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1 text-sm backdrop-blur">Knowledge Base</span>
        <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl md:text-6xl">Browse Topics</h1>
        <p class="mt-6 text-lg leading-8 text-slate-200">Browse published SRHR topics with clearer entry points.</p>
      </div>
    </div>
  </section>

  {{-- Categories Grid --}}
  <section class="py-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      @if ($categories->isEmpty())
        <div class="rounded-[2rem] border border-slate-200 bg-white p-10 text-center shadow-sm dark:border-slate-800 dark:bg-slate-950">
          <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">No topics yet</p>
          <h2 class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">Topics are coming soon.</h2>
          <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-600 dark:text-slate-400">Check back shortly for published categories and content.</p>
        </div>
      @else
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($categories as $category)
            <a href="{{ route('public.categories.show', $category) }}"
               class="group relative flex flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm transition hover:shadow-lg hover:-translate-y-1 dark:border-slate-800 dark:bg-slate-950">
              <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
              </div>
              <h2 class="text-xl font-bold text-slate-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400 transition">{{ $category->name }}</h2>
              @if ($category->description)
                <p class="mt-3 flex-1 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ Str::limit($category->description, 120) }}</p>
              @endif
              <div class="mt-6 flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500">
                  {{ $category->contents_count }} {{ Str::plural('article', $category->contents_count) }}
                </span>
                <span class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 dark:text-indigo-400 group-hover:gap-2 transition-all">
                  Explore
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </span>
              </div>
            </a>
          @endforeach
        </div>
      @endif
    </div>
  </section>
</x-layouts.site>
