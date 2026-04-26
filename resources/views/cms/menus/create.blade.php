<x-layouts.app title="New Menu" eyebrow="CMS Navigation" heading="Create menu" subheading="Define an app navigation group that can be mapped to home, onboarding, or feature entry points.">
    <form method="POST" action="{{ route('cms.menus.store') }}">
        @csrf
        @include('cms.menus._form', ['submitLabel' => 'Create menu'])
    </form>
</x-layouts.app>