<x-layouts.app title="New Menu Item" eyebrow="CMS Navigation" heading="Create menu item" subheading="Attach content, categories, routes, or WebView destinations to the selected menu.">
    <form method="POST" action="{{ route('cms.menus.items.store', $menu) }}">
        @csrf
        @include('cms.menu-items._form', ['submitLabel' => 'Create menu item'])
    </form>
</x-layouts.app>