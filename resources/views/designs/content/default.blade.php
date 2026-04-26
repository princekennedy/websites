{{-- Default layout: dark gradient hero + prose body + related cards (original design) --}}
<section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white">
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(99,102,241,0.25),transparent_35%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,0.2),transparent_30%)]"></div>
  <div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-24">
    <div class="max-w-3xl">
      @if ($content->category)
        <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1 text-sm backdrop-blur">{{ $content->category->name }}</span>
      @endif
      <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl md:text-6xl">{{ $content->title }}</h1>
      @if ($content->summary)
        <p class="mt-6 text-lg leading-8 text-slate-200">{{ $content->summary }}</p>
      @endif
    </div>
  </div>
</section>

<section class="py-20">
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-3xl">
      @if ($content->body)
        <div class="prose prose-slate dark:prose-invert max-w-none">
          {!! $content->body !!}
        </div>
      @endif
    </div>

    @if ($relatedContents->isNotEmpty())
      <div class="mt-20">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Related</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          @foreach ($relatedContents as $related)
            <a href="{{ route('public.contents.show', $related) }}" class="block rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
              <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $related->title }}</h3>
              @if ($related->summary)
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $related->summary }}</p>
              @endif
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</section>
