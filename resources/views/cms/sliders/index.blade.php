<x-layouts.app title="Sliders" eyebrow="CMS Homepage" heading="Hero sliders" subheading="Manage homepage hero slides, captions, and call-to-action buttons from the backend.">
    @if (auth()->user()?->hasCmsPermission('cms.manage.sliders'))
        <x-slot:headerAction>
            <a href="{{ route('cms.sliders.create') }}" class="inline-flex items-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">New slide</a>
        </x-slot:headerAction>
    @endif

    <div class="space-y-4">
        @forelse ($sliders as $slider)
            <article class="cms-card cms-gradient-card cms-card-hover p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="flex max-w-4xl gap-5">
                        <div class="hidden w-60 shrink-0 overflow-hidden rounded-3xl border border-slate-200/70 bg-white/70 p-2 dark:border-white/10 dark:bg-slate-950/30 md:block">
                            <img src="{{ $slider->imageUrl() ?: asset('seed/hero-slide-1.svg') }}" alt="{{ $slider->title }}" class="h-36 w-full rounded-2xl object-cover">
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-stone-400">
                                <span>{{ $slider->kicker ?: 'Homepage slide' }}</span>
                                <span class="cms-chip px-3 py-1">{{ $slider->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                            <h3 class="cms-heading mt-3 text-2xl font-semibold">{{ $slider->title }}</h3>
                            <p class="mt-2 text-sm text-slate-600 dark:text-stone-300">{{ $slider->caption ?: 'No caption added yet.' }}</p>
                            <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-500 dark:text-stone-400">
                                <span>Slug: {{ $slider->slug }}</span>
                                <span>Layout: {{ $slider->normalizedLayoutType() }}</span>
                                <span>Sort order: {{ $slider->sort_order }}</span>
                                <span>Buttons: {{ collect([$slider->primary_button_text, $slider->secondary_button_text])->filter()->count() }}</span>
                            </div>
                        </div>
                    </div>

                    @if (auth()->user()?->hasCmsPermission('cms.manage.sliders'))
                        <div class="flex gap-2">
                            <a href="{{ route('cms.sliders.edit', $slider) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-200 hover:text-slate-900 dark:bg-white/5 dark:text-stone-300 dark:hover:bg-white/10 dark:hover:text-white" title="Edit">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                            </a>
                            <form method="POST" action="{{ route('cms.sliders.destroy', $slider) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-50 text-rose-600 transition hover:bg-rose-100 hover:text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/20" title="Delete" onclick="return confirm('Delete this slide?');">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <article class="cms-empty-state p-10 text-center">
                No slider entries yet.
            </article>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $sliders->links() }}
    </div>
</x-layouts.app>