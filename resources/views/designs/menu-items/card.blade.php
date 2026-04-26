{{-- Card layout: card-per-content-item, category sections as card groups --}}
@php
  $pageCategories = collect($pageCategories ?? []);
  $pageContents = collect($pageContents ?? []);
  $pageContext = array_merge([
    'eyebrow' => 'Menu page',
    'description' => '',
  ], $pageContext ?? []);
@endphp

<div class="bg-slate-50 dark:bg-slate-900">
  <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">

    {{-- Page header --}}
    <div class="mb-12 max-w-2xl">
      <span class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">{{ $pageContext['eyebrow'] }}</span>
      <h1 class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">{{ $menuItem->title }}</h1>
      @if (filled($pageContext['description']))
        <p class="mt-3 text-base text-slate-600 dark:text-slate-400">{{ $pageContext['description'] }}</p>
      @endif
    </div>

    @if ($pageCategories->isEmpty() && $pageContents->isEmpty())
      <div class="rounded-[2rem] border border-slate-200 bg-white p-10 text-center shadow-sm dark:border-slate-800 dark:bg-slate-950">
        <p class="text-slate-500 dark:text-slate-400">No content linked to this page yet.</p>
      </div>
    @else
      <div class="space-y-14">
        @foreach ($pageCategories as $category)
          <section>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $category->name }}</h2>
            @if (filled($category->description))
              <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $category->description }}</p>
            @endif
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
              @foreach ($category->contents as $content)
                <a href="{{ route('public.contents.show', $content) }}"
                   class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
                  <h3 class="font-semibold text-slate-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400">{{ $content->title }}</h3>
                  @if ($content->summary)
                    <p class="mt-2 flex-1 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($content->summary, 100) }}</p>
                  @endif
                </a>
              @endforeach
            </div>
          </section>
        @endforeach

        @if ($pageContents->isNotEmpty())
          <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($pageContents as $content)
              <a href="{{ route('public.contents.show', $content) }}"
                 class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
                <h3 class="font-semibold text-slate-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400">{{ $content->title }}</h3>
                @if ($content->summary)
                  <p class="mt-2 flex-1 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($content->summary, 100) }}</p>
                @endif
              </a>
            @endforeach
          </div>
        @endif
      </div>
    @endif

  </div>
</div>
