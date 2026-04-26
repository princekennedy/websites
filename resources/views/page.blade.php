@php
	// Determine page title based on template type
	$pageTitle = match ($pageTemplate ?? 'menu-show') {
		'categories-index' => 'Topics | '.data_get($publicSite ?? [], 'brand.name', 'Brandly'),
		'categories-show' => ($category?->name ?? 'Topic').' | '.data_get($publicSite ?? [], 'brand.name', 'Brandly'),
		'contents-index' => 'Content Library | '.data_get($publicSite ?? [], 'brand.name', 'Brandly'),
		'contents-show' => ($content?->title ?? 'Content').' | '.data_get($publicSite ?? [], 'brand.name', 'Brandly'),
		'menu-item-show' => ($menuItem?->title ?? 'Page').' | '.data_get($publicSite ?? [], 'brand.name', 'Brandly'),
		default => ($menu?->name ?? 'Page').' | '.data_get($publicSite ?? [], 'brand.name', 'Brandly'),
	};

	$categoryLayoutView = 'designs.content-categories.'.($category?->normalizedLayoutType() ?? 'default');
	$contentLayoutView = 'designs.content.'.($content?->normalizedLayoutType() ?? 'default');
	$menuItemLayoutView = 'designs.menu-items.'.($menuItem?->normalizedLayoutType() ?? 'default');
@endphp

<x-layouts.site :title="$pageTitle">
	@switch($pageTemplate ?? 'menu-show')
		{{-- Menu/Standard Page Template --}}
		@case('menu-show')
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

					@if ($pageCategories?->isNotEmpty())
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

					@if ($pageContents?->isNotEmpty())
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
			@break

		{{-- Content Categories Index --}}
		@case('categories-index')
			<section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white">
				<div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(99,102,241,0.25),transparent_35%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,0.2),transparent_30%)]"></div>
				<div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-24">
					<div class="max-w-3xl">
						<span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1 text-sm backdrop-blur">Knowledge Base</span>
						<h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl md:text-6xl">Browse Topics</h1>
						<p class="mt-6 text-lg leading-8 text-slate-200">Browse published SRHR topics with clearer entry points.</p>
					</div>
				</div>
			</section>

			<section class="py-20">
				<div class="mx-auto max-w-7xl px-6 lg:px-8">
					@if ($categories->isEmpty())
						<div class="rounded-[2rem] border border-slate-200 bg-white p-10 text-center shadow-sm dark:border-slate-800 dark:bg-slate-950">
							<p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">No topics yet</p>
							<h2 class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">Topics are coming soon.</h2>
							<p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-600 dark:text-slate-400">Check back shortly for published categories and content.</p>
						</div>
					@else
						<div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
							@foreach ($categories as $cat)
								<a href="{{ route('public.categories.show', $cat) }}"
									class="group relative flex flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm transition hover:shadow-lg hover:-translate-y-1 dark:border-slate-800 dark:bg-slate-950">
									<div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400">
										<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
										</svg>
									</div>
									<h2 class="text-xl font-bold text-slate-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400 transition">{{ $cat->name }}</h2>
									@if ($cat->description)
										<p class="mt-3 flex-1 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ Str::limit($cat->description, 120) }}</p>
									@endif
									<div class="mt-6 flex items-center justify-between">
										<span class="text-xs font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500">
											{{ $cat->contents_count }} {{ Str::plural('article', $cat->contents_count) }}
										</span>
										<span class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 dark:text-indigo-400 group-hover:gap-2 transition-all">
											Explore
											<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
										</span>
									</div>
								</a>
							@endforeach
						</div>
					@endif
				</div>
			</section>
			@break

		{{-- Content Categories Show --}}
		@case('categories-show')
			@include(view()->exists($categoryLayoutView) ? $categoryLayoutView : 'designs.content-categories.default')
			@break

		{{-- Content Index with Search --}}
		@case('contents-index')
			<section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white">
				<div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(99,102,241,0.25),transparent_35%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,0.2),transparent_30%)]"></div>
				<div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-24">
					<div class="max-w-3xl">
						<span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1 text-sm backdrop-blur">Content Library</span>
						<h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl md:text-6xl">Explore Content</h1>
						<p class="mt-6 text-lg leading-8 text-slate-200">Published SRHR content arranged like a modern landing library.</p>
					</div>
				</div>
			</section>

			<section class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
				<div class="mx-auto max-w-7xl px-6 lg:px-8">
					<form method="GET" action="{{ route('public.contents.index') }}" class="flex flex-wrap items-center gap-3 py-4">
						<input id="content-search"
							type="search" name="q" value="{{ $search }}"
							placeholder="Search content..."
							class="flex-1 min-w-[200px] rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 placeholder-slate-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white" />

						<select id="content-type-filter" name="type"
							class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
							<option value="">All types</option>
							@foreach ($typeOptions as $value => $label)
								<option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
							@endforeach
						</select>

						<select id="content-category-filter" name="category"
							class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
							<option value="">All topics</option>
							@foreach ($filterCategories as $cat)
								<option value="{{ $cat->slug }}" @selected($selectedCategory === $cat->slug)>{{ $cat->name }}</option>
							@endforeach
						</select>

						<button id="content-search-btn" type="submit"
							class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
							Search
						</button>

						@if ($search || $selectedType || $selectedCategory)
							<a href="{{ route('public.contents.index') }}"
								class="rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-900">
								Clear
							</a>
						@endif
					</form>
				</div>
			</section>

			<section class="py-16">
				<div class="mx-auto max-w-7xl px-6 lg:px-8">
					@if ($contents->isEmpty())
						<div class="rounded-[2rem] border border-slate-200 bg-white p-10 text-center shadow-sm dark:border-slate-800 dark:bg-slate-950">
							<p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">Nothing found</p>
							<h2 class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">No content matches your search.</h2>
							<p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-600 dark:text-slate-400">Try adjusting your filters or check back later.</p>
						</div>
					@else
						<div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
							@foreach ($contents as $item)
								<a href="{{ route('public.contents.show', $item) }}"
									class="group flex flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm transition hover:shadow-lg hover:-translate-y-1 dark:border-slate-800 dark:bg-slate-950">
									<div class="flex flex-1 flex-col p-8">
										<div class="flex items-center gap-2">
											@if ($item->category)
												<span class="rounded-full bg-indigo-50 px-3 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
													{{ $item->category->name }}
												</span>
											@endif
											<span class="rounded-full bg-slate-100 px-3 py-0.5 text-xs font-medium capitalize text-slate-600 dark:bg-slate-800 dark:text-slate-400">
												{{ str_replace('_', ' ', $item->content_type) }}
											</span>
										</div>
										<h2 class="mt-4 text-lg font-bold leading-snug text-slate-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400 transition">
											{{ $item->title }}
										</h2>
										@if ($item->summary)
											<p class="mt-3 flex-1 text-sm leading-6 text-slate-600 dark:text-slate-400">
												{{ Str::limit($item->summary, 130) }}
											</p>
										@endif
										<div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4 dark:border-slate-800">
											<span class="text-xs text-slate-400 dark:text-slate-500">
												{{ optional($item->published_at)->format('d M Y') }}
											</span>
											<span class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 dark:text-indigo-400 group-hover:gap-2 transition-all">
												Read
												<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
											</span>
										</div>
									</div>
								</a>
							@endforeach
						</div>

						@if ($contents->hasPages())
							<div class="mt-12">
								{{ $contents->withQueryString()->links() }}
							</div>
						@endif
					@endif
				</div>
			</section>
			@break

		{{-- Content Show --}}
		@case('contents-show')
			@include(view()->exists($contentLayoutView) ? $contentLayoutView : 'designs.content.default')
			@break

		{{-- Menu Item Show --}}
		@case('menu-item-show')
			@include(view()->exists($menuItemLayoutView) ? $menuItemLayoutView : 'designs.menu-items.default')

			<x-slot name="scripts">
				<script>
					document.querySelectorAll('[data-media-slider]').forEach((slider) => {
						const slides = Array.from(slider.querySelectorAll('[data-slide]'));
						const dots = Array.from(slider.querySelectorAll('[data-dot]'));
						const prev = slider.querySelector('[data-prev]');
						const next = slider.querySelector('[data-next]');
						if (slides.length <= 1) return;
						let current = 0;
						const showSlide = (index) => {
							slides.forEach((s, i) => { s.classList.toggle('opacity-100', i === index); s.classList.toggle('opacity-0', i !== index); });
							dots.forEach((d, i) => { d.classList.toggle('bg-white', i === index); d.classList.toggle('bg-white/50', i !== index); });
							current = index;
						};
						prev?.addEventListener('click', () => showSlide((current - 1 + slides.length) % slides.length));
						next?.addEventListener('click', () => showSlide((current + 1) % slides.length));
						dots.forEach((dot, index) => dot.addEventListener('click', () => showSlide(index)));
						window.setInterval(() => showSlide((current + 1) % slides.length), 5000);
					});
				</script>
			</x-slot>
			@break

		@default
			<div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
				<p class="text-slate-600 dark:text-slate-400">Unknown page template: {{ $pageTemplate }}</p>
			</div>
	@endswitch
</x-layouts.site>
