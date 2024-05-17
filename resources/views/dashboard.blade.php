<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Főoldal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 view-reports-padding">
                            <p class="top5">Top 5 jelentésíró a héten</p>

                            @if ($topReports->isEmpty())
                            <p>Még senki nem csinált semmit :(</p>
                            @else
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Név</th>
                                        <th scope="col">Darab</th>
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
                <div class="col-md-6 col-12">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 view-reports-padding">
                            <p class="top5">Statisztikák</p>
                            <p><b>Jelentéseid száma:</b> {{ $reportCount }}</p>
                            @if ($lastReportDate != '-')
                                <p><b>Utolsó felvitt jelentésed:</b> {{ \Illuminate\Support\Carbon::parse($lastReportDate)->format('Y.m.d H:i') }} </p>                                
                            @endif

                            @if ($dutyMinuteSum == null)
                                <p><b>Szolgálati idő:</b> 0 perc</p>
                            @else
                                <p><b>Szolgálati idő:</b> {{ $dutyMinuteSum }} perc</p>
                            @endif
                            <p>Ennyi időt kell még szolgálatban lenned, hogy első legyél: <b>{{ $minutesUntilTopDutyTime }}</b> perc</p>
                            <p>Az összes leadott jelentés <b>{{ $userReportPercentage }}%</b>-át te adtad le.</p>
                            
                            <br>

                            <p><b>Összes jelentés száma:</b> {{ $allReportCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
