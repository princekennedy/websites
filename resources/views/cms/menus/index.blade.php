<x-layouts.app title="Menus" eyebrow="CMS Navigation" heading="Menu builder" subheading="Create database-driven navigation structures that the app can request and render dynamically.">
    @php
        $menuItemLayoutOptions = \App\Support\DesignLayouts::menuItemOptions();
    @endphp

    @if (auth()->user()?->hasCmsPermission('cms.manage.menus'))
        <x-slot:headerAction>
            <a href="{{ route('cms.menus.create') }}" class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">New menu</a>
        </x-slot:headerAction>
    @endif

    <x-cms.list-view-switcher storage-key="cms:list-view:menus" target-id="cms-listing-menus" default="card" />

    <div id="cms-listing-menus">
        <div data-view-panel="table" class="hidden">
            <div class="cms-table-wrap">
                <table class="min-w-full divide-y divide-slate-200/70 text-left text-sm dark:divide-white/10">
                    <thead class="bg-white/50 text-slate-500 dark:bg-white/5 dark:text-stone-400">
                        <tr>
                            <th class="px-5 py-4 font-medium">Name</th>
                            <th class="px-5 py-4 font-medium">Location</th>
                            <th class="px-5 py-4 font-medium">Visibility</th>
                            <th class="px-5 py-4 font-medium">Items</th>
                            <th class="px-5 py-4 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse ($menus as $menu)
                            <tr class="bg-white/70 text-slate-700 dark:bg-slate-950/30 dark:text-stone-200">
                                <td class="px-5 py-4">
                                    <p class="font-medium text-slate-900 dark:text-white">{{ $menu->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-stone-400">{{ \Illuminate\Support\Str::limit($menu->description ?: 'No description yet.', 120) }}</p>
                                </td>
                                <td class="px-5 py-4 text-slate-500 dark:text-stone-400">{{ $menu->location ?: 'Not set' }}</td>
                                <td class="px-5 py-4 text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-stone-400">{{ ucfirst($menu->visibility ?: 'public') }}</td>
                                <td class="px-5 py-4 text-slate-500 dark:text-stone-400">{{ $menu->items_count }}</td>
                                <td class="px-5 py-4">
                                    <div class="cms-action-group cms-action-group--end">
                                        <x-cms.layout-preview-launcher
                                            section="menu-items"
                                            :layout="$menu->normalizedLayoutType()"
                                            :options="$menuItemLayoutOptions"
                                            :params="['menu_id' => $menu->id]"
                                            title="Menu Layout Preview"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        </x-cms.layout-preview-launcher>
                                        <a href="{{ route('cms.menus.show', $menu) }}" class="cms-action-btn cms-action-btn-sm cms-action-btn--preview" title="View items">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-500 dark:text-stone-400">No menus yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div data-view-panel="card" class="grid gap-4 xl:grid-cols-2">
            @forelse ($menus as $menu)
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-semibold text-white">{{ $menu->name }}</h3>
                        <p class="mt-2 text-sm text-stone-400">{{ $menu->description ?: 'No description yet.' }}</p>
                    </div>
                    <span class="rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $menu->is_active ? 'text-emerald-200' : 'text-stone-400' }}">{{ $menu->is_active ? 'Active' : 'Inactive' }}</span>
                </div>

                <div class="mt-4 flex flex-wrap gap-4 text-sm text-stone-400">
                    <span>Slug: {{ $menu->slug }}</span>
                    <span>Layout: {{ $menu->normalizedLayoutType() }}</span>
                    <span>Location: {{ $menu->location ?: 'Not set' }}</span>
                    <span>Visibility: {{ ucfirst($menu->visibility ?: 'public') }}</span>
                    <span>Items: {{ $menu->items_count }}</span>
                </div>

                <div class="mt-6 flex gap-3">
                    @if (auth()->user()?->hasCmsPermission('cms.manage.menus'))
                        <div class="cms-action-group">
                            <x-cms.layout-preview-launcher
                                section="menu-items"
                                :layout="$menu->normalizedLayoutType()"
                                :options="$menuItemLayoutOptions"
                                :params="['menu_id' => $menu->id]"
                                title="Menu Layout Preview"
                                button-class="cms-action-btn cms-action-btn-md cms-action-btn--preview"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            </x-cms.layout-preview-launcher>
                            <a href="{{ route('cms.menus.show', $menu) }}" class="cms-action-btn cms-action-btn-md cms-action-btn--preview" title="View items">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            </a>
                            <a href="{{ route('cms.menus.edit', $menu) }}" class="cms-action-btn cms-action-btn-md cms-action-btn--edit" title="Edit">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                            </a>
                            <form method="POST" action="{{ route('cms.menus.destroy', $menu) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="cms-action-btn cms-action-btn-md cms-action-btn--delete" title="Delete" onclick="return confirm('Delete this menu and its items?');">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-stone-400">Read only</span>
                    @endif
                </div>
            </article>
            @empty
                <article class="rounded-3xl border border-dashed border-white/10 bg-white/5 p-10 text-center text-stone-400 xl:col-span-2">
                    No menus yet.
                </article>
            @endforelse
        </div>
    </div>
</x-layouts.app>