<x-layouts.site title="Register | {{ config('app.name', 'Sample Platform') }}">
    <section class="mx-auto grid min-h-[calc(100vh-88px)] max-w-7xl items-center gap-8 px-6 py-10 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-600 dark:text-indigo-400">Create Access</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight text-slate-900 dark:text-white">Create your {{ config('app.name', 'Sample Platform') }} account.</h1>
            <p class="mt-4 max-w-xl text-base leading-7 text-slate-600 dark:text-slate-300">This registration flow supports person-space accounts for mobile and personalized features. CMS access is reserved for users who have been granted administrator permissions, while public pages stay open to everyone.</p>

            <div class="mt-8 space-y-4">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-800/50">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">What you get</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">An authenticated account that works with the mobile API, permission syncing, and future personalized experiences.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-800/50">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">What comes next</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Administrators can grant CMS access separately, while the same identity remains valid for public browsing and mobile flows.</p>
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-xl dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Create account</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Use a real email so seeded and authored content can be attributed correctly.</p>

            <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="name" class="text-sm font-medium text-slate-900 dark:text-slate-200">Full name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400" required autofocus>
                    @error('name')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="text-sm font-medium text-slate-900 dark:text-slate-200">Phone</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400" required>
                    @error('phone')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="text-sm font-medium text-slate-900 dark:text-slate-200">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400" required>
                    @error('email')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="website_name" class="text-sm font-medium text-slate-900 dark:text-slate-200">Website name</label>
                    <input id="website_name" name="website_name" type="text" value="{{ old('website_name') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400" required>
                    @error('website_name')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="text-sm font-medium text-slate-900 dark:text-slate-200">Password</label>
                    <input id="password" name="password" type="password" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400" required>
                    @error('password')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="text-sm font-medium text-slate-900 dark:text-slate-200">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400" required>
                </div>

                <button type="submit" class="w-full rounded-full bg-indigo-600 px-5 py-3.5 font-semibold text-white transition hover:bg-indigo-700">Create account</button>
            </form>

            <p class="mt-6 text-sm text-slate-600 dark:text-slate-400">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-indigo-600 transition hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">Sign in</a>
            </p>

            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">
                Prefer to explore first?
                <a href="{{ route('public.contents.index') }}" class="font-semibold text-indigo-600 transition hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">Read public content</a>
            </p>
        </div>
    </section>
</x-layouts.site>