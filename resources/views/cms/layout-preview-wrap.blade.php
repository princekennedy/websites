<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($theme ?? 'light') === 'dark'])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @unless (app()->runningUnitTests())
        @vite(['resources/css/app.css'])
    @endunless
    <style>
        /* Keep horizontal overflow contained while allowing the preview document to scroll vertically. */
        html, body {
            overflow-x: hidden;
            overflow-y: auto;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50 text-slate-900 transition-colors dark:from-slate-950 dark:via-slate-950 dark:to-slate-900 dark:text-slate-100">
    {!! $html !!}
</body>
</html>
