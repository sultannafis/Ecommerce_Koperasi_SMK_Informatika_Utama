<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Koperasi SMKIUTAMA</title>
        <link rel="icon" href="{{ asset('storage/images/smk.png') }}" type="image/png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<!-- Di layout blade (app.blade.php atau guest.blade.php) -->
@if(app()->environment('production'))
    <link rel="stylesheet" href="{{ asset('build/assets/app-[hash].css') }}">
    <script src="{{ asset('build/assets/app-[hash].js') }}" defer></script>
@else
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif
    </head>

    <body class="font-sans antialiased bg-gradient-to-br from-slate-100 to-blue-100">
        <div class="min-h-screen">
            @include('layouts.navigation')

            @if (isset($header))
                <header class="bg-white/80 backdrop-blur-sm shadow-sm">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 animate-fade-in">
                    {{ $slot }}
                </div>
            </main>
        </div>
        
        @stack('scripts')
    </body>
</html>