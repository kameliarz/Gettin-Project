<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@php
    $theme = request()->cookie('theme', 'light');
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gettin')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('images/gettin-icon.ico') }}?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/gettin-icon.ico') }}?v=2">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans antialiased"
    style="{{ $theme === 'dark' ? 'background-color:#726B65; color:white;' : 'background-color:white; color:#111827;' }}"
>
    <x-navbar />

    <div style="position: fixed; top: 80px; right: 20px; z-index: 9999;">
        @if ($theme === 'dark')
            <a href="{{ route('set.theme', 'light') }}"
               title="Ganti ke Tema Terang"
               style="font-size: 28px; text-decoration: none;">
                🌙
            </a>
        @else
            <a href="{{ route('set.theme', 'dark') }}"
               title="Ganti ke Tema Gelap"
               style="font-size: 28px; text-decoration: none;">
                ☀️
            </a>
        @endif
    </div>

    <main>
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    <x-footer />
</body>
</html>
