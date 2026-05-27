<!-- Header (Centered) -->
<header class="w-full z-50 bg-gradient-to-r from-sky-600 to-indigo-600 text-white">
  @php $siteMenus = collect($siteMenus ?? []); @endphp
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="flex items-center justify-between py-6">
      <div class="flex-1 text-center">
        <a href="/" class="inline-block text-2xl font-extrabold tracking-tight">{{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}</a>
      </div>

      <nav class="hidden md:flex gap-6 absolute left-0 right-0 justify-center pointer-events-none">
        <div class="pointer-events-auto inline-flex items-center gap-6 bg-white/0">
          @foreach($siteMenus as $menu)
            @php $items = collect($menu['items'] ?? []); $href = $menu['href'] ?? '#'; @endphp
            @if($items->isNotEmpty())
              <div class="relative group">
                <a href="{{ $href }}" class="text-sm font-medium text-white hover:text-sky-200">{{ $menu['title'] }}</a>
                <div class="invisible absolute left-1/2 top-full -translate-x-1/2 mt-3 w-56 rounded-lg bg-white text-slate-900 p-2 opacity-0 shadow-lg transition-all duration-200 group-hover:visible group-hover:opacity-100">
                  @foreach($items as $item)
                    <a href="{{ $item['href'] ?: '#' }}" class="block px-3 py-2 text-sm hover:bg-slate-100">{{ $item['title'] }}</a>
                  @endforeach
                </div>
              </div>
            @else
              <a href="{{ $href }}" class="text-sm font-medium text-white hover:text-sky-200">{{ $menu['title'] }}</a>
            @endif
          @endforeach
        </div>
      </nav>

      <div class="flex items-center gap-4 ml-auto">
        <button class="rounded-full bg-white/10 p-2">⚙</button>
      </div>
    </div>
  </div>
</header>
