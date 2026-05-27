{{-- Gallery layout: visual content grid with headline and featured excerpt --}}
<div class="bg-slate-950 text-white">
  <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
    <div class="grid gap-12 lg:grid-cols-[1.2fr_0.8fr] lg:items-end">
      <div>
        @if ($content->category)
          <span class="inline-flex rounded-full bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-slate-200">{{ $content->category->name }}</span>
        @endif
        <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">{{ $content->title }}</h1>
        @if ($content->summary)
          <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">{{ $content->summary }}</p>
        @endif
      </div>
      <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-xl backdrop-blur">
        <p class="text-sm uppercase tracking-[0.24em] text-sky-200">Featured</p>
        <p class="mt-4 text-base leading-7 text-slate-100">A gallery-forward layout that pairs story-focused copy with a curated content preview grid.</p>
      </div>
    </div>
  </div>
</div>

<section class="bg-white py-20 dark:bg-slate-950">
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      @foreach ($relatedContents->take(6) as $related)
        <a href="{{ route('public.contents.show', $related) }}" class="group overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-50 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
          <div class="p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">{{ $related->category?->name ?? 'Story' }}</p>
            <h2 class="mt-4 text-xl font-semibold text-slate-900 dark:text-white">{{ $related->title }}</h2>
            @if ($related->summary)
              <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">{{ Str::limit($related->summary, 100) }}</p>
            @endif
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
