<x-layouts.app title="Edit Category" eyebrow="CMS Taxonomy" heading="Edit category" subheading="Refine naming, descriptions, and ordering as the SRHR information architecture evolves.">
    <form method="POST" action="{{ route('cms.categories.update', $category) }}">
        @csrf
        @method('PUT')
        @include('cms.categories._form', ['submitLabel' => 'Update category'])
    </form>
</x-layouts.app>