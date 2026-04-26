@php
    $imageMedia = collect([]);
    $featuredImage = $content->featuredImageUrl();

    if (filled($featuredImage)) {
        $imageMedia = $imageMedia->push([
            'url' => $featuredImage,
            'name' => $content->title,
        ]);
    }

    $imageMedia = $imageMedia->concat(
        $content->getMedia('attachments')
            ->filter(fn ($media) => str_starts_with((string) $media->mime_type, 'image/'))
            ->map(fn ($media) => [
                'url' => $media->getUrl(),
                'name' => $media->name,
            ])
    )
        ->unique('url')
        ->values();

    $downloadMedia = $content->attachmentItems();
    $sliderId = 'content-slider-'.$content->getKey();
@endphp

<article class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950">
  <div class="grid gap-0 lg:grid-cols-[1.05fr_0.95fr]">
    <div class="border-b border-slate-200 p-8 dark:border-slate-800 lg:border-b-0 lg:border-r lg:p-10">
      <div class="flex flex-wrap items-center gap-3 text-sm">
        @if ($content->category)
          <span class="rounded-full bg-indigo-100 px-3 py-1 font-medium text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">{{ $content->category->name }}</span>
        @endif
        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ ucfirst($content->content_type) }}</span>
      </div>

      <h2 class="mt-5 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $content->title }}</h2>

      @if (filled($content->summary))
        <p class="mt-4 text-lg leading-8 text-slate-600 dark:text-slate-400">{{ $content->summary }}</p>
      @endif

      @if (filled($content->body))
        <div class="mt-6 space-y-4 text-base leading-8 text-slate-700 dark:text-slate-300">
          {!! $content->body !!}
        </div>
      @endif

      @if ($content->blocks->isNotEmpty())
        <div class="mt-8 space-y-5">
          @foreach ($content->blocks as $block)
            <section class="rounded-3xl bg-slate-50 p-6 dark:bg-slate-900/70">
              @if (filled($block->title))
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $block->title }}</h3>
              @endif
              @if (filled($block->body))
                <div class="mt-3 space-y-3 text-base leading-7 text-slate-600 dark:text-slate-400">
                  {!! $block->body !!}
                </div>
              @endif
            </section>
          @endforeach
        </div>
      @endif

      @if (collect($downloadMedia)->isNotEmpty())
        <div class="mt-8 border-t border-slate-200 pt-6 dark:border-slate-800">
          <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-900 dark:text-white">Attachments</h3>
          <div class="mt-4 flex flex-wrap gap-3">
            @foreach ($downloadMedia as $media)
              <a href="{{ $media['url'] }}" target="_blank" rel="noreferrer" class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-300 dark:hover:border-indigo-500 dark:hover:text-indigo-300">{{ $media['name'] }}</a>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    <div class="relative bg-slate-100 dark:bg-slate-900/50">
      @if ($imageMedia->isNotEmpty())
        <div data-media-slider id="{{ $sliderId }}" class="relative h-full min-h-[320px] overflow-hidden lg:min-h-[100%]">
          @foreach ($imageMedia as $mediaIndex => $media)
            <div data-slide class="{{ $mediaIndex === 0 ? 'opacity-100' : 'opacity-0' }} absolute inset-0 transition-opacity duration-500">
              <img src="{{ $media['url'] }}" alt="{{ $media['name'] }}" class="h-full w-full object-cover" />
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/35 via-transparent to-transparent"></div>
            </div>
          @endforeach

          @if ($imageMedia->count() > 1)
            <button type="button" data-prev class="absolute left-4 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/20 p-3 text-white backdrop-blur hover:bg-white/30">❮</button>
            <button type="button" data-next class="absolute right-4 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/20 p-3 text-white backdrop-blur hover:bg-white/30">❯</button>
            <div class="absolute bottom-6 left-1/2 z-10 flex -translate-x-1/2 gap-2">
              @foreach ($imageMedia as $mediaIndex => $media)
                <button type="button" data-dot class="h-3 w-3 rounded-full {{ $mediaIndex === 0 ? 'bg-white' : 'bg-white/50' }}"></button>
              @endforeach
            </div>
          @endif
        </div>
      @else
        <div class="flex h-full min-h-[320px] items-center justify-center px-8 py-12 text-center">
          <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Media ready</p>
            <p class="mt-3 text-base leading-7 text-slate-600 dark:text-slate-400">Add a featured image or attachment images to this content entry to show a visual gallery here.</p>
          </div>
        </div>
      @endif
    </div>
  </div>
</article>
