<div class="py-12" id="closed-weekly-stats-block">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 view-reports-padding">
                <p class="top5">Előző hét (lezárt)</p>
                <table class="table table-striped table-hover view-reports" id="closed-weekly-stats">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">IC név</th>
                            <th scope="col">Jelentések</th>
                            <th scope="col">Utolsó jelentés</th>
                            <th scope="col">Jelentések megtekintése</th>
                            <th scope="col">Szolgálati idő (perc)</th>
                            <th scope="col">Utolsó szolgálat leadása</th>
                            <th scope="col">Szolgálatok megtekintése</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($closedUserStats as $closedUserStat)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $closedUserStat->charactername }}</td>
                            <td>{{ $closedUserStat->reportCount }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($closedUserStat->lastReportDate)->format('Y.m.d H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.viewClosedUserReports', $closedUserStat->id) }}" method="get">
                                    <x-primary-button>
                                        {{ __('Jelentések') }}
                                    </x-primary-button>
                                </form>
                            </td>
                            <td>{{ $closedUserStat->dutyMinuteSum }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($closedUserStat->lastDutyDate)->format('Y.m.d H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.viewClosedUserDuty', $closedUserStat->id) }}" method="get">
                                    <x-primary-button>
                                        {{ __('Szolgálatok') }}
                                    </x-primary-button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>