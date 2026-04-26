<x-layouts.app title="Dashboard" eyebrow="CMS Overview" heading="Delivery dashboard" subheading="This CMS manages websites, content, sliders, menus, and runtime settings, with `default` as the current live design baseline.">
    @if (auth()->user()?->hasCmsPermission('cms.manage.contents'))
        <x-slot:headerAction>
            <a href="{{ route('cms.contents.create') }}" class="inline-flex items-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">Create content</a>
        </x-slot:headerAction>
    @endif

    <section class="mb-6 grid gap-4 lg:grid-cols-[1.1fr_0.9fr]">
        <article class="cms-card cms-gradient-card p-6">
            <p class="cms-kicker text-xs font-semibold uppercase tracking-[0.35em]">Access mode</p>
            <h3 class="cms-heading mt-3 text-2xl font-semibold">{{ auth()->user()?->canManageAnyCmsModule() ? 'Management access enabled' : 'Read-only workspace' }}</h3>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-stone-300">
                {{ auth()->user()?->canManageAnyCmsModule() ? 'This account can create and update content, menus, and app settings based on assigned permissions.' : 'This account can review the dashboard, existing content, and navigation structure but cannot add or change records.' }}
            </p>
        </article>
        <article class="cms-card cms-gradient-card p-6">
            <p class="cms-kicker text-xs font-semibold uppercase tracking-[0.35em]">Quick actions</p>
            <div class="mt-4 flex flex-wrap gap-3">
                @if (auth()->user()?->hasCmsPermission('cms.manage.contents'))
                    <a href="{{ route('cms.contents.create') }}" class="rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-sm font-medium text-slate-700 transition hover:-translate-y-0.5 hover:border-sky-200 hover:text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-stone-200">New content</a>
                @endif
                @if (auth()->user()?->hasCmsPermission('cms.manage.menus'))
                    <a href="{{ route('cms.menus.index') }}" class="rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-sm font-medium text-slate-700 transition hover:-translate-y-0.5 hover:border-sky-200 hover:text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-stone-200">Manage menus</a>
                @endif
                @if (auth()->user()?->hasCmsPermission('cms.manage.settings'))
                    <a href="{{ route('cms.settings.index') }}" class="rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-sm font-medium text-slate-700 transition hover:-translate-y-0.5 hover:border-sky-200 hover:text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-stone-200">Update settings</a>
                @endif
                @unless (auth()->user()?->canManageAnyCmsModule())
                    <span class="rounded-full border border-slate-200 bg-white/70 px-4 py-2 text-sm font-medium text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-stone-400">No write actions available</span>
                @endunless
            </div>
        </article>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="cms-card cms-gradient-card p-5">
            <p class="text-sm text-slate-500 dark:text-stone-400">Websites</p>
            <p class="cms-stat-number mt-3 text-4xl font-semibold">{{ $stats['websites'] }}</p>
        </article>
        <article class="cms-card cms-gradient-card p-5">
            <p class="text-sm text-slate-500 dark:text-stone-400">Categories</p>
            <p class="cms-stat-number mt-3 text-4xl font-semibold">{{ $stats['categories'] }}</p>
        </article>
        <article class="cms-card cms-gradient-card p-5">
            <p class="text-sm text-slate-500 dark:text-stone-400">Content Entries</p>
            <p class="cms-stat-number mt-3 text-4xl font-semibold">{{ $stats['contents'] }}</p>
        </article>
        <article class="cms-card cms-gradient-card p-5">
            <p class="text-sm text-slate-500 dark:text-stone-400">Sliders</p>
            <p class="cms-stat-number mt-3 text-4xl font-semibold">{{ $stats['sliders'] }}</p>
        </article>
        <article class="cms-card cms-gradient-card p-5">
            <p class="text-sm text-slate-500 dark:text-stone-400">Menus</p>
            <p class="cms-stat-number mt-3 text-4xl font-semibold">{{ $stats['menus'] }}</p>
        </article>
        <article class="cms-card cms-gradient-card p-5">
            <p class="text-sm text-slate-500 dark:text-stone-400">Menu Items</p>
            <p class="cms-stat-number mt-3 text-4xl font-semibold">{{ $stats['menuItems'] }}</p>
        </article>
        <article class="cms-card cms-gradient-card p-5">
            <p class="text-sm text-slate-500 dark:text-stone-400">Settings</p>
            <p class="cms-stat-number mt-3 text-4xl font-semibold">{{ $stats['settings'] }}</p>
        </article>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
        <article class="cms-card cms-gradient-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="cms-heading text-lg font-semibold">Recent content</h3>
                    <p class="text-sm text-slate-500 dark:text-stone-400">The last edited entries ready for mobile rendering and publishing workflows.</p>
                </div>
                <a href="{{ route('cms.contents.index') }}" class="text-sm font-semibold text-sky-600 hover:text-sky-700 dark:text-sky-300 dark:hover:text-sky-200">View all</a>
            </div>

            <div class="cms-table-wrap mt-5">
                <table class="min-w-full divide-y divide-slate-200/70 text-left text-sm dark:divide-white/10">
                    <thead class="bg-white/50 text-slate-500 dark:bg-white/5 dark:text-stone-400">
                        <tr>
                            <th class="px-4 py-3 font-medium">Title</th>
                            <th class="px-4 py-3 font-medium">Category</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse ($recentContents as $content)
                            <tr class="bg-white/70 text-slate-700 dark:bg-slate-950/30 dark:text-stone-200">
                                <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $content->title }}</td>
                                <td class="px-4 py-3">{{ $content->category?->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3">
                                    <span class="cms-chip px-3 py-1 text-xs uppercase tracking-[0.2em]">{{ $content->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-500 dark:text-stone-400">{{ $content->updated_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-500 dark:text-stone-400">No content entries yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="cms-card cms-gradient-card p-6">
            <h3 class="cms-heading text-lg font-semibold">Module highlights</h3>
            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-700 dark:text-stone-300">
                @foreach ($moduleHighlights as $highlight)
                    <div class="cms-card bg-white/65 px-4 py-3 dark:bg-slate-950/30">
                        <div class="flex items-center justify-between gap-4">
                            <p class="font-medium text-slate-900 dark:text-white">{{ $highlight['label'] }}</p>
                            <span class="cms-chip cms-chip-accent px-3 py-1 text-xs uppercase tracking-[0.2em]">{{ $highlight['count'] }}</span>
                        </div>
                        <p class="mt-2 text-slate-500 dark:text-stone-400">{{ $highlight['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
</x-layouts.app>