<!-- Header (Split) -->
<header class="w-full bg-white border-b border-slate-200">
  @php $siteMenus = collect($siteMenus ?? []); @endphp
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="flex items-center justify-between py-5">
      <div class="flex items-center gap-6">
        <a href="/" class="text-xl font-bold text-slate-900">{{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}</a>
        <nav class="hidden md:flex items-center gap-4">
          @foreach($siteMenus as $menu)
            @php $items = collect($menu['items'] ?? []); $href = $menu['href'] ?? '#'; @endphp
            @if($items->isNotEmpty())
              <div class="relative group">
                <a href="{{ $href }}" class="text-sm font-medium text-slate-700 hover:text-indigo-600">{{ $menu['title'] }}</a>
                <div class="invisible absolute left-0 top-full mt-3 w-64 rounded-lg bg-white p-2 opacity-0 shadow-lg transition-all duration-200 group-hover:visible group-hover:opacity-100">
                  @foreach($items as $item)
                    <a href="{{ $item['href'] ?: '#' }}" class="block px-3 py-2 text-sm hover:bg-slate-100">{{ $item['title'] }}</a>
                  @endforeach
                </div>
              </div>
            @else
              <a href="{{ $href }}" class="text-sm font-medium text-slate-700 hover:text-indigo-600">{{ $menu['title'] }}</a>
            @endif
          @endforeach
        </nav>
      </div>

      <div class="flex items-center gap-4">
        <div class="hidden md:flex items-center gap-3">
          <a href="#" class="text-sm text-slate-600 hover:text-slate-900">Login</a>
          <a href="#" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Get Started</a>
        </div>

        <button class="md:hidden rounded-lg p-2 hover:bg-slate-100">☰</button>
      </div>
    </div>
  </div>
</header>
