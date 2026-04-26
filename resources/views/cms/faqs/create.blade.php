<x-layouts.app title="New FAQ" eyebrow="CMS Knowledge Base" heading="Create FAQ" subheading="Add a trusted answer for a common SRHR question.">
    <form method="POST" action="{{ route('cms.faqs.store') }}">
        @csrf
        @include('cms.faqs._form', ['submitLabel' => 'Create FAQ'])
    </form>
</x-layouts.app>