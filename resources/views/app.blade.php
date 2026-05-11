<!DOCTYPE html>
<html lang="ml">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @php
            $component = $page['component'] ?? '';
            $props = $page['props'] ?? [];
            $post = $props['post'] ?? null;

            if ($component === 'Blog/Show' && $post) {
                $metaTitle      = str_replace("-", " ", $post['slug']) . ' – Malayalam erotic stories';
                $metaDesc       = 'Read ' . str_replace("-", " ", $post['slug']) . ' in Malayalam. A romantic and thrilling story series. Continue reading now.';
                $metaKeywords   = str_replace("-", " ", $post['slug']) . ', malayalam kambi stories, kambikathakal, kambikatha, malayalam stories, kambi kathakal latest';
                $metaCanonical  = route('blog.show', $post['slug']);
                $ogTitle        = $post['title'];
                $ogDesc         = 'Read ' . str_replace("-", " ", $post['slug']) . ' in Malayalam. A romantic and thrilling story series. Continue reading now.';
                $ogImage        = $post['cover_image_url'] ?? 'https://kambikutan.com/cover.jpg';
                $ogUrl          = route('blog.show', $post['slug']);
                $ogType         = 'article';
            } else {
                $metaTitle      = 'Malayalam Kambi Stories – Latest Kambikathakal Daily | Kambikutan';
                $metaDesc       = 'Read the latest Malayalam kambikathakal with new stories added daily. Explore romance, fantasy & real-life kambi stories only on Kambikutan.';
                $metaKeywords   = 'malayalam kambi stories, kambikathakal, kambikatha, malayalam stories, kambi kathakal latest';
                $metaCanonical  = url('/');
                $ogTitle        = 'Malayalam Kambi Stories – Kambikutan';
                $ogDesc         = 'Read latest kambikathakal in Malayalam. New stories updated daily.';
                $ogImage        = 'https://kambikutan.com/cover.jpg';
                $ogUrl          = url('/');
                $ogType         = 'website';
            }
        @endphp
        <title>{{ $metaTitle }}</title>
        <meta data-inertia="description" name="description" content="{{ $metaDesc }}">
        <meta name="keywords" content="{{ $metaKeywords }}">
        <meta name="robots" content="index, follow">
        <link data-inertia="canonical" rel="canonical" href="{{ $metaCanonical }}">

        <meta data-inertia="og:title" property="og:title" content="{{ $ogTitle }}">
        <meta data-inertia="og:description" property="og:description" content="{{ $ogDesc }}">
        <meta data-inertia="og:image" property="og:image" content="{{ $ogImage }}">
        <meta data-inertia="og:url" property="og:url" content="{{ $ogUrl }}">
        <meta data-inertia="og:type" property="og:type" content="{{ $ogType }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta data-inertia="twitter:title" name="twitter:title" content="{{ $ogTitle }}">
        <meta data-inertia="twitter:description" name="twitter:description" content="{{ $ogDesc }}">
        <meta data-inertia="twitter:image" name="twitter:image" content="{{ $ogImage }}">
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

        <script async src="https://www.googletagmanager.com/gtag/js?id=G-1S3M836CHF"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', 'G-1S3M836CHF');
        </script>
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
