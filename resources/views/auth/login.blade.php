<x-layouts.site title="Log In | {{ config('app.name', 'Sample Platform') }}">
    <section class="mx-auto grid min-h-[calc(100vh-88px)] max-w-7xl items-center gap-8 px-6 py-10 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-600 dark:text-indigo-400">Welcome Back</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight text-slate-900 dark:text-white">Sign in to manage website content.</h1>
            <p class="mt-4 max-w-xl text-base leading-7 text-slate-600 dark:text-slate-300">Use your account to access the CMS workspace if you have administrator permissions, or to manage your personal platform identity while public pages remain available without sign-in.</p>

            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-800/50">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Secure session auth</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Laravel session authentication protects the CMS, while role-aware checks prevent non-admin users from entering administrator screens.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-800/50">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Public pages stay open</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Visitors can still read published content, FAQs, quizzes, and service listings without logging in.</p>
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-xl dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Log in</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Enter your email and password to continue to the CMS dashboard.</p>

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="email" class="text-sm font-medium text-slate-900 dark:text-slate-200">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400" required autofocus>
                    @error('email')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="text-sm font-medium text-slate-900 dark:text-slate-200">Password</label>
                    <input id="password" name="password" type="password" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400" required>
                </div>

                <label class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 dark:border-slate-700 dark:bg-slate-950 dark:border-slate-700 dark:focus:ring-indigo-500">
                    Keep me signed in on this device
                </label>

                <button type="submit" class="w-full rounded-full bg-indigo-600 px-5 py-3.5 font-semibold text-white transition hover:bg-indigo-700">Continue to CMS</button>
            </form>

            <p class="mt-6 text-sm text-slate-600 dark:text-slate-400">
                Need an account?
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 transition hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">Create one here</a>
            </p>

            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">
                Looking for public information instead?
                <a href="{{ route('public.contents.index') }}" class="font-semibold text-indigo-600 transition hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">Browse content</a>
            </p>
        </div>
    </section>
</x-layouts.site>