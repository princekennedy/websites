<x-layouts.app title="Edit Menu" eyebrow="CMS Navigation" heading="Edit menu" subheading="Update menu metadata.">
    <div class="mb-6 flex gap-3">
        <a href="{{ route('cms.menus.show', $menu) }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-stone-400 dark:hover:text-stone-300">&larr; Back to Menu</a>
    </div>

    <form method="POST" action="{{ route('cms.menus.update', $menu) }}">
        @csrf
        @method('PUT')
        @include('cms.menus._form', ['submitLabel' => 'Update menu'])
    </form>
</x-layouts.app>