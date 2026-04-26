{{-- Default layout: dark gradient hero + sectioned card content (original design) --}}
@php
  $pageCategories = collect($pageCategories ?? []);
  $pageContents = collect($pageContents ?? []);
  $pageContext = array_merge([
    'eyebrow' => 'Menu page',
    'description' => 'No linked categories or published content are assigned to this page yet.',
  ], $pageContext ?? []);
@endphp

<section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white">
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(99,102,241,0.25),transparent_35%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,0.2),transparent_30%)]"></div>
  <div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-24">
    <div class="max-w-3xl">
      <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1 text-sm backdrop-blur">{{ $pageContext['eyebrow'] }}</span>
      <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl md:text-6xl">{{ $menuItem->title }}</h1>
      <p class="mt-6 text-lg leading-8 text-slate-200">{{ $pageContext['description'] }}</p>
    </div>
  </div>
</section>

<section class="py-20">
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    @if ($pageCategories->isEmpty() && $pageContents->isEmpty())
      <div class="rounded-[2rem] border border-slate-200 bg-white p-10 text-center shadow-sm dark:border-slate-800 dark:bg-slate-950">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">No linked content</p>
        <h2 class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">This menu page is ready for content.</h2>
        <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-600 dark:text-slate-400">Link one or more categories to this menu item, or use the target reference as a fallback for direct content selection.</p>
      </div>
    @else
      <div class="space-y-10">
        @foreach ($pageCategories as $category)
          <section class="space-y-6 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-950 lg:p-10">
            <div class="max-w-3xl">
              <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">Category</p>
              <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $category->name }}</h2>
              @if (filled($category->description))
                <p class="mt-4 text-base leading-7 text-slate-600 dark:text-slate-400">{{ $category->description }}</p>
              @endif
            </div>

            @if ($category->contents->isEmpty())
              <div class="rounded-3xl bg-slate-50 px-6 py-8 text-sm text-slate-600 dark:bg-slate-900/70 dark:text-slate-400">No published content is currently assigned to this category.</div>
            @else
              <div class="space-y-8">
                @foreach ($category->contents as $content)
                  @include('designs.menu-items._content-card', ['content' => $content])
                @endforeach
              </div>
            @endif
          </section>
        @endforeach

        @if ($pageContents->isNotEmpty())
          <section class="space-y-8">
            @foreach ($pageContents as $content)
              @include('designs.menu-items._content-card', ['content' => $content])
            @endforeach
          </section>
        @endif
      </div>
    @endif
  </div>
</section>
