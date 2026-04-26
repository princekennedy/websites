<!-- Header (Editorial) -->
<header class="fixed top-0 left-0 right-0 z-50 border-b border-amber-200 bg-amber-50/95 backdrop-blur dark:border-amber-900 dark:bg-slate-950/95">
  @php
    $siteMenus = collect($siteMenus ?? []);
  @endphp
  <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6 lg:px-8">
    <a href="/" class="text-2xl font-semibold tracking-wide text-slate-900 dark:text-white">{{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}</a>

    <nav class="hidden items-center gap-8 md:flex">
      @foreach ($siteMenus as $menu)
        @if (filled($menu['href'] ?? null))
          <a href="{{ $menu['href'] }}" class="text-sm font-medium uppercase tracking-[0.14em] text-slate-700 transition hover:text-amber-700 dark:text-slate-300 dark:hover:text-amber-400">{{ $menu['title'] }}</a>
        @endif
      @endforeach
    </nav>

    <button id="menuBtn" class="md:hidden rounded-lg p-2 hover:bg-amber-100 dark:hover:bg-slate-800">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
    </button>
  </div>

  <div id="mobileMenu" class="hidden border-t border-amber-200 bg-amber-50 md:hidden dark:border-slate-800 dark:bg-slate-950">
    <div class="space-y-1 px-6 py-3">
      @foreach ($siteMenus as $menu)
        @if (filled($menu['href'] ?? null))
          <a href="{{ $menu['href'] }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-amber-100 dark:hover:bg-slate-800">{{ $menu['title'] }}</a>
        @endif
      @endforeach
    </div>
  </div>
</header>
