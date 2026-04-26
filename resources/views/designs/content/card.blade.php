{{-- Card layout: no hero, card-style content blocks, magazine feel --}}
<div class="bg-slate-50 dark:bg-slate-900">
  <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">

    {{-- Page header --}}
    <div class="max-w-3xl">
      @if ($content->category)
        <a href="{{ route('public.categories.show', $content->category) }}"
           class="inline-flex items-center gap-1.5 rounded-full bg-indigo-100 px-3 py-0.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-200 dark:bg-indigo-900/40 dark:text-indigo-300">
          {{ $content->category->name }}
        </a>
      @endif
      <h1 class="mt-4 text-3xl font-extrabold leading-tight text-slate-900 dark:text-white sm:text-4xl">{{ $content->title }}</h1>
      @if ($content->summary)
        <p class="mt-3 text-lg text-slate-600 dark:text-slate-400">{{ $content->summary }}</p>
      @endif
    </div>

    {{-- Body card --}}
    @if ($content->body)
      <div class="mt-10 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-950 lg:p-12">
        <div class="prose prose-slate dark:prose-invert max-w-none">
          {!! $content->body !!}
        </div>
      </div>
    @endif

    {{-- Related cards --}}
    @if ($relatedContents->isNotEmpty())
      <div class="mt-16">
        <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Related</h2>
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($relatedContents as $related)
            <a href="{{ route('public.contents.show', $related) }}"
               class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
              <span class="text-xs font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500">
                {{ str_replace('_', ' ', $related->content_type) }}
              </span>
              <h3 class="mt-2 font-semibold text-slate-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400">{{ $related->title }}</h3>
              @if ($related->summary)
                <p class="mt-2 flex-1 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($related->summary, 90) }}</p>
              @endif
            </a>
          @endforeach
        </div>
      </div>
    @endif

  </div>
</div>
