<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — {{ config('app.name') }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
            background: #fff;
            color: #111827;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            -webkit-font-smoothing: antialiased;
        }
        .wrap    { text-align: center; max-width: 28rem; }
        .code    { font-size: 6rem; font-weight: 800; line-height: 1;
                   color: #e5e7eb; letter-spacing: -0.05em; }
        .title   { margin-top: 1rem; font-size: 1.5rem; font-weight: 700; color: #111827; }
        .message { margin-top: 0.5rem; font-size: 0.9375rem; color: #6b7280; line-height: 1.6; }
        .home    { display: inline-flex; align-items: center; gap: 0.375rem;
                   margin-top: 2rem; color: #4f46e5; font-size: 0.875rem;
                   font-weight: 500; text-decoration: none; }
        .home:hover { text-decoration: underline; }
        .divider { margin-top: 2.5rem; padding-top: 2.5rem; border-top: 1px solid #f3f4f6;
                   font-size: 0.75rem; color: #9ca3af; }
        .divider a { color: #6b7280; text-decoration: none; }
        .divider a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="code">@yield('code')</div>
        <h1 class="title">@yield('title')</h1>
        <p class="message">@yield('message')</p>
        <a href="/" class="home">← Back to home</a>
        <div class="divider">
            {{ config('app.name') }} &nbsp;·&nbsp; <a href="/admin/login">Admin</a>
        </div>
    </div>
</body>
</html>
