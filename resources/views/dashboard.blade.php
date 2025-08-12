<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Főoldal') }}
        </h2>
    </x-slot>

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
                            <p class="top5 text-lg">Statisztikák</p>
                            <p class="text-xl my-1"><b>Jelentéseid száma:</b> {{ $userReportCount }} darab</p>
                            @if ($minimumReportCount - $userReportCount > 0)
                                <p class="text-lg"><i>(Minimum jelentés számhoz <b>{{ $minimumReportCount - $userReportCount }} darab</b> kell még)</i></p>
                                <p class="text-lg"><i>(Dupla héthez <b>{{ $minimumDoubleRankupReportCount - $userReportCount }} darab</b> jelentés kell még)</i></p>
                            @else
                                <p class="text-lg"><i>(<b>Megvan</b> a minimum jelentés számod)</i></p>
                                @if ($minimumDoubleRankupReportCount - $userReportCount > 0)
                                    <p class="text-lg"><i>(Dupla héthez <b>{{ $minimumDoubleRankupReportCount - $userReportCount }} darab</b> jelentés kell még)</i></p>
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
                                <p class="text-lg"><i>(Minimum szolgálati időhöz <b>{{ $minimumDutyTime - $userSumOfDutyTime }} perc</b> kell még)</i></p>
                                <p class="text-lg"><i>(Dupla héthez <b>{{ $minimumDoubleRankupDutyTime - $userSumOfDutyTime }} perc</b> kell még)</i></p>
                            @else
                                <p class="text-lg"><i>(<b>Megvan</b> a minimum szolgálati időd)</i></p>
                                @if ($minimumDoubleRankupDutyTime - $userSumOfDutyTime > 0)
                                    <p class="text-lg"><i>(Dupla héthez <b>{{ $minimumDoubleRankupDutyTime - $userSumOfDutyTime }} perc</b> kell még)</i></p>
                                @else
                                    <p class="text-lg"><i>(<b>Megvan</b> a dupla héthez a szolgálati időd)</i></p>
                                @endif
                            @endif
                            <p>Ennyi időt kell még szolgálatban lenned, hogy első legyél: <b>{{ $minutesLeftUntilHavingTopDutyTime }} perc</b></p>

                            <br>

                            <p class="text-lg"><b>OMSZ jelentések száma:</b> {{ $allReportCount }} darab</p>
                            <p>Az OMSZ eddig összesen <b>{{ $sumOfDutyTime }} percet</b> töltött szolgálatban.</p>
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
                                <p class="text-base italic float-right">{{ $discordAnnouncement["time"] }}</p>
                                <p class="text-xl my-2 font-bold">{{ $discordAnnouncement["author"] }}</p>
                                <p class="text-lg mx-3">{!! $discordAnnouncement['message'] !!}</p>
                                @if (isset($discordAnnouncement["images"]))
                                <div class="row">
                                    @foreach ($discordAnnouncement["images"] as $discordAnnouncementImage)
                                        <a href="{{ $discordAnnouncementImage }}" target="_blank" class="col-md-4 col-sm-12 my-3">
                                            <img src="{{ $discordAnnouncementImage }}" alt="Discord felhivások kép" class="border-solid border-1">
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
