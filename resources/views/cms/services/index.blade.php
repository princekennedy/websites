<x-layouts.app title="Services" eyebrow="CMS Referrals" heading="Service directory" subheading="Manage youth-friendly facilities, referral points, and practical contact information for the app.">
    @if (auth()->user()?->hasCmsPermission('cms.manage.services'))
        <x-slot:headerAction>
            <a href="{{ route('cms.services.create') }}" class="inline-flex items-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">New service</a>
        </x-slot:headerAction>
    @endif

    <div class="grid gap-4 xl:grid-cols-2">
        @forelse ($services as $service)
            <article class="cms-card cms-gradient-card cms-card-hover p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">
                            <span>{{ $service->district ?: 'District pending' }}</span>
                            <span class="cms-chip px-3 py-1">{{ $service->is_featured ? 'Featured' : 'Standard' }}</span>
                        </div>
                        <h3 class="cms-heading mt-3 text-2xl font-semibold">{{ $service->name }}</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-stone-300">{{ $service->summary ?: 'No summary yet.' }}</p>
                    </div>
                    <span class="cms-chip px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $service->is_active ? 'text-sky-600 dark:text-sky-300' : 'text-slate-500 dark:text-stone-400' }}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
                </div>

                <div class="mt-4 space-y-2 text-sm text-slate-500 dark:text-stone-400">
                    <p>Category: {{ $service->category?->name ?? 'Unassigned' }}</p>
                    <p>Hours: {{ $service->service_hours ?: 'Not set' }}</p>
                    <p>Phone: {{ $service->contact_phone ?: 'Not set' }}</p>
                </div>

                @if (auth()->user()?->hasCmsPermission('cms.manage.services'))
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('cms.services.edit', $service) }}" class="rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-sm font-medium text-slate-700 transition hover:-translate-y-0.5 hover:border-sky-200 hover:text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-stone-200">Edit</a>
                        <form method="POST" action="{{ route('cms.services.destroy', $service) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full border border-rose-200 bg-white/80 px-4 py-2 text-sm font-medium text-rose-600 transition hover:-translate-y-0.5 hover:border-rose-300 hover:text-rose-700 dark:border-rose-400/30 dark:bg-white/5 dark:text-rose-200" onclick="return confirm('Delete this service entry?');">Delete</button>
                        </form>
                    </div>
                @else
                    <p class="mt-6 text-sm font-medium text-slate-500 dark:text-stone-400">Read only</p>
                @endif
            </article>
        @empty
            <article class="cms-empty-state p-10 text-center xl:col-span-2">
                No service listings yet.
            </article>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $services->links() }}
    </div>
</x-layouts.app>