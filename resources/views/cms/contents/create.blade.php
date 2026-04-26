<x-layouts.app title="New Content" eyebrow="CMS Content" heading="Create content" subheading="Create a publishable content entry with rich text formatting for the public website and mobile experience.">
    <div class="mb-6 flex gap-3">
        <a href="{{ request('category_id') ? route('cms.categories.show', request('category_id')) : route('cms.contents.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-stone-400 dark:hover:text-stone-300">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Back
        </a>
    </div>
    <form method="POST" action="{{ route('cms.contents.store') }}" enctype="multipart/form-data">
        @csrf
        @include('cms.contents._form', ['submitLabel' => 'Create content'])
    </form>
</x-layouts.app>