<!-- Header -->
<header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur border-b border-slate-200 transition-colors duration-200 dark:bg-slate-950/90 dark:border-slate-800">
  @php
    $siteMenus = collect($siteMenus ?? []);
  @endphp
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="flex h-16 items-center justify-between">
      <a href="/" class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}</a>

      <nav class="hidden items-center gap-8 md:flex">
        <a href="{{ route('home') }}" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400">Home</a>

        @foreach ($siteMenus as $menu)
          @php
            $menuItems = collect($menu['items'] ?? []);
          @endphp

          @if ($menuItems->isNotEmpty())
            <div class="relative group">
              <button class="flex items-center gap-2 text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400">
                {{ $menu['title'] }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div class="invisible absolute left-0 top-full mt-3 w-72 rounded-2xl border border-slate-200 bg-white p-2 opacity-0 shadow-xl transition-all duration-200 group-hover:visible group-hover:opacity-100 dark:border-slate-700 dark:bg-slate-800">
                @foreach ($menuItems as $item)
                  @php
                    $children = collect($item['children'] ?? []);
                  @endphp

                  @if ($children->isNotEmpty())
                    <div class="rounded-xl px-4 py-3">
                      <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">{{ $item['title'] }}</p>
                      <div class="mt-2 space-y-1">
                        @foreach ($children as $child)
                          <a href="{{ $child['href'] ?: '#' }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700">{{ $child['title'] }}</a>
                        @endforeach
                      </div>
                    </div>
                  @elseif (filled($item['href'] ?? null))
                    <a href="{{ $item['href'] }}" class="block rounded-xl px-4 py-3 text-sm hover:bg-slate-100 dark:hover:bg-slate-700">{{ $item['title'] }}</a>
                  @endif
                @endforeach
              </div>
            </div>
          @endif
        @endforeach
      </nav>

      <div class="flex items-center gap-4">
        <button id="themeToggle" class="rounded-full bg-slate-100 p-2 text-slate-600 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
          <svg id="themeToggleLightIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
          </svg>
          <svg id="themeToggleDarkIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block dark:hidden" viewBox="0 0 20 20" fill="currentColor">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
          </svg>
        </button>

        @auth
        <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
          @csrf
          <button type="submit" class="rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-indigo-700">Logout</button>
        </form>
        @else
        <a href="/login" class="hidden rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-indigo-700 md:inline-flex">Login</a>
        @endauth

        <button id="menuBtn" class="md:hidden rounded-lg p-2 hover:bg-slate-100 dark:hover:bg-slate-800">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobileMenu" class="hidden border-t border-slate-200 bg-white md:hidden dark:border-slate-800 dark:bg-slate-900">
    <div class="space-y-2 px-6 py-4">
      <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-800">Home</a>

      @foreach ($siteMenus as $menu)
        @php
          $menuItems = collect($menu['items'] ?? []);
        @endphp

        @if ($menuItems->isNotEmpty())
          <details class="rounded-lg">
            <summary class="cursor-pointer list-none rounded-lg px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-800">{{ $menu['title'] }}</summary>
            <div class="mt-2 ml-3 space-y-1">
              @foreach ($menuItems as $item)
                @php
                  $children = collect($item['children'] ?? []);
                @endphp

                @if ($children->isNotEmpty())
                  <details class="rounded-lg">
                    <summary class="cursor-pointer list-none rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-800">{{ $item['title'] }}</summary>
                    <div class="mt-1 ml-3 space-y-1">
                      @foreach ($children as $child)
                        <a href="{{ $child['href'] ?: '#' }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-800">{{ $child['title'] }}</a>
                      @endforeach
                    </div>
                  </details>
                @elseif (filled($item['href'] ?? null))
                  <a href="{{ $item['href'] }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-800">{{ $item['title'] }}</a>
                @endif
              @endforeach
            </div>
          </details>
        @endif
      @endforeach
      @auth
      <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="inline-flex w-full justify-center rounded-full bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Logout</button>
      </form>
      @else
      <a href="/login" class="mt-2 inline-flex w-full justify-center rounded-full bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Login</a>
      @endauth
    </div>
  </div>
</header>