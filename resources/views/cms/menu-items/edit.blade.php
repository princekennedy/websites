<x-layouts.app title="Edit Menu Item" eyebrow="CMS Navigation" heading="Edit menu item" subheading="Refine menu targeting and app navigation behavior for this entry.">
    <form method="POST" action="{{ route('cms.menus.items.update', [$menu, $item]) }}">
        @csrf
        @method('PUT')
        @include('cms.menu-items._form', ['submitLabel' => 'Update menu item'])
    </form>
</x-layouts.app>