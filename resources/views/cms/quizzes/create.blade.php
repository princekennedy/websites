<x-layouts.app title="New Quiz" eyebrow="CMS Interactivity" heading="Create quiz" subheading="Build a learning quiz with structured questions and answer feedback.">
    <form method="POST" action="{{ route('cms.quizzes.store') }}">
        @csrf
        @include('cms.quizzes._form', ['submitLabel' => 'Create quiz'])
    </form>
</x-layouts.app>