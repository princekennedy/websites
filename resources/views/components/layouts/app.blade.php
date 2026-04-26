<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'CMS' }} | {{ config('app.name', 'Sample Platform') }}</title>
        <script>
            (() => {
                const storedTheme = window.localStorage.getItem('srhr-cms-theme') || 'light';
                document.documentElement.classList.toggle('dark', storedTheme === 'dark');
            })();
        </script>
        @unless (app()->runningUnitTests())
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endunless
    </head>
    <body class="cms-shell h-screen overflow-hidden transition-colors">
        <div class="relative z-10 flex h-screen flex-col">
            <header class="shrink-0 z-50 px-4 py-4 sm:px-6 lg:px-8">
                <div class="cms-panel cms-gradient-card mx-auto max-w-[96rem] px-5 py-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 via-cyan-500 to-orange-400 text-base font-black tracking-[0.2em] text-white shadow-lg shadow-orange-200/40 dark:shadow-none">CMS</div>
                            <div>
                                <p class="cms-kicker text-[0.72rem] font-semibold uppercase tracking-[0.35em]">{{ config('app.name', 'Sample Platform') }}</p>
                                <h1 class="cms-title mt-1 text-xl font-bold tracking-tight">CMS Workspace</h1>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
                            @php
                                $workspaceWebsites = auth()->user()?->websites()->orderBy('name')->get() ?? collect();
                            @endphp
                            @if ($workspaceWebsites->isNotEmpty())
                                <form method="POST" action="{{ route('cms.websites.switch', auth()->user()?->currentWebsite ?? $workspaceWebsites->first()) }}" class="flex items-center gap-2">
                                    @csrf
                                    <label for="website-switcher" class="sr-only">Active website</label>
                                    <select
                                        id="website-switcher"
                                        class="rounded-full border border-slate-200/80 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-sky-200 focus:border-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-white/10 dark:bg-white/5 dark:text-stone-200 dark:focus:ring-sky-500/20"
                                        onchange="if (this.value) { this.form.action = this.value; this.form.submit(); }"
                                    >
                                        @foreach ($workspaceWebsites as $workspaceWebsite)
                                            <option value="{{ route('cms.websites.switch', $workspaceWebsite) }}" @selected(auth()->user()?->current_website_id === $workspaceWebsite->id)>
                                                {{ $workspaceWebsite->name }}@if(auth()->user()?->current_website_id === $workspaceWebsite->id) (active)@endif
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif
                            <a href="{{ route('cms.websites.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200/80 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:-translate-y-0.5 hover:border-sky-200 hover:text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-stone-200 dark:hover:text-white">Websites</a>
                            <div class="cms-glass rounded-2xl px-4 py-2.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ auth()->user()?->name }}</p>
                                    @foreach (auth()->user()?->getRoleNames() ?? [] as $role)
                                        <span class="cms-chip cms-chip-accent px-2.5 py-1 text-[0.68rem] font-semibold uppercase tracking-[0.16em]">{{ $role }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <button type="button" data-theme-toggle class="inline-flex items-center justify-center rounded-full border border-slate-200/80 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:-translate-y-0.5 hover:border-sky-200 hover:text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-stone-200 dark:hover:text-white">
                                Toggle theme
                            </button>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <input type="hidden" name="redirect_to" value="login">
                                <button type="submit" class="inline-flex items-center justify-center rounded-full border border-rose-200/80 bg-white/80 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:-translate-y-0.5 hover:border-rose-300 hover:text-rose-700 dark:border-rose-400/20 dark:bg-white/5 dark:text-rose-200 dark:hover:text-rose-100">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="mx-auto grid w-full max-w-[96rem] flex-1 min-h-0 items-start gap-5 px-4 pb-6 sm:px-6 lg:grid-cols-[290px_1fr] lg:px-8 overflow-y-auto lg:overflow-hidden">
                <aside class="cms-panel cms-gradient-card h-fit px-5 py-6 lg:h-full lg:overflow-y-auto">
                    <div class="mb-6 rounded-[1.2rem] border border-slate-200/80 bg-white/70 px-4 py-4 dark:border-white/10 dark:bg-white/5">
                        <p class="cms-kicker text-[0.7rem] font-semibold uppercase tracking-[0.32em]">Navigation</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-stone-300">Manage website workspaces, content, navigation, sliders, and runtime configuration from one workspace.</p>
                    </div>

                    <nav class="space-y-2">
                        @php
                            $navigation = [
                                ['label' => 'Dashboard', 'route' => 'cms.dashboard'],
                                ['label' => 'Websites', 'route' => 'cms.websites.index'],
                                ['label' => 'Content', 'route' => 'cms.contents.index'],
                                ['label' => 'Content Categories', 'route' => 'cms.categories.index'],
                                ['label' => 'Menus', 'route' => 'cms.menus.index'],
                                ['label' => 'Sliders', 'route' => 'cms.sliders.index'],
                                ['label' => 'Settings', 'route' => 'cms.settings.index'],
                            ];
                        @endphp

                        @foreach ($navigation as $item)
                            <a
                                href="{{ route($item['route']) }}"
                                class="cms-nav-link flex items-center justify-between px-4 py-3 text-sm font-medium {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') ? 'cms-nav-link-active text-slate-900 dark:text-white' : 'text-slate-700 dark:text-stone-300' }}"
                            >
                                <span>{{ $item['label'] }}</span>
                                <span class="text-[0.68rem] uppercase tracking-[0.3em] text-slate-400 dark:text-stone-500">Open</span>
                            </a>
                        @endforeach
                    </nav>
                </aside>

                <main class="min-w-0 pb-6 h-fit lg:h-full lg:overflow-y-auto">
                    <div class="mx-auto max-w-7xl">
                        <header class="cms-panel cms-gradient-card mb-6 px-6 py-6 sm:px-8">
                            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                                <div>
                                    <p class="cms-kicker text-xs font-semibold uppercase tracking-[0.35em]">{{ $eyebrow ?? 'Content Management System' }}</p>
                                    <h2 class="cms-heading mt-2 text-3xl font-bold tracking-tight">{{ $heading ?? 'CMS' }}</h2>
                                    @if (! empty($subheading ?? null))
                                        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-stone-300">{{ $subheading }}</p>
                                    @endif
                                </div>

                                @isset($headerAction)
                                    <div class="shrink-0">
                                        {{ $headerAction }}
                                    </div>
                                @endisset
                            </div>
                        </header>

                        @if (session('status'))
                            <div class="cms-panel mb-6 border border-emerald-200/70 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-100">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="cms-panel mb-6 border border-rose-200/80 bg-rose-50/90 px-4 py-3 text-sm text-rose-700 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-100">
                                <p class="font-semibold">Please correct the highlighted fields.</p>
                                <ul class="mt-2 space-y-1 text-rose-700/90 dark:text-rose-50/90">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <script>
            (() => {
                const toggle = document.querySelector('[data-theme-toggle]');
                if (!toggle) {
                    return;
                }

                toggle.addEventListener('click', () => {
                    const isDark = document.documentElement.classList.toggle('dark');
                    window.localStorage.setItem('srhr-cms-theme', isDark ? 'dark' : 'light');
                });
            })();
        </script>
    </body>
</html>