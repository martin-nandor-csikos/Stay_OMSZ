<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'OMSZ') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- CSS/Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Scripts -->
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <div class="footer-text dark:text-gray-100">&copy; 2024-2026 MateLUL <em>(Hibajelentésért keress fel
                    Discordon, @matelul)</em></div>
        </footer>
    </div>

    @if (Auth::check() && (Auth::user()->promoted_to_rank || !is_null(Auth::user()->closed_week_salary)))
        <script>
            $(function() {
                @if (Auth::user()->promoted_to_rank && !is_null(Auth::user()->closed_week_salary))
                    @if (Auth::user()->rank_change_type === 'demotion')
                        Swal.fire({
                            title: 'Lefokozás!',
                            text: 'Lefokozásban részesültél: {{ Auth::user()->promoted_from_rank ?? "Nincs" }} -> {{ Auth::user()->promoted_to_rank }}.',
                            icon: 'warning',
                            confirmButtonText: 'Tovább',
                        }).then(() => {
                            @if (Auth::user()->closed_week_message)
                                Swal.fire({
                                    title: 'Heti fizetés',
                                    text: '{{ Auth::user()->closed_week_message }}',
                                    icon: 'warning',
                                    confirmButtonText: 'Rendben',
                                });
                            @else
                                Swal.fire({
                                    title: 'Fizetésnap!',
                                    html: '<div class="text-left space-y-2"><p><strong>Összeg:</strong> ${{ Auth::user()->closed_week_salary }}</p><p><strong>Bónusz:</strong> {{ Auth::user()->closed_week_bonus > 0 ? Auth::user()->closed_week_bonus . "%" : "0%" }}</p></div>',
                                    icon: 'success',
                                    confirmButtonText: 'Rendben',
                                });
                            @endif
                        });
                    @else
                        Swal.fire({
                            title: 'Előléptetés!',
                            text: 'Gratulálunk! Előléptetésben részesültél: {{ Auth::user()->promoted_from_rank ?? "Nincs" }} -> {{ Auth::user()->promoted_to_rank }}.',
                            icon: 'success',
                            confirmButtonText: 'Tovább',
                        }).then(() => {
                            @if (Auth::user()->closed_week_message)
                                Swal.fire({
                                    title: 'Heti fizetés',
                                    text: '{{ Auth::user()->closed_week_message }}',
                                    icon: 'warning',
                                    confirmButtonText: 'Rendben',
                                });
                            @else
                                Swal.fire({
                                    title: 'Fizetésnap!',
                                    html: '<div class="text-left space-y-2"><p><strong>Összeg:</strong> ${{ Auth::user()->closed_week_salary }}</p> {{ Auth::user()->closed_week_bonus > 0 ? "<p><strong>Bónusz:</strong>" . Auth::user()->closed_week_bonus . "%</p>" : "" }} <p>Csak így tovább! :)</p><p><em class="text-xs">"Elmúlt a remegésöm, mert megjött a fizetésöm" -Belga</em></p></div>',
                                    icon: 'success',
                                    confirmButtonText: 'Rendben',
                                });
                            @endif
                        });
                    @endif
                @elseif (Auth::user()->promoted_to_rank)
                    @if (Auth::user()->rank_change_type === 'demotion')
                        Swal.fire({
                            title: 'Lefokozás!',
                            text: 'Lefokozásban részesültél: {{ Auth::user()->promoted_from_rank ?? "Nincs" }} -> {{ Auth::user()->promoted_to_rank }}.',
                            icon: 'warning',
                            confirmButtonText: 'Rendben',
                        });
                    @else
                        Swal.fire({
                            title: 'Előléptetés!',
                            text: 'Gratulálunk! Előléptetésben részesültél: {{ Auth::user()->promoted_from_rank ?? "Nincs" }} -> {{ Auth::user()->promoted_to_rank }}.',
                            icon: 'success',
                            confirmButtonText: 'Rendben',
                        });
                    @endif
                @elseif (!is_null(Auth::user()->closed_week_salary))
                    @if (Auth::user()->closed_week_message)
                        Swal.fire({
                            title: 'Heti fizetés',
                            text: '{{ Auth::user()->closed_week_message }}',
                            icon: 'warning',
                            confirmButtonText: 'Rendben',
                        });
                    @else
                        Swal.fire({
                            title: 'Fizetésnap!',
                            html: '<div class="text-left space-y-2"><p><strong>Összeg:</strong> ${{ Auth::user()->closed_week_salary }}</p> {{ Auth::user()->closed_week_bonus > 0 ? "<p><strong>Bónusz:</strong>" . Auth::user()->closed_week_bonus . "%</p>" : "" }} <p>Csak így tovább! :)</p><p><em class="text-xs">"Elmúlt a remegésöm, mert megjött a fizetésöm" -Belga</em></p></div>',
                            icon: 'success',
                            confirmButtonText: 'Rendben',
                        });
                    @endif
                @endif
            });
        </script>
        @php
            \App\Models\User::where('id', Auth::id())->update([
                'promoted_from_rank' => null,
                'promoted_to_rank' => null,
                'rank_change_type' => null,
                'closed_week_salary' => null,
                'closed_week_bonus' => null,
                'closed_week_calculation' => null,
                'closed_week_message' => null,
            ]);
        @endphp
    @endif
</body>

</html>
