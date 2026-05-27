{{-- Split slider: left-side copy with right-side slide previews and a visual carousel feel --}}
@php $slides = collect($slides ?? []); @endphp

<section id="home" class="bg-white dark:bg-slate-950">
  <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
    <div class="grid gap-12 lg:grid-cols-[1fr_1.1fr] lg:items-center">
      <div class="space-y-6">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-600 dark:text-indigo-400">Split slider</p>
        <h2 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-5xl">A balanced text and preview experience for slide-led stories</h2>
        <p class="max-w-xl text-lg leading-8 text-slate-600 dark:text-slate-400">Use this layout when the narrative needs a strong editorial lead with visual slide previews alongside.</p>
        @if ($slides->isNotEmpty())
          <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($slides->take(2) as $slide)
              <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">{{ $slide['kicker'] ?? 'Slide' }}</p>
                <h3 class="mt-3 text-xl font-semibold text-slate-900 dark:text-white">{{ $slide['title'] }}</h3>
                @if (filled($slide['desc'] ?? null))
                  <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">{{ Str::limit($slide['desc'], 90) }}</p>
                @endif
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <div class="space-y-6">
        @if ($slides->isNotEmpty())
          @foreach ($slides as $slide)
            <article class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950">
              @if (filled($slide['image'] ?? null))
                <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" class="h-72 w-full object-cover" />
              @endif
              <div class="p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">{{ $slide['kicker'] ?? 'Featured' }}</p>
                <h3 class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $slide['title'] }}</h3>
                @if (filled($slide['desc'] ?? null))
                  <p class="mt-4 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ $slide['desc'] }}</p>
                @endif
              </div>
            </article>
          @endforeach
        @else
          <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-12 text-center text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
            <p>No slides are configured yet.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>
