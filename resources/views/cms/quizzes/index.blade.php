<x-layouts.app title="Quizzes" eyebrow="CMS Interactivity" heading="Quiz library" subheading="Manage learning quizzes and the structured questions consumed by the Android app.">
    @if (auth()->user()?->hasCmsPermission('cms.manage.quizzes'))
        <x-slot:headerAction>
            <a href="{{ route('cms.quizzes.create') }}" class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">New quiz</a>
        </x-slot:headerAction>
    @endif

    <div class="grid gap-4 xl:grid-cols-2">
        @forelse ($quizzes as $quiz)
            <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-stone-400">
                            <span>{{ $quiz->audience }}</span>
                            <span class="rounded-full border border-white/10 px-3 py-1">{{ $quiz->status }}</span>
                        </div>
                        <h3 class="mt-3 text-2xl font-semibold text-white">{{ $quiz->title }}</h3>
                        <p class="mt-2 text-sm text-stone-400">{{ $quiz->summary ?: 'No summary yet.' }}</p>
                    </div>
                    <span class="rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] text-stone-300">{{ $quiz->questions_count }} questions</span>
                </div>

                @if (auth()->user()?->hasCmsPermission('cms.manage.quizzes'))
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('cms.quizzes.edit', $quiz) }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-emerald-300">Edit</a>
                        <form method="POST" action="{{ route('cms.quizzes.destroy', $quiz) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full border border-rose-400/30 px-4 py-2 text-sm font-medium text-rose-200" onclick="return confirm('Delete this quiz?');">Delete</button>
                        </form>
                    </div>
                @else
                    <p class="mt-6 text-sm font-medium text-stone-400">Read only</p>
                @endif
            </article>
        @empty
            <article class="rounded-3xl border border-dashed border-white/10 bg-white/5 p-10 text-center text-stone-400 xl:col-span-2">
                No quizzes yet.
            </article>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $quizzes->links() }}
    </div>
</x-layouts.app>