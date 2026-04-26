<x-layouts.app title="Edit Content" eyebrow="CMS Content" heading="Edit content" subheading="Update publication state, audience, and rich text body content before it reaches the public website and mobile app.">
    <div class="mb-6 flex gap-3">
        <a href="{{ $content->category_id ? route('cms.categories.show', $content->category_id) : route('cms.contents.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-stone-400 dark:hover:text-stone-300">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Back
        </a>
    </div>
    <form method="POST" action="{{ route('cms.contents.update', $content) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('cms.contents._form', ['submitLabel' => 'Update content'])
    </form>

    <section class="mt-6 rounded-3xl border border-white/10 bg-white/5 p-6">
        <h3 class="text-lg font-semibold text-white">Block readiness</h3>
        <p class="mt-2 text-sm text-stone-400">The main body now uses CKEditor. This entry currently has {{ $content->blocks->count() }} block{{ $content->blocks->count() === 1 ? '' : 's' }} stored separately for a future block-management workflow.</p>
    </section>
</x-layouts.app>