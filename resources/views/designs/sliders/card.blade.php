{{-- Card slider: 3-up card grid showing multiple slides as feature cards --}}
@php
  $slides = collect($slides ?? []);
@endphp

<section id="home" class="bg-slate-50 dark:bg-slate-900">
  <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">

    @if ($slides->isEmpty())
      <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center dark:border-slate-800 dark:bg-slate-950">
        <p class="text-slate-500 dark:text-slate-400">No slides configured.</p>
      </div>
    @else
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($slides as $slide)
          <div class="group flex flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950">
            @if (filled($slide['image'] ?? null))
              <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}"
                   class="h-48 w-full object-cover">
            @endif
            <div class="flex flex-1 flex-col p-6">
              @if (filled($slide['kicker'] ?? null))
                <span class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">{{ $slide['kicker'] }}</span>
              @endif
              <h2 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">{{ $slide['title'] }}</h2>
              @if (filled($slide['desc'] ?? null))
                <p class="mt-2 flex-1 text-sm text-slate-600 dark:text-slate-400">{{ $slide['desc'] }}</p>
              @endif
              @if (!empty($slide['buttons']))
                <div class="mt-6 flex flex-wrap gap-3">
                  @foreach ($slide['buttons'] as $btn)
                    <a href="{{ $btn['link'] }}"
                       class="rounded-full px-4 py-2 text-sm font-semibold transition {{ str_contains($btn['class'] ?? '', 'bg-indigo') ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'border border-slate-300 text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300' }}">
                      {{ $btn['text'] }}
                    </a>
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif

  </div>
</section>
