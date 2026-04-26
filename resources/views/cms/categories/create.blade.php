<x-layouts.app title="New Category" eyebrow="CMS Taxonomy" heading="Create category" subheading="Set up a reusable classification for SRHR content and menu targeting.">
    <form method="POST" action="{{ route('cms.categories.store') }}">
        @csrf
        @include('cms.categories._form', ['submitLabel' => 'Create category'])
    </form>
</x-layouts.app>