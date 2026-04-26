<!-- Footer (Card) -->
<footer class="pb-6 pt-2">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="rounded-2xl border border-slate-200 bg-white px-6 py-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}</p>
          <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">&copy; {{ now()->year }} All rights reserved</p>
        </div>
        <div class="flex items-center gap-4 text-sm text-slate-600 dark:text-slate-300">
          <a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Home</a>
          <a href="{{ route('public.contents.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Content</a>
          <a href="{{ route('public.categories.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Topics</a>
        </div>
      </div>
    </div>
  </div>
</footer>
