<!-- Footer (Default) -->
@php
  $siteMenus = collect($siteMenus ?? []);
@endphp
<footer class="border-t border-slate-200 bg-white/90 transition-colors duration-200 dark:border-slate-800 dark:bg-slate-950/90">
  <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
    <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr_0.8fr_0.8fr]">
      <div>
        <a href="/" class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}</a>
        <p class="mt-4 max-w-md text-sm leading-7 text-slate-600 dark:text-slate-400">{{ data_get($publicSite ?? [], 'brand.message', 'Build websites and manage content with confidence.') }}</p>
      </div>

      <div>
        <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-900 dark:text-white">Explore</h2>
        <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-400">
          @foreach ($siteMenus->take(3) as $menu)
            @if (filled($menu['href'] ?? null))
              <a href="{{ $menu['href'] }}" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">{{ $menu['title'] }}</a>
            @endif
          @endforeach
        </div>
      </div>

      <div>
        <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-900 dark:text-white">Support</h2>
        <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-400">
          @if (filled(data_get($publicSite ?? [], 'support.email_href')))
            <a href="{{ data_get($publicSite, 'support.email_href') }}" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">{{ data_get($publicSite, 'support.email') }}</a>
          @endif
          @if (filled(data_get($publicSite ?? [], 'support.phone_href')))
            <a href="{{ data_get($publicSite, 'support.phone_href') }}" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">{{ data_get($publicSite, 'support.phone') }}</a>
          @endif
          <a href="{{ route('public.contents.index') }}" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">Content</a>
        </div>
      </div>

      <div>
        <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-900 dark:text-white">Account</h2>
        <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-400">
          <a href="{{ route('login') }}" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">Login</a>
          <a href="{{ route('register') }}" class="block transition hover:text-indigo-600 dark:hover:text-indigo-400">Get Started</a>
        </div>
      </div>
    </div>

    <div class="mt-10 flex flex-col gap-3 border-t border-slate-200 pt-6 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between">
      <p>&copy; {{ now()->year }} {{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}. All rights reserved.</p>
      <p>Default footer design</p>
    </div>
  </div>
</footer>
