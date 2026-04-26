{{-- Card layout: grid-first category listing, card-per-content --}}
<div class="bg-slate-50 dark:bg-slate-900">
  <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">

    {{-- Page header --}}
    <div class="mb-12">
      <a href="{{ route('public.categories.index') }}"
         class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 hover:text-indigo-600 dark:text-slate-400">
        ← All Topics
      </a>
      <h1 class="mt-3 text-3xl font-extrabold text-slate-900 dark:text-white">{{ $category->name }}</h1>
      @if ($category->description)
        <p class="mt-3 max-w-2xl text-base text-slate-600 dark:text-slate-400">{{ $category->description }}</p>
      @endif
    </div>

    @if ($contents->isEmpty())
      <div class="rounded-[2rem] border border-slate-200 bg-white p-10 text-center shadow-sm dark:border-slate-800 dark:bg-slate-950">
        <p class="text-slate-500 dark:text-slate-400">No published articles in this topic yet.</p>
      </div>
    @else
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($contents as $content)
          <a href="{{ route('public.contents.show', $content) }}"
             class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
            <div class="flex flex-1 flex-col p-6">
              <span class="rounded-full bg-slate-100 px-3 py-0.5 text-xs font-medium capitalize text-slate-600 self-start dark:bg-slate-800 dark:text-slate-400">
                {{ str_replace('_', ' ', $content->content_type) }}
              </span>
              <h2 class="mt-3 text-base font-bold text-slate-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400 transition">
                {{ $content->title }}
              </h2>
              @if ($content->summary)
                <p class="mt-2 flex-1 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($content->summary, 120) }}</p>
              @endif
            </div>
          </a>
        @endforeach
      </div>

      @if ($contents->hasPages())
        <div class="mt-12">{{ $contents->withQueryString()->links() }}</div>
      @endif
    @endif

  </div>
</div>
