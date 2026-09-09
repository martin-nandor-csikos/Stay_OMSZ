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

        window.adminAlertQueue = Promise.resolve();
        window.queueAdminAlert = function(callback) {
            window.adminAlertQueue = window.adminAlertQueue.then(callback);

            return window.adminAlertQueue;
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

    @php
        $queuedUserAlerts = collect();

        if (Auth::check()) {
            $queuedUserAlerts = \Illuminate\Support\Facades\DB::table('user_alert_notifications')
                ->where('user_id', Auth::id())
                ->orderBy('id')
                ->get()
                ->map(function ($notification) {
                    $notification->payload = json_decode($notification->payload, true) ?: [];

                    return $notification;
                });

            if (Auth::user()->promoted_to_rank) {
                $queuedUserAlerts->push((object) [
                    'id' => null,
                    'type' => 'rank_change',
                    'payload' => [
                        'from_rank' => Auth::user()->promoted_from_rank ?? 'Nincs',
                        'to_rank' => Auth::user()->promoted_to_rank,
                        'change_type' => Auth::user()->rank_change_type ?? 'promotion',
                    ],
                ]);
            }

            if (!is_null(Auth::user()->closed_week_salary)) {
                $queuedUserAlerts->push((object) [
                    'id' => null,
                    'type' => 'closed_week_salary',
                    'payload' => [
                        'salary' => Auth::user()->closed_week_salary,
                        'bonus' => Auth::user()->closed_week_bonus ?? 0,
                        'calculation' => Auth::user()->closed_week_calculation ?? '',
                        'message' => Auth::user()->closed_week_message,
                    ],
                ]);
            }
        }
    @endphp

    @if ($queuedUserAlerts->isNotEmpty())
        <script>
            window.addEventListener('load', function() {
                @foreach ($queuedUserAlerts as $queuedUserAlert)
                    @if ($queuedUserAlert->type === 'rank_change')
                        @php
                            $isDemotion = ($queuedUserAlert->payload['change_type'] ?? 'promotion') === 'demotion';
                            $fromRank = $queuedUserAlert->payload['from_rank'] ?? 'Nincs';
                            $toRank = $queuedUserAlert->payload['to_rank'] ?? 'Nincs';
                        @endphp
                        window.queueAdminAlert(function() {
                            return Swal.fire({
                                title: @json($isDemotion ? 'Lefokozás!' : 'Előléptetés!'),
                                text: @json(($isDemotion ? 'Lefokozásban részesültél: ' : 'Gratulálunk! Előléptetésben részesültél: ') . $fromRank . ' -> ' . $toRank . '.'),
                                icon: @json($isDemotion ? 'warning' : 'success'),
                                confirmButtonText: 'Rendben',
                            });
                        });
                    @elseif ($queuedUserAlert->type === 'point_change')
                        @php
                            $pointType = $queuedUserAlert->payload['point_type'] ?? 'pont';
                            $oldValue = $queuedUserAlert->payload['old_value'] ?? 0;
                            $newValue = $queuedUserAlert->payload['new_value'] ?? 0;
                            $reason = $queuedUserAlert->payload['reason'] ?? '';
                            $pointIncreased = (int) $newValue > (int) $oldValue;
                            $pointAlertTitle = $pointType === 'hibapont'
                                ? ($pointIncreased ? 'Hibapontot kaptál' : 'Hibapontot vesztettél')
                                : ($pointIncreased ? 'Pluszpontot kaptál' : 'Pluszpontot vesztettél');
                            $pointAlertIcon = $pointType === 'hibapont'
                                ? ($pointIncreased ? 'warning' : 'success')
                                : ($pointIncreased ? 'success' : 'warning');
                            $pointAlertHtml = '<div class="text-center space-y-2">';

                            if ($pointAlertIcon === 'success') {
                                $pointAlertHtml .= '<p>Gratulálunk!</p>';
                            }

                            $pointAlertHtml .= '<p><strong>Régi érték:</strong> ' . e($oldValue) . '</p><p><strong>Új érték:</strong> ' . e($newValue) . '</p><p><strong>Indok:</strong> ' . e($reason) . '</p></div>';
                        @endphp
                        window.queueAdminAlert(function() {
                            return Swal.fire({
                                title: @json($pointAlertTitle),
                                html: @json($pointAlertHtml),
                                icon: @json($pointAlertIcon),
                                confirmButtonText: 'Rendben',
                            });
                        });
                    @elseif ($queuedUserAlert->type === 'closed_week_salary')
                        @php
                            $salaryMessage = $queuedUserAlert->payload['message'] ?? null;
                            $salary = $queuedUserAlert->payload['salary'] ?? 0;
                            $bonus = $queuedUserAlert->payload['bonus'] ?? 0;
                            $paydayHtml = '<div class="text-center space-y-2"><p><strong>Összeg:</strong> $' . e($salary) . '</p>';

                            if ((int) $bonus > 0) {
                                $paydayHtml .= '<p><strong>Bónusz:</strong> ' . e($bonus) . '%</p>';
                            }

                            $paydayHtml .= '<p>Csak így tovább! :)</p><p><em class="text-xs">"Elmúlt a remegésöm, mert megjött a fizetésöm" -Belga</em></p></div>';
                        @endphp
                        window.queueAdminAlert(function() {
                            return Swal.fire({
                                @if ($salaryMessage)
                                    title: 'Heti fizetés',
                                    text: @json($salaryMessage),
                                    icon: 'warning',
                                @else
                                    title: 'Fizetésnap!',
                                    html: @json($paydayHtml),
                                    icon: 'success',
                                @endif
                                confirmButtonText: 'Rendben',
                            });
                        });
                    @endif
                @endforeach
            });
        </script>
        @php
            $displayedNotificationIds = $queuedUserAlerts->pluck('id')->filter()->all();

            if (!empty($displayedNotificationIds)) {
                \Illuminate\Support\Facades\DB::table('user_alert_notifications')
                    ->whereIn('id', $displayedNotificationIds)
                    ->delete();
            }

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
