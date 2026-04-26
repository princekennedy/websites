{{-- Editorial slider: large left-text + right image split layout, single featured slide --}}
@php
  $slides = collect($slides ?? []);
  $slide = $slides->first();
@endphp

<section id="home" class="bg-white dark:bg-slate-950">
  @if ($slide)
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="py-16 lg:grid lg:grid-cols-2 lg:gap-16 lg:py-24">

        {{-- Text column --}}
        <div class="flex flex-col justify-center">
          @if (filled($slide['kicker'] ?? null))
            <span class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-600 dark:text-indigo-400">
              {{ $slide['kicker'] }}
            </span>
          @endif
          <h1 class="mt-4 text-4xl font-extrabold leading-tight text-slate-900 dark:text-white sm:text-5xl">
            {{ $slide['title'] }}
          </h1>
          @if (filled($slide['desc'] ?? null))
            <p class="mt-6 text-lg leading-8 text-slate-600 dark:text-slate-400">{{ $slide['desc'] }}</p>
          @endif
          @if (!empty($slide['buttons']))
            <div class="mt-8 flex flex-wrap gap-4">
              @foreach ($slide['buttons'] as $btn)
                <a href="{{ $btn['link'] }}"
                   class="rounded-full px-6 py-3 text-sm font-semibold transition {{ str_contains($btn['class'] ?? '', 'bg-indigo') ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'border border-slate-300 text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                  {{ $btn['text'] }}
                </a>
              @endforeach
            </div>
          @endif

          {{-- Slide thumbnails for remaining slides --}}
          @if ($slides->count() > 1)
            <div class="mt-10 flex gap-4">
              @foreach ($slides->skip(1)->take(3) as $thumb)
                <div class="flex-1 rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-900">
                  <p class="text-xs font-semibold text-slate-900 dark:text-white">{{ Str::limit($thumb['title'], 40) }}</p>
                </div>
              @endforeach
            </div>
          @endif
        </div>

        {{-- Image column --}}
        <div class="mt-12 flex items-center lg:mt-0">
          @if (filled($slide['image'] ?? null))
            <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}"
                 class="w-full rounded-[2rem] object-cover shadow-xl" style="max-height:480px;">
          @else
            <div class="flex h-80 w-full items-center justify-center rounded-[2rem] bg-gradient-to-br from-indigo-100 to-violet-100 dark:from-indigo-900/30 dark:to-violet-900/30">
              <span class="text-4xl font-extrabold text-indigo-200 dark:text-indigo-800">{{ Str::limit($slide['title'], 2) }}</span>
            </div>
          @endif
        </div>

      </div>
    </div>
  @endif
</section>
