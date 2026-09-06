<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'OMSZ') }} - Publikus dokumentum</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link
        href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.2/b-3.2.4/b-colvis-3.2.4/b-html5-3.2.4/fh-4.0.3/r-3.0.5/datatables.min.css"
        rel="stylesheet" integrity="sha384-wRLflRid+jzri7W6Iggnx/IjQo9fiSa0C4bU2gq7P/D75UdvjTDums+ReZqYk/+i"
        crossorigin="anonymous">
    <script
        src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.2/b-3.2.4/b-colvis-3.2.4/b-html5-3.2.4/fh-4.0.3/r-3.0.5/datatables.min.js"
        integrity="sha384-UyA4SOdyKqxGgKVKP6EFXx9ILdyrPFoLJwVpeqT+wR1k2cwLDenloK73is/sRFwU" crossorigin="anonymous">
    </script>
</head>

<body class="font-sans antialiased dark:bg-gray-900 hidden">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <nav x-data="{ open: false }" class="bg-gray-50 dark:bg-gray-800 border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('publicDocument.index') }}">
                                <img class="block h-9 w-auto fill-current text-gray-800 logo"
                                    src="{{ asset('img/OMSZ.svg') }}" alt="OMSZ logo">
                            </a>
                        </div>

                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('publicDocument.index')" :active="request()->routeIs('publicDocument.index')">
                                {{ __('Publikus dokumentum') }}
                            </x-nav-link>

                            <x-nav-link :href="route('publicDocument.vehicles')" :active="request()->routeIs('publicDocument.vehicles')">
                                {{ __('Járművek') }}
                            </x-nav-link>

                            @include('layouts.important_documents_dropdown')
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <button id="theme-toggle" type="button" style="margin-right: 15px"
                            class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                            Világos mód
                        </button>
                    </div>

                    <div class="-me-2 flex items-center sm:hidden">
                        <button id="theme-toggle-resp" type="button" style="margin-right: 15px"
                            class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                            Világos mód
                        </button>

                        <button @click="open = ! open"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-300 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('publicDocument.index')" :active="request()->routeIs('publicDocument.index')">
                        {{ __('Publikus dokumentum') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('publicDocument.vehicles')" :active="request()->routeIs('publicDocument.vehicles')">
                        {{ __('Járművek') }}
                    </x-responsive-nav-link>

                    <div class="px-3 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                        {{ __('Fontos dokumentumok') }}
                    </div>
                    <x-responsive-nav-link href="https://docs.google.com/document/d/1NX7BJq_gpV4AT91tUuMOOeFmnk_daJifhA0sBBcH0o0" :active="false" target="_blank" rel="noopener noreferrer">
                        {{ __('Frakció szabályzat') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="https://docs.google.com/document/d/1k9WmCBlmBSuH1W5ccQsniZzk1Fzi1Ltt" :active="false" target="_blank" rel="noopener noreferrer">
                        {{ __('Rádiózási segédlet') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="https://docs.google.com/document/d/1YBgXuT4D1HhVlAn_HD6FdxGbD0clXgJP" :active="false" target="_blank" rel="noopener noreferrer">
                        {{ __('Ellátási segédlet') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="https://docs.google.com/spreadsheets/d/15yS_PBe7-qe928YhbZmGEj4m3BXz5Lva/edit?gid=666139380" :active="false" target="_blank" rel="noopener noreferrer">
                        {{ __('Parkolóhely kiosztás') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        </nav>

        <main class="dark:bg-gray-900">
            {{ $slot }}
        </main>

        <footer class="bg-gray-100 dark:bg-gray-900">
            <div class="flex justify-center">
                <img src="{{ asset('img/hivatas_az_eletert.jpg') }}" alt="Hivatás az életért"
                    class="max-w-full h-auto rounded">
            </div>
            <div class="footer-text dark:text-gray-100">&copy; 2024-2026 MateLUL <em>(Hibajelentésért keress fel
                    Discordon, @matelul)</em></div>
        </footer>
    </div>
</body>

</html>
