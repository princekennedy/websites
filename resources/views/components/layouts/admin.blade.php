<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $title ?? 'Brandly' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
    }
  </script>
  <script>
    (() => {
        const storedTheme = window.localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (storedTheme === 'dark' || (!storedTheme && prefersDark)) {
            document.documentElement.classList.add('dark');
        }
    })();
  </script>
</head>
<body class="bg-slate-50 text-slate-800 transition-colors duration-200 dark:bg-slate-900 dark:text-slate-100">
  <main class="pt-[65px]">
    @if (session('status'))
      <div class="mx-auto max-w-7xl px-6 pt-6 lg:px-8">
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-100">
          {{ session('status') }}
        </div>
      </div>
    @endif

    {{ $slot }}
  </main>


  <script>
    const menuBtn = document.getElementById('menuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    menuBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });

    const themeToggleBtn = document.getElementById('themeToggle');
    themeToggleBtn.addEventListener('click', function() {
      if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
      } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
      }
    });
  </script>
  {{ $scripts ?? '' }}
</body>
</html>