{{-- Default layout: dark gradient hero + card grid (original design) --}}
<section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white">
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(99,102,241,0.25),transparent_35%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,0.2),transparent_30%)]"></div>
  <div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-24">
    <div class="max-w-3xl">
      <a href="{{ route('public.categories.index') }}"
         class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-4 py-1 text-sm backdrop-blur hover:bg-white/20 transition">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        All Topics
      </a>
      <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl md:text-6xl" style="color: #ffffff; text-shadow: 0 10px 30px rgba(15, 23, 42, 0.55);">{{ $category->name }}</h1>
      @if ($category->description)
        <p class="mt-6 text-lg leading-8 text-slate-200">{{ $category->description }}</p>
      @endif
    </div>
  </div>
</section>

<section class="bg-slate-50 py-16 text-slate-950 sm:py-20">
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="grid gap-4 rounded-[2rem] border border-slate-200/80 bg-white/90 p-5 shadow-sm shadow-slate-200/70 backdrop-blur sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end sm:p-6">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-600">Category overview</p>
        <h2 class="mt-2 text-2xl font-bold text-slate-900">Published resources in {{ $category->name }}</h2>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Browse the latest guidance, articles, and reference material in this topic with readable cards on a lighter surface.</p>
      </div>
      <div class="inline-flex items-center gap-3 rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-slate-300/40">
        <span>{{ number_format($contents->total()) }}</span>
        <span class="text-slate-300">published item{{ $contents->total() === 1 ? '' : 's' }}</span>
      </div>
    </div>

    @if ($contents->isEmpty())
      <div class="mt-8 rounded-[2rem] border border-slate-200 bg-white p-10 text-center shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">Coming soon</p>
        <h2 class="mt-3 text-3xl font-bold text-slate-900">No published content yet.</h2>
        <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-600">Check back shortly for articles in this topic.</p>
      </div>
    @else
      <div class="mt-10 grid gap-6 sm:mt-12 sm:grid-cols-2 sm:gap-8 lg:grid-cols-3">
        @foreach ($contents as $content)
          <a href="{{ route('public.contents.show', $content) }}"
             class="group flex flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm shadow-slate-200/60 transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-100/60">
            <div class="flex flex-1 flex-col p-6 sm:p-8">
              <span class="self-start rounded-full bg-slate-100 px-3 py-0.5 text-xs font-medium capitalize text-slate-600">
                {{ str_replace('_', ' ', $content->content_type) }}
              </span>
              <h2 class="mt-4 text-lg font-bold leading-snug text-slate-900 transition group-hover:text-indigo-600">
                {{ $content->title }}
              </h2>
              @if ($content->summary)
                <p class="mt-3 flex-1 text-sm leading-6 text-slate-600">
                  {{ \Illuminate\Support\Str::limit($content->summary, 130) }}
                </p>
              @endif
              <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4">
                <span class="text-xs text-slate-400">
                  {{ optional($content->published_at)->format('d M Y') }}
                </span>
                <span class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 transition-all group-hover:gap-2">
                  Read
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </span>
              </div>
            </div>
          </a>
        @endforeach
      </div>

      @if ($contents->hasPages())
        <div class="mt-12 rounded-[1.5rem] border border-slate-200 bg-white px-4 py-5 shadow-sm shadow-slate-200/50">
          {{ $contents->withQueryString()->links() }}
        </div>
      @endif
    @endif
  </div>
</section>
