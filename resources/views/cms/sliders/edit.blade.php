<x-layouts.app title="Edit Slide" eyebrow="CMS Homepage" heading="Edit slider entry" subheading="Update the homepage hero slide image, caption, and button content.">
    <form method="POST" action="{{ route('cms.sliders.update', $slider) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('cms.sliders._form', ['submitLabel' => 'Update slide'])
    </form>
</x-layouts.app>