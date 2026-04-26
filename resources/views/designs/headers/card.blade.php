<!-- Header (Card) -->
<header class="fixed top-3 left-0 right-0 z-50">
  @php
    $siteMenus = collect($siteMenus ?? []);
  @endphp
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="rounded-2xl border border-slate-200 bg-white/95 px-4 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/95">
      <div class="flex h-14 items-center justify-between">
        <a href="/" class="text-lg font-semibold text-slate-900 dark:text-white">{{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}</a>

        <nav class="hidden items-center gap-5 md:flex">
          @foreach ($siteMenus as $menu)
            @if (filled($menu['href'] ?? null))
              <a href="{{ $menu['href'] }}" class="text-sm text-slate-600 transition hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400">{{ $menu['title'] }}</a>
            @endif
          @endforeach
        </nav>

        <button id="menuBtn" class="md:hidden rounded-lg p-2 hover:bg-slate-100 dark:hover:bg-slate-800">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
      </div>

      <div id="mobileMenu" class="hidden border-t border-slate-200 py-2 dark:border-slate-700 md:hidden">
        <div class="space-y-1">
          @foreach ($siteMenus as $menu)
            @if (filled($menu['href'] ?? null))
              <a href="{{ $menu['href'] }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-800">{{ $menu['title'] }}</a>
            @endif
          @endforeach
        </div>
      </div>
    </div>
  </div>
</header>
