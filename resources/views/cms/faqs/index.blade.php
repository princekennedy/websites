<x-layouts.app title="FAQs" eyebrow="CMS Knowledge Base" heading="Frequently asked questions" subheading="Maintain trusted answers for high-frequency SRHR questions surfaced in the Android app.">
    @if (auth()->user()?->hasCmsPermission('cms.manage.faqs'))
        <x-slot:headerAction>
            <a href="{{ route('cms.faqs.create') }}" class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">New FAQ</a>
        </x-slot:headerAction>
    @endif

    <div class="space-y-4">
        @forelse ($faqs as $faq)
            <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-3xl">
                        <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-stone-400">
                            <span>{{ $faq->category?->name ?? 'Unassigned' }}</span>
                            <span class="rounded-full border border-white/10 px-3 py-1">{{ $faq->audience }}</span>
                            <span class="rounded-full border border-white/10 px-3 py-1">{{ $faq->is_published ? 'Published' : 'Draft' }}</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-semibold text-white">{{ $faq->question }}</h3>
                        <p class="mt-2 text-sm text-stone-400">{{ \Illuminate\Support\Str::limit(strip_tags($faq->answer), 180) }}</p>
                    </div>

                    @if (auth()->user()?->hasCmsPermission('cms.manage.faqs'))
                        <div class="flex gap-3">
                            <a href="{{ route('cms.faqs.edit', $faq) }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-emerald-300">Edit</a>
                            <form method="POST" action="{{ route('cms.faqs.destroy', $faq) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-rose-400/30 px-4 py-2 text-sm font-medium text-rose-200" onclick="return confirm('Delete this FAQ entry?');">Delete</button>
                            </form>
                        </div>
                    @else
                        <span class="text-sm font-medium text-stone-400">Read only</span>
                    @endif
                </div>
            </article>
        @empty
            <article class="rounded-3xl border border-dashed border-white/10 bg-white/5 p-10 text-center text-stone-400">
                No FAQ entries yet.
            </article>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $faqs->links() }}
    </div>
</x-layouts.app>