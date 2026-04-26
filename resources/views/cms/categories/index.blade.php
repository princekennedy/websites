<x-layouts.app title="Categories" eyebrow="CMS Taxonomy" heading="Content categories" subheading="Organize SRHR topics for app navigation, discovery, and permissions-aware publishing.">
    @if (auth()->user()?->hasCmsPermission('cms.manage.categories'))
        <x-slot:headerAction>
            <a href="{{ route('cms.categories.create') }}" class="inline-flex items-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">New category</a>
        </x-slot:headerAction>
    @endif

    <div class="cms-table-wrap">
        <table class="min-w-full divide-y divide-slate-200/70 text-left text-sm dark:divide-white/10">
            <thead class="bg-white/50 text-slate-500 dark:bg-white/5 dark:text-stone-400">
                <tr>
                    <th class="px-5 py-4 font-medium">Name</th>
                    <th class="px-5 py-4 font-medium">Slug</th>
                    <th class="px-5 py-4 font-medium">Layout</th>
                    <th class="px-5 py-4 font-medium">Entries</th>
                    <th class="px-5 py-4 font-medium">Status</th>
                    <th class="px-5 py-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($categories as $category)
                    <tr class="bg-white/70 text-slate-700 dark:bg-slate-950/30 dark:text-stone-200">
                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-900 dark:text-white">{{ $category->name }}</p>
                            @if ($category->description)
                                <p class="mt-1 text-xs text-slate-500 dark:text-stone-400">{{ \Illuminate\Support\Str::limit($category->description, 80) }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-slate-500 dark:text-stone-400">{{ $category->slug }}</td>
                        <td class="px-5 py-4 text-slate-500 dark:text-stone-400">{{ $category->normalizedLayoutType() }}</td>
                        <td class="px-5 py-4">{{ $category->contents_count }}</td>
                        <td class="px-5 py-4">
                            <span class="cms-chip px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $category->is_active ? 'text-sky-600 dark:text-sky-300' : 'text-slate-500 dark:text-stone-400' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if (auth()->user()?->hasCmsPermission('cms.manage.categories'))
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('cms.categories.show', $category) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-sky-50 text-sky-600 transition hover:bg-sky-100 hover:text-sky-700 dark:bg-sky-500/10 dark:text-sky-400 dark:hover:bg-sky-500/20" title="View contents">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    </a>
                                    <a href="{{ route('cms.categories.edit', $category) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-200 hover:text-slate-900 dark:bg-white/5 dark:text-stone-300 dark:hover:bg-white/10 dark:hover:text-white" title="Edit">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('cms.categories.destroy', $category) }}" class="block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-50 text-rose-600 transition hover:bg-rose-100 hover:text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/20" title="Delete" onclick="return confirm('Delete this category?');">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-sm font-medium text-slate-500 dark:text-stone-400">Read only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-500 dark:text-stone-400">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>