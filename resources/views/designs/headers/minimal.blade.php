<!-- Header (Minimal) -->
<header class="fixed top-0 left-0 right-0 z-50 border-b border-slate-200 bg-white transition-colors dark:border-slate-800 dark:bg-slate-950">
  @php
    $siteMenus = collect($siteMenus ?? []);
  @endphp
  <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6 lg:px-8">
    <a href="/" class="text-xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}</a>

    <nav class="hidden items-center gap-6 md:flex">
      @foreach ($siteMenus as $menu)
        @php
          $menuHref = $menu['href'] ?? null;
        @endphp
        @if (filled($menuHref))
          <a href="{{ $menuHref }}" class="text-sm text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">{{ $menu['title'] }}</a>
        @endif
      @endforeach
    </nav>

    <div class="flex items-center gap-3">
      <button id="themeToggle" class="rounded-md border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
        <svg id="themeToggleLightIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden dark:block" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
        <svg id="themeToggleDarkIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 block dark:hidden" viewBox="0 0 20 20" fill="currentColor"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" /></svg>
      </button>
      <button id="menuBtn" class="md:hidden rounded-md border border-slate-200 p-2 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
      </button>
    </div>
  </div>

  <div id="mobileMenu" class="hidden border-t border-slate-200 bg-white md:hidden dark:border-slate-800 dark:bg-slate-900">
    <div class="space-y-1 px-6 py-3">
      @foreach ($siteMenus as $menu)
        @php $menuHref = $menu['href'] ?? null; @endphp
        @if (filled($menuHref))
          <a href="{{ $menuHref }}" class="block rounded-md px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">{{ $menu['title'] }}</a>
        @endif
      @endforeach
    </div>
  </div>
</header>
