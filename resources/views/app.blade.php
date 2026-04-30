<!DOCTYPE html>
<html lang="ml">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name') }}</title>

        {{--
            Fonts loaded:
            · Noto Sans Malayalam — UI text in Malayalam
            · Noto Serif Malayalam — blog prose in Malayalam
            · Instrument Sans      — Latin UI chrome (nav, buttons, labels)
        --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Noto+Sans+Malayalam:wght@400;500;600;700&family=Noto+Serif+Malayalam:wght@400;500;600;700&display=swap" rel="stylesheet">

        @routes
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
