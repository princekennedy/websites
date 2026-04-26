<x-layouts.site :title="$menuItem->title.' | '.data_get($publicSite ?? [], 'brand.name', 'Brandly')">
  @include('designs.menu-items.'.$menuItem->normalizedLayoutType())

  <x-slot name="scripts">
    <script>
      document.querySelectorAll('[data-media-slider]').forEach((slider) => {
        const slides = Array.from(slider.querySelectorAll('[data-slide]'));
        const dots = Array.from(slider.querySelectorAll('[data-dot]'));
        const prev = slider.querySelector('[data-prev]');
        const next = slider.querySelector('[data-next]');
        if (slides.length <= 1) return;
        let current = 0;
        const showSlide = (index) => {
          slides.forEach((s, i) => { s.classList.toggle('opacity-100', i === index); s.classList.toggle('opacity-0', i !== index); });
          dots.forEach((d, i) => { d.classList.toggle('bg-white', i === index); d.classList.toggle('bg-white/50', i !== index); });
          current = index;
        };
        prev?.addEventListener('click', () => showSlide((current - 1 + slides.length) % slides.length));
        next?.addEventListener('click', () => showSlide((current + 1) % slides.length));
        dots.forEach((dot, index) => dot.addEventListener('click', () => showSlide(index)));
        window.setInterval(() => showSlide((current + 1) % slides.length), 5000);
      });
    </script>
  </x-slot>
</x-layouts.site>