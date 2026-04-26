<x-layouts.app title="Edit FAQ" eyebrow="CMS Knowledge Base" heading="Edit FAQ" subheading="Refine question wording, answer accuracy, and publishing state.">
    <form method="POST" action="{{ route('cms.faqs.update', $faq) }}">
        @csrf
        @method('PUT')
        @include('cms.faqs._form', ['submitLabel' => 'Update FAQ'])
    </form>
</x-layouts.app>