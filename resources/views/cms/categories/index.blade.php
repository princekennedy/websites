<x-layouts.app title="Categories" eyebrow="CMS Taxonomy" heading="Content categories" subheading="Organize SRHR topics for app navigation, discovery, and permissions-aware publishing.">
    @php
        $categoryLayoutOptions = \App\Support\DesignLayouts::categoryOptions();
    @endphp

    @if (auth()->user()?->hasCmsPermission('cms.manage.categories'))
        <x-slot:headerAction>
            <a href="{{ route('cms.categories.create') }}" class="inline-flex items-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">New category</a>
        </x-slot:headerAction>
    @endif

    <x-cms.list-view-switcher storage-key="cms:list-view:categories" target-id="cms-listing-categories" default="table" />

    <div id="cms-listing-categories">
        <div data-view-panel="table">
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
                                        <div class="cms-action-group cms-action-group--end">
                                            <x-cms.layout-preview-launcher
                                                section="content-categories"
                                                :layout="$category->normalizedLayoutType()"
                                                :options="$categoryLayoutOptions"
                                                :params="['category_id' => $category->id]"
                                                title="Category Layout Preview"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                            </x-cms.layout-preview-launcher>
                                    <a href="{{ route('cms.categories.show', $category) }}" class="cms-action-btn cms-action-btn-sm cms-action-btn--preview" title="View contents">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    </a>
                                    <a href="{{ route('cms.categories.edit', $category) }}" class="cms-action-btn cms-action-btn-sm cms-action-btn--edit" title="Edit">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('cms.categories.destroy', $category) }}" class="block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="cms-action-btn cms-action-btn-sm cms-action-btn--delete" title="Delete" onclick="return confirm('Delete this category?');">
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
        </div>

        <div data-view-panel="card" class="hidden grid gap-4 xl:grid-cols-2">
            @forelse ($categories as $category)
                <article class="cms-card cms-gradient-card p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $category->name }}</h3>
                            <p class="mt-2 text-sm text-slate-500 dark:text-stone-400">{{ $category->description ?: 'No description yet.' }}</p>
                        </div>
                        <span class="rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $category->is_active ? 'text-sky-600 dark:text-sky-300' : 'text-stone-400' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-500 dark:text-stone-400">
                        <span>Slug: {{ $category->slug }}</span>
                        <span>Layout: {{ $category->normalizedLayoutType() }}</span>
                        <span>Entries: {{ $category->contents_count }}</span>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <x-cms.layout-preview-launcher
                            section="content-categories"
                            :layout="$category->normalizedLayoutType()"
                            :options="$categoryLayoutOptions"
                            :params="['category_id' => $category->id]"
                            title="Category Layout Preview"
                            button-class="cms-action-btn cms-action-btn-md cms-action-btn--preview"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                        </x-cms.layout-preview-launcher>

                        @if (auth()->user()?->hasCmsPermission('cms.manage.categories'))
                            <a href="{{ route('cms.categories.show', $category) }}" class="cms-action-btn cms-action-btn-md cms-action-btn--preview" title="View contents">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            </a>
                            <a href="{{ route('cms.categories.edit', $category) }}" class="cms-action-btn cms-action-btn-md cms-action-btn--edit" title="Edit">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                            </a>
                        @endif
                    </div>
                </article>
            @empty
                <article class="cms-empty-state p-10 text-center xl:col-span-2">No categories yet.</article>
            @endforelse
        </div>
    </div>
</x-layouts.app>