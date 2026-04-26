<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @unless (app()->runningUnitTests())
        @vite(['resources/css/app.css'])
    @endunless
    <style>
        /* Suppress scrollbars inside preview frames */
        html, body { overflow: hidden; }
    </style>
</head>
<body class="bg-white min-h-screen">
    {!! $html !!}
</body>
</html>
