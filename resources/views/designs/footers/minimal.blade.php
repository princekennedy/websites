<!-- Footer (Minimal) -->
<footer class="border-t border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
  <div class="mx-auto flex max-w-7xl flex-col gap-4 px-6 py-8 text-sm text-slate-500 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between lg:px-8">
    <p>&copy; {{ now()->year }} {{ data_get($publicSite ?? [], 'brand.name', 'Brandly') }}</p>
    <div class="flex items-center gap-5">
      <a href="{{ route('home') }}" class="hover:text-slate-900 dark:hover:text-white">Home</a>
      <a href="{{ route('public.contents.index') }}" class="hover:text-slate-900 dark:hover:text-white">Content</a>
      <a href="{{ route('public.categories.index') }}" class="hover:text-slate-900 dark:hover:text-white">Topics</a>
    </div>
  </div>
</footer>
