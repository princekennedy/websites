<x-layouts.app title="Websites" eyebrow="CMS Tenancy" heading="Website workspaces" subheading="Create and switch between website tenants for isolated content, settings, and navigation management.">
    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <section class="cms-card cms-gradient-card p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="cms-kicker text-xs font-semibold uppercase tracking-[0.35em]">Your websites</p>
                    <h3 class="cms-heading mt-2 text-2xl font-semibold">Available workspaces</h3>
                </div>
                <span class="cms-chip px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]">{{ $websites->count() }} total</span>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($websites as $website)
                    <article class="cms-card bg-white/65 p-5 dark:bg-slate-950/30">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">
                                    <span>{{ $website->slug }}</span>
                                    @if ($currentWebsiteId === $website->id)
                                        <span class="cms-chip cms-chip-accent px-3 py-1">Current</span>
                                    @endif
                                </div>
                                <h4 class="cms-heading mt-3 text-xl font-semibold">{{ $website->name }}</h4>
                                <p class="mt-2 text-sm text-slate-500 dark:text-stone-400">
                                    Domain: {{ $website->domain ?: 'Not set' }}
                                </p>
                            </div>

                            @if ($currentWebsiteId !== $website->id)
                                <form method="POST" action="{{ route('cms.websites.switch', $website) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-sm font-medium text-slate-700 transition hover:-translate-y-0.5 hover:border-sky-200 hover:text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-stone-200">Switch</button>
                                </form>
                            @else
                                <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-100">Active workspace</span>
                            @endif
                        </div>
                    </article>
                @empty
                    <article class="cms-empty-state p-10 text-center">
                        No websites available yet.
                    </article>
                @endforelse
            </div>
        </section>

        <aside class="cms-card cms-gradient-card p-6">
            <p class="cms-kicker text-xs font-semibold uppercase tracking-[0.35em]">Create workspace</p>
            <h3 class="cms-heading mt-2 text-2xl font-semibold">Add a new website</h3>
            <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-stone-300">Each website gets its own categories, content, menus, FAQs, quizzes, services, and app settings.</p>

            <form method="POST" action="{{ route('cms.websites.store') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="name" class="text-sm font-medium text-slate-900 dark:text-stone-200">Website name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" class="cms-input mt-2" required>
                </div>

                <div>
                    <label for="domain" class="text-sm font-medium text-slate-900 dark:text-stone-200">Domain</label>
                    <input id="domain" name="domain" type="text" value="{{ old('domain') }}" class="cms-input mt-2" placeholder="optional-domain.test">
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">Create website</button>
            </form>
        </aside>
    </div>
</x-layouts.app>
