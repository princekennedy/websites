{{-- Minimal slider: clean white background, left-aligned text, no image --}}
@php
  $slides = collect($slides ?? []);
@endphp

<section id="home" class="bg-white dark:bg-slate-950">
  <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-28">
    @if ($slides->isNotEmpty())
      @php $slide = $slides->first(); @endphp
      @if (filled($slide['kicker'] ?? null))
        <span class="inline-flex rounded-full bg-indigo-50 px-4 py-1 text-sm font-medium text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
          {{ $slide['kicker'] }}
        </span>
      @endif
      <h1 class="mt-6 max-w-3xl text-4xl font-extrabold leading-tight text-slate-900 dark:text-white sm:text-5xl md:text-6xl">
        {{ $slide['title'] }}
      </h1>
      @if (filled($slide['desc'] ?? null))
        <p class="mt-6 max-w-xl text-lg text-slate-600 dark:text-slate-400">{{ $slide['desc'] }}</p>
      @endif
      @if (!empty($slide['buttons']))
        <div class="mt-8 flex flex-wrap gap-4">
          @foreach ($slide['buttons'] as $btn)
            <a href="{{ $btn['link'] }}" class="rounded-full px-6 py-3 text-sm font-semibold transition {{ str_contains($btn['class'] ?? '', 'bg-indigo') ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'border border-slate-300 text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800' }}">
              {{ $btn['text'] }}
            </a>
          @endforeach
        </div>
      @endif
    @endif
  </div>
</section>
