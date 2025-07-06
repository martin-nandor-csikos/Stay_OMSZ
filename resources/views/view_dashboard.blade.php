<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-200 view-reports-padding">
                        <p class="top5 text-lg">Top 5 jelentésíró a héten</p>

                        @if ($topReports->isEmpty())
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
                                @foreach ($topReports as $topReport)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $topReport->charactername }}</td>
                                    <td>{{ $topReport->reportCount }}</td>
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
                        <p class="text-xl my-1"><b>Jelentéseid száma:</b> {{ $reportCount }} darab</p>
                        @if ($minimumReportCount - $reportCount > 0)
                            <p class="text-lg"><i>(Minimum jelentés számhoz <b>{{ $minimumReportCount - $reportCount }} darab</b> kell még)</i></p>
                            <p class="text-lg"><i>(Dupla héthez <b>{{ $minimumDoubleRankupReportCount - $reportCount }} darab</b> jelentés kell még)</i></p>
                        @else
                            <p class="text-lg"><i>(<b>Megvan</b> a minimum jelentés számod)</i></p>
                            @if ($minimumDoubleRankupReportCount - $reportCount > 0)
                                <p class="text-lg"><i>(Dupla héthez <b>{{ $minimumDoubleRankupReportCount - $reportCount }} darab</b> jelentés kell még)</i></p>
                            @else
                                <p class="text-lg"><i>(<b>Megvan</b> a dupla héthez a jelentés számod)</i></p>
                            @endif
                        @endif

                        @if ($lastReportDate != '-')
                            <p><b>Utolsó felvitt jelentésed:</b> {{ \Illuminate\Support\Carbon::parse($lastReportDate)->format('Y.m.d H:i') }} </p>
                        @endif
                        <p>Az összes leadott jelentés <b>{{ $userReportPercentage }}%</b>-át te adtad le.</p>

                        <br>

                        @if ($dutyMinuteSum == null)
                            <p class="text-xl my-1"><b>Szolgálati idő:</b> 0 perc</p>
                            <p class="text-lg"><i>(Minimum szolgálati időhöz <b>{{ $minimumDutyTime }} perc</b> kell még)</i></p>
                            <p class="text-lg"><i>(Dupla héthez <b>{{ $minimumDoubleRankupDutyTime }} perc</b> kell még)</i></p>
                        @else
                            <p class="text-xl my-1"><b>Szolgálati idő:</b> {{ $dutyMinuteSum }} perc</p>
                            @if ($minimumDutyTime - $dutyMinuteSum > 0)
                                <p class="text-lg"><i>(Minimum szolgálati időhöz <b>{{ $minimumDutyTime - $dutyMinuteSum }} perc</b> kell még)</i></p>
                                <p class="text-lg"><i>(Dupla héthez <b>{{ $minimumDoubleRankupDutyTime - $dutyMinuteSum }} perc</b> kell még)</i></p>
                            @else
                                <p class="text-lg"><i>(<b>Megvan</b> a minimum szolgálati időd)</i></p>
                                @if ($minimumDoubleRankupDutyTime - $dutyMinuteSum > 0)
                                    <p class="text-lg"><i>(Dupla héthez <b>{{ $minimumDoubleRankupDutyTime - $dutyMinuteSum }} perc</b> kell még)</i></p>
                                @else
                                    <p class="text-lg"><i>(<b>Megvan</b> a dupla héthez a szolgálati időd)</i></p>
                                @endif
                            @endif
                        @endif
                        <p>Ennyi időt kell még szolgálatban lenned, hogy első legyél: <b>{{ $minutesUntilTopDutyTime }} perc</b></p>

                        <br>

                        <p class="text-lg"><b>OMSZ jelentések száma:</b> {{ $allReportCount }} darab</p>
                        @if ($sumDutyTime == 0)
                            <p>Az OMSZ eddig összesen <b>0 percet</b> töltött szolgálatban.</p>
                        @else
                            <p>Az OMSZ eddig összesen <b>{{ $sumDutyTime }} percet</b> töltött szolgálatban.</p>
                        @endif
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
                            <p class="text-xl my-1 font-bold">{{ $discordAnnouncement["author"] }}</p>
                            <p class="text-lg mx-5">{!! $discordAnnouncement['message'] !!}</p>
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
