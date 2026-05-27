{{-- Hero slider: immersive full-screen hero with bold caption and overlay --}}
@php $slides = collect($slides ?? []); $slide = $slides->first(); @endphp

<section id="home" class="relative overflow-hidden bg-slate-950 text-white">
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(56,189,248,0.35),transparent_30%),radial-gradient(circle_at_bottom_right,rgba(168,85,247,0.25),transparent_35%)]"></div>
  <div class="relative mx-auto max-w-7xl px-6 py-24 lg:px-8 lg:py-32">
    <div class="grid gap-16 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
      <div class="max-w-2xl">
        @if ($slide)
          @if (filled($slide['kicker'] ?? null))
            <span class="inline-flex rounded-full bg-white/10 px-4 py-1 text-sm font-semibold uppercase tracking-[0.24em] text-sky-200">{{ $slide['kicker'] }}</span>
          @endif
          <h1 class="mt-6 text-5xl font-extrabold tracking-tight text-white sm:text-6xl">{{ $slide['title'] }}</h1>
          @if (filled($slide['desc'] ?? null))
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-200">{{ $slide['desc'] }}</p>
          @endif
          @if (!empty($slide['buttons']))
            <div class="mt-10 flex flex-wrap gap-4">
              @foreach ($slide['buttons'] as $btn)
                <a href="{{ $btn['link'] }}" class="inline-flex rounded-full px-6 py-3 text-sm font-semibold transition {{ str_contains($btn['class'] ?? '', 'bg-indigo') ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'border border-white/20 text-white hover:bg-white/10' }}">{{ $btn['text'] }}</a>
              @endforeach
            </div>
          @endif
        @else
          <div class="space-y-4">
            <p class="text-sm uppercase tracking-[0.24em] text-sky-200">Starter slide</p>
            <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl">No slides configured yet</h1>
            <p class="max-w-2xl text-lg leading-8 text-slate-300">Add a slide to preview the hero display with large text, soft overlays, and strong CTAs.</p>
          </div>
        @endif
      </div>

      <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-slate-950/20 backdrop-blur">
        <p class="text-sm uppercase tracking-[0.24em] text-sky-200">Slide details</p>
        @if ($slide)
          <div class="mt-6 space-y-4 text-slate-100">
            <p><span class="font-semibold text-white">Title:</span> {{ $slide['title'] }}</p>
            @if (filled($slide['desc'] ?? null))
              <p>{{ Str::limit($slide['desc'], 120) }}</p>
            @endif
            <p><span class="font-semibold text-white">Buttons:</span> {{ count($slide['buttons'] ?? []) }}</p>
          </div>
        @else
          <p class="mt-6 text-sm text-slate-300">Create a slide to fill this hero section with a compelling headline and action links.</p>
        @endif
      </div>
    </div>
  </div>
</section>
