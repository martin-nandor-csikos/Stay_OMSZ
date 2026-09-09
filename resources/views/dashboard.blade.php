<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Főoldal') }}
        </h2>
    </x-slot>

    @if (session('show-first-login-alert'))
        <script>
            $(function() {
                Swal.fire({
                    title: 'Felhasználónév és jelszó változtatás emlékeztető',
                    text: 'Ne felejts el felhasználónevet és jelszót változtatni. A maximum biztonságért érdemes megváltoztatni az előre generált jelszavad.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Felhasználónév és jelszó változtatás',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Bezárás',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('profile.edit') }}";
                    }
                });
            });
        </script>
    @endif

    <style>
        .dashboard-stat-help .dashboard-stat-tooltip {
            display: none;
            width: max-content;
            max-width: calc(100vw - 2rem);
            white-space: nowrap;
            z-index: 1000;
        }

        .dashboard-stat-help:hover .dashboard-stat-tooltip,
        .dashboard-stat-help:focus-within .dashboard-stat-tooltip {
            display: block;
        }

        .dashboard-stat-help:hover,
        .dashboard-stat-help:focus-within {
            z-index: 999;
        }

        .dashboard-stat-help-button {
            background-color: #0066db;
        }

        .dashboard-stat-help-button:hover,
        .dashboard-stat-help-button:focus {
            background-color: #0066db;
        }

        .dashboard-stat-goblet {
            display: inline-block;
            width: 0.85rem;
            height: 0.85rem;
            margin-right: 0.25rem;
            clip-path: polygon(18% 8%, 82% 8%, 76% 48%, 58% 66%, 58% 82%, 72% 82%, 72% 94%, 28% 94%, 28% 82%, 42% 82%, 42% 66%, 24% 48%);
            vertical-align: -0.1rem;
        }

        .dashboard-stat-goblet-gold {
            background-color: #f59e0b;
        }

        .dashboard-stat-goblet-silver {
            background-color: #cbd5e1;
        }

        .dashboard-stat-goblet-bronze {
            background-color: #b45309;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-200 view-reports-padding">
                            <p class="top5 text-lg">Top 5 jelentésíró a héten</p>

                            @if ($top5UsersWithMostReports->isEmpty())
                                <p>Még senki nem csinált semmit :(</p>
                            @else
                                <table class="display view-reports" id="top5">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Név</th>
                                            <th scope="col">Jelentések</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($top5UsersWithMostReports as $topUser)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $topUser->charactername }}</td>
                                                <td>{{ $topUser->reportCount }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12 stats">
                    <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-200 view-reports-padding">
                            <p class="top5 text-lg">Heti statisztika</p>
                            <p class="text-xl my-1"><b>Jelentéseid száma:</b> {{ $userReportCount }} darab</p>
                            @if ($minimumReportCount - $userReportCount > 0)
                                <p class="text-lg"><i>(Minimum jelentés számhoz
                                        <b>{{ $minimumReportCount - $userReportCount }} darab</b> kell még)</i></p>
                                <p class="text-lg"><i>(Dupla héthez
                                        <b>{{ $minimumDoubleRankupReportCount - $userReportCount }} darab</b> jelentés
                                        kell még)</i></p>
                            @else
                                <p class="text-lg"><i>(<b>Megvan</b> a minimum jelentés számod)</i></p>
                                @if ($minimumDoubleRankupReportCount - $userReportCount > 0)
                                    <p class="text-lg"><i>(Dupla héthez
                                            <b>{{ $minimumDoubleRankupReportCount - $userReportCount }} darab</b>
                                            jelentés kell még)</i></p>
                                @else
                                    <p class="text-lg"><i>(<b>Megvan</b> a dupla héthez a jelentés számod)</i></p>
                                @endif
                            @endif

                            @if ($latestUserReportDate != '-')
                                <p><b>Utolsó felvitt jelentésed:</b> {{ $latestUserReportDate }} </p>
                            @endif
                            <p>Az összes leadott jelentés <b>{{ $userReportPercentage }}%</b>-át te adtad le.</p>

                            <br>

                            <p class="text-xl my-1"><b>Szolgálati idő:</b> {{ $userSumOfDutyTime }} perc</p>
                            @if ($minimumDutyTime - $userSumOfDutyTime > 0)
                                <p class="text-lg"><i>(Minimum szolgálati időhöz
                                        <b>{{ $minimumDutyTime - $userSumOfDutyTime }} perc</b> kell még)</i></p>
                                <p class="text-lg"><i>(Dupla héthez
                                        <b>{{ $minimumDoubleRankupDutyTime - $userSumOfDutyTime }} perc</b> kell
                                        még)</i></p>
                            @else
                                <p class="text-lg"><i>(<b>Megvan</b> a minimum szolgálati időd)</i></p>
                                @if ($minimumDoubleRankupDutyTime - $userSumOfDutyTime > 0)
                                    <p class="text-lg"><i>(Dupla héthez
                                            <b>{{ $minimumDoubleRankupDutyTime - $userSumOfDutyTime }} perc</b> kell
                                            még)</i></p>
                                @else
                                    <p class="text-lg"><i>(<b>Megvan</b> a dupla héthez a szolgálati időd)</i></p>
                                @endif
                            @endif
                            <p>Ennyi időt kell még szolgálatban lenned, hogy első legyél:
                                <b>{{ $minutesLeftUntilHavingTopDutyTime }} perc</b></p>

                            <br>

                            <p class="text-lg"><b>OMSZ jelentések száma:</b> {{ $allReportCount }} darab</p>
                            <p>Az OMSZ eddig összesen <b>{{ $sumOfDutyTime }} percet</b> töltött szolgálatban.</p>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $rankingIconClass = function ($rank) {
                    return match ((int) $rank) {
                        1 => 'dashboard-stat-goblet dashboard-stat-goblet-gold',
                        2 => 'dashboard-stat-goblet dashboard-stat-goblet-silver',
                        3 => 'dashboard-stat-goblet dashboard-stat-goblet-bronze',
                        default => null,
                    };
                };
            @endphp

            <div class="row my-5">
                <div class="col-md-12 col-sm-12">
                    <div class="bg-gray-50 dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-200 view-reports-padding">
                            <p class="top5 text-lg">Saját statisztika (Összesen)</p>

                            <div class="grid">
                                <p class="text-lg">
                                    <b>Leadott jelentések:</b>
                                    {{ $personalTotalStats['reports']['value'] }} darab
                                    <span class="ms-4 font-semibold">
                                        @if ($rankingIconClass($personalTotalStats['reports']['ranking']['rank']))
                                            <span class="{{ $rankingIconClass($personalTotalStats['reports']['ranking']['rank']) }}" aria-hidden="true"></span>
                                        @endif
                                        {{ $personalTotalStats['reports']['ranking']['display'] }}
                                    </span>
                                    <span class="dashboard-stat-help relative inline-flex ms-1">
                                        <span tabindex="0"
                                            class="dashboard-stat-help-button w-5 h-5 inline-flex items-center justify-center rounded-full text-white text-xs font-bold leading-none cursor-help focus:outline-none focus:ring-2 focus:ring-blue-300"
                                            aria-label="Leadott jelentések rangsor magyarázat">?</span>
                                        <span
                                            class="dashboard-stat-tooltip absolute left-0 top-7 z-20 rounded-md bg-gray-800 border border-gray-700 p-3 text-sm text-gray-100 shadow-lg">
                                            @if ($personalTotalStats['reports']['ranking']['rank'] === 1)
                                                {{ $personalTotalStats['reports']['ranking']['tooltip'] }}
                                            @else
                                                <b>Előtted:</b> {{ $personalTotalStats['reports']['ranking']['previous_user_name'] }} ({{ $personalTotalStats['reports']['ranking']['previous_user_value'] }})
                                            @endif
                                        </span>
                                    </span>
                                </p>

                                <p class="text-lg">
                                    <b>Szolgálati idő:</b>
                                    {{ $personalTotalStats['duty_time']['value'] }} perc
                                    <span class="ms-4 font-semibold">
                                        @if ($rankingIconClass($personalTotalStats['duty_time']['ranking']['rank']))
                                            <span class="{{ $rankingIconClass($personalTotalStats['duty_time']['ranking']['rank']) }}" aria-hidden="true"></span>
                                        @endif
                                        {{ $personalTotalStats['duty_time']['ranking']['display'] }}
                                    </span>
                                    <span class="dashboard-stat-help relative inline-flex ms-1">
                                        <span tabindex="0"
                                            class="dashboard-stat-help-button w-5 h-5 inline-flex items-center justify-center rounded-full text-white text-xs font-bold leading-none cursor-help focus:outline-none focus:ring-2 focus:ring-blue-300"
                                            aria-label="Szolgálati idő rangsor magyarázat">?</span>
                                        <span
                                            class="dashboard-stat-tooltip absolute left-0 top-7 z-20 rounded-md bg-gray-800 border border-gray-700 p-3 text-sm text-gray-100 shadow-lg">
                                            @if ($personalTotalStats['duty_time']['ranking']['rank'] === 1)
                                                {{ $personalTotalStats['duty_time']['ranking']['tooltip'] }}
                                            @else
                                                <b>Előtted:</b> {{ $personalTotalStats['duty_time']['ranking']['previous_user_name'] }} ({{ $personalTotalStats['duty_time']['ranking']['previous_user_value'] }})
                                            @endif
                                        </span>
                                    </span>
                                </p>

                                <p class="text-lg">Top 3 jelentés leadó <b>{{ $personalTotalStats['top_three_report_count'] }}</b> alkalommal voltál összesen.</p>
                                <p class="text-lg"><b>Fizetés:</b> ${{ number_format($personalTotalStats['salary'], 0, '.', ' ') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row my-5">
                <div class="col-md-12 col-sm-12">
                    <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-200 view-reports-padding">
                            <p class="top5 text-lg">Felhívások</p>

                            @foreach ($discordAnnouncements as $discordAnnouncement)
                                <div class="p-6 my-4 bg-gray-100 dark:bg-gray-700">
                                    <p class="text-base italic float-right">{{ $discordAnnouncement['time'] }}</p>
                                    <p class="text-xl my-2 font-bold">{{ $discordAnnouncement['author'] }}</p>
                                    <p class="text-lg mx-3">{!! $discordAnnouncement['message'] !!}</p>
                                    @if (isset($discordAnnouncement['images']))
                                        <div class="row">
                                            @foreach ($discordAnnouncement['images'] as $discordAnnouncementImage)
                                                <a href="{{ $discordAnnouncementImage }}" target="_blank"
                                                    class="col-md-4 col-sm-12 my-3">
                                                    <img src="{{ $discordAnnouncementImage }}"
                                                        alt="Discord felhivások kép" class="border-solid border-1">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            var top5Table = new DataTable('#top5', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                responsive: true,
                ordering: false,
                searching: false,
                paging: false,
                info: false,
            });
        });
    </script>

    {{-- <script src="js/dashboard_ajax.js"></script> --}}
</x-app-layout>
