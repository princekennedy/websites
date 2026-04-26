<x-layouts.admin title="{{ config('app.name', 'Sample Platform') }} CMS">
<!-- Header -->
<header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur border-b border-slate-200 transition-colors duration-200 dark:bg-slate-950/90 dark:border-slate-800">
  @php
    $siteMenus = collect($siteMenus ?? []);
  @endphp
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="flex h-16 items-center justify-between">
      <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ env("APP_NAME") }}</a>

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
        @if(auth()->user()->hasAdminCmsRole())
        <a href="/dashboard" class="hidden rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-indigo-700 md:inline-flex">Manage</a>
        @endif
        @else
        <a href="{{ route('register') }}" class="hidden rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-600 dark:hover:bg-slate-800 md:inline-flex">Sign up</a>
        <a href="{{ route('login') }}" class="hidden rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-indigo-700 md:inline-flex">Login</a>
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
      <a href="{{ route('register') }}" class="mt-2 inline-flex w-full justify-center rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Sign up</a>
      <a href="{{ route('login') }}" class="inline-flex w-full justify-center rounded-full bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Login</a>
      @endauth
    </div>
  </div>
</header>

<section class="relative overflow-hidden bg-gradient-to-br from-slate-100 via-white to-emerald-50 py-16 dark:from-slate-950 dark:via-slate-900 dark:to-slate-900">
  <div class="absolute inset-x-0 top-0 h-72 bg-[radial-gradient(circle_at_top_right,_rgba(79,70,229,0.18),_transparent_38%),radial-gradient(circle_at_left,_rgba(16,185,129,0.16),_transparent_32%)] dark:bg-[radial-gradient(circle_at_top_right,_rgba(129,140,248,0.16),_transparent_38%),radial-gradient(circle_at_left,_rgba(52,211,153,0.12),_transparent_32%)]"></div>

  <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
    <div class="grid items-center gap-10 lg:grid-cols-[1.15fr_0.85fr]">
      <div>
        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-600 dark:text-indigo-400">Website CMS</p>
        <h1 class="mt-4 max-w-3xl text-4xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-5xl">Manage websites, content, and publishing from one admin workspace.</h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600 dark:text-slate-300">This admin portal gives your team a single place to update pages, menus, service listings, quizzes, FAQs, and site settings while keeping login and sign-up flows available for users who need access.</p>

        <div class="mt-8 flex flex-col gap-4 sm:flex-row">
          @auth
            @if(auth()->user()->hasAdminCmsRole())
              <a href="/dashboard" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-700">Open CMS dashboard</a>
            @else
              <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-700">Return to website</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="inline-flex w-full items-center justify-center rounded-full border border-slate-300 px-6 py-3.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 sm:w-auto">Sign out</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-700">Login to CMS</a>
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-6 py-3.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Create an account</a>
          @endauth
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-3">
          <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm backdrop-blur dark:border-white/10 dark:bg-white/5">
            <p class="text-3xl font-bold text-slate-900 dark:text-white">1</p>
            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">Central CMS</p>
            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Manage multiple website sections from one consistent interface.</p>
          </div>
          <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm backdrop-blur dark:border-white/10 dark:bg-white/5">
            <p class="text-3xl font-bold text-slate-900 dark:text-white">24/7</p>
            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">Publishing control</p>
            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Keep public information fresh with a workflow built for regular content updates.</p>
          </div>
          <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm backdrop-blur dark:border-white/10 dark:bg-white/5">
            <p class="text-3xl font-bold text-slate-900 dark:text-white">Role-based</p>
            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">Secure access</p>
            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Admins can manage content while standard users keep access to sign in and register.</p>
          </div>
        </div>
      </div>

      <div class="rounded-[2rem] border border-slate-200 bg-white/90 p-8 shadow-2xl shadow-slate-200/60 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90 dark:shadow-none">
        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-600 dark:text-emerald-400">What you can manage</p>
        <div class="mt-6 space-y-4">
          <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-800/50">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Website content</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Maintain categories, articles, FAQs, quizzes, and service center information for each website experience.</p>
          </div>
          <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-800/50">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Navigation and branding</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Update menus, homepage sliders, and configurable site settings without redeploying the application.</p>
          </div>
          <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-800/50">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">User access</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Use the same authentication system for admin logins and new account registration so user onboarding stays simple.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-20">
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-3xl text-center">
      <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-600 dark:text-indigo-400">Core workflow</p>
      <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl">A simple path from account access to website management.</h2>
      <p class="mt-4 text-lg leading-8 text-slate-600 dark:text-slate-300">The page now explains what this CMS is for, who should log in, and how new users can sign up before they start managing website content.</p>
    </div>

    <div class="mt-14 grid gap-6 lg:grid-cols-3">
      <div class="rounded-[1.75rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-lg font-bold text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">01</div>
        <h3 class="mt-6 text-xl font-semibold text-slate-900 dark:text-white">Access the account flow</h3>
        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">Visitors can choose login if they already have credentials or sign up if they need a new account.</p>
      </div>

      <div class="rounded-[1.75rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-lg font-bold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">02</div>
        <h3 class="mt-6 text-xl font-semibold text-slate-900 dark:text-white">Manage website data</h3>
        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">Authorized CMS users can update content structures, navigation, service details, and public-facing information.</p>
      </div>

      <div class="rounded-[1.75rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-lg font-bold text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">03</div>
        <h3 class="mt-6 text-xl font-semibold text-slate-900 dark:text-white">Keep public sites current</h3>
        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">Teams can publish accurate SRHR content and maintain website quality without changing application code.</p>
      </div>
    </div>
  </div>
</section>

<section class="pb-20">
  <div class="mx-auto max-w-7xl px-6 lg:px-8">
    <div class="rounded-[2rem] bg-slate-900 px-8 py-12 text-white shadow-xl dark:border dark:border-slate-800 dark:bg-slate-950 sm:px-12">
      <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-2xl">
          <p class="text-sm font-semibold uppercase tracking-[0.35em] text-emerald-300">Ready to continue?</p>
          <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Use the CMS to manage websites with a clear login and sign-up path.</h2>
          <p class="mt-4 text-base leading-7 text-slate-300">This page is intended to orient users before they enter the admin area and direct them to the right action based on whether they already have an account.</p>
        </div>

        <div class="flex flex-col gap-4 sm:flex-row">
          @auth
            @if(auth()->user()->hasAdminCmsRole())
              <a href="/dashboard" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">Go to dashboard</a>
            @endif
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Visit public site</a>
          @else
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">Login</a>
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Sign up</a>
          @endauth
        </div>
      </div>
    </div>
  </div>
</section>

</x-layouts.admin>