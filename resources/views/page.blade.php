<x-layouts.site :title="($menu?->name ?? 'Page').' | '.data_get($publicSite ?? [], 'brand.name', 'Brandly')">
	@if (($menu?->slug ?? null) === 'home')
		<x-home.slider />
	@endif

	<section class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
		<div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
			<p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">{{ $menu?->name ?? 'Page' }}</p>

			@if ($primaryContent)
				<h1 class="mt-3 text-3xl font-bold leading-tight text-slate-900 dark:text-white sm:text-4xl">{{ $primaryContent->title }}</h1>
				@if (filled($primaryContent->summary))
					<p class="mt-4 max-w-3xl text-lg leading-7 text-slate-600 dark:text-slate-400">{{ $primaryContent->summary }}</p>
				@elseif (filled($menu?->description))
					<p class="mt-4 max-w-3xl text-lg leading-7 text-slate-600 dark:text-slate-400">{{ $menu->description }}</p>
				@endif
			@else
				<h1 class="mt-3 text-3xl font-bold leading-tight text-slate-900 dark:text-white sm:text-4xl">{{ $menu?->name ?? 'Page' }}</h1>
				<p class="mt-4 max-w-3xl text-lg leading-7 text-slate-600 dark:text-slate-400">{{ $menu?->description ?: 'This page is ready. Add published content linked to this menu to populate it automatically.' }}</p>
			@endif
		</div>
	</section>

	<section class="py-12">
		<div class="mx-auto max-w-7xl px-6 lg:px-8">
			@if ($primaryContent && filled($primaryContent->body))
				<article class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-950 lg:p-10">
					<div class="prose prose-slate dark:prose-invert max-w-none">
						{!! $primaryContent->body !!}
					</div>
				</article>
			@endif

			@if ($pageCategories->isNotEmpty())
				<div class="mt-12 space-y-10">
					@foreach ($pageCategories as $category)
						<section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-950">
							<h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $category->name }}</h2>
							@if (filled($category->description))
								<p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $category->description }}</p>
							@endif

							<div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
								@forelse ($category->contents as $content)
									<a href="{{ route('public.contents.show', $content) }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-indigo-300 hover:shadow-sm dark:border-slate-700 dark:bg-slate-900">
										<h3 class="font-semibold text-slate-900 dark:text-white">{{ $content->title }}</h3>
										@if (filled($content->summary))
											<p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Str::limit($content->summary, 110) }}</p>
										@endif
									</a>
								@empty
									<p class="text-sm text-slate-500 dark:text-slate-400">No published content is linked to this category yet.</p>
								@endforelse
							</div>
						</section>
					@endforeach
				</div>
			@endif

			@if ($pageContents->isNotEmpty())
				<section class="mt-12">
					<h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Linked content</h2>
					<div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
						@foreach ($pageContents as $content)
							<a href="{{ route('public.contents.show', $content) }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-indigo-300 hover:shadow-sm dark:border-slate-700 dark:bg-slate-900">
								<h3 class="font-semibold text-slate-900 dark:text-white">{{ $content->title }}</h3>
								@if (filled($content->summary))
									<p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Str::limit($content->summary, 110) }}</p>
								@endif
							</a>
						@endforeach
					</div>
				</section>
			@endif
		</div>
	</section>
</x-layouts.site>
