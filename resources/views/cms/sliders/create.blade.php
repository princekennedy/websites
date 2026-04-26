<x-layouts.app title="New Slide" eyebrow="CMS Homepage" heading="Create slider entry" subheading="Add a new homepage hero slide with caption text and button links.">
    <form method="POST" action="{{ route('cms.sliders.store') }}" enctype="multipart/form-data">
        @csrf
        @include('cms.sliders._form', ['submitLabel' => 'Create slide'])
    </form>
</x-layouts.app>