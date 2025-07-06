<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- CSS/Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.2/b-3.2.4/b-colvis-3.2.4/b-html5-3.2.4/r-3.0.5/datatables.min.css" rel="stylesheet" integrity="sha384-F5W6Z/wXr1Yp234N+6pgySIq43ZUeyKmEt1+bXv0MY6IC7RCULxOWcRFYeJc+V5b" crossorigin="anonymous">
        <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.2/b-3.2.4/b-colvis-3.2.4/b-html5-3.2.4/r-3.0.5/datatables.min.js" integrity="sha384-WTotflUg0Ci3bGemidvn/UBWvJum6xXUEDqpf6hDlcMiIqIYwK0DEzYyFv96akX1" crossorigin="anonymous"></script>
        <script>
            window.routes = {
                dashboard: "{{ route('dashboardTable') }}",
            };
        </script>
    </head>
    <body class="font-sans antialiased dark:bg-gray-900 hidden">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-gray-50 dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="dark:bg-gray-900">
                {{ $slot }}
            </main>

            <footer class="bg-gray-100 dark:bg-gray-900">
                <div class="footer-text dark:text-gray-100">&copy; 2024-2025 MateLUL <em>(Hibajelentésért keress fel Discordon, @matelul)</em></div>
            </footer>
        </div>
    </body>
</html>
