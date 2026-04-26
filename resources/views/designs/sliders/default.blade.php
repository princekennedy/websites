{{-- Default slider: full-height image carousel with fade transition and dot navigation (original design) --}}
@php
  $slides = collect($slides ?? []);
@endphp

<section id="home" class="relative overflow-hidden">
  <div class="relative h-[85vh] min-h-[560px] w-full">
    @forelse ($slides as $index => $slide)
      <div class="slide absolute inset-0 transition-opacity duration-700 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}">
        <img src="{{ $slide['image'] }}" class="h-full w-full object-cover" alt="{{ $slide['title'] ?: 'Slide '.($index + 1) }}" />
        <div class="absolute inset-0 bg-slate-900/55"></div>
        <div class="absolute inset-0 flex items-center">
          <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-2xl text-white">
              <span class="inline-flex rounded-full bg-white/15 px-4 py-1 text-sm backdrop-blur">{{ $slide['kicker'] }}</span>
              <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl md:text-6xl">{{ $slide['title'] }}</h1>
              <p class="mt-6 text-lg text-slate-200">{{ $slide['desc'] }}</p>
              <div class="mt-8 flex flex-wrap gap-4">
                @foreach ($slide['buttons'] as $btn)
                  <a href="{{ $btn['link'] }}" class="rounded-full px-6 py-3 font-semibold text-white {{ $btn['class'] }}">{{ $btn['text'] }}</a>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="absolute inset-0 flex items-center justify-center bg-slate-900">
        <p class="text-slate-400">No slides configured.</p>
      </div>
    @endforelse

    @if ($slides->count() > 1)
      <button id="prevBtn" class="absolute left-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/20 p-3 text-white backdrop-blur hover:bg-white/30">❮</button>
      <button id="nextBtn" class="absolute right-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/20 p-3 text-white backdrop-blur hover:bg-white/30">❯</button>
      <div class="absolute bottom-8 left-1/2 z-20 flex -translate-x-1/2 gap-3">
        @foreach ($slides as $index => $slide)
          <button class="dot h-3 w-3 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}"></button>
        @endforeach
      </div>
    @endif
  </div>

  <script>
    (() => {
      const container = document.currentScript.closest('section');
      if (!container) return;
      const slides = container.querySelectorAll('.slide');
      const dots = container.querySelectorAll('.dot');
      const prevBtn = container.querySelector('#prevBtn');
      const nextBtn = container.querySelector('#nextBtn');
      let current = 0;
      const showSlide = (index) => {
        slides.forEach((slide, i) => {
          slide.classList.toggle('opacity-100', i === index);
          slide.classList.toggle('opacity-0', i !== index);
          if (dots[i]) {
            dots[i].classList.toggle('bg-white', i === index);
            dots[i].classList.toggle('bg-white/50', i !== index);
          }
        });
        current = index;
      };
      if (nextBtn) nextBtn.addEventListener('click', () => showSlide((current + 1) % slides.length));
      if (prevBtn) prevBtn.addEventListener('click', () => showSlide((current - 1 + slides.length) % slides.length));
      dots.forEach((dot, i) => dot.addEventListener('click', () => showSlide(i)));
      if (slides.length > 1) setInterval(() => showSlide((current + 1) % slides.length), 5000);
    })();
  </script>
</section>
