<x-layouts.app title="Settings" eyebrow="CMS Runtime" heading="App settings" subheading="Manage public app labels, support contacts, and operational configuration values used at runtime.">

    @if (! auth()->user()?->hasCmsPermission('cms.manage.settings'))
        <div class="cms-card mb-6 bg-white/70 px-4 py-3 text-sm text-slate-500 dark:bg-white/5 dark:text-stone-400">
            This account can review settings but cannot update them.
        </div>
    @endif

    <form method="POST" action="{{ route('cms.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        @foreach ($settings as $group => $groupSettings)
            <section class="cms-card cms-gradient-card p-6">
                <div class="mb-5">
                    <p class="cms-kicker text-xs font-semibold uppercase tracking-[0.35em]">{{ $group }}</p>
                    <h3 class="cms-heading mt-2 text-xl font-semibold">{{ str($group)->headline() }}</h3>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    @foreach ($groupSettings as $setting)
                        <div class="cms-card bg-white/65 p-4 dark:bg-slate-950/30">
                            <label for="setting_{{ $setting->key }}" class="text-sm font-medium text-slate-900 dark:text-stone-200">{{ $setting->label }}</label>
                            @if ($setting->input_type === 'textarea')
                                <textarea id="setting_{{ $setting->key }}" name="settings[{{ $setting->key }}]" rows="4" class="cms-textarea mt-2">{{ old('settings.'.$setting->key, $setting->value) }}</textarea>
                            @elseif ($setting->input_type === 'upload' || $setting->input_type === 'upload_multiple')
                                <input id="setting_{{ $setting->key }}" name="setting_uploads[{{ $setting->key }}]{{ $setting->input_type === 'upload_multiple' ? '[]' : '' }}" type="file" accept="image/*" class="cms-input mt-2 border-dashed text-sm text-slate-500 dark:text-stone-300" {{ $setting->input_type === 'upload_multiple' ? 'multiple' : '' }}>
                                <input type="hidden" name="settings[{{ $setting->key }}]" value="{{ old('settings.'.$setting->key, is_array($setting->value) ? json_encode($setting->value) : $setting->value) }}">
                                @if ($setting->getMedia('setting_asset')->isNotEmpty())
                                    <div class="mt-3 flex flex-wrap gap-2 overflow-hidden rounded-2xl border border-slate-200/70 bg-white/70 p-2 dark:border-white/10 dark:bg-slate-950/30">
                                        @foreach ($setting->getMedia('setting_asset') as $media)
                                            <img src="{{ $media->getUrl() }}" alt="{{ $setting->label }}" class="h-28 w-40 rounded-xl object-cover">
                                        @endforeach
                                    </div>
                                @endif
                            @elseif ($setting->input_type === 'boolean')
                                <label class="mt-3 flex items-center gap-3 text-sm text-slate-700 dark:text-stone-200">
                                    <input id="setting_{{ $setting->key }}" type="checkbox" name="settings[{{ $setting->key }}]" value="1" class="h-4 w-4 rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-400 dark:border-white/20 dark:bg-slate-950" @checked(old('settings.'.$setting->key, $setting->value) == '1')>
                                    Enabled
                                </label>
                            @else
                                <input id="setting_{{ $setting->key }}" name="settings[{{ $setting->key }}]" type="text" value="{{ old('settings.'.$setting->key, $setting->value) }}" class="cms-input mt-2">
                            @endif

                            @if ($setting->description)
                                <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-stone-500">{{ $setting->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="flex justify-end">
            @if (auth()->user()?->hasCmsPermission('cms.manage.settings'))
                <button type="submit" class="inline-flex items-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200/50 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-cyan-600 dark:shadow-none">Save settings</button>
            @endif
        </div>
    </form>
</x-layouts.app>