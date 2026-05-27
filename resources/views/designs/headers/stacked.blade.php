<!-- Header (Stacked) -->
<header class="w-full bg-slate-50">
  @php $siteMenus = collect($siteMenus ?? []); @endphp
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="py-4">
      <div class="flex items-center justify-between">
        <a href="/" class="text-lg font-semibold text-slate-900">{{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}</a>
        <div class="hidden md:flex items-center gap-4">
          @foreach($siteMenus as $menu)
            @php $href = $menu['href'] ?? '#'; $items = collect($menu['items'] ?? []); @endphp
            @if($items->isNotEmpty())
              <details class="group relative">
                <summary class="cursor-pointer list-none text-sm font-medium">{{ $menu['title'] }}</summary>
                <div class="mt-2 rounded-lg bg-white p-2 shadow-sm">
                  @foreach($items as $item)
                    <a href="{{ $item['href'] ?: '#' }}" class="block px-3 py-2 text-sm hover:bg-slate-100">{{ $item['title'] }}</a>
                  @endforeach
                </div>
              </details>
            @else
              <a href="{{ $href }}" class="text-sm font-medium text-slate-700">{{ $menu['title'] }}</a>
            @endif
          @endforeach
        </div>
      </div>

      <div class="mt-3 border-t border-slate-200 pt-3">
        <nav class="flex gap-4 text-sm text-slate-600">
          @foreach($siteMenus as $menu)
            <a href="{{ $menu['href'] ?? '#' }}" class="hover:text-slate-900">{{ $menu['title'] }}</a>
          @endforeach
        </nav>
      </div>
    </div>
  </div>
</header>
