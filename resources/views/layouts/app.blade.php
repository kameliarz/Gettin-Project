<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gettin')</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('images/gettin-icon.ico') }}?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/gettin-icon.ico') }}?v=2">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white font-sans text-gray-950 antialiased">
    <x-navbar />

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
