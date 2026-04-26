{{-- Editorial layout: two-column menu page with content details in main + sidebar context --}}
@php
  $pageCategories = collect($pageCategories ?? []);
  $pageContents = collect($pageContents ?? []);
  $pageContext = array_merge([
    'eyebrow' => 'Menu page',
    'description' => '',
  ], $pageContext ?? []);
@endphp

<div class="bg-white dark:bg-slate-950">

  <div class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
      <span class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">{{ $pageContext['eyebrow'] }}</span>
      <h1 class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">{{ $menuItem->title }}</h1>
      @if (filled($pageContext['description']))
        <p class="mt-2 max-w-2xl text-base text-slate-600 dark:text-slate-400">{{ $pageContext['description'] }}</p>
      @endif
    </div>
  </div>

  <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
    @if ($pageCategories->isEmpty() && $pageContents->isEmpty())
      <p class="text-slate-500 dark:text-slate-400">No content has been linked to this page yet.</p>
    @else
      <div class="lg:grid lg:grid-cols-[1fr_260px] lg:gap-12">

        {{-- Main content --}}
        <div class="space-y-12">
          @foreach ($pageCategories as $category)
            <section>
              <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $category->name }}</h2>
              @if (filled($category->description))
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $category->description }}</p>
              @endif
              <div class="mt-5 divide-y divide-slate-200 dark:divide-slate-800">
                @foreach ($category->contents as $content)
                  <a href="{{ route('public.contents.show', $content) }}"
                     class="group flex items-center justify-between py-4">
                    <span class="font-medium text-slate-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400 transition">
                      {{ $content->title }}
                    </span>
                    <svg class="h-4 w-4 shrink-0 text-slate-300 group-hover:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                  </a>
                @endforeach
              </div>
            </section>
          @endforeach

          @if ($pageContents->isNotEmpty())
            <section>
              <div class="divide-y divide-slate-200 dark:divide-slate-800">
                @foreach ($pageContents as $content)
                  <a href="{{ route('public.contents.show', $content) }}"
                     class="group flex items-start justify-between gap-4 py-5">
                    <div>
                      <h3 class="font-semibold text-slate-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400 transition">
                        {{ $content->title }}
                      </h3>
                      @if ($content->summary)
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($content->summary, 120) }}</p>
                      @endif
                    </div>
                    <svg class="mt-1 h-4 w-4 shrink-0 text-slate-300 group-hover:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                  </a>
                @endforeach
              </div>
            </section>
          @endif
        </div>

        {{-- Sidebar --}}
        <aside class="mt-10 lg:mt-0">
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">On this page</p>
            <div class="mt-4 space-y-2">
              @foreach ($pageCategories as $category)
                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $category->name }}</p>
                @foreach ($category->contents as $content)
                  <a href="{{ route('public.contents.show', $content) }}"
                     class="block pl-3 text-sm text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400">
                    {{ $content->title }}
                  </a>
                @endforeach
              @endforeach
              @foreach ($pageContents as $content)
                <a href="{{ route('public.contents.show', $content) }}"
                   class="block text-sm text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400">
                  {{ $content->title }}
                </a>
              @endforeach
            </div>
          </div>
        </aside>

      </div>
    @endif
  </div>
</div>
