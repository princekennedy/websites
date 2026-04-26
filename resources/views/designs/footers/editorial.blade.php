<!-- Footer (Editorial) -->
<footer class="border-t border-amber-200 bg-amber-50 dark:border-slate-800 dark:bg-slate-950">
  <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">{{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}</p>
    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600 dark:text-slate-300">{{ data_get($publicSite ?? [], 'brand.message', 'Build websites and manage content with confidence.') }}</p>
    <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-600 dark:text-slate-300">
      <a href="{{ route('home') }}" class="hover:text-amber-700 dark:hover:text-amber-400">Home</a>
      <a href="{{ route('public.contents.index') }}" class="hover:text-amber-700 dark:hover:text-amber-400">Content</a>
      <a href="{{ route('public.categories.index') }}" class="hover:text-amber-700 dark:hover:text-amber-400">Topics</a>
    </div>
    <p class="mt-6 text-xs text-slate-500 dark:text-slate-400">&copy; {{ now()->year }} {{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}</p>
  </div>
</footer>
