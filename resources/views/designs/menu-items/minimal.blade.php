{{-- Minimal layout: clean white menu page with stacked content list --}}
@php
  $pageCategories = collect($pageCategories ?? []);
  $pageContents = collect($pageContents ?? []);
  $pageContext = array_merge([
    'eyebrow' => 'Menu page',
    'description' => '',
  ], $pageContext ?? []);
@endphp

<div class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
  <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">{{ $pageContext['eyebrow'] }}</p>
    <h1 class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $menuItem->title }}</h1>
    @if (filled($pageContext['description']))
      <p class="mt-3 max-w-2xl text-base text-slate-600 dark:text-slate-400">{{ $pageContext['description'] }}</p>
    @endif
  </div>
</div>

<div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
  @if ($pageCategories->isEmpty() && $pageContents->isEmpty())
    <p class="text-slate-500 dark:text-slate-400">No content has been linked to this page yet.</p>
  @else
    <div class="space-y-10">
      @foreach ($pageCategories as $category)
        <section>
          <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $category->name }}</h2>
          @if (filled($category->description))
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $category->description }}</p>
          @endif
          <div class="mt-4 divide-y divide-slate-200 dark:divide-slate-800">
            @foreach ($category->contents as $content)
              <a href="{{ route('public.contents.show', $content) }}"
                 class="flex items-center justify-between py-4 hover:text-indigo-600 dark:hover:text-indigo-400">
                <span class="font-medium text-slate-900 dark:text-white">{{ $content->title }}</span>
                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                </svg>
              </a>
            @endforeach
          </div>
        </section>
      @endforeach

      @foreach ($pageContents as $content)
        <a href="{{ route('public.contents.show', $content) }}"
           class="flex items-center justify-between border-t border-slate-200 py-4 hover:text-indigo-600 dark:border-slate-800 dark:hover:text-indigo-400">
          <span class="font-medium text-slate-900 dark:text-white">{{ $content->title }}</span>
          <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
          </svg>
        </a>
      @endforeach
    </div>
  @endif
</div>
