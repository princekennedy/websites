<x-layouts.app title="View Menu" eyebrow="Menu Details" heading="{{ $menu->name }}" subheading="Manage the navigation items for this menu.">
    @php
        $menuItemLayoutOptions = \App\Support\DesignLayouts::menuItemOptions();
    @endphp

    <x-slot:headerAction>
        <div class="flex items-center gap-2">
            <x-cms.layout-preview-launcher
                section="menu-items"
                :layout="$menu->normalizedLayoutType()"
                :options="$menuItemLayoutOptions"
                :params="['menu_id' => $menu->id]"
                title="Menu Layout Preview"
                button-label="Preview Menu"
                button-class="inline-flex items-center rounded-full bg-sky-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-600"
            />
            <a href="{{ route('cms.menus.items.create', $menu) }}" class="inline-flex items-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-emerald-300">Add item</a>
        </div>
    </x-slot:headerAction>

    <div class="mb-6 flex gap-3">
        <a href="{{ route('cms.menus.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-stone-400 dark:hover:text-stone-300">&larr; Back to Menus</a>
        <span class="text-slate-300 dark:text-stone-600">|</span>
        <a href="{{ route('cms.menus.edit', $menu) }}" class="text-sm font-medium text-sky-600 hover:text-sky-700 dark:text-sky-400">Edit Menu</a>
    </div>

    <section class="mt-6 rounded-3xl border border-slate-200/70 bg-white/50 p-6 dark:border-white/10 dark:bg-white/5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Menu items</h3>
                <p class="text-sm text-slate-500 dark:text-stone-400">Each item can target content, categories, routes, or externally managed web pages.</p>
                <p class="mt-1 text-xs uppercase tracking-[0.15em] text-slate-400 dark:text-stone-500">Menu visibility: {{ ucfirst($menu->visibility ?: 'public') }}</p>
            </div>
            <a href="{{ route('cms.menus.items.create', $menu) }}" class="text-sm font-medium text-sky-600 hover:text-sky-700 dark:text-sky-300">Create item</a>
        </div>

        <x-cms.list-view-switcher storage-key="cms:list-view:menu-show" target-id="cms-listing-menu-show" default="table" />

        <div id="cms-listing-menu-show" class="mt-5">
            <div data-view-panel="table" class="overflow-hidden rounded-2xl border border-slate-200/70 dark:border-white/10">
                <table class="min-w-full divide-y divide-slate-200/70 text-left text-sm dark:divide-white/10">
                <thead class="bg-white/50 text-slate-500 dark:bg-white/5 dark:text-stone-400">
                    <tr>
                        <th class="px-4 py-3 font-medium">Title</th>
                        <th class="px-4 py-3 font-medium">Layout</th>
                        <th class="px-4 py-3 font-medium">Target</th>
                        <th class="px-4 py-3 font-medium">Flags</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse ($menu->items as $item)
                        <tr class="bg-white/70 text-slate-700 dark:bg-slate-950/30 dark:text-stone-200">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-900 dark:text-white">{{ $item->title }}</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-stone-400">Sort order: {{ $item->sort_order }}{{ $item->parent ? ' | Child of '.$item->parent->title : '' }}</p>
                            </td>
                            <td class="px-4 py-3 uppercase tracking-[0.15em] text-slate-500 dark:text-stone-400">{{ $item->normalizedLayoutType() }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-stone-400">{{ $item->target_reference ?: ($item->route ?: 'None') }}</td>
                            <td class="px-4 py-3 text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-stone-400">{{ ucfirst($item->visibility ?: 'public') }} | {{ $item->is_active ? 'Active' : 'Inactive' }}{{ $item->open_in_webview ? ' | WebView' : '' }}</td>
                            <td class="px-4 py-3">
                                <div class="cms-action-group cms-action-group--end">
                                    <x-cms.layout-preview-launcher
                                        section="menu-items"
                                        :layout="$item->normalizedLayoutType()"
                                        :options="$menuItemLayoutOptions"
                                        :params="['menu_item_id' => $item->id]"
                                        title="Menu Item Layout Preview"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    </x-cms.layout-preview-launcher>
                                    <a href="{{ route('cms.menus.items.edit', [$menu, $item]) }}" class="cms-action-btn cms-action-btn-sm cms-action-btn--edit" title="Edit">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('cms.menus.items.destroy', [$menu, $item]) }}" class="block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="cms-action-btn cms-action-btn-sm cms-action-btn--delete" title="Delete" onclick="return confirm('Delete this menu item?');">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-stone-400">No items in this menu yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div data-view-panel="card" class="hidden grid gap-4 md:grid-cols-2">
                @forelse ($menu->items as $item)
                    <article class="rounded-2xl border border-slate-200/70 bg-white/70 p-5 dark:border-white/10 dark:bg-slate-950/30">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white">{{ $item->title }}</h4>
                                <p class="mt-1 text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-stone-400">{{ ucfirst($item->visibility ?: 'public') }} | {{ $item->is_active ? 'Active' : 'Inactive' }}</p>
                            </div>
                            <div class="cms-action-group">
                                <x-cms.layout-preview-launcher
                                    section="menu-items"
                                    :layout="$item->normalizedLayoutType()"
                                    :options="$menuItemLayoutOptions"
                                    :params="['menu_item_id' => $item->id]"
                                    title="Menu Item Layout Preview"
                                    button-class="cms-action-btn cms-action-btn-sm cms-action-btn--preview"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                </x-cms.layout-preview-launcher>
                                <a href="{{ route('cms.menus.items.edit', [$menu, $item]) }}" class="cms-action-btn cms-action-btn-sm cms-action-btn--edit" title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                </a>
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-slate-500 dark:text-stone-400">{{ $item->target_reference ?: ($item->route ?: 'None') }}</p>
                        <p class="mt-2 text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-stone-400">Layout: {{ $item->normalizedLayoutType() }}</p>
                    </article>
                @empty
                    <article class="cms-empty-state p-10 text-center md:col-span-2">No items in this menu yet.</article>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.app>
