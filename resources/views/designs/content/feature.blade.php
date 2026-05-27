{{-- Feature layout: bold hero with gradient panel and rich media section --}}
<div class="bg-slate-950 text-white">
  <div class="relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(56,189,248,0.25),transparent_35%),radial-gradient(circle_at_bottom_right,rgba(168,85,247,0.18),transparent_30%)]"></div>
    <div class="relative mx-auto max-w-7xl px-6 py-24 lg:px-8">
      <div class="max-w-3xl">
        @if ($content->category)
          <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-1 text-sm uppercase tracking-[0.24em] text-slate-200">{{ $content->category->name }}</span>
        @endif
        <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">{{ $content->title }}</h1>
        @if ($content->summary)
          <p class="mt-6 text-lg leading-8 text-slate-200">{{ $content->summary }}</p>
        @endif
        <div class="mt-10 flex flex-wrap gap-4">
          <a href="#content" class="inline-flex items-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-slate-950/10 transition hover:bg-slate-100">Read the story</a>
          <a href="#related" class="inline-flex items-center rounded-full border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Explore related</a>
        </div>
      </div>
    </div>
  </div>
</div>

<section id="content" class="bg-white py-20 dark:bg-slate-950">
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="grid gap-10 lg:grid-cols-[1.6fr_0.9fr] lg:items-start">
      <article>
        @if ($content->body)
          <div class="prose prose-slate dark:prose-invert max-w-none">
            {!! $content->body !!}
          </div>
        @endif
      </article>

      <aside class="space-y-6 rounded-3xl border border-slate-200 bg-slate-50 p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        @if ($content->published_at)
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Published</p>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">{{ $content->published_at->format('F j, Y') }}</p>
          </div>
        @endif
        @if ($content->author)
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Author</p>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">{{ $content->author }}</p>
          </div>
        @endif
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Overview</p>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">A modern feature layout with bold hero, immersive content area, and a contextual detail sidebar.</p>
        </div>
      </aside>
    </div>

    @if ($relatedContents->isNotEmpty())
      <div id="related" class="mt-20">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Related stories</h2>
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
          @foreach ($relatedContents as $related)
            <a href="{{ route('public.contents.show', $related) }}" class="group block overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
              <div class="p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">{{ $related->category?->name ?? 'Article' }}</p>
                <h3 class="mt-3 text-xl font-semibold text-slate-900 dark:text-white">{{ $related->title }}</h3>
                @if ($related->summary)
                  <p class="mt-4 text-sm text-slate-600 dark:text-slate-400">{{ Str::limit($related->summary, 110) }}</p>
                @endif
              </div>
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</section>
