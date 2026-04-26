<x-layouts.app title="Edit Quiz" eyebrow="CMS Interactivity" heading="Edit quiz" subheading="Refine quiz copy, publishing state, and answer sets.">
    <form method="POST" action="{{ route('cms.quizzes.update', $quiz) }}">
        @csrf
        @method('PUT')
        @include('cms.quizzes._form', ['submitLabel' => 'Update quiz'])
    </form>
</x-layouts.app>