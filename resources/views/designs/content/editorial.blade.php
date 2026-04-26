{{-- Editorial layout: two-column with sidebar metadata, newspaper feel --}}
<div class="bg-white dark:bg-slate-950">

  {{-- Compact header strip --}}
  <div class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
      @if ($content->category)
        <a href="{{ route('public.categories.show', $content->category) }}"
           class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
          {{ $content->category->name }}
        </a>
      @endif
      <h1 class="mt-2 text-4xl font-extrabold leading-tight text-slate-900 dark:text-white sm:text-5xl">{{ $content->title }}</h1>
      @if ($content->summary)
        <p class="mt-3 max-w-3xl text-lg text-slate-600 dark:text-slate-400">{{ $content->summary }}</p>
      @endif
    </div>
  </div>

  {{-- Two-column body --}}
  <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
    <div class="lg:grid lg:grid-cols-[1fr_300px] lg:gap-14">

      {{-- Main content --}}
      <div>
        @if ($content->body)
          <div class="prose prose-slate dark:prose-invert max-w-none">
            {!! $content->body !!}
          </div>
        @endif
      </div>

      {{-- Sidebar --}}
      <aside class="mt-12 space-y-8 lg:mt-0">

        @if ($content->category)
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Topic</p>
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">{{ $content->category->name }}</p>
            @if ($content->category->description)
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($content->category->description, 100) }}</p>
            @endif
            <a href="{{ route('public.categories.show', $content->category) }}"
               class="mt-3 block text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400">
              Browse this topic →
            </a>
          </div>
        @endif

        @if ($content->published_at)
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Published</p>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">{{ $content->published_at->format('d F Y') }}</p>
          </div>
        @endif

        @if ($relatedContents->isNotEmpty())
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Related</p>
            <div class="mt-4 space-y-4">
              @foreach ($relatedContents as $related)
                <a href="{{ route('public.contents.show', $related) }}"
                   class="block text-sm font-medium text-slate-900 hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400">
                  {{ $related->title }}
                </a>
              @endforeach
            </div>
          </div>
        @endif

      </aside>
    </div>
  </div>
</div>
