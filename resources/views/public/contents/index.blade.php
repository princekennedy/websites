<x-layouts.site title="Content Library | SRHR Connect">
  {{-- Hero --}}
  <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(99,102,241,0.25),transparent_35%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,0.2),transparent_30%)]"></div>
    <div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-24">
      <div class="max-w-3xl">
        <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1 text-sm backdrop-blur">Content Library</span>
        <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl md:text-6xl">Explore Content</h1>
        <p class="mt-6 text-lg leading-8 text-slate-200">Published SRHR content arranged like a modern landing library.</p>
      </div>
    </div>
  </section>

  {{-- Filters --}}
  <section class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <form method="GET" action="{{ route('public.contents.index') }}" class="flex flex-wrap items-center gap-3 py-4">
        <input id="content-search"
               type="search" name="q" value="{{ $search }}"
               placeholder="Search content…"
               class="flex-1 min-w-[200px] rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 placeholder-slate-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white" />

        <select id="content-type-filter" name="type"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
          <option value="">All types</option>
          @foreach ($typeOptions as $value => $label)
            <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
          @endforeach
        </select>

        <select id="content-category-filter" name="category"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
          <option value="">All topics</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->slug }}" @selected($selectedCategory === $cat->slug)>{{ $cat->name }}</option>
          @endforeach
        </select>

        <button id="content-search-btn" type="submit"
                class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
          Search
        </button>

        @if ($search || $selectedType || $selectedCategory)
          <a href="{{ route('public.contents.index') }}"
             class="rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-900">
            Clear
          </a>
        @endif
      </form>
    </div>
  </section>

  {{-- Content grid --}}
  <section class="py-16">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      @if ($contents->isEmpty())
        <div class="rounded-[2rem] border border-slate-200 bg-white p-10 text-center shadow-sm dark:border-slate-800 dark:bg-slate-950">
          <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">Nothing found</p>
          <h2 class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">No content matches your search.</h2>
          <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-600 dark:text-slate-400">Try adjusting your filters or check back later.</p>
        </div>
      @else
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($contents as $content)
            <a href="{{ route('public.contents.show', $content) }}"
               class="group flex flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm transition hover:shadow-lg hover:-translate-y-1 dark:border-slate-800 dark:bg-slate-950">
              <div class="flex flex-1 flex-col p-8">
                <div class="flex items-center gap-2">
                  @if ($content->category)
                    <span class="rounded-full bg-indigo-50 px-3 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
                      {{ $content->category->name }}
                    </span>
                  @endif
                  <span class="rounded-full bg-slate-100 px-3 py-0.5 text-xs font-medium capitalize text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                    {{ str_replace('_', ' ', $content->content_type) }}
                  </span>
                </div>
                <h2 class="mt-4 text-lg font-bold leading-snug text-slate-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400 transition">
                  {{ $content->title }}
                </h2>
                @if ($content->summary)
                  <p class="mt-3 flex-1 text-sm leading-6 text-slate-600 dark:text-slate-400">
                    {{ Str::limit($content->summary, 130) }}
                  </p>
                @endif
                <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4 dark:border-slate-800">
                  <span class="text-xs text-slate-400 dark:text-slate-500">
                    {{ optional($content->published_at)->format('d M Y') }}
                  </span>
                  <span class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 dark:text-indigo-400 group-hover:gap-2 transition-all">
                    Read
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                  </span>
                </div>
              </div>
            </a>
          @endforeach
        </div>

        {{-- Pagination --}}
        @if ($contents->hasPages())
          <div class="mt-12">
            {{ $contents->withQueryString()->links() }}
          </div>
        @endif
      @endif
    </div>
  </section>
</x-layouts.site>
