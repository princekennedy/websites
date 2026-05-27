{{-- Showcase slider: multiple slide cards arranged in a horizontal feature grid --}}
@php $slides = collect($slides ?? []); @endphp

<section id="home" class="bg-slate-50 dark:bg-slate-950">
  <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
    <div class="mb-12 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-600 dark:text-indigo-400">Showcase</p>
        <h2 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-5xl">Featured slider collection</h2>
      </div>
      <div class="text-sm text-slate-600 dark:text-slate-400">A clean showcase of slide content in card format for editorial and promotional layouts.</div>
    </div>

    @if ($slides->isEmpty())
      <div class="rounded-3xl border border-slate-200 bg-white p-16 text-center text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-950 dark:text-slate-400">No slides configured.</div>
    @else
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($slides as $slide)
          <article class="group overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-slate-800 dark:bg-slate-950">
            @if (filled($slide['image'] ?? null))
              <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" class="h-56 w-full object-cover" />
            @endif
            <div class="p-6">
              @if (filled($slide['kicker'] ?? null))
                <span class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-600 dark:text-indigo-400">{{ $slide['kicker'] }}</span>
              @endif
              <h3 class="mt-4 text-xl font-semibold text-slate-900 dark:text-white">{{ $slide['title'] }}</h3>
              @if (filled($slide['desc'] ?? null))
                <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ Str::limit($slide['desc'], 110) }}</p>
              @endif
              @if (!empty($slide['buttons']))
                <div class="mt-6 flex flex-wrap gap-3">
                  @foreach ($slide['buttons'] as $btn)
                    <a href="{{ $btn['link'] }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">{{ $btn['text'] }}</a>
                  @endforeach
                </div>
              @endif
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </div>
</section>
